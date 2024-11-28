<?php

require_once 'models/NotificationModel.php';

class NotificationController {
    private $notificationModel;

    public function __construct(NotificationModel $notificationModel) {
        $this->notificationModel = $notificationModel;
    }

    public function createNotification($data) {
        if ($this->notificationModel->creerNotification($data)) {
            return "Notification créée avec succès!";
        } else {
            return "Erreur lors de la création de la notification.";
        }
    }

    public function getNotification($id) {
        $result = $this->notificationModel->getNotificationById($id);
        if ($result) {
            return $result;
        } else {
            return "Notification non trouvée.";
        }
    }

    public function updateNotification($id, $data) {
        if ($this->notificationModel->modifierNotification($id, $data)) {
            return "Notification mise à jour avec succès!";
        } else {
            return "Erreur lors de la mise à jour de la notification.";
        }
    }

    public function deleteNotification($id) {
        if ($this->notificationModel->supprimerNotification($id)) {
            return "Notification supprimée avec succès!";
        } else {
            return "Erreur lors de la suppression de la notification.";
        }
    }
}

?>
