<?php
$servidor = "localhost";
$basededatos = "bike";
$usuario = "root";
$contrasenia = ""; // Sigue siendo cadena vacía, ya que tu phpMyAdmin confirma que no hay contraseña
try {
    $conexion = new PDO(
        "mysql:host=$servidor;port=3306;dbname=$basededatos", // <-- Agrega 'port=3305;'
        $usuario,
        $contrasenia
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Recomendado para depuración
} catch (PDOException $ex) {
    echo "Error de conexión a la base de datos: " . $ex->getMessage();
    exit(); // Detener la ejecución si hay un error de conexión
}

function checkLogin()
{
    session_start();
    if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
        header("Location: http://localhost/AppBikeStore/index.php");
        exit();
    }
}


// Función para mostrar mensajes de éxito/error
function showAlert($type, $message)
{
    if (!empty($message)) {
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show mt-3" role="alert">';
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}
?>