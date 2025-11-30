<?php
require_once __DIR__ . '/../src/functions.php';
$pdo = getPDO();

// verifica la validez del tokemm csrf
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error','Token CSRF inválido');
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }

    // maneja el inicio de sesión del usuario, con diferentes validaciones, 
    // como que no estén vacíos y comprueba la base de datos
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        set_flash('error','Usuario y contraseña son obligatorios.');
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        set_flash('success','Bienvenido.');
        header('Location: ' . BASE_URL . 'tickets/list.php');
        exit;
    } else {
        set_flash('error','Credenciales incorrectas.');
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

// genera el formulario de inicio de sesión
require_once __DIR__ . '/../templates/header.php';
?>
<h2>Login</h2>
<form method="post" action="">
  <?= csrf_field() ?>
  <label>Usuario<br><input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>"></label><br>
  <label>Contraseña<br><input type="password" name="password"></label><br><br>
  <button type="submit">Entrar</button>
</form>
<?php require_once __DIR__ . '/../templates/footer.php'; 
?>
