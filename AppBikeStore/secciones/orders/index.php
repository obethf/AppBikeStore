<?php
require_once '../../bd.php';
checkLogin();

// Obtener todos los pedidos con información de cliente
$sql = "SELECT o.order_id, o.order_date, o.estado,
               CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
               c.phone, c.email, u.usuario AS created_by
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        JOIN usuario u ON o.usuario_id = u.usuario_id
        ORDER BY o.order_date DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensajes de éxito/error
$message = $_GET['message'] ?? '';
$messageType = $_GET['messageType'] ?? '';
?>

<?php include '../../templates/header.php'; ?>

<style>
    .bg-verde-claro {
        background-color: #a8e6cf !important;
        color: #155724 !important;
    }

    .btn-verde-claro {
        background-color: #a8e6cf;
        color: #155724;
        border: 1px solid #81c784;
    }

    .btn-verde-claro:hover {
        background-color: #81c784;
        color: white;
    }

    .badge-verde-claro {
        background-color: #81c784;
        color: white;
    }
</style>

<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-verde-claro">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">Gestión de Pedidos</h2>
                <div>
                    <a href="../../index.php" class="btn btn-verde-claro btn-sm me-2">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                    <a href="crear.php" class="btn btn-verde-claro btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Nuevo Pedido
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($pedidos)): ?>
                <div class="alert alert-info">
                    No hay pedidos registrados. Comienza creando uno nuevo.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <!-- <th>Fecha</th> Eliminado -->
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td class="fw-bold"><?= $pedido['order_id'] ?></td>
                                    <!-- <td><?= date('d/m/Y', strtotime($pedido['order_date'])) ?></td> -->
                                    <td><?= htmlspecialchars($pedido['customer_name']) ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($pedido['phone']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($pedido['email']) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = [
                                            'pendiente' => 'badge-verde-claro',
                                            'completado' => 'badge-verde-claro',
                                            'anulado' => 'bg-danger text-white'
                                        ][$pedido['estado']] ?? 'bg-secondary text-white';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= ucfirst($pedido['estado']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="ver.php?id=<?= $pedido['order_id'] ?>" class="btn btn-sm btn-outline-info"
                                                data-bs-toggle="tooltip" title="Ver Detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="editar.php?id=<?= $pedido['order_id'] ?>"
                                                class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Editar"
                                                <?= $pedido['estado'] !== 'pendiente' ? 'disabled' : '' ?>>
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <?php if ($pedido['estado'] === 'pendiente'): ?>
                                                <a href="anular.php?id=<?= $pedido['order_id'] ?>"
                                                    class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Anular">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                                <a href="eliminar.php?id=<?= $pedido['order_id'] ?>"
                                                    class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="imprimir.php?order_id=<?= $pedido['order_id'] ?>"
                                               class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Imprimir PDF" target="_blank">
                                                <i class="bi bi-printer"></i>
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

        <div class="card-footer bg-light text-center">
            <small class="text-muted">UpDS - Universidad Privada Domingo Savio</small>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<?php include '../../templates/footer.php'; ?>
