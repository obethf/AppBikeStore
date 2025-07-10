<?php
//La función getOrders sirve para obtener una lista de todos los pedidos realizados, 
// incluyendo el nombre del cliente que hizo cada pedido, 
// ordenados del más reciente al más antiguo.
function getOrders($conexion)
{
    $sql = "SELECT o.*, CONCAT(c.first_name, ' ', c.last_name) AS customer_name 
            FROM orders o 
            JOIN customers c ON o.customer_id = c.customer_id
            ORDER BY o.order_date DESC";
    return $conexion->query($sql);
}
//La función getOrderDetails sirve para obtener los detalles de un pedido específico, 
// incluyendo el nombre completo del cliente que lo hizo.
function getOrderDetails($conexion, $order_id)
{
    $sql = "SELECT o.*, CONCAT(c.first_name, ' ', c.last_name) AS customer_name 
            FROM orders o 
            JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.order_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
//La función getOrderItems sirve para obtener los productos 
// de un pedido específico, incluyendo el nombre de cada producto.
function getOrderItems($conexion, $order_id)
{
    $sql = "SELECT oi.*, p.product_name 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//Cancela (anula) un pedido en la base de datos cambiando su estado a 'anulado'.
function cancelOrder($conexion, $order_id)
{
    $sql = "UPDATE orders SET estado = 'anulado' WHERE order_id = ?";
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([$order_id]);
}
?>