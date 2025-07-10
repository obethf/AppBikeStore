<?php
require_once '../../bd.php';
checkLogin();

// Verificar si se recibió un ID de pedido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['id'];
$error = '';
$success = '';

try {
    // Obtener información del pedido
    $sqlPedido = "SELECT o.*, CONCAT(c.first_name, ' ', c.last_name) AS customer_name 
                 FROM orders o 
                 JOIN customers c ON o.customer_id = c.customer_id 
                 WHERE o.order_id = ?";
    $stmtPedido = $conexion->prepare($sqlPedido);
    $stmtPedido->execute([$order_id]);
    $pedido = $stmtPedido->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        header("Location: index.php");
        exit();
    }

    // Obtener items del pedido
    $sqlItems = "SELECT oi.*, p.product_name, p.price 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.product_id 
                WHERE oi.order_id = ?";
    $stmtItems = $conexion->prepare($sqlItems);
    $stmtItems->execute([$order_id]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    // Obtener clientes para el select
    $sqlClientes = "SELECT customer_id, CONCAT(first_name, ' ', last_name) AS nombre FROM customers";
    $clientes = $conexion->query($sqlClientes)->fetchAll(PDO::FETCH_ASSOC);

    // Obtener productos para el select
    $sqlProductos = "SELECT product_id, product_name, price FROM products";
    $productos = $conexion->query($sqlProductos)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error al cargar datos: " . $e->getMessage();
}

// Procesar actualización del pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conexion->beginTransaction();

        // 1. Actualizar información básica del pedido
        $sqlUpdatePedido = "UPDATE orders SET customer_id = ?, order_date = ? WHERE order_id = ?";
        $stmtUpdatePedido = $conexion->prepare($sqlUpdatePedido);
        $stmtUpdatePedido->execute([
            $_POST['cliente'],
            $_POST['fecha'],
            $order_id
        ]);

        // 2. Eliminar items antiguos
        $sqlDeleteItems = "DELETE FROM order_items WHERE order_id = ?";
        $stmtDeleteItems = $conexion->prepare($sqlDeleteItems);
        $stmtDeleteItems->execute([$order_id]);

        // 3. Insertar nuevos items
        $sqlInsertItem = "INSERT INTO order_items (order_id, product_id, quantity, price, discount) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmtInsertItem = $conexion->prepare($sqlInsertItem);

        foreach ($_POST['productos'] as $producto) {
            if (!empty($producto['id']) && !empty($producto['cantidad'])) {
                // Obtener precio actual del producto
                $stmtPrecio = $conexion->prepare("SELECT price FROM products WHERE product_id = ?");
                $stmtPrecio->execute([$producto['id']]);
                $precio = $stmtPrecio->fetchColumn();

                $stmtInsertItem->execute([
                    $order_id,
                    $producto['id'],
                    $producto['cantidad'],
                    $precio,
                    0.00 // Descuento (puedes modificarlo si es necesario)
                ]);
            }
        }

        $conexion->commit();

        $_SESSION['message'] = "Pedido #$order_id actualizado exitosamente";
        $_SESSION['messageType'] = 'success';
        header("Location: index.php");
        exit();

    } catch (PDOException $e) {
        $conexion->rollBack();
        $error = "Error al actualizar el pedido: " . $e->getMessage();
    }
}
?>

<?php include '../../templates/header.php'; ?>

