<?php
//Es la parte del backend que complementa la función JavaScript
require_once '../../bd.php';
checkLogin();

$order_id = $_GET['id'] ?? 0;

try {
    $sql = "UPDATE orders SET estado = 'anulado' WHERE order_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$order_id]);

    $_SESSION['message'] = "Pedido #$order_id ha sido anulado correctamente";
    $_SESSION['messageType'] = 'success';
} catch (PDOException $e) {
    $_SESSION['message'] = "Error al anular el pedido: " . $e->getMessage();
    $_SESSION['messageType'] = 'danger';
}

header("Location: index.php");
exit();
?>