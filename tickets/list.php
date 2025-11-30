<?php
//cargamos funciones verificamos el usuario y obtenemosl a conexion a la base de datos
require_once __DIR__ . '/../src/functions.php';
require_login();
$pdo = getPDO();

//recogemos la ID de la incidencia por URL y validamos que sea correcto
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;

//contamos el total de incidencias
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE title LIKE ? OR description LIKE ?");
    $stmt->execute([$like,$like]);
} else {
    $stmt = $pdo->query("SELECT COUNT(*) FROM tickets");
}
$total = (int)$stmt->fetchColumn();
$pager = paginate($page, $perPage, $total);

//obtenemos los tickets segun la pagina
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE title LIKE ? OR description LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->execute([$like,$like,$pager['perPage'],$pager['offset']]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM tickets ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->execute([$pager['perPage'],$pager['offset']]);
}
$rows = $stmt->fetchAll();

//cabezera HTML
require_once __DIR__ . '/../templates/header.php';
?>

<h2>Listado de Incidencias</h2>
<!-- formulario de busqueda -->
<form method="get" action="">
  <input type="text" name="q" placeholder="Buscar..." value="<?= e($q) ?>">
  <button type="submit">Buscar</button>
</form>

<!-- tabla de resultados -->
<table class="table" role="table">
<thead><tr><th>ID</th><th>Título</th><th>Prioridad</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
  <td><?= e((string)$r['id']) ?></td>
  <td><?= e($r['title']) ?></td>
  <td><?= e($r['priority']) ?></td>
  <td><?= e($r['status']) ?></td>
  <td>
    <a href="<?= 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/view.php' ?>tickets/view.php?id=<?= e((string)$r['id']) ?>">Ver</a> |
    <a href="<?= 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/edit.php' ?>tickets/edit.php?id=<?= e((string)$r['id']) ?>">Editar</a> |
    <a href="<?= 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/delete.php' ?>tickets/delete.php?id=<?= e((string)$r['id']) ?>" onclick="return confirm('¿Borrar?')">Borrar</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- informacion de la paginacion y los enlaces -->
<div class="small">
Página <?= e((string)$pager['page']) ?> de <?= e((string)$pager['totalPages']) ?> |
Total: <?= e((string)$pager['total']) ?>
</div>

<div>
<?php for($p=1;$p<=$pager['totalPages'];$p++): ?>
  <?php if ($p == $pager['page']): ?>
    <strong><?= $p ?></strong>
  <?php else: ?>
    <a href="?q=<?= urlencode($q) ?>&page=<?= $p ?>"><?= $p ?></a>
  <?php endif; ?>
<?php endfor; ?>
</div>

<!-- pie de pagina -->
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
