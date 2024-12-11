<?php

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'srinathon';


$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                
];

try {
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("Database Connection Failed: " . $e->getMessage(), 0);
    die("Database connection failed. Please try again later.");
}
?>
