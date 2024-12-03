<?php
function insertTestData($conn) {
    // Nettoyage des tables existantes
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $tables = ['Intervention', 'Client', 'Technicien', 'Utilisateur'];
    foreach ($tables as $table) {
        $conn->query("TRUNCATE TABLE $table");
    }
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    // Insertion des utilisateurs
    $users = [
        // Techniciens
        ["Sophie", "Martin", "sophie.martin@brooke.co", password_hash("password123", PASSWORD_DEFAULT), "technicien"],
        ["Thomas", "Bernard", "thomas.bernard@brooke.co", password_hash("password123", PASSWORD_DEFAULT), "technicien"],
        ["Julie", "Dubois", "julie.dubois@brooke.co", password_hash("password123", PASSWORD_DEFAULT), "technicien"],
        
        // Clients
        ["Jean", "Dupont", "jean.dupont@email.com", password_hash("password123", PASSWORD_DEFAULT), "client"],
        ["Marie", "Laurent", "marie.laurent@email.com", password_hash("password123", PASSWORD_DEFAULT), "client"],
        ["Pierre", "Moreau", "pierre.moreau@email.com", password_hash("password123", PASSWORD_DEFAULT), "client"],
        ["Alice", "Petit", "alice.petit@email.com", password_hash("password123", PASSWORD_DEFAULT), "client"],
        ["Lucas", "Roux", "lucas.roux@email.com", password_hash("password123", PASSWORD_DEFAULT), "client"],
        
        // Préposé
        ["Claire", "Robert", "claire.robert@brooke.co", password_hash("password123", PASSWORD_DEFAULT), "prepose"]
    ];

    foreach ($users as $user) {
        $sql = "INSERT INTO Utilisateur (Nom, Prenom, Email, MotDePasse, Type) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $user[0], $user[1], $user[2], $user[3], $user[4]);
        $stmt->execute();
    }

    // Création des techniciens
    $techniciens = ["Sophie Martin", "Thomas Bernard", "Julie Dubois"];
    foreach ($techniciens as $tech) {
        list($prenom, $nom) = explode(" ", $tech);
        $sql = "INSERT INTO Technicien (UtilisateurID) 
                SELECT UtilisateurID FROM Utilisateur 
                WHERE Nom = ? AND Prenom = ? AND Type = 'technicien'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nom, $prenom);
        $stmt->execute();
    }

    // Création des clients
    $clients = ["Jean Dupont", "Marie Laurent", "Pierre Moreau", "Alice Petit", "Lucas Roux"];
    foreach ($clients as $client) {
        list($prenom, $nom) = explode(" ", $client);
        $sql = "INSERT INTO Client (UtilisateurID, Adresse, Telephone) 
                SELECT UtilisateurID, CONCAT('123 rue ', ?, ' 75000 Paris'), '0123456789'
                FROM Utilisateur 
                WHERE Nom = ? AND Prenom = ? AND Type = 'client'";
        $stmt = $conn->prepare($sql);
        $nom_rue = $nom;
        $stmt->bind_param("sss", $nom_rue, $nom, $prenom);
        $stmt->execute();
    }

    // Types d'interventions avec descriptions détaillées
    $interventions = [
        [
            "type" => "Installation Fibre",
            "description" => "Installation complète fibre optique avec configuration box et tests débit"
        ],
        [
            "type" => "Dépannage Internet",
            "description" => "Diagnostic et résolution problèmes de connexion, test ligne et équipements"
        ],
        [
            "type" => "Configuration WiFi",
            "description" => "Optimisation couverture WiFi, paramétrage sécurité et test performance"
        ],
        [
            "type" => "Installation TV",
            "description" => "Installation décodeur TV, configuration chaînes et test qualité image"
        ],
        [
            "type" => "Maintenance Box",
            "description" => "Mise à jour firmware, vérification connexion et paramètres"
        ]
    ];

    // Statuts possibles avec leur probabilité
    $statuts = [
        ["Planifiée", 40],
        ["En cours", 30],
        ["Terminée", 20],
        ["Annulée", 10]
    ];

    // Création des interventions pour chaque technicien
    $technicienIds = [1, 2, 3]; // IDs des techniciens
    foreach ($technicienIds as $techId) {
        for ($i = 1; $i <= 10; $i++) {
            $clientId = rand(1, count($clients));
            $intervention = $interventions[array_rand($interventions)];
            
            // Génération date et heure
            $joursDelta = rand(-5, 15); // Interventions sur 3 semaines
            $date = date('Y-m-d', strtotime("$joursDelta days"));
            $heureDebut = str_pad(rand(8, 16), 2, "0", STR_PAD_LEFT);
            $debutIntervention = "$date $heureDebut:00:00";
            $finIntervention = "$date " . str_pad($heureDebut + 2, 2, "0", STR_PAD_LEFT) . ":00:00";
            
            // Sélection du statut selon les probabilités
            $rand = rand(1, 100);
            $statutCumul = 0;
            $statut = "";
            foreach ($statuts as $s) {
                $statutCumul += $s[1];
                if ($rand <= $statutCumul) {
                    $statut = $s[0];
                    break;
                }
            }
            
            // Commentaire personnalisé
            $commentaire = "Intervention #$i - " . $intervention['type'] . " chez client $clientId";
            
            $sql = "INSERT INTO Intervention (TechnicienID, ClientID, TypeIntervention, Description, 
                    DebutIntervention, FinIntervention, StatutIntervention, Commentaires) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iissssss", 
                $techId, 
                $clientId, 
                $intervention['type'], 
                $intervention['description'], 
                $debutIntervention, 
                $finIntervention, 
                $statut, 
                $commentaire
            );
            $stmt->execute();
        }
    }

    echo "Données de test insérées avec succès\n";
}
?>