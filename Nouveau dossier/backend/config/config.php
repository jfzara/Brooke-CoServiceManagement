<?php

    require 'database.php';

    $config = [
        'app_name' => 'BrookeAndCo',
        'app_version' => '1.0.0',
        'debug' => true,
        'timezone' => 'America/Toronto',
        'locale' => 'fr_CA',
    ];

    // Définir la timezone
    date_default_timezone_set($config['timezone']);

?>
