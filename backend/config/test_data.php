<?php
function insertTestData($conn) {
    try {
        // Désactiver temporairement les contraintes de clé étrangère
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        
        // Nettoyer les tables existantes dans l'ordre inverse des dépendances
        $tables = [
            'NotificationClient',
            'DisponibiliteHebdomadaireTechnicien',
            'PlanningHebdomadaireTechnicien',
            'Intervention',
            'Gestionnaire',
            'Prepose',
            'Technicien',
            'Client',
            'Utilisateur'
        ];
        
        foreach ($tables as $table) {
            $conn->query("TRUNCATE TABLE $table");
            error_log("Table $table nettoyée");
        }
        
        // Réactiver les contraintes de clé étrangère
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");

        // Données utilisateurs
        $utilisateurs = [
            ['Nom' => 'Dupont', 'Prenom' => 'Jean', 'Email' => 'jean.dupont@example.com', 'MotDePasse' => 'password123', 'Type' => 'client'],
            ['Nom' => 'Martin', 'Prenom' => 'Sophie', 'Email' => 'sophie.martin@example.com', 'MotDePasse' => 'password123', 'Type' => 'technicien'],
            ['Nom' => 'Lefevre', 'Prenom' => 'Luc', 'Email' => 'luc.lefevre@example.com', 'MotDePasse' => 'password123', 'Type' => 'prepose'],
            ['Nom' => 'Durand', 'Prenom' => 'Marie', 'Email' => 'marie.durand@example.com', 'MotDePasse' => 'password123', 'Type' => 'gestionnaire'],
            ['Nom' => 'Moreau', 'Prenom' => 'Pierre', 'Email' => 'pierre.moreau@example.com', 'MotDePasse' => 'password123', 'Type' => 'client']
        ];

        // Insérer les utilisateurs
        $utilisateurIds = [];
        foreach ($utilisateurs as $utilisateur) {
            $stmt = $conn->prepare("INSERT INTO Utilisateur (Nom, Prenom, Email, MotDePasse, Type) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête utilisateur: " . $conn->error);
            }
            
            $stmt->bind_param("sssss", 
                $utilisateur['Nom'],
                $utilisateur['Prenom'],
                $utilisateur['Email'],
                $utilisateur['MotDePasse'],
                $utilisateur['Type']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'insertion de l'utilisateur: " . $stmt->error);
            }
            
            $utilisateurIds[$utilisateur['Email']] = $conn->insert_id;
            $stmt->close();
            error_log("Utilisateur inséré: " . $utilisateur['Email']);
        }

        // Données clients
        $clients = [
            [
                'UtilisateurID' => $utilisateurIds['jean.dupont@example.com'],
                'Adresse' => '123 Rue de la Paix, Paris',
                'Telephone' => '0123456789',
                'Demandes' => 'Installation fibre optique'
            ],
            [
                'UtilisateurID' => $utilisateurIds['pierre.moreau@example.com'],
                'Adresse' => '456 Avenue des Champs-Élysées, Paris',
                'Telephone' => '0987654321',
                'Demandes' => 'Problème de connexion internet'
            ]
        ];

        // Insérer les clients
        $clientIds = [];
        foreach ($clients as $client) {
            $stmt = $conn->prepare("INSERT INTO Client (UtilisateurID, Adresse, Telephone, Demandes) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête client: " . $conn->error);
            }
            
            $stmt->bind_param("isss",
                $client['UtilisateurID'],
                $client['Adresse'],
                $client['Telephone'],
                $client['Demandes']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'insertion du client: " . $stmt->error);
            }
            
            $clientIds[] = $conn->insert_id;
            $stmt->close();
            error_log("Client inséré pour l'utilisateur ID: " . $client['UtilisateurID']);
        }

        // Données techniciens
        $technicien = [
            'UtilisateurID' => $utilisateurIds['sophie.martin@example.com']
        ];
        
        $stmt = $conn->prepare("INSERT INTO Technicien (UtilisateurID) VALUES (?)");
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête technicien: " . $conn->error);
        }
        
        $stmt->bind_param("i", $technicien['UtilisateurID']);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'insertion du technicien: " . $stmt->error);
        }
        
        $technicienId = $conn->insert_id;
        $stmt->close();
        error_log("Technicien inséré pour l'utilisateur ID: " . $technicien['UtilisateurID']);

        // Données préposés
        $prepose = [
            'UtilisateurID' => $utilisateurIds['luc.lefevre@example.com']
        ];
        
        $stmt = $conn->prepare("INSERT INTO Prepose (UtilisateurID) VALUES (?)");
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête préposé: " . $conn->error);
        }
        
        $stmt->bind_param("i", $prepose['UtilisateurID']);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'insertion du préposé: " . $stmt->error);
        }
        
        $stmt->close();
        error_log("Préposé inséré pour l'utilisateur ID: " . $prepose['UtilisateurID']);

        // Données gestionnaires
        $gestionnaire = [
            'UtilisateurID' => $utilisateurIds['marie.durand@example.com']
        ];
        
        $stmt = $conn->prepare("INSERT INTO Gestionnaire (UtilisateurID) VALUES (?)");
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête gestionnaire: " . $conn->error);
        }
        
        $stmt->bind_param("i", $gestionnaire['UtilisateurID']);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'insertion du gestionnaire: " . $stmt->error);
        }
        
        $stmt->close();
        error_log("Gestionnaire inséré pour l'utilisateur ID: " . $gestionnaire['UtilisateurID']);

        // Données interventions
        $interventions = [
            [
                'TechnicienID' => $technicienId,
                'ClientID' => $clientIds[0],
                'TypeIntervention' => 'Installation',
                'Description' => 'Installation fibre optique et configuration box',
                'DebutIntervention' => '2024-01-15 09:00:00',
                'FinIntervention' => '2024-01-15 12:00:00',
                'StatutIntervention' => 'Planifiée',
                'Commentaires' => 'Première installation'
            ],
            [
                'TechnicienID' => $technicienId,
                'ClientID' => $clientIds[1],
                'TypeIntervention' => 'Dépannage',
                'Description' => 'Diagnostic et réparation connexion',
                'DebutIntervention' => '2024-01-16 14:00:00',
                'FinIntervention' => '2024-01-16 16:00:00',
                'StatutIntervention' => 'Planifiée',
                'Commentaires' => 'Problème de connexion intermittente'
            ]
        ];

        // Insérer les interventions
        foreach ($interventions as $intervention) {
            $stmt = $conn->prepare("INSERT INTO Intervention (TechnicienID, ClientID, TypeIntervention, Description, DebutIntervention, FinIntervention, StatutIntervention, Commentaires) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête intervention: " . $conn->error);
            }
            
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
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'insertion de l'intervention: " . $stmt->error);
            }
            
            $interventionId = $conn->insert_id;
            error_log("Intervention insérée avec ID: " . $interventionId);

            // Créer l'entrée correspondante dans le planning
            $date = new DateTime($intervention['DebutIntervention']);
            $numeroSemaine = $date->format('W');
            $jourSemaine = $date->format('l');

            $stmt = $conn->prepare("INSERT INTO PlanningHebdomadaireTechnicien (TechnicienID, InterventionID, ClientID, NumeroSemaine, JourSemaine, DebutIntervention, FinIntervention, StatutIntervention, Commentaires) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête planning: " . $conn->error);
            }
            
            $stmt->bind_param("iiissssss",
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
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'insertion dans le planning: " . $stmt->error);
            }
            
            $stmt->close();
            error_log("Planning inséré pour l'intervention ID: " . $interventionId);
        }

        // Données disponibilités
        $disponibilites = [
            [
                'TechnicienID' => $technicienId,
                'NumeroSemaine' => 2,
                'JourDisponible' => '2024-01-15',
                'DebutDisponibilite' => '2024-01-15 08:00:00',
                'FinDisponibilite' => '2024-01-15 18:00:00'
            ],
            [
                'TechnicienID' => $technicienId,
                'NumeroSemaine' => 2,
                'JourDisponible' => '2024-01-16',
                'DebutDisponibilite' => '2024-01-16 08:00:00',
                'FinDisponibilite' => '2024-01-16 18:00:00'
            ]
        ];

        foreach ($disponibilites as $disponibilite) {
            $stmt = $conn->prepare("INSERT INTO DisponibiliteHebdomadaireTechnicien (TechnicienID, NumeroSemaine, JourDisponible, DebutDisponibilite, FinDisponibilite) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête disponibilité: " . $conn->error);
            }
            
            $stmt->bind_param("iisss",
                $disponibilite['TechnicienID'],
                $disponibilite['NumeroSemaine'],
                $disponibilite['JourDisponible'],
                $disponibilite['DebutDisponibilite'],
                $disponibilite['FinDisponibilite']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'insertion de la disponibilité: " . $stmt->error);
            }
            
            $stmt->close();
            error_log("Disponibilité insérée pour le technicien ID: " . $technicienId);
        }

        // Données notifications
        $notifications = [
            [
                'ClientID' => $clientIds[0],
                'TechnicienID' => $technicienId,
                'TypeNotification' => 'Confirmation',
                'Message' => 'Votre intervention est confirmée pour le 15 janvier',
                'DateEnvoi' => '2024-01-10',
                'HeureEnvoi' => '10:00:00',
                'Lu' => false
            ],
            [
                'ClientID' => $clientIds[1],
                'TechnicienID' => $technicienId,
                'TypeNotification' => 'Rappel',
                'Message' => 'Rappel: Intervention prévue demain',
                'DateEnvoi' => '2024-01-15',
                'HeureEnvoi' => '14:00:00',
                'Lu' => false
            ]
        ];

        foreach ($notifications as $notification) {
            $stmt = $conn->prepare("INSERT INTO NotificationClient (ClientID, TechnicienID, TypeNotification, Message, DateEnvoi, HeureEnvoi, Lu) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête notification: " . $conn->error);
            }
            
            $stmt->bind_param("iissssi",
                $notification['ClientID'],
                $notification['TechnicienID'],
                $notification['TypeNotification'],
                $notification['Message'],
                $notification['DateEnvoi'],
                $notification['HeureEnvoi'],
                $notification['Lu']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'insertion de la notification: " . $stmt->error);
            }
            
            $stmt->close();
            error_log("Notification insérée pour le client ID: " . $notification['ClientID']);
        }

        error_log("Toutes les données de test ont été insérées avec succès");
        
    } catch (Exception $e) {
        error_log("Erreur lors de l'insertion des données de test: " . $e->getMessage());
        throw $e;
    }
}
?>