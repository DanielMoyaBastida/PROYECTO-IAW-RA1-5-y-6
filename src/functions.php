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
//Generamos el token csrf
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
//Generamos un campo oculto para poder insertar el token en el formulario
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="'.e(csrf_token()).'">';
}
//Verficamos si el token de la petición coincide con el del usuario que la hace
function verify_csrf_token(?string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
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