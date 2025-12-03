<?php
// con esto redirigimos al usuario a list.php
header('Location: /login.php');
exit;
?>
<html>
<head>
</head>
<body>
    <h1>Pulsa el enlace para ir al login</h1>
    <button>
        <a href="public/login.php">
        Ir a login
        </a>
    </button>
</body>
</html>