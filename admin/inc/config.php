<?php
$host = 'localhost';
$db = 'onlinevotingsystem';
$user = 'root';
$pass = '';
$charset = 'utf8mb4'; // Character set to support all Unicode characters

//DSN(Data Source Name), A string that describes the connection, tells PDO to use MySQL with the given host, database, and charset.
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    //it tries to create a pdo object that establish the connection 
    $pdo = new PDO($dsn, $user, $pass);

    // Enable exceptions for errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
    die;
}
?>