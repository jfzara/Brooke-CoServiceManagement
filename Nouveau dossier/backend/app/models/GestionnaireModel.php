<?php

class GestionnaireModel {
    private $conn;
    public $GestionnaireID;
    public $UtilisateurID;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getGestionnaireById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Gestionnaire WHERE GestionnaireID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->GestionnaireID = $result['GestionnaireID'];
            $this->UtilisateurID = $result['UtilisateurID'];
        }
        return $result;
    }

    public function creerGestionnaire($data) {
        $stmt = $this->conn->prepare("INSERT INTO Gestionnaire (UtilisateurID) VALUES (?)");
        $stmt->bind_param('i', $data['UtilisateurID']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierGestionnaire($id, $data) {
        $stmt = $this->conn->prepare("UPDATE Gestionnaire SET UtilisateurID = ? WHERE GestionnaireID = ?");
        $stmt->bind_param('ii', $data['UtilisateurID'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerGestionnaire($id) {
        $stmt = $this->conn->prepare("DELETE FROM Gestionnaire WHERE GestionnaireID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

?>
