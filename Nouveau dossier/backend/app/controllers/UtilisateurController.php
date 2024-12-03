<?php
require_once __DIR__ . '/../models/UtilisateurModel.php';

class UtilisateurController {
    private $utilisateurModel;

    public function __construct() {
        $this->utilisateurModel = new UtilisateurModel();
    }

    public function connexion($email, $motDePasse) {
        $utilisateur = $this->utilisateurModel->verifierUtilisateur($email, $motDePasse);
        
        if ($utilisateur) {
            return [
                'status' => 'success',
                'message' => 'Connexion réussie',
                'result' => [
                    'utilisateur' => [
                        'UtilisateurID' => $utilisateur['UtilisateurID'],
                        'Email' => $utilisateur['Email'],
                        'Nom' => $utilisateur['Nom'],
                        'Prenom' => $utilisateur['Prenom'],
                        'Type' => $utilisateur['Type']
                    ]
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Email ou mot de passe incorrect'
            ];
        }
    }

    public function createUtilisateur($data) {
        try {
            $result = $this->utilisateurModel->creerUtilisateur($data);
            return [
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Utilisateur créé avec succès' : 'Erreur lors de la création'
            ];
        } catch (Exception $e) {
            error_log("Exception dans createUtilisateur: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUtilisateur($id) {
        try {
            $utilisateur = $this->utilisateurModel->getUtilisateurById($id);
            return [
                'status' => $utilisateur ? 'success' : 'error',
                'result' => $utilisateur ? ['utilisateur' => $utilisateur] : null,
                'message' => $utilisateur ? null : 'Utilisateur non trouvé'
            ];
        } catch (Exception $e) {
            error_log("Exception dans getUtilisateur: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateUtilisateur($id, $data) {
        try {
            $result = $this->utilisateurModel->modifierUtilisateur($id, $data);
            return [
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Utilisateur mis à jour avec succès' : 'Erreur lors de la mise à jour'
            ];
        } catch (Exception $e) {
            error_log("Exception dans updateUtilisateur: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteUtilisateur($id) {
        try {
            $result = $this->utilisateurModel->supprimerUtilisateur($id);
            return [
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Utilisateur supprimé avec succès' : 'Erreur lors de la suppression'
            ];
        } catch (Exception $e) {
            error_log("Exception dans deleteUtilisateur: " . $e->getMessage());
            throw $e;
        }
    }
}
?>