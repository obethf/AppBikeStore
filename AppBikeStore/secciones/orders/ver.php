<?php
require_once '../../bd.php';
checkLogin();

$order_id = $_GET['id'] ?? 0;

// Obtener información del pedido
$sql = "SELECT o.*, CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
               c.phone, c.email, c.street, c.city, c.state
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.order_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$order_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener items del pedido
$sql = "SELECT oi.*, p.product_name, p.foto 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totales
$subtotal = 0;
$total_descuento = 0;
$total = 0;

foreach ($items as $item) {
    $subtotal_item = $item['price'] * $item['quantity'];
    $descuento_item = $subtotal_item * ($item['discount'] / 100);
    $total_item = $subtotal_item - $descuento_item;

    $subtotal += $subtotal_item;
    $total_descuento += $descuento_item;
    $total += $total_item;
}
?>

<?php include '../../templates/header.php'; ?>

<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">Detalles del Pedido #<?= $pedido['order_id'] ?></h2>
                <div>
                    <a href="index.php" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                    <button onclick="imprimirPDF()" class="btn btn-light btn-sm ms-2">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body" id="pedido-detalles">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Información del Cliente</h5>
                    <p><strong>Nombre:</strong> <?= htmlspecialchars($pedido['customer_name']) ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['phone']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($pedido['email']) ?></p>
                    <p><strong>Dirección:</strong> <?= htmlspecialchars($pedido['street']) ?>,
                        <?= htmlspecialchars($pedido['city']) ?>, <?= htmlspecialchars($pedido['state']) ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5>Información del Pedido</h5>
                    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($pedido['order_date'])) ?></p>
                    <p><strong>Estado:</strong>
                        <?php
                        $badgeClass = [
                            'pendiente' => 'bg-warning',
                            'completado' => 'bg-success',
                            'anulado' => 'bg-danger'
                        ][$pedido['estado']] ?? 'bg-secondary';
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= ucfirst($pedido['estado']) ?>
                        </span>
                    </p>
                </div>
            </div>

            <h5 class="mb-3">Productos</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item):
                            $subtotal_item = $item['price'] * $item['quantity'];
                            $descuento_item = $subtotal_item * ($item['discount'] / 100);
                            $total_item = $subtotal_item - $descuento_item;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($item['foto'])): ?>
                                            <img src="<?= $url_base ?>secciones/products/imagenes/<?= $item['foto'] ?>"
                                                class="rounded me-3" width="60" height="60" style="object-fit: cover;">
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td><?= number_format($item['price'], 2) ?> Bs.</td>
                                <td><?= $item['quantity'] ?></td>
                                <td>
                                    <?= number_format($item['discount'], 2) ?>%
                                    (-<?= number_format($descuento_item, 2) ?> Bs.)
                                </td>
                                <td><?= number_format($total_item, 2) ?> Bs.</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Subtotal:</th>
                            <th><?= number_format($subtotal, 2) ?> Bs.</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Descuento Total:</th>
                            <th>-<?= number_format($total_descuento, 2) ?> Bs.</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th><?= number_format($total, 2) ?> Bs.</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Librerías para generar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
async function imprimirPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const content = document.getElementById("pedido-detalles");

    await html2canvas(content).then((canvas) => {
        const imgData = canvas.toDataURL("image/png");
        const imgProps = doc.getImageProperties(imgData);
        const pdfWidth = doc.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        doc.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
        doc.save("Pedido_<?= $pedido['order_id'] ?>.pdf");
    });
}
</script>

<?php include '../../templates/footer.php'; ?>
