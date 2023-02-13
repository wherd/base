<?php

return [
    // Set operational mode (debug / production )
    'mode' => 'debug',

    // Set default timezeone
    'timezone' => 'Europe/London',

    // Session information
    'session_name' => 'wherd',
    'session_expires' => 30,

    // Database connection details
    'database' => [
        'mysql:host=localhost;dbname=wherd',
        'root',
        'root',
        [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ],
    ],
];
