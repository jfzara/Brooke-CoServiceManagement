<?php

require_once 'models/ClientModel.php';

class ClientController {
    private $clientModel;

    public function __construct(ClientModel $clientModel) {
        $this->clientModel = $clientModel;
    }

    public function createClient($data) {
        if ($this->clientModel->creerClient($data)) {
            return "Client créé avec succès!";
        } else {
            return "Erreur lors de la création du client.";
        }
    }

    public function getClient($id) {
        $result = $this->clientModel->getClientById($id);
        if ($result) {
            return $result;
        } else {
            return "Client non trouvé.";
        }
    }

    public function updateClient($id, $data) {
        if ($this->clientModel->modifierClient($id, $data)) {
            return "Client mis à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour du client.";
        }
    }

    public function deleteClient($id) {
        if ($this->clientModel->supprimerClient($id)) {
            return "Client supprimé avec succès!";
        } else {
            return "Erreur lors de la suppression du client.";
        }
    }
}

?>
