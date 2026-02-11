<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/pdo.php';
require_once __DIR__.'/../app/utils.php';
require_login();

$id = $_GET['id'] ?? null;
if (!$id) die('ID no válido');

$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) die('Incidencia no encontrada');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Detalle</title>
<?php require_once __DIR__.'/../app/theme.php'; ?>
<link rel="stylesheet" href="css/stylesheet.css">
</head>
<body class="<?= $tema === 'oscuro' ? 'theme-dark' : 'theme-light' ?>">
<div class="container">
  <h1>Detalle de incidencia</h1>

  <div class="card">
    <p><strong class="kv">Nombre:</strong> <?= e($item['nombre']) ?></p>
    <p><strong class="kv">Categoría:</strong> <?= e($item['categoria']) ?></p>
    <p><strong class="kv">Ubicación:</strong> <?= e($item['ubicacion']) ?></p>
    <p><strong class="kv">Prioridad:</strong> <?= e($item['stock']) ?></p>
  </div>

  <p style="margin-top:12px;"><a class="btn" href="items_list.php">Volver</a></p>
</div>
</body>
</html>