<?php
require_once '../../bd.php';
checkLogin();

$sql = "SELECT * FROM usuario";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = $_GET['message'] ?? '';
$messageType = $_GET['messageType'] ?? '';
?>

<?php include '../../templates/header.php'; ?>

<!-- Estilos personalizados modernos -->
<style>
    .custom-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .custom-header {
        background-color: #b2f2bb;
        color: #155724;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #a5d6a7;
    }

    .custom-btn {
        background-color: #81c784;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease;
    }

    .custom-btn:hover {
        background-color: #66bb6a;
        color: #fff;
    }

    .custom-badge {
        background-color: #66bb6a;
        color: white;
        padding: 0.25em 0.75em;
        border-radius: 1em;
        font-size: 0.9em;
    }

    .table-custom thead {
        background-color: #e8f5e9;
    }

    .btn-group .btn-outline {
        border: 1px solid #81c784;
        color: #388e3c;
        background-color: white;
        border-radius: 0.5rem;
    }

    .btn-group .btn-outline:hover {
        background-color: #c8e6c9;
    }
</style>

<div class="container py-4">
    <div class="card custom-card">
        <div class="custom-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Gestión de Usuarios</h2>
            <div>
                <a href="../../index.php" class="custom-btn me-2">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <a href="crear.php" class="custom-btn">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Usuario
                </a>
            </div>
        </div>

        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($usuarios)): ?>
                <div class="alert alert-info">
                    No hay usuarios registrados. Comienza agregando uno nuevo.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="25%">Usuario</th>
                                <th width="30%">Correo</th>
                                <th width="15%">Rol</th>
                                <th width="25%" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($usuario['usuario_id']) ?></td>
                                    <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                                    <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                    <td>
                                        <span class="custom-badge">
                                            <?= htmlspecialchars($usuario['role']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="editar.php?id=<?= $usuario['usuario_id'] ?>" class="btn btn-sm btn-outline" title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="eliminar.php?id=<?= $usuario['usuario_id'] ?>" class="btn btn-sm btn-outline" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
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

        <div class="card-footer bg-white border-top">
            <small class="text-muted">Mostrando <?= count($usuarios) ?> usuarios</small>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<?php include '../../templates/footer.php'; ?>
