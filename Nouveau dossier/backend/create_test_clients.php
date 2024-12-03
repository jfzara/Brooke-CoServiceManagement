<?php
header('Content-Type: text/plain');

try {
    $conn = new mysqli('localhost', 'root', '', 'brookeandco');
    
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }

    // Données des clients test
    $clients = [
        [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@email.com',
            'motDePasse' => password_hash('password123', PASSWORD_DEFAULT),
            'type' => 'client',
            'adresse' => '123 rue de Paris, 75001 Paris',
            'telephone' => '0123456789'
        ],
        [
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'marie.martin@email.com',
            'motDePasse' => password_hash('password123', PASSWORD_DEFAULT),
            'type' => 'client',
            'adresse' => '456 avenue des Champs-Élysées, 75008 Paris',
            'telephone' => '0123456790'
        ],
        [
            'nom' => 'Bernard',
            'prenom' => 'Pierre',
            'email' => 'pierre.bernard@email.com',
            'motDePasse' => password_hash('password123', PASSWORD_DEFAULT),
            'type' => 'client',
            'adresse' => '789 boulevard Saint-Germain, 75006 Paris',
            'telephone' => '0123456791'
        ]
    ];

    // Création des utilisateurs et clients
    foreach ($clients as $client) {
        // Vérifier si l'utilisateur existe déjà
        $stmt = $conn->prepare("SELECT UtilisateurID FROM Utilisateur WHERE Email = ?");
        $stmt->bind_param("s", $client['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // 1. Créer l'utilisateur
            $stmt = $conn->prepare("INSERT INTO Utilisateur (Nom, Prenom, Email, MotDePasse, Type) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", 
                $client['nom'],
                $client['prenom'],
                $client['email'],
                $client['motDePasse'],
                $client['type']
            );
            
            if ($stmt->execute()) {
                $utilisateurId = $conn->insert_id;
                echo "✅ Utilisateur créé: " . $client['prenom'] . " " . $client['nom'] . "\n";
                
                // 2. Créer le client
                $stmt = $conn->prepare("INSERT INTO Client (UtilisateurID, Adresse, Telephone) VALUES (?, ?, ?)");
                $stmt->bind_param("iss",
                    $utilisateurId,
                    $client['adresse'],
                    $client['telephone']
                );
                
                if ($stmt->execute()) {
                    echo "✅ Client créé avec succès\n";
                } else {
                    echo "❌ Erreur lors de la création du client: " . $stmt->error . "\n";
                }
            } else {
                echo "❌ Erreur lors de la création de l'utilisateur: " . $stmt->error . "\n";
            }
        } else {
            echo "ℹ️ L'utilisateur " . $client['email'] . " existe déjà\n";
        }
    }

    echo "\nVérification des clients terminée avec succès!";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>