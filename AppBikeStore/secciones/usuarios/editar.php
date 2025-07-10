<?php
require_once '../../bd.php';
checkLogin();

$message = '';
$messageType = '';

// Obtener datos del usuario a editar
$id = $_GET['id'] ?? 0;
$usuario = null;

try {
    $sql = "SELECT * FROM usuario WHERE usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header("Location: index.php?message=Usuario no encontrado&messageType=danger");
        exit();
    }
} catch (PDOException $ex) {
    $message = 'Error al obtener el usuario: ' . $ex->getMessage();
    $messageType = 'danger';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioData = $_POST['usuario'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $role = $_POST['role'] ?? 'Usuario';
    $clave = $_POST['clave'] ?? '';

    try {
        if (!empty($clave)) {
            // Actualizar con nueva contraseña
            $claveHash = md5($clave); // Nota: md5 no es seguro, es solo para ejemplo
            $sql = "UPDATE usuario SET usuario = ?, clave = ?, correo = ?, role = ? WHERE usuario_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$usuarioData, $claveHash, $correo, $role, $id]);
        } else {
            // Actualizar sin cambiar contraseña
            $sql = "UPDATE usuario SET usuario = ?, correo = ?, role = ? WHERE usuario_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$usuarioData, $correo, $role, $id]);
        }

        $message = 'Usuario actualizado exitosamente';
        $messageType = 'success';

        // Redirigir después de 2 segundos
        header("Refresh: 2; URL=index.php?message=" . urlencode($message) . "&messageType=" . $messageType);
    } catch (PDOException $ex) {
        $message = 'Error al actualizar el usuario: ' . $ex->getMessage();
        $messageType = 'danger';
    }
}
?>

<?php include '../../templates/header.php'; ?>

<div class="container">
    <h1 class="my-4">Editar Usuario</h1>

    <div class="mb-4">
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($usuario): ?>
        <form method="post">
            <input type="hidden" name="id" value="<?= $usuario['usuario_id'] ?>">

            <div class="mb-3">
                <label for="usuario" class="form-label">Nombre de Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario"
                    value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="clave" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" class="form-control" id="clave" name="clave">
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo"
                    value="<?= htmlspecialchars($usuario['correo']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Rol</label>
                <select class="form-select" id="role" name="role">
                    <option value="Usuario" <?= $usuario['role'] === 'Usuario' ? 'selected' : '' ?>>Usuario</option>
                    <option value="Administrador" <?= $usuario['role'] === 'Administrador' ? 'selected' : '' ?>>Administrador
                    </option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        </form>
    <?php endif; ?>
</div>

<?php include '../../templates/footer.php'; ?>