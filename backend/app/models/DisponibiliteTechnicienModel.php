<?php

class DisponibiliteTechnicienModel {
    private $conn;
    public $DisponibiliteID;
    public $TechnicienID;
    public $Date;
    public $HeureDebut;
    public $HeureFin;

    public function __construct() {
        global $conn; 
        $this->conn = $conn;
    }

    public function getDisponibiliteTechnicienById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM DisponibiliteTechnicien WHERE DisponibiliteID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->DisponibiliteID = $result['DisponibiliteID'];
            $this->TechnicienID = $result['TechnicienID'];
            $this->Date = $result['Date'];
            $this->HeureDebut = $result['HeureDebut'];
            $this->HeureFin = $result['HeureFin'];
        }
        return $result;
    }

    public function creerDisponibiliteTechnicien($data) {
        $stmt = $this->conn->prepare("INSERT INTO DisponibiliteTechnicien (TechnicienID, Date, HeureDebut, HeureFin) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $data['TechnicienID'], $data['Date'], $data['HeureDebut'], $data['HeureFin']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierDisponibiliteTechnicien($id, $data) {
        $stmt = $this->conn->prepare("UPDATE DisponibiliteTechnicien SET TechnicienID = ?, Date = ?, HeureDebut = ?, HeureFin = ? WHERE DisponibiliteID = ?");
        $stmt->bind_param('isssi', $data['TechnicienID'], $data['Date'], $data['HeureDebut'], $data['HeureFin'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerDisponibiliteTechnicien($id) {
        $stmt = $this->conn->prepare("DELETE FROM DisponibiliteTechnicien WHERE DisponibiliteID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

?>
