<?php
require_once("../../bd.php");
require_once("../../templates/header.php");
checkLogin();

// Obtener datos del producto
$id = isset($_GET['id']) ? $_GET['id'] : null;
$producto = null;

if ($id) {
    try {
        $sentencia = $conexion->prepare("SELECT * FROM products WHERE product_id = ?");
        $sentencia->execute([$id]);
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error al obtener el producto: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }
}

// Procesar el formulario
if ($_POST) {
    $nombre = $_POST['product_name'];
    $modelo = $_POST['model_year'];
    $precio = $_POST['price'];
    $foto_actual = $_POST['foto_actual'];

    // Manejo de imagen
    $foto = $foto_actual;
    if ($_FILES['foto']['name'] != '') {
        // Eliminar imagen anterior si existe
        if ($foto_actual && file_exists(__DIR__ . "/imagenes/" . $foto_actual)) {
            unlink(__DIR__ . "/imagenes/" . $foto_actual);
        }

        // Subir nueva imagen
        $nombreArchivo = uniqid() . "_" . str_replace(' ', '_', $_FILES['foto']['name']);
        $tmpFoto = $_FILES['foto']['tmp_name'];
        move_uploaded_file($tmpFoto, __DIR__ . "/imagenes/" . $nombreArchivo);
        $foto = $nombreArchivo;
    }

    try {
        $sentencia = $conexion->prepare("UPDATE products SET product_name = ?, foto = ?, model_year = ?, price = ? WHERE product_id = ?");
        $sentencia->execute([$nombre, $foto, $modelo, $precio, $id]);

        $_SESSION['message'] = "Producto actualizado correctamente";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error al actualizar: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <?php include("../../templates/header.php"); ?>
</head>

<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Editar Producto</h4>
                    </div>

                    <div class="card-body">
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show">
                                <?= $_SESSION['message'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['message']);
                            unset($_SESSION['message_type']); ?>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="foto_actual" value="<?= $producto['foto'] ?? '' ?>">

                            <div class="mb-3">
                                <label for="product_name" class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" id="product_name" name="product_name"
                                    value="<?= htmlspecialchars($producto['product_name'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="model_year" class="form-label">Año del Modelo</label>
                                <input type="number" class="form-control" id="model_year" name="model_year"
                                    value="<?= $producto['model_year'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Precio (Bs.)</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price"
                                    value="<?= $producto['price'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="foto" class="form-label">Imagen del Producto</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">

                                <?php if (!empty($producto['foto'])): ?>
                                    <div class="mt-2">
                                        <p class="mb-1">Imagen actual:</p>
                                        <img src="<?= $url_base ?>secciones/products/imagenes/<?= $producto['foto'] ?>"
                                            class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("../../templates/footer.php"); ?>
</body>

</html>