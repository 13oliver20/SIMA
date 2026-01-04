<?php
require_once(__DIR__ . "/../../includes/conexion.php");
// Verifica que los campos obligatorios están presentes
if (!isset($_POST['idsocio']) || !isset($_POST['dni']) || !isset($_POST['nombre']) || !isset($_POST['apellido_pat'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
    exit;
}

// Recupera los datos del formulario
$idsocio = $_POST['idsocio'];
$dni = $_POST['dni'];
$nombre = $_POST['nombre'];
$apellido_pat = $_POST['apellido_pat'];
$apellido_mat = $_POST['apellido_mat'];
$genero = $_POST['genero'];
$departamento = $_POST['departamento'];
$provincia = $_POST['provincia'];
$distrito = $_POST['distrito'];

// Realiza la actualización en la base de datos
// Asegúrate de que aquí esté el código para actualizar los datos del socio
// Ejemplo de consulta (esto debe ser ajustado a tu estructura de base de datos)
$query = "UPDATE socio SET dni = ?, nombre = ?, apellido_pat = ?, apellido_mat = ?, genero = ?, departamento = ?, provincia = ?, distrito = ? WHERE idsocio = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ssssssssi', $dni, $nombre, $apellido_pat, $apellido_mat, $genero, $departamento, $provincia, $distrito, $idsocio);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Socio actualizado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Hubo un error al actualizar el socio.']);
}
?>
