<?php
require_once("../modelos/reportes_costos.php");

$reporte = new ReportesCostos();

$desde    = $_GET['desde'] ?? '';
$hasta    = $_GET['hasta'] ?? '';
$estado   = $_GET['estado'] ?? '';
$vehiculo = $_GET['vehiculo'] ?? '';

$data = $reporte->traer_costos_totales($desde, $hasta, $estado, $vehiculo);

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=costos_totales.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr style='background:#eaeaea; font-weight:bold'>
        <th>ID</th>
        <th>Patente</th>
        <th>Precio Tomado</th>
        <th>Gastos</th>
        <th>Costo Final</th>
        <th>Precio Público / Venta</th>
        <th>Margen</th>
        <th>% Rentabilidad</th>
      </tr>";

foreach ($data as $v) {

    $precio_pub_venta = $v['precio_venta'] > 0 
        ? $v['precio_venta'] 
        : $v['precio_publico'];

    echo "<tr>
            <td>{$v['idvehiculos']}</td>
            <td>{$v['patente']}</td>
            <td>{$v['precio_tomado']}</td>
            <td>{$v['gastos']}</td>
            <td>{$v['costo_final']}</td>
            <td>{$precio_pub_venta}</td>
            <td>{$v['margen']}</td>
            <td>{$v['rentabilidad']}%</td>
          </tr>";
}

echo "</table>";
exit;
