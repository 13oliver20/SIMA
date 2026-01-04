<?php
include '../../includes/header.php';
include '../../includes/conexion.php';

// Verificar si se ha recibido un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listar_laborable.php?error=ID no válido');
    exit();
}

$idgrupo = (int)$_GET['id'];

// Iniciar una transacción para mayor seguridad
$conn->begin_transaction();

try {
    // Verificar si el grupo existe antes de eliminar
    $query_verificar = "SELECT COUNT(*) as total FROM dia_laborable_has_grupo WHERE grupo_idgrupo = ?";
    $stmt_verificar = $conn->prepare($query_verificar);
    $stmt_verificar->bind_param("i", $idgrupo);
    $stmt_verificar->execute();
    $stmt_verificar->bind_result($total);
    $stmt_verificar->fetch();
    $stmt_verificar->close();

    if ($total == 0) {
        throw new Exception("No se encontró la relación a eliminar.");
    }

    // Eliminar las relaciones en la tabla intermedia
    $query_delete = "DELETE FROM dia_laborable_has_grupo WHERE grupo_idgrupo = ?";
    $stmt_delete = $conn->prepare($query_delete);
    $stmt_delete->bind_param("i", $idgrupo);
    $stmt_delete->execute();
    
    if ($stmt_delete->affected_rows === 0) {
        throw new Exception("No se pudo eliminar la relación.");
    }

    $stmt_delete->close();
    
    // Confirmar la eliminación
    $conn->commit();
    $conn->close();
    
    // Redirigir con mensaje de éxito
    header('Location: listar_laborable.php?success=deleted');
    exit();
} catch (Exception $e) {
    // Si hay un error, revertir cambios
    $conn->rollback();
    $conn->close();
    
    // Redirigir con mensaje de error
    header('Location: listar_laborable.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>
