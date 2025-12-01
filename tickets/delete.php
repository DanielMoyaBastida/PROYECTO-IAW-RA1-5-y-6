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
    header('Location: ../list.php');
    exit;
}


try {
    $pdo->beginTransaction();

    // obtenemos los datos del tiket para almacenarlos en auditoria
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? FOR UPDATE");
    $stmt->execute([$id]);
    $tickets = $stmt->fetch();
    if (!$tickets) {
        $pdo->rollBack();
        set_flash('error','Incidencia no encontrada');
        header('Location: ../list.php');
        exit;
    }

    //eliminamos la incidencia de la base de datos
    $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->execute([$id]);

    // insertamos el registro de la tabla auditoria y guardamos algunos datos importantes antes de borrarlos
    $stmt = $pdo->prepare("INSERT INTO registros_auditoria (accion, entidad, id_entidad, id_usuario, detalles) VALUES (?, ?, ?, ?, ?");
    $detalles = json_encode(['titulo'=>$tickets['titulo'],'prioridad'=>$tickets['prioridad'],'estado'=>$tickets['estado']]);
    //conseguimos el usuario que realizo la accion y ejecutamos el insert en la auditoria
    $usuario = current_user();
    $stmt->execute(['delete','tickets',$id, $usuario ? $usuario['id'] : null, $detalles]);

    //confirmamos los cambios
    $pdo->commit();
    set_flash('success','Incidencia borrada correctamente (auditoría registrada).');
    header('Location: ../tickets/list.php');
    exit;
} 
catch (Exception $ex) {
    //si hay algun error se revierte todo lo que hemos hecho
    if ($pdo->inTransaction()) $pdo->rollBack();
    //y se registra el error en el log
    error_log('Error borrado: ' . $ex->getMessage());
    set_flash('error','Error al borrar (se ha revertido).');
    header('Location: ../tickets/list.php');
    exit;
}