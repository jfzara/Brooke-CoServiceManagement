<?php

class NotificationClientModel {
    private $conn;
    public $NotificationID;
    public $TechnicienID;
    public $ClientID;
    public $TypeNotification;
    public $Message;
    public $DateEnvoi;
    public $HeureEnvoi;
    public $Lu;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getNotificationClientById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM NotificationClient WHERE NotificationID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->NotificationID = $result['NotificationID'];
            $this->TechnicienID = $result['TechnicienID'];
            $this->ClientID = $result['ClientID'];
            $this->TypeNotification = $result['TypeNotification'];
            $this->Message = $result['Message'];
            $this->DateEnvoi = $result['DateEnvoi'];
            $this->HeureEnvoi = $result['HeureEnvoi'];
            $this->Lu = $result['Lu'];
        }
        return $result;
    }

    public function creerNotificationClient($data) {
        $stmt = $this->conn->prepare("INSERT INTO NotificationClient (TechnicienID, ClientID, TypeNotification, Message, DateEnvoi, HeureEnvoi, Lu) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iissssi', $data['TechnicienID'], $data['ClientID'], $data['TypeNotification'], $data['Message'], $data['DateEnvoi'], $data['HeureEnvoi'], $data['Lu']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierNotificationClient($id, $data) {
        $stmt = $this->conn->prepare("UPDATE NotificationClient SET TechnicienID = ?, ClientID = ?, TypeNotification = ?, Message = ?, DateEnvoi = ?, HeureEnvoi = ?, Lu = ? WHERE NotificationID = ?");
        $stmt->bind_param('iissssii', $data['TechnicienID'], $data['ClientID'], $data['TypeNotification'], $data['Message'], $data['DateEnvoi'], $data['HeureEnvoi'], $data['Lu'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerNotificationClient($id) {
        $stmt = $this->conn->prepare("DELETE FROM NotificationClient WHERE NotificationID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

?>
