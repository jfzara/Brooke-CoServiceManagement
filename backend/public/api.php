<?php
// Setup response headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include controllers
require_once '../app/controllers/UtilisateurController.php';

try {
    // Log request details
    error_log("Nouvelle requête - Méthode: " . $_SERVER['REQUEST_METHOD'] . ", Action: " . ($_GET['action'] ?? 'non définie'));
    
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    switch ($requestMethod) {
        case 'POST':
            handlePostRequest($action);
            break;
        default:
            sendJsonResponse(['status' => 'error', 'message' => 'Méthode non autorisée'], 405);
    }
} catch (Exception $e) {
    error_log("Exception globale: " . $e->getMessage());
    sendJsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
}

function handlePostRequest($action) {
    try {
        switch ($action) {
            case 'login':
                $input = json_decode(file_get_contents("php://input"), true);
                error_log("Données de login reçues: " . json_encode($input));
                
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
                
                error_log("Réponse du contrôleur: " . json_encode($response));
                
                $httpCode = $response['status'] === 'success' ? 200 : 401;
                sendJsonResponse($response, $httpCode);
                break;

            default:
                error_log("Action non reconnue: " . $action);
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

function sendJsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}
?>