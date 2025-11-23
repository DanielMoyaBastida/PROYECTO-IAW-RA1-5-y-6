<?php
// inicia aplicación y sesión
require_once DIR . '/config.php';
session_start();
//Verifica si un usuario ha iniciado sesión. Si no es así, redirige al usuario a la página de inicio de sesión.
function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/public/login.php' . 'login.php');
        exit;
    }
}
// Función para comprobar la información del usuario
function current_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $u = $stmt->fetch();
    return $u ?: null;
}
?>