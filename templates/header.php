<?php
//aqui se incluye el archivo functions uqe contiene las funciones
require_once __DIR__ . '/../src/functions.php';
//obtiene la informacion del usuario logeado
$user = current_user();
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Gestor de Incidencias</title>
<style>
    /*estilos CSS para la pagina */
:root { --bg: #fff; --fg: #000; --accent: #0a6;}
body { font-family: Arial, sans-serif; margin:0; padding:1rem; background:var(--bg); color:var(--fg); }
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
.nav a { margin-right:0.5rem; }
.container { max-width:900px; margin:0 auto; }


body.theme-dark { --bg:#121212; --fg:#eaeaea; --accent:#6cf; }
.small { font-size:0.9rem; }
.error { color: #b00; }
.success { color: #0b0; }
.table { border-collapse: collapse; width:100%;}
.table th, .table td { border:1px solid #ddd; padding:0.4rem; text-align:left; }
</style>
</head>
<body class="<?= e('theme-' . ($pref === 'dark' ? 'dark' : 'light')) ?>">
<div class="container">
  <div class="header">
    <div>
      <h1>Gestor de Incidencias</h1>
      <div class="small">PHP + PDO (sin frameworks)</div>
    </div>
    <div class="nav">
      <?php if ($user): ?>
        Hola, <?= e($user['username']) ?> |
        <a href="<?= BASE_URL ?>tickets/list.php">Listado</a>
        <a href="<?= BASE_URL ?>tickets/create.php">Crear</a>
        <a href="<?= BASE_URL ?>logout.php">Logout</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>login.php">Login</a>
      <?php endif; ?>
    </div>
  </div>
  <?php if ($m=get_flash('success')): ?><div class="success"><?= e($m) ?></div><?php endif; ?>
  <?php if ($m=get_flash('error')): ?><div class="error"><?= e($m) ?></div><?php endif; ?>