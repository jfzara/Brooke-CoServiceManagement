<?php
require_once __DIR__ . '/../models/InterventionModel.php';

class InterventionController {
    private $interventionModel;

    public function __construct() {
        $this->interventionModel = new InterventionModel();
    }

    public function getAllInterventions() {
        try {
            $interventions = $this->interventionModel->getAllInterventions();
            return [
                'status' => 'success',
                'data' => $interventions,
                'message' => 'Interventions récupérées avec succès'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function createIntervention($data) {
        try {
            $result = $this->interventionModel->creerIntervention($data);
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Intervention créée avec succès!'
                ];
            } else {
                throw new Exception("Erreur lors de la création de l'intervention.");
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getIntervention($id) {
        try {
            $result = $this->interventionModel->getInterventionById($id);
            if ($result) {
                return [
                    'status' => 'success',
                    'data' => $result
                ];
            } else {
                throw new Exception("Intervention non trouvée.");
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateIntervention($id, $data) {
        try {
            $result = $this->interventionModel->modifierIntervention($id, $data);
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Intervention mise à jour avec succès!'
                ];
            } else {
                throw new Exception("Erreur lors de la mise à jour de l'intervention.");
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteIntervention($id) {
        try {
            $result = $this->interventionModel->supprimerIntervention($id);
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Intervention supprimée avec succès!'
                ];
            } else {
                throw new Exception("Erreur lors de la suppression de l'intervention.");
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateStatut($id, $statut, $date = null, $heure = null, $description = null) {
        try {
            $result = $this->interventionModel->updateStatut($id, $statut, $date, $heure, $description);
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Statut de l\'intervention mis à jour avec succès!'
                ];
            } else {
                throw new Exception("Erreur lors de la mise à jour du statut de l'intervention.");
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getInterventionsByTechnicien($technicienId) {
        try {
            $interventions = $this->interventionModel->getInterventionsByTechnicien($technicienId);
            return [
                'status' => 'success',
                'data' => $interventions
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function assignerTechnicien($interventionId, $technicienId) {
        try {
            $result = $this->interventionModel->assignerTechnicien($interventionId, $technicienId);
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Technicien assigné avec succès!'
                ];
            } else {
                throw new Exception("Erreur lors de l'assignation du technicien.");
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
?>

