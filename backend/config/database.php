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

// Création de la base de données si elle n'existe pas
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Base de données créée avec succès ou déjà existante<br/>";
} else {
    echo "Erreur lors de la création de la base de données: " . $conn->error;
    die();
}

// Sélection de la base de données
$conn->select_db($dbname);