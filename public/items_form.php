<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/pdo.php';
require_once __DIR__.'/../app/csrf.php';
require_once __DIR__.'/../app/utils.php';
require_login();

$id = $_GET['id'] ?? null;
$error = '';
$item = ['nombre'=>'','categoria'=>'','ubicacion'=>'','stock'=>0];

// Si es edición
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id=?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) die('No existe');
}

// Guardar
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!check_csrf($_POST['csrf'] ?? '')) {
        $error = 'CSRF inválido';
    } else {
        $nombre = $_POST['nombre'] ?? '';
        if (!$nombre) $error = 'Nombre obligatorio';

        if (!$error) {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE items SET nombre=?, categoria=?, ubicacion=?, stock=? WHERE id=?");
                $stmt->execute([
                    $nombre,
                    $_POST['categoria'],
                    $_POST['ubicacion'],
                    $_POST['stock'],
                    $id
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO items (nombre,categoria,ubicacion,stock) VALUES (?,?,?,?)");
                $stmt->execute([
                    $nombre,
                    $_POST['categoria'],
                    $_POST['ubicacion'],
                    $_POST['stock']
                ]);
            }
            header('Location: items_list.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $id ? 'Editar' : 'Crear' ?> incidencia</title>
  <?php require_once __DIR__.'/../app/theme.php'; ?>
  <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body class="<?= $tema === 'oscuro' ? 'theme-dark' : 'theme-light' ?>">
<div class="container">
  <h1><?= $id ? 'Editar' : 'Crear' ?> incidencia</h1>

  <?php if($error): ?>
    <p class="error"><?= e($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

    <label>Nombre</label>
    <input type="text" name="nombre" value="<?= e($item['nombre']) ?>">

    <label>Categoría</label>
    <input type="text" name="categoria" value="<?= e($item['categoria']) ?>">

    <label>Ubicación</label>
    <input type="text" name="ubicacion" value="<?= e($item['ubicacion']) ?>">

    <label>Stock / Prioridad</label>
    <input type="number" name="stock" value="<?= e($item['stock']) ?>">

    <div style="margin-top:12px;">
      <button class="btn" type="submit"><?= $id ? 'Actualizar' : 'Crear' ?></button>
      <a href="items_list.php" class="btn" style="background:transparent;color:inherit;border:1px solid rgba(0,0,0,0.06); margin-left:8px;">Volver</a>
    </div>
  </form>
</div>
</body>
</html>