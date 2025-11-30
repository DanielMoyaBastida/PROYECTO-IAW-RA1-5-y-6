<?php
//añadimos las funciones necesarioas desde otro archivo
require_once __DIR__ . '/../src/functions.php';
//verificamos el usuario
require_login();
//obtenemos la conexion a la base de datos
$pdo = getPDO();

//recogemos el ID de la incidencia desde la URL y validamos que la ID sea valida
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('error','ID inválido');
    header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/list.php' . 'tickets/list.php');
    exit;
}


try {
    $pdo->beginTransaction();

    // obtenemos los datos del tiket para almacenarlos en auditoria
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
        $pdo->rollBack();
        set_flash('error','Incidencia no encontrada');
        header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/list.php' . 'tickets/list.php');
        exit;
    }

    //eliminamos la incidencia de la base de datos
    $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->execute([$id]);

    // insertamos el registro de la tabla auditoria y guardamos algunos datos importantes antes de borrarlos
    $stmt = $pdo->prepare("INSERT INTO audit_logs (action, entity, entity_id, user_id, details, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $details = json_encode(['title'=>$ticket['title'],'priority'=>$ticket['priority'],'status'=>$ticket['status']]);
    //conseguimos el usuario que realizo la accion y ejecutamos el insert en la auditoria
    $user = current_user();
    $stmt->execute(['delete','ticket',$id, $user ? $user['id'] : null, $details]);

    //confirmamos los cambios
    $pdo->commit();
    set_flash('success','Incidencia borrada correctamente (auditoría registrada).');
    header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/list.php' . 'tickets/list.php');
    exit;
} 
catch (Exception $ex) {
    //si hay algun error se revierte todo lo que hemos hecho
    if ($pdo->inTransaction()) $pdo->rollBack();
    //y se registra el error en el log
    error_log('Error borrado: ' . $ex->getMessage());
    set_flash('error','Error al borrar (se ha revertido).');
    header('Location: ' . 'https://github.com/DanielMoyaBastida/PROYECTO-IAW-RA1-5-y-6/blob/main/tickets/list.php' . 'tickets/list.php');
    exit;
}