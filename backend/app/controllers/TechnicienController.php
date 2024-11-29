<?php
class TechnicienController {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli('localhost', 'root', '', 'brookeandco');
            if ($this->conn->connect_error) {
                error_log("Erreur de connexion MySQL: " . $this->conn->connect_error);
                throw new Exception("Erreur de connexion à la base de données");
            }
            error_log("Connexion à la base de données réussie dans TechnicienController");
        } catch (Exception $e) {
            error_log("Exception dans le constructeur TechnicienController: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTechnicienInterventions($technicienId) {
        try {
            error_log("Début getTechnicienInterventions pour technicienId: " . $technicienId);
            
            // Vérifier d'abord si le technicien existe
            $checkTechnicien = $this->conn->prepare("SELECT TechnicienID FROM Technicien WHERE TechnicienID = ?");
            $checkTechnicien->bind_param("i", $technicienId);
            $checkTechnicien->execute();
            $techResult = $checkTechnicien->get_result();
            
            if ($techResult->num_rows === 0) {
                error_log("Aucun technicien trouvé avec l'ID: " . $technicienId);
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
                    LEFT JOIN Utilisateur c ON i.ClientID = c.UtilisateurID
                    LEFT JOIN Client cl ON c.UtilisateurID = cl.UtilisateurID
                    WHERE i.TechnicienID = ?
                    ORDER BY i.DebutIntervention DESC";

            error_log("Requête SQL préparée: " . $sql);

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Erreur de préparation SQL: " . $this->conn->error);
                throw new Exception("Erreur de préparation de la requête");
            }

            $stmt->bind_param("i", $technicienId);
            
            if (!$stmt->execute()) {
                error_log("Erreur d'exécution SQL: " . $stmt->error);
                throw new Exception("Erreur d'exécution de la requête");
            }

            $result = $stmt->get_result();
            $interventions = [];

            while ($row = $result->fetch_assoc()) {
                error_log("Intervention trouvée: " . json_encode($row));
                
                $intervention = [
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
                $interventions[] = $intervention;
            }

            $stmt->close();

            error_log("Nombre d'interventions trouvées: " . count($interventions));
            error_log("Données des interventions: " . json_encode($interventions));

            return [
                'status' => 'success',
                'message' => count($interventions) > 0 ? 'Interventions récupérées avec succès' : 'Aucune intervention trouvée',
                'data' => $interventions
            ];

        } catch (Exception $e) {
            error_log("Exception dans getTechnicienInterventions: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}
?>