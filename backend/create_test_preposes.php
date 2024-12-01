<?php
header('Content-Type: text/plain');

try {
    $conn = new mysqli('localhost', 'root', '', 'brookeandco');
    
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }

    // Données des préposés test
    $preposes = [
        [
            'nom' => 'Dubois',
            'prenom' => 'Marie',
            'email' => 'marie.dubois@brooke.co',
            'motDePasse' => password_hash('password123', PASSWORD_DEFAULT),
            'type' => 'prepose'
        ],
        [
            'nom' => 'Lambert',
            'prenom' => 'Thomas',
            'email' => 'thomas.lambert@brooke.co',
            'motDePasse' => password_hash('password123', PASSWORD_DEFAULT),
            'type' => 'prepose'
        ],
        [
            'nom' => 'Rousseau',
            'prenom' => 'Sophie',
            'email' => 'sophie.rousseau@brooke.co',
            'motDePasse' => password_hash('password123', PASSWORD_DEFAULT),
            'type' => 'prepose'
        ]
    ];

    // Création des utilisateurs et préposés
    foreach ($preposes as $prepose) {
        // Vérifier si l'utilisateur existe déjà
        $stmt = $conn->prepare("SELECT UtilisateurID FROM Utilisateur WHERE Email = ?");
        $stmt->bind_param("s", $prepose['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // 1. Créer l'utilisateur
            $stmt = $conn->prepare("INSERT INTO Utilisateur (Nom, Prenom, Email, MotDePasse, Type) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", 
                $prepose['nom'],
                $prepose['prenom'],
                $prepose['email'],
                $prepose['motDePasse'],
                $prepose['type']
            );
            
            if ($stmt->execute()) {
                $utilisateurId = $conn->insert_id;
                echo "✅ Utilisateur créé: " . $prepose['prenom'] . " " . $prepose['nom'] . "\n";
                
                // 2. Créer le préposé
                $stmt = $conn->prepare("INSERT INTO Prepose (UtilisateurID) VALUES (?)");
                $stmt->bind_param("i", $utilisateurId);
                
                if ($stmt->execute()) {
                    echo "✅ Préposé créé avec succès\n";
                } else {
                    echo "❌ Erreur lors de la création du préposé: " . $stmt->error . "\n";
                }
            } else {
                echo "❌ Erreur lors de la création de l'utilisateur: " . $stmt->error . "\n";
            }
        } else {
            echo "ℹ️ L'utilisateur " . $prepose['email'] . " existe déjà\n";
        }
    }

    echo "\nCréation des préposés terminée avec succès!";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>