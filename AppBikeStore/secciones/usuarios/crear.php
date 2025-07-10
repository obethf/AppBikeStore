<?php
require_once '../../bd.php';
checkLogin();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $role = $_POST['role'] ?? 'Usuario';

    // Validaciones básicas
    if (empty($usuario) || empty($clave) || empty($correo)) {
        $message = 'Todos los campos son obligatorios';
        $messageType = 'danger';
    } else {
        try {
            // Verificar si el usuario ya existe
            $sqlCheck = "SELECT COUNT(*) FROM usuario WHERE usuario = ?";
            $stmtCheck = $conexion->prepare($sqlCheck);
            $stmtCheck->execute([$usuario]);

            if ($stmtCheck->fetchColumn() > 0) {
                $message = 'El nombre de usuario ya está en uso';
                $messageType = 'danger';
            } else {
                // Hash de la contraseña (mejoraría con password_hash())
                $claveHash = md5($clave); // Nota: md5 no es seguro, es solo para ejemplo

                $sql = "INSERT INTO usuario (usuario, clave, correo, role) VALUES (?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$usuario, $claveHash, $correo, $role]);

                $message = 'Usuario creado exitosamente';
                $messageType = 'success';

                // Redirigir después de 2 segundos
                header("Refresh: 2; URL=index.php?message=" . urlencode($message) . "&messageType=" . $messageType);
            }
        } catch (PDOException $ex) {
            $message = 'Error al crear el usuario: ' . $ex->getMessage();
            $messageType = 'danger';
        }
    }
}
?>

<?php include '../../templates/header.php'; ?>

<div class="container">
    <h1 class="my-4">Crear Nuevo Usuario</h1>

    <div class="mb-4">
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="usuario" class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>

        <div class="mb-3">
            <label for="clave" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="clave" name="clave" required>
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Rol</label>
            <select class="form-select" id="role" name="role">
                <option value="Usuario">Usuario</option>
                <option value="Administrador">Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Crear Usuario</button>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>