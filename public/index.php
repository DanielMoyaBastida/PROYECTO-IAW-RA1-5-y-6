<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once __DIR__.'/../app/auth.php';
require_login();

// Leer cookie
$tema = $_COOKIE['tema'] ?? 'claro';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= $tema === 'oscuro' ? 'theme-dark' : 'theme-light' ?>">

<h1>Panel de control</h1>
<p>Usuario: <?= $_SESSION['user'] ?></p>
<div class="container">
<nav>
    <ul>
        <li><a href="items_list.php">Listado</a></li>
        <li><a href="items_form.php">Nueva incidencia</a></li>
        <li><a href="preferencias.php">Preferencias</a></li>
    </ul>
</nav>
</div>
<a href="logout.php">Cerrar sesión</a>
</body>
</html>