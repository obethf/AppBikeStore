<?php
require_once '../../bd.php';
checkLogin();

// Verificar que se haya proporcionado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID de pedido no válido";
    $_SESSION['messageType'] = 'danger';
    header("Location: index.php");
    exit();
}

$order_id = $_GET['id'];

try {
    // Primero verificar el estado del pedido
    $sqlCheck = "SELECT estado FROM orders WHERE order_id = :order_id";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->execute([':order_id' => $order_id]);
    $order = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $_SESSION['message'] = "Pedido no encontrado";
        $_SESSION['messageType'] = 'danger';
        header("Location: index.php");
        exit();
    }

    // Solo permitir eliminación si está pendiente
    if ($order['estado'] !== 'pendiente') {
        $_SESSION['message'] = "Solo se pueden eliminar pedidos en estado 'pendiente'";
        $_SESSION['messageType'] = 'danger';
        header("Location: index.php");
        exit();
    }

    $conexion->beginTransaction();

    // 1. Eliminar los items del pedido primero (por la restricción de clave foránea)
    $sqlDeleteItems = "DELETE FROM order_items WHERE order_id = :order_id";
    $stmtItems = $conexion->prepare($sqlDeleteItems);
    $stmtItems->execute([':order_id' => $order_id]);

    // 2. Eliminar el pedido
    $sqlDeleteOrder = "DELETE FROM orders WHERE order_id = :order_id";
    $stmtOrder = $conexion->prepare($sqlDeleteOrder);
    $stmtOrder->execute([':order_id' => $order_id]);

    $conexion->commit();

    $_SESSION['message'] = "Pedido eliminado correctamente";
    $_SESSION['messageType'] = 'success';

} catch (PDOException $e) {
    $conexion->rollBack();
    $_SESSION['message'] = "Error al eliminar el pedido: " . $e->getMessage();
    $_SESSION['messageType'] = 'danger';
}

header("Location: index.php");
exit();
?>