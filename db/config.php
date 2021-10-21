<?php

/**
 * Configuration for database connection
 *
 * NOTE: if you change $dbname, please also change it on line 3 of db/init.sql
 */

$host       = "localhost"; // local host
$username   = "root"; // database username for local host
$password   = "root99"; // user password
$dbname     = "kms_darksky_db"; // database name
$dsn        = "mysql:host=$host;dbname=$dbname";
$options    = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
              ];
