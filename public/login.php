<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/utils.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'] ?? '')) {
        $error = 'CSRF token inválido';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$_POST['username'] ?? '']);
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
            $_SESSION['user'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Usuario o contraseña incorrecta";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="theme-light">
<div class="container">
<h1>Iniciar sesión</h1>
<?php if($error): ?>
<p class="error"><?= e($error) ?></p>
<?php endif; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<input type="text" name="username" placeholder="Usuario" value="<?= e(old('username')) ?>"><br><br>
<input type="password" name="password" placeholder="Contraseña"><br><br>
<button type="submit">Entrar</button>
</form>
</div>
</body>
</html>