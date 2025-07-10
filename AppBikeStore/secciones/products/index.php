<?php
require_once '../../bd.php';
require_once '../../templates/header.php';
checkLogin();

// Mostrar mensajes
if (isset($_SESSION['message'])) {
    showAlert($_SESSION['message_type'], $_SESSION['message']);
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

try {
    $stmt = $conexion->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    showAlert('danger', "Error: " . $e->getMessage());
}
?>

<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">Gestión de Productos</h2>
                <a href="crear.php" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
                </a>
            </div>
        </div>

        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    No hay productos registrados. Comienza agregando uno nuevo.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="25%">Nombre</th>
                                <th width="15%">Imagen</th>
                                <th width="10%">Modelo</th>
                                <th width="15%">Precio</th>
                                <th width="20%" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="fw-bold"><?= $product['product_id'] ?></td>
                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td>
                                        <?php if (!empty($product['foto'])): ?>
                                            <img src="<?= $url_base ?>secciones/products/imagenes/<?= $product['foto'] ?>"
                                                class="img-thumbnail rounded"
                                                alt="<?= htmlspecialchars($product['product_name']) ?>"
                                                style="width: 80px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Sin imagen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $product['model_year'] ?></td>
                                    <td class="fw-bold text-success"><?= number_format($product['price'], 2) ?> Bs.</td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="editar.php?id=<?= $product['product_id'] ?>"
                                                class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="eliminar.php?id=<?= $product['product_id'] ?>"
                                                class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer bg-light">
            <small class="text-muted">Mostrando <?= count($products) ?> productos</small>
        </div>
    </div>
</div>

<!-- Activar tooltips de Bootstrap -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<!-- Incluir iconos de Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<?php require_once '../../templates/footer.php'; ?>
