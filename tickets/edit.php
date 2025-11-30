<?php
//cargamos las funciones, verificamos el usuario y obtenemos la conexion a la base de datos
require_once __DIR__ . '/../src/functions.php';
require_login();
$pdo = getPDO();

//recogemos la ID de la incidencia desde URL y validamos que sea correcto
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('error','ID inválido');
    header('Location: ../list.php');
    exit;
}

//consultamos la incidencia para mostrar los datos del formulario
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$ticket = $stmt->fetch();

//si no existe se notifica y se redirige
if (!$ticket) {
    set_flash('error','Incidencia no encontrada');
    header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/list.php' . 'tickets/list.php');
    exit;
}

//arrays para almacenar errores
$errors = [];
$old = $ticket;

//se mira si el formulario a sido enviado y se verifica el tokem CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error','Token CSRF inválido');
        header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/edit.php' . 'tickets/edit.php?id=' . $id);
        exit;
    }

    //recogemos los datos enviador por el usuario
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $status = $_POST['status'] ?? 'abierta';

    //los guardamos para recargarlo si hay errores y validamos los campos
    $old = compact('id','title','description','priority','status');
    if ($title === '') $errors['title'] = 'El título es obligatorio';
    if ($description === '') $errors['description'] = 'La descripción es obligatoria';
    if (!in_array($priority, ['low','medium','high'])) $errors['priority'] = 'Prioridad inválida';
    if (!in_array($status, ['abierta','cerrada'])) $errors['status'] = 'Estado inválido';

    //si no hay errores se actualiza la incidencia
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE tickets SET title = ?, description = ?, priority = ?, status = ? WHERE id = ?");
        $stmt->execute([$title,$description,$priority,$status,$id]);
        set_flash('success','Incidencia actualizada');
        header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/view.php' . 'tickets/view.php?id=' . $id);
        exit;
    }
}

//cabecera HTML
require_once __DIR__ . '/../templates/header.php';
?>

<!-- formulario para editar la incidencia -->
<h2>Editar incidencia #<?= e((string)$id) ?></h2>
<form method="post" action="">
  <?= csrf_field() ?>

  <label>Título<br>
    <input type="text" name="title" value="<?= e($old['title']) ?>">
    <?php if(isset($errors['title'])): ?><div class="error"><?= e($errors['title']) ?></div><?php endif; ?>
  </label><br>

  <label>Descripción<br>
    <textarea name="description"><?= e($old['description']) ?></textarea>
    <?php if(isset($errors['description'])): ?><div class="error"><?= e($errors['description']) ?></div><?php endif; ?>
  </label><br>

  <label>Prioridad
    <select name="priority">
      <option value="low" <?= $old['priority'] === 'low' ? 'selected' : '' ?>>Baja</option>
      <option value="medium" <?= $old['priority'] === 'medium' ? 'selected' : '' ?>>Media</option>
      <option value="high" <?= $old['priority'] === 'high' ? 'selected' : '' ?>>Alta</option>
    </select>
  </label><br>

  <label>Estado
    <select name="status">
      <option value="abierta" <?= $old['status'] === 'abierta' ? 'selected' : '' ?>>Abierta</option>
      <option value="cerrada" <?= $old['status'] === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
    </select>
  </label><br><br>
  <button type="submit">Guardar</button>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
