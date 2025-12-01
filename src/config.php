<?php
declare(strict_types=1);

// Definiciones de la configuración de la Base de Datos
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'gestor_inc');
define('DB_USER', 'root');
define('DB_PASS', 'Macedonia.12');

// Función para obtener la conexión a la base de datos
function getPDO(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host= 127.0.0.1 ";"dbname= gestor_inc ";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, root, $opt);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    return $pdo;
}
?>