<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">Editar Pedido #<?= $order_id ?></h2>
                <a href="index.php" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cliente" class="form-label">Cliente *</label>
                            <select class="form-select" id="cliente" name="cliente" required>
                                <option value="">Seleccionar cliente...</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['customer_id'] ?>"
                                        <?= ($pedido['customer_id'] == $cliente['customer_id'] || ($_POST['cliente'] ?? '') == $cliente['customer_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cliente['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha del Pedido *</label>
                            <input type="date" class="form-control" id="fecha" name="fecha"
                                value="<?= htmlspecialchars($_POST['fecha'] ?? $pedido['order_date']) ?>" required>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3">Productos *</h5>
                <div id="productos-container">
                    <?php
                    $productosMostrar = isset($_POST['productos']) ? $_POST['productos'] : $items;
                    foreach ($productosMostrar as $index => $producto):
                        ?>
                        <div class="row producto-item mb-3">
                            <div class="col-md-5">
                                <select class="form-select producto-select" name="productos[<?= $index ?>][id]" required>
                                    <option value="">Seleccionar producto...</option>
                                    <?php foreach ($productos as $prod): ?>
                                        <option value="<?= $prod['product_id'] ?>" data-precio="<?= $prod['price'] ?>"
                                            <?= ($producto['product_id'] ?? $producto['id'] ?? '') == $prod['product_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prod['product_name']) ?> -
                                            <?= number_format($prod['price'], 2) ?> Bs.
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control cantidad" name="productos[<?= $index ?>][cantidad]"
                                    min="1" value="<?= $producto['quantity'] ?? $producto['cantidad'] ?? 1 ?>" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control precio" readonly value="<?=
                                    isset($producto['product_id']) ? number_format($producto['price'] * $producto['quantity'], 2) . ' Bs.' :
                                    (isset($producto['id']) ? number_format($productos[array_search($producto['id'], array_column($productos, 'product_id'))]['price'] * ($producto['cantidad'] ?? 1), 2) . ' Bs.' : '')
                                    ?>">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm quitar-producto">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mb-4">
                    <button type="button" id="agregar-producto" class="btn btn-secondary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Agregar Producto
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Resumen del Pedido</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal"><?= number_format(array_sum(array_map(function ($item) {
                                        return $item['price'] * $item['quantity'];
                                    }, $items)), 2) ?> Bs.</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Descuento:</span>
                                    <span id="descuento">0.00 Bs.</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total:</span>
                                    <span id="total"><?= number_format(array_sum(array_map(function ($item) {
                                        return $item['price'] * $item['quantity'];
                                    }, $items)), 2) ?> Bs.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Actualizar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Incluir iconos de Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<!-- Script para manejo dinámico de productos -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Contador para nuevos productos
        let contadorProductos = <?= count(isset($_POST['productos']) ? $_POST['productos'] : $items) ?>;

        // Calcular total inicial
        calcularTotal();

        // Agregar nuevo producto
        document.getElementById('agregar-producto').addEventListener('click', function () {
            const nuevoProducto = document.querySelector('.producto-item').cloneNode(true);
            nuevoProducto.innerHTML = nuevoProducto.innerHTML.replace(/productos\[0\]/g, `productos[${contadorProductos}]`);
            nuevoProducto.querySelector('.cantidad').value = 1;
            nuevoProducto.querySelector('.precio').value = '';
            document.getElementById('productos-container').appendChild(nuevoProducto);
            contadorProductos++;

            // Reiniciar selección
            const select = nuevoProducto.querySelector('.producto-select');
            select.selectedIndex = 0;
        });

        // Quitar producto
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('quitar-producto') || e.target.closest('.quitar-producto')) {
                if (document.querySelectorAll('.producto-item').length > 1) {
                    const item = e.target.closest('.producto-item');
                    item.remove();
                    calcularTotal();
                }
            }
        });

        // Calcular precios cuando cambia selección o cantidad
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('producto-select') || e.target.classList.contains('cantidad')) {
                const item = e.target.closest('.producto-item');
                const select = item.querySelector('.producto-select');
                const cantidad = item.querySelector('.cantidad');
                const precio = item.querySelector('.precio');

                if (select.value) {
                    const precioUnitario = parseFloat(select.selectedOptions[0].dataset.precio);
                    const total = precioUnitario * parseInt(cantidad.value);
                    precio.value = total.toFixed(2) + ' Bs.';
                } else {
                    precio.value = '';
                }

                calcularTotal();
            }
        });

        // Función para calcular totales
        function calcularTotal() {
            let subtotal = 0;

            document.querySelectorAll('.producto-item').forEach(item => {
                const precio = item.querySelector('.precio').value;
                if (precio) {
                    subtotal += parseFloat(precio.replace(' Bs.', ''));
                }
            });

            document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' Bs.';
            document.getElementById('total').textContent = subtotal.toFixed(2) + ' Bs.';
        }
    });
</script>

<?php include '../../templates/footer.php'; ?>