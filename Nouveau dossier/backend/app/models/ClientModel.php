<?php

class ClientModel {
    private $conn;
    public $ClientID;
    public $Adresse;
    public $Telephone;
    public $Demandes;
    public $UtilisateurID;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getClientById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Client WHERE ClientID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->ClientID = $result['ClientID'];
            $this->Adresse = $result['Adresse'];
            $this->Telephone = $result['Telephone'];
            $this->Demandes = $result['Demandes'];
            $this->UtilisateurID = $result['UtilisateurID'];
        }
        return $result;
    }

    public function creerClient($data) {
        $stmt = $this->conn->prepare("INSERT INTO Client (Adresse, Telephone, Demandes, UtilisateurID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $data['Adresse'], $data['Telephone'], $data['Demandes'], $data['UtilisateurID']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierClient($id, $data) {
        $stmt = $this->conn->prepare("UPDATE Client SET Adresse = ?, Telephone = ?, Demandes = ?, UtilisateurID = ? WHERE ClientID = ?");
        $stmt->bind_param('sssii', $data['Adresse'], $data['Telephone'], $data['Demandes'], $data['UtilisateurID'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerClient($id) {
        $stmt = $this->conn->prepare("DELETE FROM Client WHERE ClientID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

?>
