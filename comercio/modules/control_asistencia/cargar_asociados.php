<?php
require_once(__DIR__ . "/../../includes/conexion.php");

// Consultar grupos y socios
// Ejemplo con PDO:

// Obtener grupos
$stmtGrupos = $pdo->query("SELECT idgrupo, nombre FROM grupo ORDER BY nombre");
$grupos = $stmtGrupos->fetchAll(PDO::FETCH_ASSOC);

// Obtener socios (puedes unir con grupo para traerlos juntos)
$stmtSocios = $pdo->query("
    SELECT s.idsocio, s.nombre, s.dni, g.idgrupo, g.nombre AS grupo_nombre
    FROM socio s
    INNER JOIN grupo g ON s.grupo_idgrupo = g.idgrupo
    ORDER BY g.nombre, s.nombre
");
$socios = $stmtSocios->fetchAll(PDO::FETCH_ASSOC);

// Agrupar socios por grupo para imprimir tabla
$grupos_con_socios = [];
foreach ($socios as $socio) {
    $grupos_con_socios[$socio['idgrupo']]['nombre'] = $socio['grupo_nombre'];
    $grupos_con_socios[$socio['idgrupo']]['socios'][] = $socio;
}
?>
