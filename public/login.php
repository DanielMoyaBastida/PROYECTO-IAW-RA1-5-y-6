<?php
// public/login.php
require_once DIR . '/../src/functions.php';
$pdo = getPDO();
// maneja el inicio de sesión del usuario, con diferentes validaciones, 
// como que no estén vacíos y comprueba la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        set_flash('error','Usuario y contraseña son obligatorios.');
        header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/public/login.php' . 'login.php');
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
require_once DIR . '/../templates/header.php';
?>
<h2>Login</h2>
<form method="post" action="">
  <label>Usuario<br><input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>"></label><br>
  <label>Contraseña<br><input type="password" name="password"></label><br><br>
  <button type="submit">Entrar</button>
</form>
<?php require_once DIR . '/../templates/footer.php'; 
?>