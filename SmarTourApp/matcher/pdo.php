<?php
function connessione()
{
    $pdo = null;

    $host = "localhost";
    $user = "root";
    $password = "";
    $db = "poidb";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $pdoe) {
        echo("Connection failed: " . $pdoe->getCode());
    }
    return $pdo;
}