<?php

class NotificationTechnicienModel {
    private $conn;
    public $NotificationID;
    public $TechnicienID;
    public $TypeNotification;
    public $Message;
    public $DateEnvoi;
    public $HeureEnvoi;
    public $Lu;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getNotificationTechnicienById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM NotificationTechnicien WHERE NotificationID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->NotificationID = $result['NotificationID'];
            $this->TechnicienID = $result['TechnicienID'];
            $this->TypeNotification = $result['TypeNotification'];
            $this->Message = $result['Message'];
            $this->DateEnvoi = $result['DateEnvoi'];
            $this->HeureEnvoi = $result['HeureEnvoi'];
            $this->Lu = $result['Lu'];
        }
        return $result;
    }

    public function creerNotificationTechnicien($data) {
        $stmt = $this->conn->prepare("INSERT INTO NotificationTechnicien (TechnicienID, TypeNotification, Message, DateEnvoi, HeureEnvoi, Lu) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issssi', $data['TechnicienID'], $data['TypeNotification'], $data['Message'], $data['DateEnvoi'], $data['HeureEnvoi'], $data['Lu']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierNotificationTechnicien($id, $data) {
        $stmt = $this->conn->prepare("UPDATE NotificationTechnicien SET TechnicienID = ?, TypeNotification = ?, Message = ?, DateEnvoi = ?, HeureEnvoi = ?, Lu = ? WHERE NotificationID = ?");
        $stmt->bind_param('issssii', $data['TechnicienID'], $data['TypeNotification'], $data['Message'], $data['DateEnvoi'], $data['HeureEnvoi'], $data['Lu'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerNotificationTechnicien($id) {
        $stmt = $this->conn->prepare("DELETE FROM NotificationTechnicien WHERE NotificationID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

?>
