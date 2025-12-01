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
        header('Location: ../tickets/create.php');
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