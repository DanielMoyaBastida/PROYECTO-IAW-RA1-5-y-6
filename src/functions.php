<?php
// Asegúrate de que config.php está en la misma carpeta o ajusta la ruta
require_once __DIR__ .  '/config.php'; 

// Iniciar sesión si no está iniciada ya
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Verifica si un usuario ha iniciado sesión.
//Si no es así, redirige al usuario a la página de inicio de sesión.
function require_login(): void {
  
    if (!isset($_SESSION['id_usuario'])) {
        // Usamos BASE_URL para una redirección más segura
        header('Location: ../public/login.php');
        exit;
    }
}

//Generamos el token CSRF
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

//Generamos un campo oculto para insertar el token en el formulario
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

//Verificamos si el token de la petición coincide con el de la sesión
function verify_csrf_token(?string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

//Obtiene la información del usuario actual desde la base de datos
function current_user(): ?array {
    if (empty($_SESSION['id_usuario'])) return null;

    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT id, nombre_usuario FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['id_usuario']]);

    $u = $stmt->fetch();
    return $u ?: null;
  }
//función de paginación
/**
* @param int $page
* @param int $perPage
* @param int $total
* @return array
*/
function paginate(int $page, int $perPage, int $total): array {
    $totalPages = (int)ceil($total / $perPage);
    $page = max(1, min($page, $totalPages)); // Asegura que la página sea válida
    $offset = ($page - 1) * $perPage;


    if ($total === 0) {
        $totalPages = 1; 
        $page = 1;
        $offset = 0;
    }

    return [
        'page' => $page,
        'perPage' => $perPage,
        'total' => $total,
        'totalPages' => $totalPages,
        'offset' => $offset
    ];
}

?>
