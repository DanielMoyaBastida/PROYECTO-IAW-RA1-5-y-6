<?php
// Cargamos funciones, verificamos usuario y obtenemos la conexión
require_once __DIR__'/../src/functions.php';
require_login();
$pdo = getPDO();

// Recogemos la ID de la tickets
$id = (int)($_GET['id'] ?? 0);

// CAMBIO: Tabla 'tickets' -> 'tickets'
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$tickets = $stmt->fetch();

// Comprobamos que exista la tickets
if (!$tickets) {
    if (function_exists('set_flash')) {
        set_flash('error', 'Incidencia no encontrada');
    }

    // Redirección usando BASE_URL para evitar errores de ruta
    header('Location: ../list.php');
    exit;
}

// Cabecera HTML
require_once __DIR__'/../templates/header.php';
?>

<h2>Incidencia #<?= e((string)$tickets['id']) ?></h2>

<div class="tickets-detalle">
    <p><strong>Título:</strong> <br> <?= e($tickets['titulo']) ?></p>

    <p><strong>Descripción:</strong> <br> 
    <?= nl2br(e($tickets['descripcion'])) ?>
    </p>

    <p><strong>Prioridad:</strong> <?= e($tickets['prioridad']) ?></p>

    <p><strong>Estado:</strong> <?= e($tickets['estado']) ?></p>
</div>

<hr>

<p>
  <a href="<?= /../edit?>tickets/edit.php?id=<?= e((string)$tickets['id']) ?>">Editar</a> | 

  <a href="<?= /../delete ?>tickets/delete.php?id=<?= e((string)$tickets['id']) ?>" 
     onclick="return confirm('¿Estás seguro de que quieres borrar esta tickets?');"
     style="color: red;">Borrar</a> |

  <a href="<?= /../list?>tickets/list.php">Volver al listado</a>
</p>

<?php require_once __DIR__'/../templates/footer.php'; ?>