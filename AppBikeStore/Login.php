<?php
// Inicia sesión
session_start();
if ($_POST) {
    include("./bd.php");

    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];

    // Preparamos la consulta para buscar solo por usuario
    $sentencia = $conexion->prepare("SELECT * FROM usuario WHERE usuario = :usuario");
    $sentencia->bindParam(":usuario", $usuario);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        // Verificamos la contraseña
        // Versión con md5 (si así lo tienes en la BD)
        if (md5($clave) === $registro["clave"]) {
            $_SESSION['usuario'] = $registro["usuario"];
            $_SESSION['logueado'] = true;
           $_SESSION['usuario_id'] = $registro["usuario_id"];
            $_SESSION['role'] = $registro["role"] ?? 'Usuario';
            header("Location: index.php");
          exit();
        }
        // Alternativa con password_verify (recomendado)
        // if (password_verify($clave, $registro["clave"])) {
        //     ... mismo código de sesión ...
        // }
    }

    // Si llegamos aquí es porque falló la autenticación
    $mensaje = "Error: El usuario o contraseña son incorrectos";
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Login - AppBikeStore</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-card {
            margin-top: 5rem;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card p-4 shadow">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4 fw-bold text-primary">Iniciar Sesión</h2>

                        <?php if (isset($mensaje)) { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><?php echo $mensaje ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php } ?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="usuario" class="form-label fw-semibold">Usuario:</label>
                                <input type="text" class="form-control form-control-lg" name="usuario" id="usuario"
                                    placeholder="Ingresa tu usuario" required autofocus>
                            </div>
                            <div class="mb-4">
                                <label for="clave" class="form-label fw-semibold">Contraseña:</label>
                                <input type="password" class="form-control form-control-lg" name="clave" id="clave"
                                    placeholder="Ingresa tu contraseña" required>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg py-2">Entrar</button>
                            </div>

                            <div class="text-center mt-3">
                                <a href="#" class="text-decoration-none text-secondary">¿Olvidaste tu contraseña?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>