<?php

require_once __DIR__ . '/../../config/database.php';

class UtilisateurModel {
    private $conn;
    public $UtilisateurID;
    public $Nom;
    public $Prenom;
    public $Email;
    public $MotDePasse;
    public $Type;
    public $CompteGoogleID;
    public $CompteFacebookID;
    public $CompteBrookeID;

    public function __construct() {
        // Créer une nouvelle connexion plutôt que d'utiliser une variable globale
        $this->conn = new mysqli('localhost', 'root', '', 'brookeandco');
        
        // Vérifier la connexion
        if ($this->conn->connect_error) {
            // Si la base de données n'existe pas, la créer
            $tempConn = new mysqli('localhost', 'root', '');
            $tempConn->query("CREATE DATABASE IF NOT EXISTS brookeandco");
            $tempConn->close();
            
            // Reconnecter à la base de données nouvellement créée
            $this->conn = new mysqli('localhost', 'root', '', 'brookeandco');
        }
    }

    public function getUtilisateurById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Utilisateur WHERE UtilisateurID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $this->UtilisateurID = $result['UtilisateurID'];
            $this->Nom = $result['Nom'];
            $this->Prenom = $result['Prenom'];
            $this->Email = $result['Email'];
            $this->MotDePasse = $result['MotDePasse'];
            $this->Type = $result['Type'];
            $this->CompteGoogleID = $result['CompteGoogleID'];
            $this->CompteFacebookID = $result['CompteFacebookID'];
            $this->CompteBrookeID = $result['CompteBrookeID'];
        }

        return $result;
    }

    public function creerUtilisateur($data) {
        $stmt = $this->conn->prepare("INSERT INTO Utilisateur (Nom, Prenom, Email, MotDePasse, Type, CompteGoogleID, CompteFacebookID, CompteBrookeID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssss', $data['Nom'], $data['Prenom'], $data['Email'], $data['MotDePasse'], $data['Type'], $data['CompteGoogleID'], $data['CompteFacebookID'], $data['CompteBrookeID']);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function modifierUtilisateur($id, $data) {
        $stmt = $this->conn->prepare("UPDATE Utilisateur SET Nom = ?, Prenom = ?, Email = ?, MotDePasse = ?, Type = ?, CompteGoogleID = ?, CompteFacebookID = ?, CompteBrookeID = ? WHERE UtilisateurID = ?");
        $stmt->bind_param('ssssssssi', $data['Nom'], $data['Prenom'], $data['Email'], $data['MotDePasse'], $data['Type'], $data['CompteGoogleID'], $data['CompteFacebookID'], $data['CompteBrookeID'], $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function supprimerUtilisateur($id) {
        $stmt = $this->conn->prepare("DELETE FROM Utilisateur WHERE UtilisateurID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function verifierUtilisateur($email, $motDePasse) {
        $query = "SELECT * FROM utilisateur WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $utilisateur = $result->fetch_assoc();
        
        if ($utilisateur && ($motDePasse === $utilisateur['MotDePasse'])) {
            return $utilisateur; // Connexion réussie
        } else {
            return false; // Échec de la connexion
        }
    }

    public function getUtilisateurByFacebookID($facebookID) {
        $stmt = $this->conn->prepare("SELECT * FROM Utilisateur WHERE CompteFacebookID = ?");
        $stmt->bind_param('s', $facebookID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result ? $result : false;
    }    
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>