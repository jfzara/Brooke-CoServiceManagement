<?php
class TechnicienController {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli('localhost', 'root', '', 'brookeandco');
            if ($this->conn->connect_error) {
                throw new Exception("Erreur de connexion à la base de données");
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getAllTechniciens() {
        try {
            // Jointure avec la table Utilisateur pour récupérer les informations
            $sql = "SELECT t.TechnicienID, t.UtilisateurID, 
                           u.Nom, u.Prenom, u.Email 
                    FROM Technicien t
                    INNER JOIN Utilisateur u ON t.UtilisateurID = u.UtilisateurID 
                    ORDER BY u.Nom, u.Prenom";
            
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'exécution de la requête");
            }

            $result = $stmt->get_result();
            $techniciens = [];
            
            while ($row = $result->fetch_assoc()) {
                $techniciens[] = [
                    'TechnicienID' => $row['TechnicienID'],
                    'UtilisateurID' => $row['UtilisateurID'],
                    'Nom' => $row['Nom'],
                    'Prenom' => $row['Prenom'],
                    'Email' => $row['Email']
                ];
            }

            $stmt->close();

            return [
                'status' => 'success',
                'data' => $techniciens,
                'message' => count($techniciens) . ' technicien(s) trouvé(s)'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des techniciens: ' . $e->getMessage()
            ];
        }
    }

    public function getTechniciensDisponibles($dateDebut, $dateFin) {
        try {
            $sql = "SELECT DISTINCT t.TechnicienID, t.UtilisateurID,
                           u.Nom, u.Prenom, u.Email 
                    FROM Technicien t
                    INNER JOIN Utilisateur u ON t.UtilisateurID = u.UtilisateurID 
                    WHERE NOT EXISTS (
                        SELECT 1 
                        FROM Intervention i 
                        WHERE i.TechnicienID = t.TechnicienID 
                        AND (
                            (i.DebutIntervention BETWEEN ? AND ?)
                            OR (i.FinIntervention BETWEEN ? AND ?)
                            OR (? BETWEEN i.DebutIntervention AND i.FinIntervention)
                        )
                    )
                    ORDER BY u.Nom, u.Prenom";
    
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }
    
            $stmt->bind_param("sssss", $dateDebut, $dateFin, $dateDebut, $dateFin, $dateDebut);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'exécution de la requête");
            }
    
            $result = $stmt->get_result();
            $techniciens = [];
            
            while ($row = $result->fetch_assoc()) {
                $techniciens[] = [
                    'TechnicienID' => $row['TechnicienID'],
                    'UtilisateurID' => $row['UtilisateurID'],
                    'Nom' => $row['Nom'],
                    'Prenom' => $row['Prenom'],
                    'Email' => $row['Email']
                ];
            }
    
            $stmt->close();
    
            return [
                'status' => 'success',
                'data' => $techniciens,
                'message' => count($techniciens) . ' technicien(s) disponible(s)'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des techniciens disponibles: ' . $e->getMessage()
            ];
        }
    }

    public function getTechnicienInterventions($technicienId) {
        try {
            $checkTechnicien = $this->conn->prepare("SELECT TechnicienID FROM Technicien WHERE TechnicienID = ?");
            $checkTechnicien->bind_param("i", $technicienId);
            $checkTechnicien->execute();
            $techResult = $checkTechnicien->get_result();
            
            if ($techResult->num_rows === 0) {
                return [
                    'status' => 'error',
                    'message' => 'Technicien non trouvé',
                    'data' => []
                ];
            }

            $sql = "SELECT 
                        i.*,
                        c.Nom as ClientNom,
                        c.Prenom as ClientPrenom,
                        cl.Adresse as ClientAdresse,
                        cl.Telephone as ClientTelephone
                    FROM Intervention i
                    LEFT JOIN Client cl ON i.ClientID = cl.ClientID
                    LEFT JOIN Utilisateur c ON cl.UtilisateurID = c.UtilisateurID
                    WHERE i.TechnicienID = ?
                    ORDER BY i.DebutIntervention DESC";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête");
            }

            $stmt->bind_param("i", $technicienId);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur d'exécution de la requête");
            }

            $result = $stmt->get_result();
            $interventions = [];

            while ($row = $result->fetch_assoc()) {
                $interventions[] = [
                    'InterventionID' => $row['InterventionID'],
                    'TechnicienID' => $row['TechnicienID'],
                    'ClientID' => $row['ClientID'],
                    'TypeIntervention' => $row['TypeIntervention'],
                    'Description' => $row['Description'],
                    'DebutIntervention' => $row['DebutIntervention'],
                    'FinIntervention' => $row['FinIntervention'],
                    'StatutIntervention' => $row['StatutIntervention'],
                    'Commentaires' => $row['Commentaires'],
                    'Client' => [
                        'Nom' => $row['ClientNom'],
                        'Prenom' => $row['ClientPrenom'],
                        'Adresse' => $row['ClientAdresse'],
                        'Telephone' => $row['ClientTelephone']
                    ]
                ];
            }

            $stmt->close();

            return [
                'status' => 'success',
                'message' => count($interventions) > 0 ? 'Interventions récupérées avec succès' : 'Aucune intervention trouvée',
                'data' => $interventions
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}
?>