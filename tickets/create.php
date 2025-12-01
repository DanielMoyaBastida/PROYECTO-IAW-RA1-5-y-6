<?php
// Añadimos información de otro fichero
require_once __DIR__ . '/../src/functions.php';
require_login();
$pdo = getPDO();

// Array para almacenar errores
$errors = [];
// Valores por defecto
$old = ['titulo' => '', 'descripcion' => '', 'prioridad' => 'media', 'estado' => 'abierta'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificamos el CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token CSRF inválido');
        // Redirección interna correcta
        header('Location: /create.php');
        exit;
    }
// Recogemos los datos del formulario
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = $_POST['prioridad'] ?? 'media';
    $estado = $_POST['estado'] ?? 'abierta';

    // Guardamos para repoblar el formulario si falla
    $old = compact('titulo', 'descripcion', 'prioridad', 'estado');

    // Validamos los datos
    if ($titulo === '') $errors['titulo'] = 'El título es obligatorio';
    if ($descripcion === '') $errors['descripcion'] = 'La descripción es obligatoria';

    // Validación de ENUMs
    if (!in_array($prioridad, ['baja', 'media', 'alta'])) $errors['prioridad'] = 'Prioridad inválida';
    if (!in_array($estado, ['abierta', 'cerrada'])) $errors['estado'] = 'Estado inválido';

    // Se guarda en la base de datos si no hay errores
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO tickets (titulo, descripcion, prioridad, estado) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $prioridad, $estado]);

        $id = (int)$pdo->lastInsertId();

        set_flash('success', 'Incidencia creada correctamente');
        // Redirección a la vista de la nueva incidencia
        header('Location: ../view.php?id=' . $id);
        exit;
    }
}
?>
<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Incidencia</title>
</head>
<body>
    <h1>Crear Nueva Incidencia</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="create.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">

        <div>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($old['titulo']); ?>" required>
        </div>

        <div>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($old['descripcion']); ?></textarea>
        </div>

        <div>
            <label for="prioridad">Prioridad:</label>
            <select id="prioridad" name="prioridad">
                <option value="baja" <?php echo $old['prioridad'] === 'baja' ? 'selected' : ''; ?>>Baja</option>
                <option value="media" <?php echo $old['prioridad'] === 'media' ? 'selected' : ''; ?>>Media</option>
                <option value="alta" <?php echo $old['prioridad'] === 'alta' ? 'selected' : ''; ?>>Alta</option>
            </select>
        </div>

        <div>
            <label for="estado">Estado:</label>
            <select id="estado" name="estado">
                <option value="abierta" <?php echo $old['estado'] === 'abierta' ? 'selected' : ''; ?>>Abierta</option>
                <option value="cerrada" <?php echo $old['estado'] === 'cerrada' ? 'selected' : ''; ?>>Cerrada</option>
            </select>
        </div>

        <button type="submit">Crear Incidencia</button>
    </form>

</body>
</html>