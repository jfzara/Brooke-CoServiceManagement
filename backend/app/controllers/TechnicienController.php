<?php
require_once __DIR__ . '/../models/TechnicienModel.php';

class TechnicienController {
    private $technicienModel;

    public function __construct() {
        $this->technicienModel = new TechnicienModel();
    }

    public function createTechnicien($data) {
        if ($this->technicienModel->creerTechnicien($data)) {
            return [
                'status' => 'success',
                'message' => 'Technicien créé avec succès!'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Erreur lors de la création du technicien.'
        ];
    }

    public function getTechnicien($id) {
        $result = $this->technicienModel->getTechnicienById($id);
        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Technicien non trouvé.'
        ];
    }

    public function getTechnicienInterventions($technicienId) {
        try {
            $interventions = $this->technicienModel->getInterventions($technicienId);
            return [
                'status' => 'success',
                'data' => $interventions
            ];
        } catch (Exception $e) {
            error_log("Erreur dans getTechnicienInterventions: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des interventions'
            ];
        }
    }

    public function updateTechnicien($id, $data) {
        if ($this->technicienModel->modifierTechnicien($id, $data)) {
            return [
                'status' => 'success',
                'message' => 'Technicien mis à jour avec succès!'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Erreur lors de la mise à jour du technicien.'
        ];
    }

    public function deleteTechnicien($id) {
        if ($this->technicienModel->supprimerTechnicien($id)) {
            return [
                'status' => 'success',
                'message' => 'Technicien supprimé avec succès!'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Erreur lors de la suppression du technicien.'
        ];
    }
}
?>