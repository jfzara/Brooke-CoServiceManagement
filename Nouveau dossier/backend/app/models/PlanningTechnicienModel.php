<?php

class PlanningTechnicienModel {
    private $conn;
    public $PlanningID;
    public $TechnicienID;
    public $InterventionID;
    public $DateIntervention;
    public $HeureDebut;
    public $HeureFin;
    public $Commentaires;

    public function __construct() {
        global $conn; 
        $this->conn = $conn;
    }

    public function getPlanningTechnicienById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM PlanningTechnicien WHERE PlanningID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->PlanningID = $result['PlanningID'];
            $this->TechnicienID = $result['TechnicienID'];
            $this->InterventionID = $result['InterventionID'];
            $this->DateIntervention = $result['DateIntervention'];
            $this->HeureDebut = $result['HeureDebut'];
            $this->HeureFin = $result['HeureFin'];
            $this->Commentaires = $result['Commentaires'];
        }
        return $result;
    }

    public function getPlanningByTechnicienByIdWithMoreInfos($technicienID) {
        // Requête pour récupérer les données de la planification
        $sql = "
            SELECT 
                 pt.PlanningID, pt.TechnicienID, pt.InterventionID, pt.DateIntervention, pt.HeureDebut, pt.HeureFin, pt.Commentaires AS PlanningCommentaires,
                i.InterventionID, i.TechnicienID, i.ClientID, i.TypeIntervention, i.Description, i.Date, i.Heure, i.Statut, i.Commentaires AS InterventionCommentaires,
                t.TechnicienID, t.UtilisateurID, u.Nom, u.Prenom
                FROM PlanningTechnicien pt
                JOIN Intervention i ON pt.InterventionID = i.InterventionID
                JOIN Technicien t ON pt.TechnicienID = t.TechnicienID
                JOIN Utilisateur u ON t.UtilisateurID = u.UtilisateurID
                WHERE t.TechnicienID = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $technicienID);
        $stmt->execute();
        $result = $stmt->get_result();
        $planningData = [];

        while ($row = $result->fetch_assoc()) {
            $planningData[] = [
                'PlanningID' => $row['PlanningID'],
                'TechnicienID' => $row['TechnicienID'],
                'InterventionID' => $row['InterventionID'],
                'DateIntervention' => $row['DateIntervention'],
                'HeureDebut' => $row['HeureDebut'],
                'HeureFin' => $row['HeureFin'],
                'PlanningCommentaires' => $row['PlanningCommentaires'],
                'ClientID' => $row['ClientID'],
                'TypeIntervention' => $row['TypeIntervention'],
                'Description' => $row['Description'],
                'Date' => $row['Date'],
                'Heure' => $row['Heure'],
                'Statut' => $row['Statut'],
                'InterventionCommentaires' => $row['InterventionCommentaires'],
                'UtilisateurID' => $row['UtilisateurID'],
                'Nom' => $row['Nom'],
                'Prenom' => $row['Prenom']
            ];
        }
        return $planningData;
    }

    public function creerPlanningTechnicien($data) {
        $stmt = $this->conn->prepare("INSERT INTO PlanningTechnicien (TechnicienID, InterventionID, DateIntervention, HeureDebut, HeureFin, Commentaires) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iissss', $data['TechnicienID'], $data['InterventionID'], $data['DateIntervention'], $data['HeureDebut'], $data['HeureFin'], $data['Commentaires']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierPlanningTechnicien($id, $data) {
        $stmt = $this->conn->prepare("UPDATE PlanningTechnicien SET TechnicienID = ?, InterventionID = ?, DateIntervention = ?, HeureDebut = ?, HeureFin = ?, Commentaires = ? WHERE PlanningID = ?");
        $stmt->bind_param('iissssi', $data['TechnicienID'], $data['InterventionID'], $data['DateIntervention'], $data['HeureDebut'], $data['HeureFin'], $data['Commentaires'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerPlanningTechnicien($id) {
        $stmt = $this->conn->prepare("DELETE FROM PlanningTechnicien WHERE PlanningID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function updateCommentaire($commentaires, $interventionID){
        $stmt = $this->conn->prepare("UPDATE PlanningTechnicien SET Commentaires = ? WHERE InterventionID = ?");
        $stmt->bind_param('si',$commentaires, $interventionID);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}

?>
