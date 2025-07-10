<?php
require_once '../../bd.php';
checkLogin();

$clientes = [];
$productos = [];
$error = '';

try {
    // Obtener clientes para el select
    $stmt = $conexion->query("SELECT customer_id, CONCAT(first_name, ' ', last_name) AS nombre FROM customers");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener productos para el select
    $stmt = $conexion->query("SELECT product_id, product_name, price FROM products");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al cargar datos: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conexion->beginTransaction();

        // 1. Insertar el pedido principal
        $sqlPedido = "INSERT INTO orders (customer_id, order_date, usuario_id) VALUES (:cliente, :fecha, :usuario_id)";
        $stmtPedido = $conexion->prepare($sqlPedido);
        $stmtPedido->execute([
            ':cliente' => $_POST['cliente'],
            ':fecha' => date('Y-m-d'),  // <-- Aquí uso la fecha actual automática
            ':usuario_id' => $_SESSION['usuario_id']
        ]);

        $order_id = $conexion->lastInsertId();

        // 2. Insertar los items del pedido
        $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price, discount) 
                    VALUES (:order_id, :product_id, :quantity, :price, :discount)";
        $stmtItem = $conexion->prepare($sqlItem);

        foreach ($_POST['productos'] as $producto) {
            if (!empty($producto['id']) && !empty($producto['cantidad'])) {
                // Obtener precio actual del producto
                $stmtPrecio = $conexion->prepare("SELECT price FROM products WHERE product_id = ?");
                $stmtPrecio->execute([$producto['id']]);
                $precio = $stmtPrecio->fetchColumn();

                $descuento = isset($producto['descuento']) ? floatval($producto['descuento']) : 0.00;

                $stmtItem->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $producto['id'],
                    ':quantity' => $producto['cantidad'],
                    ':price' => $precio,
                    ':discount' => $descuento
                ]);
            }
        }

        $conexion->commit();

        $_SESSION['message'] = "Pedido creado exitosamente con ID: $order_id";
        $_SESSION['messageType'] = 'success';
        header("Location: index.php");
        exit();

    } catch (PDOException $e) {
        $conexion->rollBack();
        $error = "Error al crear el pedido: " . $e->getMessage();
    }
}
?>

<?php include '../../templates/header.php'; ?>

<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">Nuevo Pedido</h2>
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
                                    <option value="<?= $cliente['customer_id'] ?>" <?= ($_POST['cliente'] ?? '') == $cliente['customer_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cliente['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Campo de fecha eliminado -->
                </div>

                <h5 class="mb-3">Productos *</h5>
                <div id="productos-container">
                    <?php if (isset($_POST['productos'])): ?>
                        <?php foreach ($_POST['productos'] as $index => $producto): ?>
                            <?php if (!empty($producto['id'])): ?>
                                <div class="row producto-item mb-3">
                                    <div class="col-md-4">
                                        <select class="form-select producto-select" name="productos[<?= $index ?>][id]" required>
                                            <option value="">Seleccionar producto...</option>
                                            <?php foreach ($productos as $prod): ?>
                                                <option value="<?= $prod['product_id'] ?>" data-precio="<?= $prod['price'] ?>" <?= $producto['id'] == $prod['product_id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($prod['product_name']) ?> -
                                                    <?= number_format($prod['price'], 2) ?> Bs.
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control cantidad" name="productos[<?= $index ?>][cantidad]" min="1" value="<?= $producto['cantidad'] ?? 1 ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control descuento" name="productos[<?= $index ?>][descuento]" min="0" max="100" step="0.01" value="<?= $producto['descuento'] ?? 0 ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control precio" readonly value="<?= isset($producto['id']) ? number_format($productos[array_search($producto['id'], array_column($productos, 'product_id'))]['price'] * ($producto['cantidad'] ?? 1) * (1 - ($producto['descuento'] ?? 0) / 100), 2) . ' Bs.' : '' ?>">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm quitar-producto">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="row producto-item mb-3">
                            <div class="col-md-4">
                                <select class="form-select producto-select" name="productos[0][id]" required>
                                    <option value="">Seleccionar producto...</option>
                                    <?php foreach ($productos as $producto): ?>
                                        <option value="<?= $producto['product_id'] ?>" data-precio="<?= $producto['price'] ?>">
                                            <?= htmlspecialchars($producto['product_name']) ?> -
                                            <?= number_format($producto['price'], 2) ?> Bs.
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control cantidad" name="productos[0][cantidad]" min="1" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control descuento" name="productos[0][descuento]" min="0" max="100" step="0.01" value="0">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control precio" readonly>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm quitar-producto">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
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
                                    <span id="subtotal">0.00 Bs.</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Descuento Total:</span>
                                    <span id="descuento-total">0.00 Bs.</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total:</span>
                                    <span id="total">0.00 Bs.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Guardar Pedido
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
        let contadorProductos = <?= isset($_POST['productos']) ? count($_POST['productos']) : 1 ?>;

        // Calcular total inicial
        calcularTotal();

        // Agregar nuevo producto
        document.getElementById('agregar-producto').addEventListener('click', function () {
            const nuevoProducto = document.querySelector('.producto-item').cloneNode(true);
            nuevoProducto.innerHTML = nuevoProducto.innerHTML.replace(/productos\[0\]/g, `productos[${contadorProductos}]`);
            nuevoProducto.querySelector('.cantidad').value = 1;
            nuevoProducto.querySelector('.descuento').value = 0;
            nuevoProducto.querySelector('.precio').value = '';
            document.getElementById('productos-container').appendChild(nuevoProducto);
            contadorProductos++;

            // Reiniciar selección
            const select = nuevoProducto.querySelector('.producto-select');
            select.selectedIndex = 0;
        });

        // Escuchar cambios en productos para recalcular total
        document.getElementById('productos-container').addEventListener('change', function (e) {
            if (
                e.target.classList.contains('producto-select') ||
                e.target.classList.contains('cantidad') ||
                e.target.classList.contains('descuento')
            ) {
                calcularTotal();
            }
        });

        // Quitar producto
        document.getElementById('productos-container').addEventListener('click', function (e) {
            if (e.target.closest('.quitar-producto')) {
                const productoItem = e.target.closest('.producto-item');
                productoItem.remove();
                calcularTotal();
            }
        });

        // Función para calcular totales
        function calcularTotal() {
            let subtotal = 0;
            let descuentoTotal = 0;

            document.querySelectorAll('.producto-item').forEach(function (item) {
                const select = item.querySelector('.producto-select');
                const cantidad = parseFloat(item.querySelector('.cantidad').value) || 0;
                const descuento = parseFloat(item.querySelector('.descuento').value) || 0;
                const precioUnitario = parseFloat(select.selectedOptions[0]?.dataset.precio) || 0;

                let precioSinDescuento = precioUnitario * cantidad;
                let descuentoMonto = precioSinDescuento * (descuento / 100);
                let precioConDescuento = precioSinDescuento - descuentoMonto;

                // Actualizar campo precio
                item.querySelector('.precio').value = precioConDescuento.toFixed(2) + " Bs.";

                subtotal += precioSinDescuento;
                descuentoTotal += descuentoMonto;
            });

            const total = subtotal - descuentoTotal;

            document.getElementById('subtotal').textContent = subtotal.toFixed(2) + " Bs.";
            document.getElementById('descuento-total').textContent = descuentoTotal.toFixed(2) + " Bs.";
            document.getElementById('total').textContent = total.toFixed(2) + " Bs.";
        }
    });
</script>

<?php include '../../templates/footer.php'; ?>
