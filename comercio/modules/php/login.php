<?php
$host = "localhost";
$usuario = "root"; // Cambia esto
$password = ""; // Cambia esto
$bd = "data_asociaciones";

// Conexión
$conn = new mysqli($host, $usuario, $password, $bd);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$email = $_POST['email'];
$contraseña = $_POST['contraseña'];

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($contraseña, $usuario['contraseña'])) {
        echo "¡Inicio de sesión exitoso!";
        // Aquí puedes redirigir al usuario o guardar sesión
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Correo no registrado.";
}

$conn->close();
