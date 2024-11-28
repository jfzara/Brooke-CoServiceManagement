<?php

require_once 'models/DisponibiliteModel.php';

class DisponibiliteController {
    private $disponibiliteModel;

    public function __construct(DisponibiliteModel $disponibiliteModel) {
        $this->disponibiliteModel = $disponibiliteModel;
    }

    public function createDisponibilite($data) {
        if ($this->disponibiliteModel->creerDisponibilite($data)) {
            return "Disponibilité créée avec succès!";
        } else {
            return "Erreur lors de la création de la disponibilité.";
        }
    }

    public function getDisponibilite($id) {
        $result = $this->disponibiliteModel->getDisponibiliteById($id);
        if ($result) {
            return $result;
        } else {
            return "Disponibilité non trouvée.";
        }
    }

    public function updateDisponibilite($id, $data) {
        if ($this->disponibiliteModel->modifierDisponibilite($id, $data)) {
            return "Disponibilité mise à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour de la disponibilité.";
        }
    }

    public function deleteDisponibilite($id) {
        if ($this->disponibiliteModel->supprimerDisponibilite($id)) {
            return "Disponibilité supprimée avec succès!";
        } else {
            return "Erreur lors de la suppression de la disponibilité.";
        }
    }
}

?>
