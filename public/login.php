<?php
require_once __DIR__ .  '/../src/functions.php';
$pdo = getPDO();

// Verifica la validez del token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        header('Location: login.php');
        exit;
    }

    // Recogemos los datos
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if ($nombre_usuario === '' || $contrasena === '') {
        set_flash('error', 'Usuario y contraseña son obligatorios.');
        header('Location: login.php');
        exit;
    }

  
    // Seleccionamos de la tabla 'usuarios'
    $stmt = $pdo->prepare("SELECT id, nombre_usuario, hash_contrasena FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$nombre_usuario]);
    $usuario = $stmt->fetch();

    // Verificamos la contraseña contra el campo 'hash_contrasena'
    if ($usuario && password_verify($contrasena, $usuario['hash_contrasena'])) {
        session_regenerate_id(true);

        // Guardamos el ID como 'id_usuario' para ser consistentes con la tabla de auditoría
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nombre_usuario'] = $usuario['nombre_usuario']; 

        // Redirigir a la lista de tickets
        header('Location: ../tickets/list.php'); 
        exit;
    } else {
        set_flash('error', 'Credenciales incorrectas.');
        header('Location: login.php');
        exit;
    }
}

// Genera el formulario de inicio de sesión
require_once  __DIR__ .'/../templates/header.php';
?>
<html>
<h2>Iniciar Sesión</h2>

<form method="post" action="">
  <?= csrf_field() ?>

  <label>
    Usuario<br>
    <input type="text" name="nombre_usuario" value="<?= 'nombre_usuario' ?>" required>
  </label>
  <br>

  <label>
    Contraseña<br>
    <input type="password" name="contrasena" required>
  </label>
  <br><br>

  <button type="submit">Entrar</button>
</form>
</html>
<?php require_once  '/../templates/footer.php'; ?>