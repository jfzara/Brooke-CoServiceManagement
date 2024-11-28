<?php

    $host = 'localhost';
    $dbname = 'brookeandco';
    $username = 'root';
    $password = '';

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo "Erreur de connexion: " . addslashes($conn->connect_error);
        die();
    } else {
        //echo "Connexion réussie à la base de données!";
    }

?>
