<?php
class InterventionModel {
    private $conn;
    public $InterventionID;
    public $TechnicienID;
    public $ClientID;
    public $TypeIntervention;
    public $Description;
    public $DebutIntervention;
    public $FinIntervention;
    public $StatutIntervention;
    public $Commentaires;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getAllInterventions() {
        $query = "SELECT * FROM Intervention";
        $result = $this->conn->query($query);
        $interventions = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $interventions[] = $row;
            }
        }
        
        return $interventions;
    }

    public function getInterventionById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Intervention WHERE InterventionID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->InterventionID = $result['InterventionID'];
            $this->TechnicienID = $result['TechnicienID'];
            $this->ClientID = $result['ClientID'];
            $this->TypeIntervention = $result['TypeIntervention'];
            $this->Description = $result['Description'];
            $this->DebutIntervention = $result['DebutIntervention'];
            $this->FinIntervention = $result['FinIntervention'];
            $this->StatutIntervention = $result['StatutIntervention'];
            $this->Commentaires = $result['Commentaires'];
        }
        return $result;
    }

    public function creerIntervention($data) {
        $stmt = $this->conn->prepare("INSERT INTO Intervention (TechnicienID, ClientID, TypeIntervention, Description, DebutIntervention, FinIntervention, StatutIntervention, Commentaires) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iissssss', 
            $data['TechnicienID'], 
            $data['ClientID'], 
            $data['TypeIntervention'], 
            $data['Description'],
            $data['DebutIntervention'],
            $data['FinIntervention'],
            $data['StatutIntervention'],
            $data['Commentaires']
        );
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierIntervention($id, $data) {
        $stmt = $this->conn->prepare("UPDATE Intervention SET TechnicienID = ?, ClientID = ?, TypeIntervention = ?, Description = ?, DebutIntervention = ?, FinIntervention = ?, StatutIntervention = ?, Commentaires = ? WHERE InterventionID = ?");
        $stmt->bind_param('iissssssi', 
            $data['TechnicienID'], 
            $data['ClientID'], 
            $data['TypeIntervention'], 
            $data['Description'],
            $data['DebutIntervention'],
            $data['FinIntervention'],
            $data['StatutIntervention'],
            $data['Commentaires'],
            $id
        );
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerIntervention($id) {
        $stmt = $this->conn->prepare("DELETE FROM Intervention WHERE InterventionID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function updateStatut($id, $statut, $date = null, $heure = null, $description = null) {
        $stmt = $this->conn->prepare("SELECT StatutIntervention, DebutIntervention, FinIntervention FROM Intervention WHERE InterventionID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if (!$result) {
            return false;
        }
    
        $currentStatut = $result['StatutIntervention'];
        $currentDebut = $result['DebutIntervention'];
        $currentFin = $result['FinIntervention'];
    
        $validTransitions = [
            'En attente' => ['En attente', 'En cours'],
            'En cours' => ['En cours', 'Terminé'],
            'Terminé' => ['Terminé']
        ];

        if (!isset($validTransitions[$currentStatut]) || !in_array($statut, $validTransitions[$currentStatut])) {
            return false;
        }

        $updateFields = [];
        $params = [];
    
        $updateFields[] = "StatutIntervention = ?";
        $params[] = $statut;
    
        if ($date) {
            $updateFields[] = "DebutIntervention = ?";
            $params[] = $date;
        }
    
        if ($statut == 'En cours' && $heure && !$currentDebut) {
            $updateFields[] = "DebutIntervention = ?";
            $params[] = $heure;
        }
    
        if ($statut == 'Terminé' && $heure && !$currentFin) {
            $updateFields[] = "FinIntervention = ?";
            $params[] = $heure;
        }
    
        $query = "UPDATE Intervention SET " . implode(', ', $updateFields) . " WHERE InterventionID = ?";
        $stmt = $this->conn->prepare($query);
        $params[] = $id;
        $types = str_repeat('s', count($params) - 1) . 'i';
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        if($stmt->affected_rows > 0 && $description !== null){
            $planningTechnicienModel = new PlanningTechnicienModel();
            return $planningTechnicienModel->updateCommentaire($description, $id);
        }
        return $stmt->affected_rows > 0;
    }   
    
    public function getInterventionsByTechnicien($technicienId) {
        $query = "SELECT i.*, c.Nom as ClientNom, c.Prenom as ClientPrenom 
                  FROM Intervention i
                  LEFT JOIN Client c ON i.ClientID = c.ClientID
                  WHERE i.TechnicienID = ?
                  ORDER BY i.DebutIntervention ASC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $technicienId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $interventions = [];
        while ($row = $result->fetch_assoc()) {
            $interventions[] = $row;
        }
        
        return $interventions;
    }


}
?>