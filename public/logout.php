<?php
require_once __DIR__ . '/src/config.php'; // Cargamos config para tener BASE_URL

session_start();
// Destruimos todas las variables de sesión
session_unset();
// Destruimos la sesión
session_destroy();

// Redirigimos al login
header('Location: /login.php');
exit;
?>
<html>
    
 <footer style="text-align: center; padding: 20px; font-size: 0.8em; color: #777;">
    <p>&copy; <?= date('Y') ?> Gestor de Incidencias S.L.</p>
</footer>

</body>
</html>