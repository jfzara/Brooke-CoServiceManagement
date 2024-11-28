<?php
// Include controllers
require_once '../app/controllers/UtilisateurController.php';
require_once '../app/controllers/InterventionController.php';
require_once '../app/controllers/PlanningController.php';


// Setup response header
header('Content-Type: application/json');
// Ajouter ces en-têtes au début de votre fichier PHP
header("Access-Control-Allow-Origin: *"); // Autoriser le domaine de votre frontend
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Méthodes autorisées
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // En-têtes autorisés

// Inclure Guzzle
require_once '../vendor/autoload.php'; // Incluez Guzzle via Composer

use GuzzleHttp\Client;

// Handle request method and action
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($requestMethod) {
    case 'POST':
        handlePostRequest($action);
        break;
    case 'GET':
        handleGetRequest($action);
        break;
    case 'PUT':
        handlePutRequest($action);
        break;
    case 'DELETE':
        handleDeleteRequest($action);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
}

function handlePostRequest($action) {
    switch ($action) {
        case 'login':
            $input = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($input['email'], $input['motDePasse'])) {
                echo json_encode(['status' => 'error', 'message' => 'Email ou mot de passe manquant']);
                return; 
            }

            $email = $input['email'];
            $motDePasse = $input['motDePasse'];

            $utilisateurController = new UtilisateurController();
            $response = $utilisateurController->connexion($email, $motDePasse);
            echo json_encode(['result'=> $response]);
            break;

        case 'login_facebook':
            // Récupérer le token depuis la requête POST
            $data = json_decode(file_get_contents("php://input"), true);
            $accessToken = $data['token'] ?? null;
            
            if ($accessToken) {
                // Utiliser Guzzle pour faire une requête à l'API Graph de Facebook
                $client = new Client();
                $url = "https://graph.facebook.com/v12.0/me?fields=id,name,email&access_token=" . $accessToken;

                try {
                    // Effectuer la requête GET à l'API Graph de Facebook
                    $response = $client->request('GET', $url);
                    
                    // Récupérer la réponse JSON
                    $facebookUser = json_decode($response->getBody()->getContents(), true);

                    // Vérifier que les données de l'utilisateur sont bien présentes
                    if (isset($facebookUser['id'], $facebookUser['name'], $facebookUser['email'])) {
                        // Appeler la méthode du contrôleur pour gérer la connexion ou la création de l'utilisateur
                        $utilisateurController = new UtilisateurController();
                        $result = $utilisateurController->loginWithFacebook($facebookUser);

                        // Répondre au client avec le résultat
                        echo json_encode($result);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Impossible de récupérer les informations de l\'utilisateur Facebook']);
                    }
                } catch (\GuzzleHttp\Exception\RequestException $e) {
                    // Gérer les erreurs de la requête HTTP
                    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la connexion avec Facebook', 'details' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Token manquant']);
            }
            break;
        
        // Add more POST actions for other controllers here
        default:
            echo json_encode(['status' => 'error', 'message' => 'Action non reconnue']);
    }
}

function handleGetRequest($action) {
    switch ($action) {
        case "planning":
            if(isset($_GET['TechnicienID'])){
                $id = $_GET['TechnicienID'];
                $planningController = new PlanningController();
                $response = $planningController->getPlanningWithMoreInfos($id);
                echo json_encode($response);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID Technicien Invalide']);
            }
            break;
        case "interventions":  // Nouvelle route pour les interventions
            $interventionController = new InterventionController();
            $interventions = $interventionController->getAllInterventions();
            echo json_encode($interventions);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Action non reconnue']);
    }
}

function handlePutRequest($action) {
    switch ($action) {
        case "updateStatusIntervention": 
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['InterventionID']) && isset($data['Statut'])) {
                $date = isset($data['Date']) ? $data['Date'] : null;
                $heure = isset($data['Heure']) ? $data['Heure'] : null;
                $description = isset($data['Description']) ? $data['Description'] : null;
                $interventionController = new InterventionController();
                $result = $interventionController->updateStatut($data['InterventionID'], $data['Statut'], $date, $heure, $description);
                echo json_encode(['status' => 'success', 'message' => $result]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
            }
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Action non reconnue']);
    }
}

function handleDeleteRequest($action) {
    switch ($action) {
        case "deleteIntervention":

            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Action non reconnue']);
    }
}
?>
