<?php
header('Content-Type: text/plain');

try {
    $conn = new mysqli('localhost', 'root', '', 'brookeandco');
    
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }

    $email = 'sophie.martin@brooke.co';
    
    echo "=== Vérification pour $email ===\n\n";

    // 1. Vérifier l'utilisateur
    $query = "SELECT * FROM Utilisateur WHERE Email = '$email'";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();

    if ($user) {
        echo "1. Utilisateur trouvé:\n";
        echo "   ID: " . $user['UtilisateurID'] . "\n";
        echo "   Nom: " . $user['Nom'] . "\n";
        echo "   Type: " . $user['Type'] . "\n\n";

        // 2. Vérifier l'entrée Technicien
        $query = "SELECT * FROM Technicien WHERE UtilisateurID = " . $user['UtilisateurID'];
        $result = $conn->query($query);
        $technicien = $result->fetch_assoc();

        if ($technicien) {
            echo "2. Technicien trouvé:\n";
            echo "   TechnicienID: " . $technicien['TechnicienID'] . "\n\n";

            // 3. Compter les interventions
            $query = "SELECT COUNT(*) as total FROM Intervention WHERE TechnicienID = " . $technicien['TechnicienID'];
            $result = $conn->query($query);
            $count = $result->fetch_assoc()['total'];

            echo "3. Nombre d'interventions: $count\n\n";

            if ($count > 0) {
                // 4. Lister les interventions
                $query = "SELECT i.*, c.Nom as ClientNom, c.Prenom as ClientPrenom 
                         FROM Intervention i 
                         LEFT JOIN Client c ON i.ClientID = c.ClientID 
                         WHERE i.TechnicienID = " . $technicien['TechnicienID'];
                $result = $conn->query($query);

                echo "4. Liste des interventions:\n";
                while ($intervention = $result->fetch_assoc()) {
                    echo "   - " . $intervention['TypeIntervention'] . "\n";
                    echo "     Client: " . $intervention['ClientNom'] . " " . $intervention['ClientPrenom'] . "\n";
                    echo "     Date: " . $intervention['DebutIntervention'] . "\n";
                    echo "     Statut: " . $intervention['StatutIntervention'] . "\n\n";
                }
            }
        } else {
            echo "❌ Aucune entrée dans la table Technicien\n";
        }
    } else {
        echo "❌ Aucun utilisateur trouvé avec cet email\n";
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>