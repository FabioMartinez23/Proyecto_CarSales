<?php
require_once '../modelos/reportes_costos.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$reporte = new ReportesCostos();

$desde    = $_GET['desde'] ?? '';
$hasta    = $_GET['hasta'] ?? '';
$estado   = $_GET['estado'] ?? '';
$vehiculo = $_GET['vehiculo'] ?? '';

$data = $reporte->traer_costos_totales($desde, $hasta, $estado, $vehiculo);

// HTML DEL PDF
$html = '
<h2 style="text-align:center;">Reporte de Costos Totales</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr style="background:#eaeaea;">
            <th>ID</th>
            <th>Patente</th>
            <th>Tomado</th>
            <th>Gastos</th>
            <th>Costo Final</th>
            <th>Público/Venta</th>
            <th>Margen</th>
            <th>% Rent</th>
        </tr>
    </thead>
    <tbody>
';

foreach ($data as $v) {
    $html .= '
        <tr>
            <td>'.$v['idvehiculos'].'</td>
            <td>'.$v['patente'].'</td>
            <td>$'.number_format($v['precio_tomado'],0,',','.').'</td>
            <td>$'.number_format($v['gastos'],0,',','.').'</td>
            <td>$'.number_format($v['costo_final'],0,',','.').'</td>
            <td>$'.number_format($v['precio_publico'] ?? $v['precio_venta'],0,',','.').'</td>
            <td>$'.number_format($v['margen'],0,',','.').'</td>
            <td>'.number_format($v['rentabilidad'],1).'%</td>
        </tr>';
}

$html .= '</tbody></table>';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream("costos_totales.pdf", ["Attachment" => true]);
