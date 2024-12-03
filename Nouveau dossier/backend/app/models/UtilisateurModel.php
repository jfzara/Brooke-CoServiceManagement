<?php
class UtilisateurModel {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli('localhost', 'root', '', 'brookeandco');
            
            if ($this->conn->connect_error) {
                error_log("Erreur de connexion MySQL: " . $this->conn->connect_error);
                throw new Exception("Erreur de connexion à la base de données");
            }
            
            error_log("Connexion à la base de données réussie");
        } catch (Exception $e) {
            error_log("Exception dans le constructeur UtilisateurModel: " . $e->getMessage());
            throw $e;
        }
    }

    public function verifierUtilisateur($email, $motDePasse) {
        try {
            error_log("Tentative de vérification pour l'email: " . $email);
            
            $stmt = $this->conn->prepare("SELECT * FROM Utilisateur WHERE Email = ?");
            if (!$stmt) {
                error_log("Erreur de préparation SQL: " . $this->conn->error);
                throw new Exception("Erreur de préparation de la requête");
            }

            $stmt->bind_param("s", $email);
            
            if (!$stmt->execute()) {
                error_log("Erreur d'exécution SQL: " . $stmt->error);
                throw new Exception("Erreur d'exécution de la requête");
            }
            
            $result = $stmt->get_result();
            $utilisateur = $result->fetch_assoc();
            
            if ($utilisateur) {
                error_log("Utilisateur trouvé, vérification du mot de passe");
                if (password_verify($motDePasse, $utilisateur['MotDePasse'])) {
                    error_log("Mot de passe vérifié avec succès");
                    return $utilisateur;
                } else {
                    error_log("Mot de passe incorrect");
                    return null;
                }
            } else {
                error_log("Aucun utilisateur trouvé avec cet email");
                return null;
            }
            
            $stmt->close();
        } catch (Exception $e) {
            error_log("Exception dans verifierUtilisateur: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUtilisateurById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Utilisateur WHERE UtilisateurID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $utilisateur = $result->fetch_assoc();
        $stmt->close();
        
        return $utilisateur;
    }

    public function creerUtilisateur($data) {
        $motDePasseHash = password_hash($data['motDePasse'], PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare("INSERT INTO Utilisateur (Nom, Prenom, Email, MotDePasse, Type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", 
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $motDePasseHash,
            $data['type']
        );
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    public function modifierUtilisateur($id, $data) {
        $stmt = $this->conn->prepare("UPDATE Utilisateur SET Nom = ?, Prenom = ?, Email = ?, MotDePasse = ?, Type = ? WHERE UtilisateurID = ?");
        $stmt->bind_param("sssssi", 
            $data['Nom'],
            $data['Prenom'],
            $data['Email'],
            $data['MotDePasse'],
            $data['Type'],
            $id
        );
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }

    public function supprimerUtilisateur($id) {
        $stmt = $this->conn->prepare("DELETE FROM Utilisateur WHERE UtilisateurID = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>