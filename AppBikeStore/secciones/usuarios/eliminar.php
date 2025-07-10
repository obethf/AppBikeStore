<?php
require_once '../../bd.php';
checkLogin();

$id = $_GET['id'] ?? 0;

try {
    // Verificar si el usuario a eliminar es el mismo que está logueado
    if ($_SESSION['usuario_id'] == $id) {
        header("Location: index.php?message=No puedes eliminarte a ti mismo&messageType=danger");
        exit();
    }

    $sql = "DELETE FROM usuario WHERE usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);

    $message = 'Usuario eliminado exitosamente';
    $messageType = 'success';
} catch (PDOException $ex) {
    $message = 'Error al eliminar el usuario: ' . $ex->getMessage();
    $messageType = 'danger';
}

header("Location: index.php?message=" . urlencode($message) . "&messageType=" . $messageType);
exit();