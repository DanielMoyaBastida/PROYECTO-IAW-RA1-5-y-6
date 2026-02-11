<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/pdo.php';
require_login();

$id = $_GET['id'] ?? null;
if (!$id) die('ID no válido');

try {
    $pdo->beginTransaction();

    // Auditoría
    $pdo->prepare("INSERT INTO auditoria (accion,item_id) VALUES ('BORRAR',?)")
        ->execute([$id]);

    // Borrar
    $pdo->prepare("DELETE FROM items WHERE id=?")
        ->execute([$id]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die('Rollback ejecutado');
}

header('Location: items_list.php');
exit;