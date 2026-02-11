<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once __DIR__.'/../app/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tema = $_POST['tema'] ?? 'claro';

    setcookie(
        'tema',
        $tema,
        time() + 60*60*24*30,
        '/'
    );

    header('Location: preferencias.php');
    exit;
}

$tema = $_COOKIE['tema'] ?? 'claro';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Preferencias</title>
<link rel="stylesheet" href="css/stylesheet.css">
</head>
<body class="<?= $tema === 'oscuro' ? 'theme-dark' : 'theme-light' ?>">

<div class="container">
  <h1>Preferencias</h1>

  <form method="post">
      <label> Tema </label>
      <select name="tema">
          <option value="claro" <?= $tema==='claro'?'selected':'' ?>>Claro</option>
          <option value="oscuro" <?= $tema==='oscuro'?'selected':'' ?>>Oscuro</option>
      </select>
      <div style="margin-top:12px;">
        <button class="btn" type="submit">Guardar</button>
        <a href="index.php" class="btn" style="background:transparent;color:inherit;border:1px solid rgba(0,0,0,0.06); margin-left:8px;">Volver</a>
      </div>
  </form>

  <p>Tema actual: <strong><?= $tema ?></strong></p>

  <p><a href="index.php">Volver al panel</a></p>
</div>

</body>
</html>