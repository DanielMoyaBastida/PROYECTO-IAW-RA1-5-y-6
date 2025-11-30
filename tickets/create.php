<?php
//añadimos informacion de otro fichero
require_once __DIR__ . '/../src/functions.php';
require_login();
$pdo = getPDO();

//array para almacenar errores
$errors = [];
$old = ['title'=>'','description'=>'','priority'=>'medium','status'=>'abierta'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //verificamos el CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error','Token CSRF inválido');
        header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/src/functions.php' . 'tickets/create.php');
        exit;
    }

    //recogemos los datos del formulario

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $status = $_POST['status'] ?? 'abierta';

    $old = compact('title','description','priority','status');

    //validamos los datos
    if ($title === '') $errors['title'] = 'El título es obligatorio';
    if ($description === '') $errors['description'] = 'La descripción es obligatoria';
    if (!in_array($priority, ['low','medium','high'])) $errors['priority'] = 'Prioridad inválida';
    if (!in_array($status, ['abierta','cerrada'])) $errors['status'] = 'Estado inválido';

    //se guarda en la base de datos si no hay errores
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO tickets (title,description,priority,status,created_at) VALUES (?,?,?,?,NOW())");
        $stmt->execute([$title,$description,$priority,$status]);
        $id = (int)$pdo->lastInsertId();
        set_flash('success','Incidencia creada');
        header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/view.php' . 'tickets/view.php?id=' . $id);
        exit;
    }
}

require_once __DIR__ . '/../templates/header.php';
?>

<!-- mostramos el formulario y generamos el token csrf -->
<h2>Crear incidencia</h2>
<form method="post" action="">
  <?= csrf_field() ?>

  <label>Título<br>
<!-- mostramos si hay errores -->
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
