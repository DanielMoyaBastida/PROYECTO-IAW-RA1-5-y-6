<?php
$dsn = "mysql:host=localhost;dbname=incidencias;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, 'phpuser', '1234', $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}