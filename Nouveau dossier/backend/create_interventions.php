<?php
header('Content-Type: text/plain');

try {
    $conn = new mysqli('localhost', 'root', '', 'brookeandco');
    
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }

    // 1. Récupérer le TechnicienID de Sophie
    $email = 'sophie.martin@brooke.co';
    $stmt = $conn->prepare("
        SELECT t.TechnicienID 
        FROM Technicien t 
        JOIN Utilisateur u ON t.UtilisateurID = u.UtilisateurID 
        WHERE u.Email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $technicien = $result->fetch_assoc();

    if (!$technicien) {
        die("Technicien non trouvé pour Sophie Martin");
    }

    $technicienId = $technicien['TechnicienID'];
    echo "TechnicienID trouvé: " . $technicienId . "\n";

    // 2. Récupérer les ClientIDs disponibles
    $result = $conn->query("SELECT ClientID FROM Client");
    $clients = [];
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row['ClientID'];
    }

    if (empty($clients)) {
        die("Aucun client trouvé dans la base de données");
    }

    // 3. Définir les types d'interventions possibles
    $typesInterventions = [
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

    // 4. Définir les statuts possibles
    $statuts = ['Planifiée', 'En cours', 'Terminée', 'Annulée'];

    // 5. Créer 10 interventions
    $stmt = $conn->prepare("
        INSERT INTO Intervention (
            TechnicienID, 
            ClientID, 
            TypeIntervention, 
            Description, 
            DebutIntervention, 
            FinIntervention, 
            StatutIntervention, 
            Commentaires
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    for ($i = 0; $i < 10; $i++) {
        // Sélection aléatoire des données
        $clientId = $clients[array_rand($clients)];
        $intervention = $typesInterventions[array_rand($typesInterventions)];
        $statut = $statuts[array_rand($statuts)];
        
        // Génération des dates
        $joursDelta = rand(-5, 15); // Interventions sur 3 semaines
        $date = date('Y-m-d', strtotime("$joursDelta days"));
        $heureDebut = str_pad(rand(8, 16), 2, "0", STR_PAD_LEFT);
        $debutIntervention = "$date $heureDebut:00:00";
        $finIntervention = "$date " . str_pad($heureDebut + 2, 2, "0", STR_PAD_LEFT) . ":00:00";
        
        // Commentaire personnalisé
        $commentaire = "Intervention #" . ($i + 1) . " - " . $intervention['type'];
        
        $stmt->bind_param("iissssss", 
            $technicienId,
            $clientId,
            $intervention['type'],
            $intervention['description'],
            $debutIntervention,
            $finIntervention,
            $statut,
            $commentaire
        );
        
        if ($stmt->execute()) {
            echo "✅ Intervention créée: " . $intervention['type'] . " - " . $statut . "\n";
        } else {
            echo "❌ Erreur lors de la création de l'intervention: " . $stmt->error . "\n";
        }
    }

    echo "\nCréation des interventions terminée avec succès!";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>