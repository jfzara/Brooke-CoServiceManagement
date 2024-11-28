<?php

    require 'database.php';
    require_once '../app/models/UtilisateurModel.php';
    require_once '../app/models/ClientModel.php';
    require_once '../app/models/TechnicienModel.php';
    require_once '../app/models/PreposeModel.php';
    require_once '../app/models/GestionnaireModel.php';
    require_once '../app/models/InterventionModel.php';
    require_once '../app/models/PlanningTechnicienModel.php';
    require_once '../app/models/DisponibiliteTechnicienModel.php';
    require_once '../app/models/NotificationClientModel.php';
    require_once '../app/models/NotificationTechnicienModel.php';


    $tables = [
        "CREATE TABLE IF NOT EXISTS Utilisateur (
                UtilisateurID INT AUTO_INCREMENT PRIMARY KEY,
                Nom VARCHAR(255) NOT NULL,
                Prenom VARCHAR(255) NOT NULL,
                Email VARCHAR(255) NOT NULL,
                MotDePasse VARCHAR(255) NOT NULL,
                Type ENUM ('admin', 'client', 'technicien', 'prepose', 'gestionnaire') NOT NULL,
                CompteGoogleID VARCHAR(255),
                CompteFacebookID VARCHAR(255),
                CompteBrookeID VARCHAR(255)
            )",
        "CREATE TABLE IF NOT EXISTS Prepose (
                PreposeID INT AUTO_INCREMENT PRIMARY KEY,
                UtilisateurID INT,
                FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
            )",
        "CREATE TABLE IF NOT EXISTS Gestionnaire (
                GestionnaireID INT AUTO_INCREMENT PRIMARY KEY,
                UtilisateurID INT,
                FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
            )",
        "CREATE TABLE IF NOT EXISTS Client (
                ClientID INT AUTO_INCREMENT PRIMARY KEY,
                Adresse VARCHAR(255) NOT NULL,
                Telephone VARCHAR(255) NOT NULL,
                Demandes VARCHAR(255) NOT NULL,
                UtilisateurID INT,
                FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
            )",
        "CREATE TABLE IF NOT EXISTS Technicien (
                TechnicienID INT AUTO_INCREMENT PRIMARY KEY,
                UtilisateurID INT,
                FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
            )",
        "CREATE TABLE IF NOT EXISTS Intervention (
                InterventionID INT AUTO_INCREMENT PRIMARY KEY,
                TechnicienID INT,
                ClientID INT,
                TypeIntervention VARCHAR(255) NOT NULL,
                Description TEXT,
                Date DATE,
                Heure TIME,
                HeureDebut TIME,
                HeureFin TIME,
                Statut VARCHAR(255) NOT NULL,
                Commentaires TEXT,
                FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID),
                FOREIGN KEY (ClientID) REFERENCES Client(ClientID)
            )",
        "CREATE TABLE IF NOT EXISTS PlanningTechnicien (
                PlanningID INT AUTO_INCREMENT PRIMARY KEY,
                TechnicienID INT,
                InterventionID INT,
                DateIntervention DATE,
                HeureDebut TIME,
                HeureFin TIME,
                Commentaires TEXT,
                FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID),
                FOREIGN KEY (InterventionID) REFERENCES Intervention(InterventionID)
            )",
        "CREATE TABLE IF NOT EXISTS DisponibiliteTechnicien (
                DisponibiliteID INT AUTO_INCREMENT PRIMARY KEY,
                TechnicienID INT,
                Date DATE,
                HeureDebut TIME,
                HeureFin TIME,
                FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID)
            )",
        "CREATE TABLE IF NOT EXISTS NotificationTechnicien (
                NotificationID INT AUTO_INCREMENT PRIMARY KEY,
                TechnicienID INT,
                TypeNotification VARCHAR(255) NOT NULL,
                Message TEXT,
                DateEnvoi DATE,
                HeureEnvoi TIME,
                Lu BOOLEAN,
                FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID)
            )",
        "CREATE TABLE IF NOT EXISTS NotificationClient (
                NotificationID INT AUTO_INCREMENT PRIMARY KEY,
                ClientID INT,
                TechnicienID INT,
                TypeNotification VARCHAR(255) NOT NULL,
                Message TEXT,
                DateEnvoi DATE,
                HeureEnvoi TIME,
                Lu BOOLEAN,
                FOREIGN KEY (ClientID) REFERENCES Client(ClientID),
                FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID)
            )",
        "CREATE TABLE IF NOT EXISTS Roles (
                RoleID INT PRIMARY KEY AUTO_INCREMENT,
                RoleName VARCHAR(50) UNIQUE
            )",
        "CREATE TABLE IF NOT EXISTS Permissions (
                PermissionID INT PRIMARY KEY AUTO_INCREMENT,
                PermissionName VARCHAR(50) UNIQUE
            )",
        "CREATE TABLE IF NOT EXISTS Role_Permissions (
                RoleID INT,
                PermissionID INT,
                PRIMARY KEY (RoleID, PermissionID),
                FOREIGN KEY (RoleID) REFERENCES Roles(RoleID),
                FOREIGN KEY (PermissionID) REFERENCES Permissions(PermissionID)
            )",
        "CREATE TABLE IF NOT EXISTS Utilisateur_Roles (
                UtilisateurID INT,
                RoleID INT,
                PRIMARY KEY (UtilisateurID, RoleID),
                FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID),
                FOREIGN KEY (RoleID) REFERENCES Roles(RoleID)
            )"
    ];

    // ALTER TABLE brookeandco.intervention ADD COLUMN HeureDebut TIME, ADD COLUMN HeureFin TIME;

    // Exécution des requêtes de création de tables
    $isCreate = true;

    foreach ($tables as $sql) {
        if ($conn->query($sql) === TRUE) {
            echo "<br/>Table créée avec succès: " . addslashes(substr($sql, 13, strpos($sql, '(', 13) - 13));
        } else {
            echo "Erreur lors de la création de la table: " . addslashes($conn->error);
            $isCreate = false;
        }
    }

    function dataExists($conn, $table)
    {
        $query = "SELECT COUNT(*) FROM $table";
        $result = $conn->query($query);
        if ($result) {
            $row = $result->fetch_row();
            return $row[0] > 0;
        }
        return false;
    }

    if ($isCreate) {

        $tablesToCheck = ['utilisateur', 'client', 'technicien', 'prepose', 'gestionnaire', 'intervention', 'planningtechnicien', 'disponibilitetechnicien', 'notificationclient', 'notificationtechnicien'];
        $dataExists = false;
        foreach ($tablesToCheck as $table) {
            if (dataExists($conn, $table)) {
                $dataExists = true;
                break;
            }
        }

        if (!$dataExists) {
            // Initialiser les modèles avec la connexion à la base de données
            $utilisateurModel = new UtilisateurModel();
            $clientModel = new ClientModel($conn);
            $technicienModel = new TechnicienModel($conn);
            $preposeModel = new PreposeModel($conn);
            $gestionnaireModel = new GestionnaireModel($conn);
            $interventionModel = new InterventionModel();
            $planningModel = new PlanningTechnicienModel();
            $disponibiliteModel = new DisponibiliteTechnicienModel();
            $notificationClientModel = new NotificationClientModel();
            $notificationTechnicienModel = new NotificationTechnicienModel();

            // Données utilisateurs fictives
            $utilisateurs = [
                ['Nom' => 'Dupont', 'Prenom' => 'Jean', 'Email' => 'jean.dupont@example.com', 'MotDePasse' => 'password123', 'Type' => 'client', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => ''],
                ['Nom' => 'Martin', 'Prenom' => 'Sophie', 'Email' => 'sophie.martin@example.com', 'MotDePasse' => 'password123', 'Type' => 'technicien', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => ''],
                ['Nom' => 'Lefevre', 'Prenom' => 'Luc', 'Email' => 'luc.lefevre@example.com', 'MotDePasse' => 'password123', 'Type' => 'prepose', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => ''],
                ['Nom' => 'Durand', 'Prenom' => 'Marie', 'Email' => 'marie.durand@example.com', 'MotDePasse' => 'password123', 'Type' => 'gestionnaire', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => ''],
                ['Nom' => 'Moreau', 'Prenom' => 'Pierre', 'Email' => 'pierre.moreau@example.com', 'MotDePasse' => 'password123', 'Type' => 'client', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => '']
            ];

            foreach ($utilisateurs as $utilisateur) {
                $utilisateurModel->creerUtilisateur($utilisateur);
            }

            // Données clients fictives
            $clients = [
                ['Adresse' => '123 Rue Principale', 'Telephone' => '0123456789', 'Demandes' => 'Nettoyage', 'UtilisateurID' => 1],
                ['Adresse' => '456 Avenue de la République', 'Telephone' => '0987654321', 'Demandes' => 'Réparation', 'UtilisateurID' => 5],
                ['Adresse' => '789 Boulevard des Capucines', 'Telephone' => '0147258369', 'Demandes' => 'Maintenance', 'UtilisateurID' => 1],
                ['Adresse' => '321 Place de la Concorde', 'Telephone' => '0178346952', 'Demandes' => 'Installation', 'UtilisateurID' => 5],
                ['Adresse' => '654 Rue de Rivoli', 'Telephone' => '0192837465', 'Demandes' => 'Inspection', 'UtilisateurID' => 1]
            ];

            foreach ($clients as $client) {
                $clientModel->creerClient($client);
            }

            // Données techniciens fictives
            $techniciens = [
                ['UtilisateurID' => 2],
                ['UtilisateurID' => 2],
                ['UtilisateurID' => 2],
                ['UtilisateurID' => 2],
                ['UtilisateurID' => 2]
            ];

            foreach ($techniciens as $technicien) {
                $technicienModel->creerTechnicien($technicien);
            }

            // Données préposés fictives
            $preposes = [
                ['UtilisateurID' => 1],
                ['UtilisateurID' => 1],
                ['UtilisateurID' => 1],
                ['UtilisateurID' => 1],
                ['UtilisateurID' => 1]
            ];

            foreach ($preposes as $prepose) {
                $preposeModel->creerPrepose($prepose);
            }

            // Données gestionnaires fictives
            $gestionnaires = [
                ['UtilisateurID' => 4],
                ['UtilisateurID' => 4],
                ['UtilisateurID' => 4],
                ['UtilisateurID' => 4],
                ['UtilisateurID' => 4]
            ];

            foreach ($gestionnaires as $gestionnaire) {
                $gestionnaireModel->creerGestionnaire($gestionnaire);
            }

            // Données interventions fictives
            $interventions = [
                ['TechnicienID' => 2, 'ClientID' => 1, 'TypeIntervention' => 'Réparation', 'Description' => 'Réparation de la machine X', 'Date' => '2024-10-29', 'Heure' => '10:00', 'Statut' => 'En attente', 'Commentaires' => 'Urgent'],
                ['TechnicienID' => 2, 'ClientID' => 1, 'TypeIntervention' => 'Maintenance', 'Description' => 'Maintenance du système Y', 'Date' => '2024-10-30', 'Heure' => '14:00', 'Statut' => 'En cours', 'Commentaires' => 'Routine'],
                ['TechnicienID' => 2, 'ClientID' => 1, 'TypeIntervention' => 'Installation', 'Description' => 'Installation du dispositif Z', 'Date' => '2024-10-31', 'Heure' => '09:00', 'Statut' => 'Terminé', 'Commentaires' => 'Nécessite vérification'],
                ['TechnicienID' => 2, 'ClientID' => 5, 'TypeIntervention' => 'Inspection', 'Description' => 'Inspection de la machine A', 'Date' => '2024-11-01', 'Heure' => '11:00', 'Statut' => 'En attente', 'Commentaires' => 'Préventif'],
                ['TechnicienID' => 2, 'ClientID' => 1, 'TypeIntervention' => 'reparation', 'Description' => 'reparation du site B', 'Date' => '2024-11-02', 'Heure' => '13:00', 'Statut' => 'En cours', 'Commentaires' => 'Important']
            ];

            foreach ($interventions as $intervention) {
                $interventionModel->creerIntervention($intervention);
            }

            // Données plannings fictives
            $plannings = [
                ['TechnicienID' => 2, 'InterventionID' => 1, 'DateIntervention' => '2024-10-29', 'HeureDebut' => '10:00', 'HeureFin' => '11:00', 'Commentaires' => 'Assurez-vous d\'avoir les pièces nécessaires'],
                ['TechnicienID' => 2, 'InterventionID' => 2, 'DateIntervention' => '2024-10-30', 'HeureDebut' => '14:00', 'HeureFin' => '15:00', 'Commentaires' => 'Routine'],
                ['TechnicienID' => 2, 'InterventionID' => 3, 'DateIntervention' => '2024-10-31', 'HeureDebut' => '09:00', 'HeureFin' => '10:00', 'Commentaires' => 'Nécessite vérification'],
                ['TechnicienID' => 2, 'InterventionID' => 4, 'DateIntervention' => '2024-11-01', 'HeureDebut' => '11:00', 'HeureFin' => '12:00', 'Commentaires' => 'Préventif'],
                ['TechnicienID' => 2, 'InterventionID' => 5, 'DateIntervention' => '2024-11-02', 'HeureDebut' => '13:00', 'HeureFin' => '14:00', 'Commentaires' => 'Important']
            ];

            foreach ($plannings as $planning) {
                $planningModel->creerPlanningTechnicien($planning);
            }

            // Données disponibilités fictives
            $disponibilites = [
                ['TechnicienID' => 2, 'Date' => '2024-10-30', 'HeureDebut' => '08:00', 'HeureFin' => '17:00'],
                ['TechnicienID' => 2, 'Date' => '2024-10-31', 'HeureDebut' => '08:00', 'HeureFin' => '17:00'],
                ['TechnicienID' => 2, 'Date' => '2024-11-01', 'HeureDebut' => '08:00', 'HeureFin' => '17:00'],
                ['TechnicienID' => 2, 'Date' => '2024-11-02', 'HeureDebut' => '08:00', 'HeureFin' => '17:00'],
                ['TechnicienID' => 2, 'Date' => '2024-11-03', 'HeureDebut' => '08:00', 'HeureFin' => '17:00']
            ];

            foreach ($disponibilites as $disponibilite) {
                $disponibiliteModel->creerDisponibiliteTechnicien($disponibilite);
            }

            // Données notificationsClient fictives
            $notificationClients = [
                ['TechnicienID' => 2, 'ClientID' => 1, 'TypeNotification' => 'sms', 'Message' => 'Votre intervention est prévue pour demain.', 'DateEnvoi' => '2024-10-29', 'HeureEnvoi' => '09:00', 'Lu' => 0],
                ['TechnicienID' => 2, 'ClientID' => 1, 'TypeNotification' => 'sms', 'Message' => 'Ne manquez pas votre intervention!', 'DateEnvoi' => '2024-10-30', 'HeureEnvoi' => '10:00', 'Lu' => 0],
                ['TechnicienID' => 2, 'ClientID' => 5, 'TypeNotification' => 'sms', 'Message' => 'Votre intervention a été confirmée.', 'DateEnvoi' => '2024-10-31', 'HeureEnvoi' => '11:00', 'Lu' => 0],
                ['TechnicienID' => 2, 'ClientID' => 1, 'TypeNotification' => 'sms', 'Message' => 'Votre intervention est en cours.', 'DateEnvoi' => '2024-11-01', 'HeureEnvoi' => '12:00', 'Lu' => 0],
                ['TechnicienID' => 2, 'ClientID' => 1, 'TypeNotification' => 'sms', 'Message' => 'Votre intervention est terminée.', 'DateEnvoi' => '2024-11-02', 'HeureEnvoi' => '13:00', 'Lu' => 0]
            ];

            foreach ($notificationClients as $notification) {
                $notificationClientModel->creerNotificationClient($notification);
            }

            // Données notificationsTechnicien fictives
            $notificationTechniciens = [
                ['TechnicienID' => 2, 'TypeNotification' => 'sms', 'Message' => 'Votre intervention est prévue pour demain.', 'DateEnvoi' => '2024-10-29', 'HeureEnvoi' => '09:00', 'Lu' => 0],
                ['TechnicienID' => 2, 'TypeNotification' => 'sms', 'Message' => 'Ne manquez pas votre intervention!', 'DateEnvoi' => '2024-10-30', 'HeureEnvoi' => '10:00', 'Lu' => 0],
           ];

            foreach ($notificationTechniciens as $notification) {
                $notificationTechnicienModel->creerNotificationTechnicien($notification);
            }
        }
        echo "<br/> Base de données initialisée avec des données fictives.";
    }
    $conn->close();

?>