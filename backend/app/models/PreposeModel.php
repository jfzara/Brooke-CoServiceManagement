<?php

class PreposeModel {
    private $conn;
    public $PreposeID;
    public $UtilisateurID;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPreposeById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Prepose WHERE PreposeID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->PreposeID = $result['PreposeID'];
            $this->UtilisateurID = $result['UtilisateurID'];
        }
        return $result;
    }

    public function creerPrepose($data) {
        $stmt = $this->conn->prepare("INSERT INTO Prepose (UtilisateurID) VALUES (?)");
        $stmt->bind_param('i', $data['UtilisateurID']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierPrepose($id, $data) {
        $stmt = $this->conn->prepare("UPDATE Prepose SET UtilisateurID = ? WHERE PreposeID = ?");
        $stmt->bind_param('ii', $data['UtilisateurID'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerPrepose($id) {
        $stmt = $this->conn->prepare("DELETE FROM Prepose WHERE PreposeID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

?>
