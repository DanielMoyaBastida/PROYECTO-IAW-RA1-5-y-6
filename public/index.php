<?php
// con esto redirigimos al usuario a list.php
header('Location: ../tickets/list.php');
exit;
?>
<html>
<head>
</head>
<body>
    <h1>Pulsa el enlace para ir a lista</h1>
    <button>
        <a href="tickets/list.php">
        Ir a list
        </a>
    </button>
</body>
</html>