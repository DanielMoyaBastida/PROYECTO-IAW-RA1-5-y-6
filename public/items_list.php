<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/pdo.php';
require_once __DIR__.'/../app/utils.php';
require_login();

$busqueda = $_GET['q'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM items WHERE nombre LIKE ?");
$stmt->execute(["%$busqueda%"]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de incidencias</title>
    <?php require_once __DIR__.'/../app/theme.php'; ?>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= $tema === 'oscuro' ? 'theme-dark' : 'theme-light' ?>">>
<h1>Listado de incidencias</h1>

<form method="get">
    <input type="text" name="q" value="<?= e($busqueda) ?>" placeholder="Buscar">
    <button type="submit" class="btn">Buscar</button>
    <a href="items_form.php" class="btn" style="background:transparent;color:inherit;border:1px solid rgba(0,0,0,0.06);">Crear nueva incidencia</a>
</form>


<ul>
<?php foreach($items as $item): ?>
    <li class="card" style="display:flex;justify-content:space-between;align-items:center;">
        <span><?= e($item['nombre']) ?></span>
        <span>
            <a href="items_show.php?id=<?= $item['id'] ?>">Ver</a>
            <a href="items_form.php?id=<?= $item['id'] ?>">Editar</a>
            <a href="items_delete.php?id=<?= $item['id'] ?>">Borrar</a>
        </span>
    </li>
<?php endforeach; ?>
</ul>

<p><a href="index.php">Volver al panel</a></p>

</body>
</html>