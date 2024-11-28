<?php
    require_once __DIR__ . '/database.php';
    require_once __DIR__ . '/../app/models/UtilisateurModel.php';
    require_once __DIR__ . '/../app/models/ClientModel.php';
    require_once __DIR__ . '/../app/models/TechnicienModel.php';
    require_once __DIR__ . '/../app/models/PreposeModel.php';
    require_once __DIR__ . '/../app/models/GestionnaireModel.php';
    require_once __DIR__ . '/../app/models/InterventionModel.php';
    require_once __DIR__ . '/../app/models/PlanningTechnicienModel.php';
    require_once __DIR__ . '/../app/models/DisponibiliteTechnicienModel.php';
    require_once __DIR__ . '/../app/models/NotificationClientModel.php';
    require_once __DIR__ . '/../app/models/NotificationTechnicienModel.php';

    $tables = [
        "CREATE TABLE IF NOT EXISTS Utilisateur (
            UtilisateurID INT AUTO_INCREMENT PRIMARY KEY,
            Nom VARCHAR(255) NOT NULL,
            Prenom VARCHAR(255) NOT NULL,
            Email VARCHAR(255) NOT NULL,
            MotDePasse VARCHAR(255) NOT NULL,
            Type VARCHAR(50) NOT NULL,
            CompteGoogleID VARCHAR(255),
            CompteFacebookID VARCHAR(255),
            CompteBrookeID VARCHAR(255)
        )",

        "CREATE TABLE IF NOT EXISTS Client (
            ClientID INT AUTO_INCREMENT PRIMARY KEY,
            Adresse VARCHAR(255) NOT NULL,
            Telephone VARCHAR(255) NOT NULL,
            Demandes TEXT,
            UtilisateurID INT,
            FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
        )",

        "CREATE TABLE IF NOT EXISTS Technicien (
            TechnicienID INT AUTO_INCREMENT PRIMARY KEY,
            UtilisateurID INT,
            FOREIGN KEY (UtilisateurID) REFERENCES Utilisateur(UtilisateurID)
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

        "CREATE TABLE IF NOT EXISTS Intervention (
            InterventionID INT AUTO_INCREMENT PRIMARY KEY,
            TechnicienID INT,
            ClientID INT,
            TypeIntervention VARCHAR(255) NOT NULL,
            Description TEXT,
            DebutIntervention DATETIME,
            FinIntervention DATETIME,
            StatutIntervention VARCHAR(50) NOT NULL,
            Commentaires TEXT,
            FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID),
            FOREIGN KEY (ClientID) REFERENCES Client(ClientID)
        )",

        "CREATE TABLE IF NOT EXISTS PlanningHebdomadaireTechnicien (
            PlanningID INT AUTO_INCREMENT PRIMARY KEY,
            TechnicienID INT,
            InterventionID INT,
            ClientID INT,
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
            TechnicienID INT,
            NumeroSemaine INT NOT NULL,
            JourDisponible DATE NOT NULL,
            DebutDisponibilite DATETIME NOT NULL,
            FinDisponibilite DATETIME NOT NULL,
            FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID)
        )",

        "CREATE TABLE IF NOT EXISTS NotificationClient (
            NotificationID INT AUTO_INCREMENT PRIMARY KEY,
            ClientID INT,
            TechnicienID INT,
            TypeNotification VARCHAR(50) NOT NULL,
            Message TEXT,
            DateEnvoi DATE NOT NULL,
            HeureEnvoi TIME NOT NULL,
            Lu BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (ClientID) REFERENCES Client(ClientID),
            FOREIGN KEY (TechnicienID) REFERENCES Technicien(TechnicienID)
        )"
    ];

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

    function dataExists($conn, $table) {
        $query = "SELECT COUNT(*) FROM $table";
        $result = $conn->query($query);
        if ($result) {
            $row = $result->fetch_row();
            return $row[0] > 0;
        }
        return false;
    }

    if ($isCreate) {
        $tablesToCheck = [
            'utilisateur', 'client', 'technicien', 'prepose', 'gestionnaire',
            'intervention', 'planninghebdomadairetechnicien', 'disponibilitehebdomadairetechnicien',
            'notificationclient'
        ];

        $dataExists = false;
        foreach ($tablesToCheck as $table) {
            if (dataExists($conn, $table)) {
                $dataExists = true;
                break;
            }
        }

        if (!$dataExists) {
            // Données utilisateurs fictives
            $utilisateurs = [
                ['Nom' => 'Dupont', 'Prenom' => 'Jean', 'Email' => 'jean.dupont@example.com', 'MotDePasse' => 'password123', 'Type' => 'client', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => ''],
                ['Nom' => 'Martin', 'Prenom' => 'Sophie', 'Email' => 'sophie.martin@example.com', 'MotDePasse' => 'password123', 'Type' => 'technicien', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => ''],
                ['Nom' => 'Lefevre', 'Prenom' => 'Luc', 'Email' => 'luc.lefevre@example.com', 'MotDePasse' => 'password123', 'Type' => 'prepose', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => ''],
                ['Nom' => 'Durand', 'Prenom' => 'Marie', 'Email' => 'marie.durand@example.com', 'MotDePasse' => 'password123', 'Type' => 'gestionnaire', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => ''],
                ['Nom' => 'Moreau', 'Prenom' => 'Pierre', 'Email' => 'pierre.moreau@example.com', 'MotDePasse' => 'password123', 'Type' => 'client', 'CompteGoogleID' => '', 'CompteFacebookID' => '', 'CompteBrookeID' => '']
            ];
        
            foreach ($utilisateurs as $utilisateur) {
                $sql = "INSERT INTO Utilisateur (Nom, Prenom, Email, MotDePasse, Type, CompteGoogleID, CompteFacebookID, CompteBrookeID) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssss", 
                    $utilisateur['Nom'],
                    $utilisateur['Prenom'],
                    $utilisateur['Email'],
                    $utilisateur['MotDePasse'],
                    $utilisateur['Type'],
                    $utilisateur['CompteGoogleID'],
                    $utilisateur['CompteFacebookID'],
                    $utilisateur['CompteBrookeID']
                );
                $stmt->execute();
                $stmt->close();
            }
        
            // Données clients fictives
            $clients = [
                ['Adresse' => '123 Rue Principale', 'Telephone' => '0123456789', 'Demandes' => 'Installation Fibre', 'UtilisateurID' => 1],
                ['Adresse' => '456 Avenue de la République', 'Telephone' => '0987654321', 'Demandes' => 'Support Internet', 'UtilisateurID' => 5],
                ['Adresse' => '789 Boulevard des Capucines', 'Telephone' => '0147258369', 'Demandes' => 'Configuration Réseau', 'UtilisateurID' => 1],
                ['Adresse' => '321 Place de la Concorde', 'Telephone' => '0178346952', 'Demandes' => 'Maintenance Box', 'UtilisateurID' => 5],
                ['Adresse' => '654 Rue de Rivoli', 'Telephone' => '0192837465', 'Demandes' => 'Dépannage Internet', 'UtilisateurID' => 1]
            ];
        
            foreach ($clients as $client) {
                $sql = "INSERT INTO Client (Adresse, Telephone, Demandes, UtilisateurID) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", 
                    $client['Adresse'],
                    $client['Telephone'],
                    $client['Demandes'],
                    $client['UtilisateurID']
                );
                $stmt->execute();
                $stmt->close();
            }
        
            // Données techniciens fictives
            $techniciens = [
                ['UtilisateurID' => 2]
            ];
        
            foreach ($techniciens as $technicien) {
                $sql = "INSERT INTO Technicien (UtilisateurID) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $technicien['UtilisateurID']);
                $stmt->execute();
                $stmt->close();
            }
        
            // Nouvelles données interventions télécoms
            $interventions = [
                [
                    'TechnicienID' => 2,
                    'ClientID' => 1,
                    'TypeIntervention' => 'Installation Fibre',
                    'Description' => 'Installation fibre optique + configuration box',
                    'DebutIntervention' => '2024-11-05 09:00:00',
                    'FinIntervention' => '2024-11-05 11:00:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Client prioritaire - Nouveau raccordement'
                ],
                [
                    'TechnicienID' => 2,
                    'ClientID' => 3,
                    'TypeIntervention' => 'Dépannage Internet',
                    'Description' => 'Perte de connexion intermittente - Diagnostic ligne',
                    'DebutIntervention' => '2024-11-12 14:00:00',
                    'FinIntervention' => '2024-11-12 16:00:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Problème récurrent depuis 3 jours'
                ],
                [
                    'TechnicienID' => 2,
                    'ClientID' => 2,
                    'TypeIntervention' => 'Installation Box',
                    'Description' => 'Remplacement box défectueuse + migration WiFi 6',
                    'DebutIntervention' => '2024-11-19 10:00:00',
                    'FinIntervention' => '2024-11-19 12:00:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Box prête en agence'
                ],
                [
                    'TechnicienID' => 2,
                    'ClientID' => 4,
                    'TypeIntervention' => 'Maintenance Réseau',
                    'Description' => 'Optimisation réseau local + test débit',
                    'DebutIntervention' => '2024-11-26 13:00:00',
                    'FinIntervention' => '2024-11-26 15:00:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Ralentissements signalés'
                ],
                [
                    'TechnicienID' => 2,
                    'ClientID' => 5,
                    'TypeIntervention' => 'Configuration Réseau',
                    'Description' => 'Paramétrage réseau entreprise + VPN',
                    'DebutIntervention' => '2024-12-03 09:00:00',
                    'FinIntervention' => '2024-12-03 12:00:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Installation complète - Client professionnel'
                ],
                [
                    'TechnicienID' => 2,
                    'ClientID' => 1,
                    'TypeIntervention' => 'Support Technique',
                    'Description' => 'Formation utilisation box + paramètres avancés',
                    'DebutIntervention' => '2024-12-10 14:00:00',
                    'FinIntervention' => '2024-12-10 16:00:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Nouveau client - Formation complète'
                ],
                [
                    'TechnicienID' => 2,
                    'ClientID' => 3,
                    'TypeIntervention' => 'Installation Fibre',
                    'Description' => 'Raccordement fibre + test ligne',
                    'DebutIntervention' => '2024-12-17 09:00:00',
                    'FinIntervention' => '2024-12-17 11:30:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Zone nouvellement éligible'
                ],
                [
                    'TechnicienID' => 2,
                    'ClientID' => 2,
                    'TypeIntervention' => 'Maintenance Réseau',
                    'Description' => 'Vérification installation + optimisation WiFi',
                    'DebutIntervention' => '2024-12-20 13:00:00',
                    'FinIntervention' => '2024-12-20 15:00:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Mise à niveau équipements'
                ],
                [
                    'TechnicienID' => 2,
                    'ClientID' => 4,
                    'TypeIntervention' => 'Configuration Réseau',
                    'Description' => 'Installation système de contrôle parental',
                    'DebutIntervention' => '2024-12-24 10:00:00',
                    'FinIntervention' => '2024-12-24 11:30:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Configuration sécurité famille'
                ],
                [
                    'TechnicienID' => 2,
                    'ClientID' => 5,
                    'TypeIntervention' => 'Support Technique',
                    'Description' => 'Diagnostic lenteur + optimisation réseau',
                    'DebutIntervention' => '2024-12-27 14:00:00',
                    'FinIntervention' => '2024-12-27 16:00:00',
                    'StatutIntervention' => 'En attente',
                    'Commentaires' => 'Problème de débit aux heures de pointe'
                ]
            ];
        
            foreach ($interventions as $intervention) {
                $sql = "INSERT INTO Intervention (TechnicienID, ClientID, TypeIntervention, Description, 
                        DebutIntervention, FinIntervention, StatutIntervention, Commentaires) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iissssss",
                    $intervention['TechnicienID'],
                    $intervention['ClientID'],
                    $intervention['TypeIntervention'],
                    $intervention['Description'],
                    $intervention['DebutIntervention'],
                    $intervention['FinIntervention'],
                    $intervention['StatutIntervention'],
                    $intervention['Commentaires']
                );
                $stmt->execute();
                $interventionId = $conn->insert_id;

                // Insertion dans le planning hebdomadaire
                $date = new DateTime($intervention['DebutIntervention']);
                $sql = "INSERT INTO PlanningHebdomadaireTechnicien (TechnicienID, InterventionID, ClientID,
                        NumeroSemaine, JourSemaine, DebutIntervention, FinIntervention, StatutIntervention, Commentaires)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $numeroSemaine = $date->format('W');
                $jourSemaine = $date->format('l');
                $stmt->bind_param("iiisssss",
                    $intervention['TechnicienID'],
                    $interventionId,
                    $intervention['ClientID'],
                    $numeroSemaine,
                    $jourSemaine,
                    $intervention['DebutIntervention'],
                    $intervention['FinIntervention'],
                    $intervention['StatutIntervention'],
                    $intervention['Commentaires']
                );
                $stmt->execute();
                $stmt->close();
            }
        
            // Données disponibilités hebdomadaires
            $disponibilites = [
                ['TechnicienID' => 2, 'NumeroSemaine' => 45, 'JourDisponible' => '2024-11-05', 'DebutDisponibilite' => '2024-11-05 08:00:00', 'FinDisponibilite' => '2024-11-05 18:00:00'],
                ['TechnicienID' => 2, 'NumeroSemaine' => 46, 'JourDisponible' => '2024-11-12', 'DebutDisponibilite' => '2024-11-12 08:00:00', 'FinDisponibilite' => '2024-11-12 18:00:00'],
                ['TechnicienID' => 2, 'NumeroSemaine' => 47, 'JourDisponible' => '2024-11-19', 'DebutDisponibilite' => '2024-11-19 08:00:00', 'FinDisponibilite' => '2024-11-19 18:00:00'],
                ['TechnicienID' => 2, 'NumeroSemaine' => 48, 'JourDisponible' => '2024-11-26', 'DebutDisponibilite' => '2024-11-26 08:00:00', 'FinDisponibilite' => '2024-11-26 18:00:00'],
                ['TechnicienID' => 2, 'NumeroSemaine' => 49, 'JourDisponible' => '2024-12-03', 'DebutDisponibilite' => '2024-12-03 08:00:00', 'FinDisponibilite' => '2024-12-03 18:00:00'],
                ['TechnicienID' => 2, 'NumeroSemaine' => 50, 'JourDisponible' => '2024-12-10', 'DebutDisponibilite' => '2024-12-10 08:00:00', 'FinDisponibilite' => '2024-12-10 18:00:00'],
                ['TechnicienID' => 2, 'NumeroSemaine' => 51, 'JourDisponible' => '2024-12-17', 'DebutDisponibilite' => '2024-12-17 08:00:00', 'FinDisponibilite' => '2024-12-17 18:00:00'],
                ['TechnicienID' => 2, 'NumeroSemaine' => 51, 'JourDisponible' => '2024-12-20', 'DebutDisponibilite' => '2024-12-20 08:00:00', 'FinDisponibilite' => '2024-12-20 18:00:00'],
                ['TechnicienID' => 2, 'NumeroSemaine' => 52, 'JourDisponible' => '2024-12-24', 'DebutDisponibilite' => '2024-12-24 08:00:00', 'FinDisponibilite' => '2024-12-24 18:00:00'],
                ['TechnicienID' => 2, 'NumeroSemaine' => 52, 'JourDisponible' => '2024-12-27', 'DebutDisponibilite' => '2024-12-27 08:00:00', 'FinDisponibilite' => '2024-12-27 18:00:00']
            ];
        
            foreach ($disponibilites as $disponibilite) {
                $sql = "INSERT INTO DisponibiliteHebdomadaireTechnicien (TechnicienID, NumeroSemaine, JourDisponible,
                        DebutDisponibilite, FinDisponibilite) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iisss",
                    $disponibilite['TechnicienID'],
                    $disponibilite['NumeroSemaine'],
                    $disponibilite['JourDisponible'],
                    $disponibilite['DebutDisponibilite'],
                    $disponibilite['FinDisponibilite']
                );
                $stmt->execute();
                $stmt->close();
            }
        
            echo "<br/>Données initiales insérées avec succès.";
        }
    }  

    $conn->close();
?>