<?php
// aquí borramos la información de la sesión de usuario y redirige al login
require_once _DIR_ . '/../src/functions.php';
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600);
header('Location: ../login.php');
exit;
?>