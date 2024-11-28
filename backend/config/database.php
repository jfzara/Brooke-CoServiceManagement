<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "brookeandco";

// Création de la connexion sans spécifier la base de données
$conn = new mysqli($servername, $username, $password);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Suppression de la base de données si elle existe
$sql = "DROP DATABASE IF EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Base de données supprimée avec succès<br/>";
} else {
    echo "Erreur lors de la suppression de la base de données: " . $conn->error . "<br/>";
}

// Création de la base de données
$sql = "CREATE DATABASE $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Base de données créée avec succès<br/>";
} else {
    echo "Erreur lors de la création de la base de données: " . $conn->error;
    die();
}

// Sélection de la base de données
$conn->select_db($dbname);

?>