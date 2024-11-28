<?php

class TechnicienModel {
    private $conn;
    public $TechnicienID;
    public $UtilisateurID;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getTechnicienById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Technicien WHERE TechnicienID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->TechnicienID = $result['TechnicienID'];
            $this->UtilisateurID = $result['UtilisateurID'];
        }
        return $result;
    }

    public function creerTechnicien($data) {
        $stmt = $this->conn->prepare("INSERT INTO Technicien (UtilisateurID) VALUES (?)");
        $stmt->bind_param('i', $data['UtilisateurID']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierTechnicien($id, $data) {
        $stmt = $this->conn->prepare("UPDATE Technicien SET UtilisateurID = ? WHERE TechnicienID = ?");
        $stmt->bind_param('ii', $data['UtilisateurID'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerTechnicien($id) {
        $stmt = $this->conn->prepare("DELETE FROM Technicien WHERE TechnicienID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

?>
