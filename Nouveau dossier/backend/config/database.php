<?php
// backend/config/database.php
$host = 'localhost';
$dbname = 'brookeandco';
$username = 'root';
$password = '';

try {
    // Créer une connexion sans spécifier la base de données
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Erreur de connexion: " . $conn->connect_error);
    }

    // Vérifier si la base de données existe
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    
    if ($result->num_rows === 0) {
        // Créer la base de données si elle n'existe pas
        if ($conn->query("CREATE DATABASE IF NOT EXISTS $dbname") === TRUE) {
            error_log("Base de données créée avec succès");
        } else {
            throw new Exception("Erreur lors de la création de la base de données: " . $conn->error);
        }
    }

    // Se connecter à la base de données
    $conn->select_db($dbname);

} catch (Exception $e) {
    error_log("Erreur database.php: " . $e->getMessage());
    die($e->getMessage());
}
?>