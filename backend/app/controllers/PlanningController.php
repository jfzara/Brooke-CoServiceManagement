<?php

require_once __DIR__ . '/../models/PlanningTechnicienModel.php';

class PlanningController {
    private $planningModel;

    public function __construct() {
        $this->planningModel = new PlanningTechnicienModel();
    }

    public function createPlanning($data) {
        if ($this->planningModel->creerPlanningTechnicien($data)) {
            return "Planning créé avec succès!";
        } else {
            return "Erreur lors de la création du planning.";
        }
    }

    public function getPlanning($id) {
        $result = $this->planningModel->getPlanningTechnicienById($id);
        if ($result) {
            return $result;
        } else {
            return "Planning non trouvé.";
        }
    }

    public function getPlanningWithMoreInfos($id) {
        $result = $this->planningModel->getPlanningByTechnicienByIdWithMoreInfos($id);
        if ($result) {
            return $result;
        } else {
            return [];
        }
    }

    public function updatePlanning($id, $data) {
        if ($this->planningModel->modifierPlanningTechnicien($id, $data)) {
            return "Planning mis à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour du planning.";
        }
    }

    public function deletePlanning($id) {
        if ($this->planningModel->supprimerPlanningTechnicien($id)) {
            return "Planning supprimé avec succès!";
        } else {
            return "Erreur lors de la suppression du planning.";
        }
    }
}

?>
