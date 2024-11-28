<?php

require_once 'models/TechnicienModel.php';

class TechnicienController {
    private $technicienModel;

    public function __construct(TechnicienModel $technicienModel) {
        $this->technicienModel = $technicienModel;
    }

    public function createTechnicien($data) {
        if ($this->technicienModel->creerTechnicien($data)) {
            return "Technicien créé avec succès!";
        } else {
            return "Erreur lors de la création du technicien.";
        }
    }

    public function getTechnicien($id) {
        $result = $this->technicienModel->getTechnicienById($id);
        if ($result) {
            return $result;
        } else {
            return "Technicien non trouvé.";
        }
    }

    public function updateTechnicien($id, $data) {
        if ($this->technicienModel->modifierTechnicien($id, $data)) {
            return "Technicien mis à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour du technicien.";
        }
    }

    public function deleteTechnicien($id) {
        if ($this->technicienModel->supprimerTechnicien($id)) {
            return "Technicien supprimé avec succès!";
        } else {
            return "Erreur lors de la suppression du technicien.";
        }
    }
}

?>
