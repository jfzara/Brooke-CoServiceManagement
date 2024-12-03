<?php
require_once __DIR__ . '/database.php';

// Création des tables dans l'ordre des dépendances
$tables = [
    "CREATE TABLE IF NOT EXISTS Utilisateur (
        UtilisateurID INT AUTO_INCREMENT PRIMARY KEY,
        Nom VARCHAR(100) NOT NULL,
        Prenom VARCHAR(100) NOT NULL,
        Email VARCHAR(100) NOT NULL UNIQUE,
        MotDePasse VARCHAR(100) NOT NULL,
        Type VARCHAR(50) NOT NULL,
        CompteGoogleID VARCHAR(100) NULL,
        CompteFacebookID VARCHAR(100) NULL,
        CompteBrookeID VARCHAR(100) NULL
    )",

    "CREATE TABLE IF NOT EXISTS Client (
        ClientID INT AUTO_INCREMENT PRIMARY KEY,
        UtilisateurID INT NOT NULL,
        Adresse VARCHAR(255) NOT NULL,
        Telephone VARCHAR(20) NOT NULL,
        Demandes TEXT,
        FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
    )",

    "CREATE TABLE IF NOT EXISTS Technicien (
        TechnicienID INT AUTO_INCREMENT PRIMARY KEY,
        UtilisateurID INT NOT NULL,
        FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
    )",

    "CREATE TABLE IF NOT EXISTS Prepose (
        PreposeID INT AUTO_INCREMENT PRIMARY KEY,
        UtilisateurID INT NOT NULL,
        FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
    )",

    "CREATE TABLE IF NOT EXISTS Gestionnaire (
        GestionnaireID INT AUTO_INCREMENT PRIMARY KEY,
        UtilisateurID INT NOT NULL,
        FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
    )",

    "CREATE TABLE IF NOT EXISTS Intervention (
        InterventionID INT AUTO_INCREMENT PRIMARY KEY,
        TechnicienID INT NOT NULL,
        ClientID INT NOT NULL,
        TypeIntervention VARCHAR(100) NOT NULL,
        Description TEXT,
        DebutIntervention DATETIME NOT NULL,
        FinIntervention DATETIME NOT NULL,
        StatutIntervention VARCHAR(50) NOT NULL,
        Commentaires TEXT,
        FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID),
        FOREIGN KEY (ClientID) REFERENCES Client(ClientID)
    )",

    "CREATE TABLE IF NOT EXISTS PlanningHebdomadaireTechnicien (
        PlanningID INT AUTO_INCREMENT PRIMARY KEY,
        TechnicienID INT NOT NULL,
        InterventionID INT NOT NULL,
        ClientID INT NOT NULL,
        NumeroSemaine INT NOT NULL,
        JourSemaine ENUM('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche') NOT NULL,
        DebutIntervention DATETIME NOT NULL,
        FinIntervention DATETIME NOT NULL,
        StatutIntervention VARCHAR(50) NOT NULL,
        Commentaires TEXT,
        FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID),
        FOREIGN KEY (InterventionID) REFERENCES Intervention(InterventionID),
        FOREIGN KEY (ClientID) REFERENCES Client(ClientID)
    )",

    "CREATE TABLE IF NOT EXISTS DisponibiliteHebdomadaireTechnicien (
        DisponibiliteID INT AUTO_INCREMENT PRIMARY KEY,
        TechnicienID INT NOT NULL,
        NumeroSemaine INT NOT NULL,
        JourDisponible DATE NOT NULL,
        DebutDisponibilite DATETIME NOT NULL,
        FinDisponibilite DATETIME NOT NULL,
        FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID)
    )",

    "CREATE TABLE IF NOT EXISTS NotificationClient (
        NotificationID INT AUTO_INCREMENT PRIMARY KEY,
        ClientID INT NOT NULL,
        TechnicienID INT NOT NULL,
        TypeNotification VARCHAR(50) NOT NULL,
        Message TEXT NOT NULL,
        DateEnvoi DATE NOT NULL,
        HeureEnvoi TIME NOT NULL,
        Lu BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (ClientID) REFERENCES Client(ClientID),
        FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID)
    )"
];

// Création des tables
foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table créée avec succès<br/>";
    } else {
        die("Erreur lors de la création de la table: " . $conn->error);
    }
}

// Insertion des données de test
require_once __DIR__ . '/test_data.php';
insertTestData($conn);

$conn->close();
?>