<?php
// aquí borramos la información de la sesión de usuario y redirige al login
require_once _DIR_ . '/../src/functions.php';
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600);
header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/public/login.php' . 'login.php');
exit;
?>