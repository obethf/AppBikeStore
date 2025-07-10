<?php
include_once "../../bd.php";
$sentencia = $conexion->prepare("SELECT * FROM order_items");
$sentencia->execute();
$items = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Listado de Detalles de Pedido</h2>
<a href="crear.php">Nuevo Detalle</a>
<table border="1">
    <tr>
        <th>ID</th><th>Pedido</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Descuento</th>
    </tr>
    <?php foreach($items as $item): ?>
        <tr>
            <td><?= $item['order_item_id'] ?></td>
            <td><?= $item['order_id'] ?></td>
            <td><?= $item['product_id'] ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= $item['price'] ?></td>
            <td><?= $item['discount'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
