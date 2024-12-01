<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

ini_set('display_errors', 1);
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
    error_log("Nouvelle requête - Méthode: " . $_SERVER['REQUEST_METHOD'] . ", Action: " . ($_GET['action'] ?? 'non définie'));
    
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
            error_log("Méthode non autorisée: " . $requestMethod);
            sendJsonResponse(['status' => 'error', 'message' => 'Méthode non autorisée'], 405);
    }
} catch (Exception $e) {
    error_log("Exception globale: " . $e->getMessage());
    sendJsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
}

function handleGetRequest($action) {
    try {
        error_log("Traitement requête GET - Action: " . $action);
        
        switch ($action) {
            case 'get_all_interventions':
                error_log("Récupération de toutes les interventions");
                $interventionController = new InterventionController();
                $response = $interventionController->getAllInterventions();
                sendJsonResponse($response);
                break;

            case 'get_all_techniciens':
                error_log("Récupération de tous les techniciens");
                $technicienController = new TechnicienController();
                $response = $technicienController->getAllTechniciens();
                sendJsonResponse($response);
                break;

            case 'get_technicien_interventions':
                if (!isset($_GET['technicienId'])) {
                    error_log("Erreur: ID du technicien manquant");
                    sendJsonResponse([
                        'status' => 'error',
                        'message' => 'ID du technicien manquant'
                    ], 400);
                    return;
                }
                
                error_log("Récupération des interventions pour le technicien: " . $_GET['technicienId']);
                
                $technicienController = new TechnicienController();
                $response = $technicienController->getTechnicienInterventions($_GET['technicienId']);
                
                error_log("Réponse du contrôleur: " . json_encode($response));
                
                sendJsonResponse($response);
                break;

            default:
                error_log("Action GET non reconnue: " . $action);
                sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Action non reconnue'
                ], 404);
        }
    } catch (Exception $e) {
        error_log("Exception dans handleGetRequest: " . $e->getMessage());
        throw $e;
    }
}

function handlePostRequest($action) {
    try {
        error_log("Traitement requête POST - Action: " . $action);
        $input = json_decode(file_get_contents("php://input"), true);
        error_log("Données POST reçues: " . json_encode($input));

        switch ($action) {
            case 'create_intervention':
                error_log("Création d'une nouvelle intervention");
                error_log("Données reçues pour création intervention: " . json_encode($input));

                if (!validateInterventionData($input)) {
                    error_log("Données d'intervention invalides");
                    sendJsonResponse([
                        'status' => 'error',
                        'message' => 'Données d\'intervention invalides ou incomplètes'
                    ], 400);
                    return;
                }

                $interventionController = new InterventionController();
                $response = $interventionController->createIntervention($input);
                
                error_log("Réponse création intervention: " . json_encode($response));
                sendJsonResponse($response);
                break;

            case 'login':
                error_log("Tentative de connexion");
                if (!isset($input['email']) || !isset($input['motDePasse'])) {
                    error_log("Données de login incomplètes");
                    sendJsonResponse([
                        'status' => 'error',
                        'message' => 'Email ou mot de passe manquant'
                    ], 400);
                    return;
                }

                $utilisateurController = new UtilisateurController();
                $response = $utilisateurController->connexion($input['email'], $input['motDePasse']);
                
                error_log("Réponse connexion: " . json_encode($response));
                
                $httpCode = $response['status'] === 'success' ? 200 : 401;
                sendJsonResponse($response, $httpCode);
                break;

            default:
                error_log("Action POST non reconnue: " . $action);
                sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Action non reconnue'
                ], 404);
        }
    } catch (Exception $e) {
        error_log("Exception dans handlePostRequest: " . $e->getMessage());
        throw $e;
    }
}

function validateInterventionData($data) {
    $required = ['TypeIntervention', 'Description', 'DebutIntervention', 
                 'FinIntervention', 'ClientID'];
    
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            error_log("Champ manquant ou vide: " . $field);
            return false;
        }
    }
    
    return true;
}

function sendJsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}
?>