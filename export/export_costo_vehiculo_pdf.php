<?php
require_once '../modelos/reportes_costos.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

$id = intval($_GET['id']);
$reporte = new ReportesCostos();
$data = $reporte->traer_costos_vehiculo($id);

$html = "<h2 style='text-align:center;'>Costos del Vehículo ID $id</h2>";
$html .= "<table width='100%' border='1' cellpadding='5' cellspacing='0'>";

foreach ($data as $k=>$v){
    $html .= "<tr>
        <td><strong>$k</strong></td>
        <td>$v</td>
    </tr>";
}

$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4","portrait");
$dompdf->render();
$dompdf->stream("costo_vehiculo_$id.pdf", ["Attachment"=>true]);
