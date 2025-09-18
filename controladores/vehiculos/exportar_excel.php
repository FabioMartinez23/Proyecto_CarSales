<?php
include("../../modelos/Conexion.php");
$conexion = new Conexion();

$buscador = $_GET['buscador'] ?? '';

$query = "SELECT vehiculos.*, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, precios_vehiculos.precio, colores.descripcion as nombre_color FROM vehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores LEFT JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos AND precios_vehiculos.fecha_precio = (
            SELECT MAX(fecha_precio)
            FROM precios_vehiculos AS p
            WHERE p.vehiculos_idvehiculos = vehiculos.idvehiculos
            AND p.fecha_precio <= NOW()) WHERE 
        (patente LIKE '%$buscador%' OR marcas.nombre LIKE '%$buscador%' OR modelos.nombre LIKE '%$buscador%' OR anio LIKE '%$buscador%') AND activo_vehiculo = 1 ORDER BY vehiculos.idvehiculos";
$resultado = $conexion->consultar($query);

// Cabeceras para que el navegador lo tome como Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=vehiculos.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Imprimir la tabla como HTML
echo "<table border='1'>";
echo "<tr>
        <th>Patente</th>
        <th>Año</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Tipo</th>
        <th>Precio</th>
      </tr>";

while ($row = $resultado->fetch_assoc()) {
    echo "<tr>
            <td>{$row['patente']}</td>
            <td>{$row['anio']}</td>
            <td>{$row['nombre_marca']}</td>
            <td>{$row['nombre_modelo']}</td>
            <td>{$row['nombre_tipo']}</td>
            <td>{$row['precio']}</td>
          </tr>";
}
echo "</table>";
exit;

