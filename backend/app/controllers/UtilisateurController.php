<?php

require_once __DIR__ . '/../models/UtilisateurModel.php';

class UtilisateurController {
    private $utilisateurModel;

    public function __construct() {
        $this->utilisateurModel = new UtilisateurModel();
    }

    public function createUtilisateur($data) {
        if ($this->utilisateurModel->creerUtilisateur($data)) {
            return "Utilisateur créé avec succès!";
        } else {
            return "Erreur lors de la création de l'utilisateur.";
        }
    }

    public function getUtilisateur($id) {
        $result = $this->utilisateurModel->getUtilisateurById($id);
        if ($result) {
            return $result;
        } else {
            return "Utilisateur non trouvé.";
        }
    }

    public function updateUtilisateur($id, $data) {
        if ($this->utilisateurModel->modifierUtilisateur($id, $data)) {
            return "Utilisateur mis à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour de l'utilisateur.";
        }
    }

    public function deleteUtilisateur($id) {
        if ($this->utilisateurModel->supprimerUtilisateur($id)) {
            return "Utilisateur supprimé avec succès!";
        } else {
            return "Erreur lors de la suppression de l'utilisateur.";
        }
    }

    // Ajouter la méthode de connexion 
    public function connexion($email, $motDePasse) {
         $utilisateur = $this->utilisateurModel->verifierUtilisateur($email, $motDePasse); 
         if ($utilisateur) { // Connexion réussie, retourne les informations de l'utilisateur 
            return [ 
                'status' => 'success', 
                'message' => 'Connexion réussie', 
                'utilisateur' => $utilisateur 
            ]; 
        } else { // Échec de la connexion 
            return [ 
                'status' => 'error', 
                'message' => 'Échec de la connexion' 
            ]; 
        }
    }

    public function loginWithFacebook($facebookUser) {
        // Vérifiez si un utilisateur existe déjà avec le CompteFacebookID ou l'email
        $utilisateur = $this->utilisateurModel->getUtilisateurByFacebookID($facebookUser['id']);
        
        if (!$utilisateur) {
            // L'utilisateur n'existe pas, on le crée
            $data = [
                'Nom' => $facebookUser['name'],  // Vous pouvez diviser le nom complet si nécessaire
                'Prenom' => '',                  // Facebook ne fournit pas toujours le prénom séparé
                'Email' => $facebookUser['email'],
                'MotDePasse' => '',              // Pas nécessaire, car l'authentification se fait via Facebook
                'Type' => 'facebook',            // Vous pouvez avoir un type pour l'authentification
                'CompteGoogleID' => null,
                'CompteFacebookID' => $facebookUser['id'],
                'CompteBrookeID' => null,
            ];
    
            $result = $this->utilisateurModel->creerUtilisateur($data);
    
            if ($result) {
                return [ 
                    'status' => 'success',
                    'message' => 'Utilisateur créé avec succès avec Facebook',
                    'utilisateur' => $data
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Erreur lors de la création de l\'utilisateur avec Facebook'
                ];
            }
        } else {
            // L'utilisateur existe déjà, on renvoie ses informations
            return [ 
                'status' => 'success',
                'message' => 'Connexion réussie avec Facebook',
                'utilisateur' => $utilisateur 
            ];
        }
    }    
}

?>
