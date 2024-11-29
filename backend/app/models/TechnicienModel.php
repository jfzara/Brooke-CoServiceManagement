<?php
class TechnicienModel {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli('localhost', 'root', '', 'brookeandco');
            if ($this->conn->connect_error) {
                throw new Exception("Erreur de connexion: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            error_log("Erreur dans le constructeur TechnicienModel: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTechnicienById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Technicien WHERE TechnicienID = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Erreur dans getTechnicienById: " . $e->getMessage());
            throw $e;
        }
    }

    public function getInterventions($technicienId) {
        try {
            $query = "SELECT i.*, c.Nom as ClientNom, c.Prenom as ClientPrenom, c.Adresse as ClientAdresse, 
                            c.Telephone as ClientTelephone
                     FROM Intervention i 
                     INNER JOIN Client c ON i.ClientID = c.ClientID 
                     WHERE i.TechnicienID = ?
                     ORDER BY i.DebutIntervention ASC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête: " . $this->conn->error);
            }

            $stmt->bind_param("i", $technicienId);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur d'exécution de la requête: " . $stmt->error);
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
                    'client' => [
                        'Nom' => $row['ClientNom'],
                        'Prenom' => $row['ClientPrenom'],
                        'Adresse' => $row['ClientAdresse'],
                        'Telephone' => $row['ClientTelephone']
                    ]
                ];
            }

            $stmt->close();
            return $interventions;

        } catch (Exception $e) {
            error_log("Erreur dans getInterventions: " . $e->getMessage());
            throw $e;
        }
    }

    public function creerTechnicien($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO Technicien (UtilisateurID) VALUES (?)");
            $stmt->bind_param('i', $data['UtilisateurID']);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Erreur dans creerTechnicien: " . $e->getMessage());
            throw $e;
        }
    }

    public function modifierTechnicien($id, $data) {
        try {
            $stmt = $this->conn->prepare("UPDATE Technicien SET UtilisateurID = ? WHERE TechnicienID = ?");
            $stmt->bind_param('ii', $data['UtilisateurID'], $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Erreur dans modifierTechnicien: " . $e->getMessage());
            throw $e;
        }
    }

    public function supprimerTechnicien($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM Technicien WHERE TechnicienID = ?");
            $stmt->bind_param('i', $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Erreur dans supprimerTechnicien: " . $e->getMessage());
            throw $e;
        }
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>