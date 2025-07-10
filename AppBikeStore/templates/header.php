<?php
$url_base = "http://localhost/AppBikeStore/";
?>

<!doctype html>
<html lang="en">

<head>
    <title>App Bike Store</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?php echo $url_base; ?>css/styles.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand navbar-light bg-light">
            <div class="container-fluid">
                <ul class="nav navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $url_base; ?>" aria-current="page">Inicio<span
                                class="visually-hidden">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>secciones/customers/">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>secciones/products/">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>secciones/orders/">Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $url_base; ?>secciones/usuarios/">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <a class="nav-link" href="<?php echo $url_base; ?>cerrar.php">Cerrar Sesión</a>
                        <?php else: ?>
                            <a class="nav-link" href="<?php echo $url_base; ?>Login.php">Iniciar Sesión</a>
                        <?php endif; ?>
                    </li>
                </ul>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <span class="navbar-text ms-auto">
                        Usuario: <?php echo $_SESSION['usuario']; ?>
                    </span>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="container"></main>