<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/php_errors.log');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../app/controllers/UtilisateurController.php';
require_once '../app/controllers/TechnicienController.php';
require_once '../app/controllers/InterventionController.php';

try {
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    switch ($requestMethod) {
        case 'GET':
            handleGetRequest($action);
            break;
        case 'POST':
            handlePostRequest($action);
            break;
        default:
            sendJsonResponse(['status' => 'error', 'message' => 'Méthode non autorisée'], 405);
    }
} catch (Exception $e) {
    sendJsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
}

function handleGetRequest($action) {
    try {
        switch ($action) {
            case 'get_all_interventions':
                $interventionController = new InterventionController();
                $response = $interventionController->getAllInterventions();
                sendJsonResponse($response);
                break;

            case 'get_all_techniciens':
                $technicienController = new TechnicienController();
                $response = $technicienController->getAllTechniciens();
                sendJsonResponse($response);
                break;

            case 'get_techniciens_disponibles':
                if (!isset($_GET['dateDebut']) || !isset($_GET['dateFin'])) {
                    sendJsonResponse([
                        'status' => 'error',
                        'message' => 'Les dates de début et de fin sont requises'
                    ], 400);
                    return;
                }
                $technicienController = new TechnicienController();
                $response = $technicienController->getTechniciensDisponibles($_GET['dateDebut'], $_GET['dateFin']);
                sendJsonResponse($response);
                break;

            case 'get_technicien_interventions':
                if (!isset($_GET['technicienId'])) {
                    sendJsonResponse([
                        'status' => 'error',
                        'message' => 'ID du technicien manquant'
                    ], 400);
                    return;
                }
                
                $technicienController = new TechnicienController();
                $response = $technicienController->getTechnicienInterventions($_GET['technicienId']);
                sendJsonResponse($response);
                break;

            default:
                sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Action non reconnue'
                ], 404);
        }
    } catch (Exception $e) {
        sendJsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}

function handlePostRequest($action) {
    try {
        $input = json_decode(file_get_contents("php://input"), true);

        switch ($action) {
            case 'create_intervention':
                if (!validateInterventionData($input)) {
                    sendJsonResponse([
                        'status' => 'error',
                        'message' => 'Données d\'intervention invalides ou incomplètes'
                    ], 400);
                    return;
                }

                $interventionController = new InterventionController();
                $response = $interventionController->createIntervention($input);
                sendJsonResponse($response);
                break;

            case 'login':
                if (!isset($input['email']) || !isset($input['motDePasse'])) {
                    sendJsonResponse([
                        'status' => 'error',
                        'message' => 'Email ou mot de passe manquant'
                    ], 400);
                    return;
                }

                $utilisateurController = new UtilisateurController();
                $response = $utilisateurController->connexion($input['email'], $input['motDePasse']);
                $httpCode = $response['status'] === 'success' ? 200 : 401;
                sendJsonResponse($response, $httpCode);
                break;

            default:
                sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Action non reconnue'
                ], 404);
        }
    } catch (Exception $e) {
        sendJsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}

function validateInterventionData($data) {
    $required = ['TypeIntervention', 'Description', 'DebutIntervention',
                'FinIntervention', 'ClientID'];

    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            return false;
        }
    }

    return true;
}

function sendJsonResponse($data, $httpCode = 200) {
    if (!headers_sent()) {
        http_response_code($httpCode);
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}
?>