<?php

/**
 * Open a connection via PDO to create a
 * new database and tables with structure.
 *
 */

require "config.php";

try {
    $pdo = new PDO("mysql:host=$host", $username, $password, $options);
    $sql = file_get_contents( dirname(__FILE__) . "/init.sql" );
    $pdo->exec($sql);
    
    echo "Database and weather tables created successfully.\n";
} catch(PDOException $error) {
    echo $sql . "\n" . $error->getMessage() . "\n";
}
