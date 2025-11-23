<?php
declare(strict_types=1);

//definiciones de la configuracion
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'IAWDB');
define('DB_USER', 'root');
define('DB_PASS', 'Macedonia.12');

//URL del proyecto
define('BASE_URL', 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6.git'); 

//funcion para tener la conecxion a la base de datos
function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
    }
    return $pdo;
}
?>