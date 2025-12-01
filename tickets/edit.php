<?php
// Cargamos las funciones, verificamos el usuario y obtenemos la conexión
require_once __DIR__ . '/../src/functions.php';
require_login();
$pdo = getPDO();

// Recogemos la ID de la tickets desde URL y validamos que sea correcta
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'ID inválido');
    header('Location: /list.php');
    exit;
}

// Consultamos la tickets para mostrar los datos actuales
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$id]);
$tickets = $stmt->fetch();

// Si no existe se notifica y se redirige
if (!$tickets) {
    set_flash('error', 'incidencia no encontrada');
    header('Location: /list.php');
    exit;
}

// Arrays para almacenar errores
$errors = [];
// Usamos los datos de la base de datos como valores iniciales
$old = $tickets;

// Se mira si el formulario ha sido enviado y se verifica el token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token CSRF inválido');
        // Redirigir a la misma página para evitar bucles raros
        header('Location: /edit.php?id=' . $id);
        exit;
    }

    // Recogemos los datos enviados por el usuario
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = $_POST['prioridad'] ?? 'media';
    $estado = $_POST['estado'] ?? 'abierta';

    // Los guardamos en $old por si hay errores y hay que volver a mostrar el formulario
    $old = [
        'id' => $id,
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'prioridad' => $prioridad,
        'estado' => $estado
    ];

    // Validaciones
    if ($titulo === '') $errors['titulo'] = 'El título es obligatorio';
    if ($descripcion === '') $errors['descripcion'] = 'La descripción es obligatoria';
    
    // CAMBIO: Validación de ENUMs
    if (!in_array($prioridad, ['baja', 'media', 'alta'])) $errors['prioridad'] = 'Prioridad inválida';
    if (!in_array($estado, ['abierta', 'cerrada'])) $errors['estado'] = 'Estado inválido';

    // Si no hay errores se actualiza la tickets
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE tickets SET titulo = ?, descripcion = ?, prioridad = ?, estado = ? WHERE id = ?");
        $stmt->execute([$titulo, $descripcion, $prioridad, $estado, $id]);
        
        set_flash('success', 'tickets actualizada correctamente');
        // Redirigir a la vista de detalle
        header('Location: /view.php?id=' . $id);
        exit;
    }
}

// Cabecera HTML
require_once __DIR__ . '/../templates/header.php';
?>

<h2>Editar tickets #<?= e((string)$id) ?></h2>

<form method="post" action="">
  <?= csrf_field() ?>

  <label>Título<br>
    <input type="text" name="titulo" value="<?= e($old['titulo']) ?>" style="width: 100%; max-width: 400px;">
    <?php if(isset($errors['titulo'])): ?>
        <div class="error" style="color: red;"><?= e($errors['titulo']) ?></div>
    <?php endif; ?>
  </label><br><br>

  <label>Descripción<br>
    <textarea name="descripcion" rows="5" style="width: 100%; max-width: 400px;"><?= e($old['descripcion']) ?></textarea>
    <?php if(isset($errors['descripcion'])): ?>
        <div class="error" style="color: red;"><?= e($errors['descripcion']) ?></div>
    <?php endif; ?>
  </label><br><br>

  <label>Prioridad<br>
    <select name="prioridad">
      <option value="baja" <?= $old['prioridad'] === 'baja' ? 'selected' : '' ?>>Baja</option>
      <option value="media" <?= $old['prioridad'] === 'media' ? 'selected' : '' ?>>Media</option>
      <option value="alta" <?= $old['prioridad'] === 'alta' ? 'selected' : '' ?>>Alta</option>
    </select>
    <?php if(isset($errors['prioridad'])): ?>
        <div class="error" style="color: red;"><?= e($errors['prioridad']) ?></div>
    <?php endif; ?>
  </label><br><br>

  <label>Estado<br>
    <select name="estado">
      <option value="abierta" <?= $old['estado'] === 'abierta' ? 'selected' : '' ?>>Abierta</option>
      <option value="cerrada" <?= $old['estado'] === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
    </select>
    <?php if(isset($errors['estado'])): ?>
        <div class="error" style="color: red;"><?= e($errors['estado']) ?></div>
    <?php endif; ?>
  </label><br><br>

  <button type="submit">Guardar Cambios</button> 
  <a href="tickets/list.php" style="margin-left: 10px;">Cancelar</a>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
