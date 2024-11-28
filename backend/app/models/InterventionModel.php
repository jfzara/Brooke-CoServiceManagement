<?php

class InterventionModel {
    private $conn;
    public $InterventionID;
    public $TechnicienID;
    public $ClientID;
    public $TypeIntervention;
    public $Description;
    public $Date;
    public $Heure;
    public $Statut;
    public $Commentaires;

    public function __construct() {
        global $conn; 
        $this->conn = $conn;
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
            $this->Date = $result['Date'];
            $this->Heure = $result['Heure'];
            $this->Statut = $result['Statut'];
            $this->Commentaires = $result['Commentaires'];
        }
        return $result;
    }

    public function creerIntervention($data) {
        $stmt = $this->conn->prepare("INSERT INTO Intervention (TechnicienID, ClientID, TypeIntervention, Description, Date, Heure, Statut, Commentaires) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iissssss', $data['TechnicienID'], $data['ClientID'], $data['TypeIntervention'], $data['Description'], $data['Date'], $data['Heure'], $data['Statut'], $data['Commentaires']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierIntervention($id, $data) {
        $stmt = $this->conn->prepare("UPDATE Intervention SET TechnicienID = ?, ClientID = ?, TypeIntervention = ?, Description = ?, Date = ?, Heure = ?, Statut = ?, Commentaires = ? WHERE InterventionID = ?");
        $stmt->bind_param('iissssssi', $data['TechnicienID'], $data['ClientID'], $data['TypeIntervention'], $data['Description'], $data['Date'], $data['Heure'], $data['Statut'], $data['Commentaires'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerIntervention($id) {
        $stmt = $this->conn->prepare("DELETE FROM Intervention WHERE InterventionID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    // Méthode pour mettre à jour uniquement le statut
    public function updateStatut($id, $statut, $date = null, $heure = null, $description = null) {
        // Récupérer l'état actuel de l'intervention
        $stmt = $this->conn->prepare("SELECT Statut, HeureDebut, HeureFin FROM Intervention WHERE InterventionID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if (!$result) {
            return false;  // Intervention non trouvée
        }
    
        $currentStatut = $result['Statut'];
        $currentHeureDebut = $result['HeureDebut'];
        $currentHeureFin = $result['HeureFin'];
    
        // Définir les transitions autorisées
        $validTransitions = [
            'Planifiee' => ['Planifiee', 'Assignee', 'Annulee'],
            'Assignee' => ['Assignee', 'Activee', 'Annulee'],
            'Activee' => ['Activee', 'En cours', 'Annulee'],
            'En cours' => ['En cours', 'Terminee', 'Annulee'],
            'Terminee' => ['Terminee'],
            'Annulee' => [] // L'intervention peut être annulée à n'importe quel statut
        ];

        // Vérifier si la transition est valide
        if (!in_array($statut, $validTransitions[$currentStatut])) {
            return false; // Transition invalide
        }

        // Préparer les champs à mettre à jour
        $updateFields = [];
        $params = [];
    
        // Si on change le statut, l'ajouter à la liste des champs à mettre à jour
        $updateFields[] = "Statut = ?";
        $params[] = $statut;
    
        // Si la date est fournie (par exemple, lors du changement à "Activée" ou "En cours"), on la met à jour
        if ($date) {
            $updateFields[] = "Date = ?";
            $params[] = $date;
        }
    
        // Si l'heure est fournie et qu'on passe en "En cours", on met l'heure de début à jour
        if ($statut == 'En cours' && $heure && !$currentHeureDebut) {
            $updateFields[] = "HeureDebut = ?";
            $params[] = $heure;
        }
    
        // Si on passe en "Terminée", on met l'heure de fin à jour
        if ($statut == 'Terminee' && $heure && !$currentHeureFin) {
            $updateFields[] = "HeureFin = ?";
            $params[] = $heure;
        }
    
         // Construire la requête dynamique pour mettre à jour les champs
        $query = "UPDATE Intervention SET " . implode(', ', $updateFields) . " WHERE InterventionID = ?";
        $stmt = $this->conn->prepare($query);
        // Ajouter l'ID de l'intervention à la liste des paramètres
        $params[] = $id;

        // Dynamically bind parameters (you should append 'i' for integer types and 's' for string types)
        $types = str_repeat('s', count($params) - 1) . 'i'; // 's' for all params except 'i' for the ID
        $stmt->bind_param($types, ...$params);

        // Exécuter la requête
        $stmt->execute();
        
        if($stmt->affected_rows > 0){
            $planningTechnicienModel = new PlanningTechnicienModel();
            return $planningTechnicienModel->updateCommentaire($description, $id);
        }
        return false;
    }    
}

?>
