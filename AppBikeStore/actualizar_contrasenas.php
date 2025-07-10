<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bike";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT id, contrasena FROM usuarios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $hashed = password_hash($row['contrasena'], PASSWORD_DEFAULT);
        $id = $row['id'];

        // Actualizar la contraseña con hash
        $update = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
        $update->bind_param("si", $hashed, $id);
        $update->execute();

        echo "Contraseña del usuario con ID $id actualizada.<br>";
    }
    echo "<br>¡Todas las contraseñas fueron actualizadas correctamente!";
} else {
    echo "No hay usuarios para actualizar.";
}

$conn->close();
?>
