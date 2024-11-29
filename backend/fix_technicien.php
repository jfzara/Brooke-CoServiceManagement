<?php
header('Content-Type: text/plain');

try {
    $conn = new mysqli('localhost', 'root', '', 'brookeandco');
    
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }

    $email = 'sophie.martin@brooke.co';
    
    // 1. Récupérer l'utilisateur
    $stmt = $conn->prepare("SELECT * FROM Utilisateur WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("Utilisateur non trouvé");
    }

    echo "Utilisateur trouvé: ID=" . $user['UtilisateurID'] . "\n";

    // 2. Vérifier si une entrée existe déjà dans la table Technicien
    $stmt = $conn->prepare("SELECT * FROM Technicien WHERE UtilisateurID = ?");
    $stmt->bind_param("i", $user['UtilisateurID']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // 3. Créer l'entrée dans la table Technicien
        $stmt = $conn->prepare("INSERT INTO Technicien (UtilisateurID) VALUES (?)");
        $stmt->bind_param("i", $user['UtilisateurID']);
        
        if ($stmt->execute()) {
            $technicienId = $conn->insert_id;
            echo "✅ Entrée Technicien créée avec succès. TechnicienID: " . $technicienId . "\n";
        } else {
            echo "❌ Erreur lors de la création de l'entrée Technicien: " . $stmt->error . "\n";
        }
    } else {
        echo "Une entrée Technicien existe déjà pour cet utilisateur.\n";
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>