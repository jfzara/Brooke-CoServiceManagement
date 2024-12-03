<?php

require_once 'models/PreposeModel.php';

class PreposeController {
    private $preposeModel;

    public function __construct(PreposeModel $preposeModel) {
        $this->preposeModel = $preposeModel;
    }

    public function createPrepose($data) {
        if ($this->preposeModel->creerPrepose($data)) {
            return "Préposé créé avec succès!";
        } else {
            return "Erreur lors de la création du préposé.";
        }
    }

    public function getPrepose($id) {
        $result = $this->preposeModel->getPreposeById($id);
        if ($result) {
            return $result;
        } else {
            return "Préposé non trouvé.";
        }
    }

    public function updatePrepose($id, $data) {
        if ($this->preposeModel->modifierPrepose($id, $data)) {
            return "Préposé mis à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour du préposé.";
        }
    }

    public function deletePrepose($id) {
        if ($this->preposeModel->supprimerPrepose($id)) {
            return "Préposé supprimé avec succès!";
        } else {
            return "Erreur lors de la suppression du préposé.";
        }
    }
}

?>
