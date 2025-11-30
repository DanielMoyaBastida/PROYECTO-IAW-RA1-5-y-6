<?php
//cargamos funciones verificamos usuario y obtenemos la conexion con la base de datos
require_once __DIR__ . '/../src/functions.php';
require_login();
$pdo = getPDO();

//recogemos la ID de la incidencia
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch();

//comprobamos que exista la incidencia
if (!$ticket) {
    set_flash('error','Incidencia no encontrada');
    header('Location: ../tickets/list.php');
    exit;
}

//cabecera HTML
require_once __DIR__ . '/../templates/header.php';
?>

<!-- mostramos los detalles del tiket y las acciones disponibles -->
<h2>Incidencia #<?= e((string)$ticket['id']) ?></h2>
<p><strong>Título:</strong> <?= e($ticket['title']) ?></p>
<p><strong>Descripción:</strong> <?= nl2br(e($ticket['description'])) ?></p>
<p><strong>Prioridad:</strong> <?= e($ticket['priority']) ?></p>
<p><strong>Estado:</strong> <?= e($ticket['status']) ?></p>
<p>
  <a href="<?= 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/edit.php' ?>tickets/edit.php?id=<?= e((string)$ticket['id']) ?>">Editar</a> |
  <a href="<?= 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/delete.php' ?>tickets/delete.php?id=<?= e((string)$ticket['id']) ?>" onclick="return confirm('¿Borrar?')">Borrar</a>
</p>

<!-- pie de pagina -->
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
