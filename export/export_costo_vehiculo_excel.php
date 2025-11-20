<?php
require_once("../modelos/reportes_costos.php");

$reporte = new ReportesCostos();
$id = intval($_GET['id'] ?? 0);

$data = $reporte->traer_costos_vehiculo($id);

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=costo_vehiculo_$id.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr style='background:#eaeaea; font-weight:bold'>
        <th>Concepto</th>
        <th>Valor</th>
      </tr>";

foreach ($data as $key => $val) {
    echo "<tr>
            <td><strong>$key</strong></td>
            <td>$val</td>
          </tr>";
}

echo "</table>";
exit;
