<?php
require_once("../../bd.php");
require_once("../../templates/header.php");
checkLogin();

// Verificar si se recibió el ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 1. Obtener información del producto para eliminar su imagen
        $sentencia = $conexion->prepare("SELECT foto FROM products WHERE product_id = ?");
        $sentencia->execute([$id]);
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);

        // 2. Eliminar el producto de la base de datos
        $sentencia = $conexion->prepare("DELETE FROM products WHERE product_id = ?");
        $resultado = $sentencia->execute([$id]);

        if ($resultado && $sentencia->rowCount() > 0) {
            // 3. Si se eliminó de la BD, borrar también la imagen asociada si existe
            if ($producto && !empty($producto['foto'])) {
                $ruta_imagen = __DIR__ . "/imagenes/" . $producto['foto'];
                if (file_exists($ruta_imagen) && is_file($ruta_imagen)) {
                    unlink($ruta_imagen);
                }
            }

            $_SESSION['message'] = "Producto eliminado correctamente";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: El producto no existe o no pudo ser eliminado";
            $_SESSION['message_type'] = "danger";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error en la base de datos: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "ID de producto no proporcionado";
    $_SESSION['message_type'] = "danger";
}

header("Location: index.php");
exit();
?>