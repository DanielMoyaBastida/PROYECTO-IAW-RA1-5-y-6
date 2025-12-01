<?php
// Cargamos funciones, verificamos el usuario y obtenemos la conexión
require_once __DIR__ . '/../src/functions.php';
require_login();
$pdo = getPDO();

// Recogemos la búsqueda y validamos la paginación
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;

// 1. Contamos el total de tickets (Tabla: tickets)
if ($q !== '') {
    $like = '%' . $q . '%';
    // Buscamos por 'titulo' o 'descripcion'
    $stmt = $pdo->prepare("SELECT COUNT() FROM tickets WHERE titulo LIKE ? OR descripcion LIKE ?");
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("SELECT COUNT() FROM tickets");
}
$total = (int)$stmt->fetchColumn();

// Calculamos el offset para la paginación
$pager = paginate($page, $perPage, $total);

// 2. Obtenemos los registros (Tabla: tickets)
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE titulo LIKE ? OR descripcion LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $like);
    $stmt->bindValue(2, $like);
    $stmt->bindValue(3, $pager['perPage'], PDO::PARAM_INT);
    $stmt->bindValue(4, $pager['offset'], PDO::PARAM_INT);
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM tickets ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $pager['perPage'], PDO::PARAM_INT);
    $stmt->bindValue(2, $pager['offset'], PDO::PARAM_INT);
    $stmt->execute();
}
$rows = $stmt->fetchAll();
// Cabecera HTML
require_once '/../templates/header.php';
?>

<h2>Listado de tickets</h2>

<form method="get" action="">
  <input type="text" name="q" placeholder="Buscar..." value="<?= e($q) ?>">
  <button type="submit">Buscar</button>
</form>
<br>

<a href="tickets/create.php">
    <button>+ Nueva Incidencia</button>
</a>

<table class="table" role="table" border="1" cellpadding="10" style="border-collapse: collapse; width: 100%; margin-top: 20px;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Prioridad</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($rows) > 0): ?>
        <?php foreach($rows as $r): ?>
        <tr>
            <td><?= e((string)$r['id']) ?></td>
            <td><?= e($r['titulo']) ?></td>
            <td><?= e($r['prioridad']) ?></td>
            <td><?= e($r['estado']) ?></td>
            <td>
                <a href="tickets/view.php?id=<?= e((string)$r['id']) ?>">Ver</a> |
                <a href="tickets/edit.php?id=<?= e((string)$r['id']) ?>">Editar</a> |
                <a href="tickets/delete.php?id=<?= e((string)$r['id']) ?>" 
                   onclick="return confirm('¿Estás seguro de borrar esta incidencia?');">Borrar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="5">No hay incidencias encontradas.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<div class="small" style="margin-top: 10px;">
    Página <?= e((string)$pager['page']) ?> de <?= e((string)$pager['totalPages']) ?> |
    Total: <?= e((string)$pager['total']) ?>
</div>

<div style="margin-top: 10px;">
<?php for($p = 1; $p <= $pager['totalPages']; $p++): ?>
    <?php if ($p == $pager['page']): ?>
        <strong style="margin-right: 5px;"><?= $p ?></strong>
    <?php else: ?>
        <a href="?q=<?= urlencode($q) ?>&page=<?= $p ?>" style="margin-right: 5px;"><?= $p ?></a>
    <?php endif; ?>
<?php endfor; ?>
</div>

