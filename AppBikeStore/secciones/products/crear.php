<?php
require_once("../../bd.php");


if ($_POST) {
    $nombre = $_POST['product_name'];
    $modelo = $_POST['model_year'];
    $precio = $_POST['price'];

    // Manejo de imagen
    $foto = null;
    if (isset($_FILES['foto']['name']) && $_FILES['foto']['name'] != '') {
        // Crear directorio si no existe
        $directorio_destino = __DIR__ . "/imagenes/";
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }

        // Generar nombre único para la imagen
        $nombreArchivo = uniqid() . "_" . str_replace(' ', '_', $_FILES['foto']['name']);
        $tmpFoto = $_FILES['foto']['tmp_name'];

        // Validar tipo de archivo
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

        if (in_array($extension, $extensionesPermitidas)) {
            if (move_uploaded_file($tmpFoto, $directorio_destino . $nombreArchivo)) {
                $foto = $nombreArchivo;
            } else {
                $_SESSION['message'] = "Error al subir la imagen";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $_SESSION['message'] = "Formato de imagen no permitido. Use JPG, JPEG, PNG o WEBP";
            $_SESSION['message_type'] = "danger";
        }
    }

    if (!isset($_SESSION['message'])) {
        $sentencia = $conexion->prepare("INSERT INTO products (product_name, foto, model_year, price) VALUES (?, ?, ?, ?)");
        if ($sentencia->execute([$nombre, $foto, $modelo, $precio])) {
            $_SESSION['message'] = "Producto creado correctamente";
            $_SESSION['message_type'] = "success";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['message'] = "Error al crear el producto";
            $_SESSION['message_type'] = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
    <?php include("../../templates/header.php"); ?>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-primary">Agregar Nuevo Producto</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']);
            unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="product_name" class="form-label fw-semibold">Nombre del producto:</label>
                        <input type="text" class="form-control" name="product_name" id="product_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="model_year" class="form-label fw-semibold">Año del modelo:</label>
                        <input type="number" class="form-control" name="model_year" id="model_year" required>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label fw-semibold">Precio (Bs.):</label>
                        <input type="number" class="form-control" step="0.01" name="price" id="price" required>
                    </div>

                    <div class="mb-3">
                        <label for="foto" class="form-label fw-semibold">Imagen:</label>
                        <input type="file" class="form-control" name="foto" id="foto"
                            accept="image/jpeg, image/png, image/webp">
                        <div class="form-text">Formatos permitidos: JPG, PNG, WEBP</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </div>

    <?php include("../../templates/footer.php"); ?>
</body>

</html>