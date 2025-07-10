<?php
require_once '../../bd.php';
checkLogin();

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "ID de pedido no especificado.";
    exit;
}

// Consultar el pedido con detalles de cliente
$sql = "SELECT o.order_id, o.order_date, o.estado,
               CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
               c.phone, c.email
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.order_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$order_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    echo "Pedido no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Pedido #<?= $pedido['order_id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <div class="text-center mb-4">
            <h2>Detalle del Pedido #<?= $pedido['order_id'] ?></h2>
            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($pedido['order_date'])) ?></p>
        </div>

        <table class="table table-bordered">
            <tr>
                <th>Cliente</th>
                <td><?= htmlspecialchars($pedido['customer_name']) ?></td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td><?= htmlspecialchars($pedido['phone']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($pedido['email']) ?></td>
            </tr>
            <tr>
                <th>Estado</th>
                <td><?= ucfirst($pedido['estado']) ?></td>
            </tr>
        </table>

        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            <a href="index.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</body>
</html>
