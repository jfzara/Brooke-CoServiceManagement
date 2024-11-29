<?php

require_once __DIR__ . '/../models/InterventionModel.php';

class InterventionController {
    private $interventionModel;

    public function __construct() {
        $this->interventionModel = new InterventionModel();
    }

    public function createIntervention($data) {
        if ($this->interventionModel->creerIntervention($data)) {
            return "Intervention créée avec succès!";
        } else {
            return "Erreur lors de la création de l'intervention.";
        }
    }

    public function getIntervention($id) {
        $result = $this->interventionModel->getInterventionById($id);
        if ($result) {
            return $result;
        } else {
            return "Intervention non trouvée.";
        }
    }

    public function updateIntervention($id, $data) {
        if ($this->interventionModel->modifierIntervention($id, $data)) {
            return "Intervention mise à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour de l'intervention.";
        }
    }

    public function deleteIntervention($id) {
        if ($this->interventionModel->supprimerIntervention($id)) {
            return "Intervention supprimée avec succès!";
        } else {
            return "Erreur lors de la suppression de l'intervention.";
        }
    }
    
    public function getAllInterventions() {
        return $this->interventionModel->getAllInterventions();
    }

    // Méthode pour mettre à jour le statut d'une intervention avec gestion des heures
    public function updateStatut($id, $statut, $date = null, $heure = null, $description = null) {
        // Appeler la méthode du modèle pour mettre à jour le statut, la date et l'heure
        $result = $this->interventionModel->updateStatut($id, $statut, $date, $heure, $description);

        if ($result) {
            return "Statut de l'intervention mis à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour du statut de l'intervention ou transition invalide.";
        }
    }

    public function getInterventionsByTechnicien($technicienId) {
        $interventions = $this->interventionModel->getInterventionsByTechnicien($technicienId);
        return [
            'status' => 'success',
            'data' => $interventions
        ];
    }

}

?>
