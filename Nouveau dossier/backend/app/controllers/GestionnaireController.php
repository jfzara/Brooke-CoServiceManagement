<?php

require_once 'models/GestionnaireModel.php';

class GestionnaireController {
    private $gestionnaireModel;

    public function __construct(GestionnaireModel $gestionnaireModel) {
        $this->gestionnaireModel = $gestionnaireModel;
    }

    public function createGestionnaire($data) {
        if ($this->gestionnaireModel->creerGestionnaire($data)) {
            return "Gestionnaire créé avec succès!";
        } else {
            return "Erreur lors de la création du gestionnaire.";
        }
    }

    public function getGestionnaire($id) {
        $result = $this->gestionnaireModel->getGestionnaireById($id);
        if ($result) {
            return $result;
        } else {
            return "Gestionnaire non trouvé.";
        }
    }

    public function updateGestionnaire($id, $data) {
        if ($this->gestionnaireModel->modifierGestionnaire($id, $data)) {
            return "Gestionnaire mis à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour du gestionnaire.";
        }
    }

    public function deleteGestionnaire($id) {
        if ($this->gestionnaireModel->supprimerGestionnaire($id)) {
            return "Gestionnaire supprimé avec succès!";
        } else {
            return "Erreur lors de la suppression du gestionnaire.";
        }
    }
}

?>
