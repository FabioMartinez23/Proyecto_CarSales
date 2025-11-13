<?php
require_once('../modelos/conexion.php');
require_once('../modelos/caja.php');
use Dompdf\Dompdf;
use Dompdf\Options;
require_once('../vendor/autoload.php'); // composer require dompdf/dompdf

// ============================
// PARÁMETROS
// ============================
$anio = $_GET['anio'] ?? date('Y');
$mes = $_GET['mes'] ?? date('m');
$chart_img = $_GET['chart'] ?? null; // base64 del gráfico

$caja = new Caja();
$conexion = new Conexion();

// ============================
// BALANCE DEL MES
// ============================
$query = "
    SELECT 
        DATE_FORMAT(fecha_movimiento, '%M %Y') AS periodo,
        SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS total_ingresos,
        SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) AS total_egresos,
        (SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) - 
         SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END)) AS balance
    FROM caja_movimientos
    WHERE YEAR(fecha_movimiento) = '$anio' AND MONTH(fecha_movimiento) = '$mes'
    AND activo_movimiento = 1
";
$resultado = $conexion->consultar($query);
$datos = $resultado->fetch_assoc();

$total_ingresos = $datos['total_ingresos'] ?? 0;
$total_egresos  = $datos['total_egresos'] ?? 0;
$balance        = $datos['balance'] ?? 0;

// ============================
// CIERRE Y USUARIO
// ============================
$query_cierre = "
    SELECT cc.*, u.username
    FROM cierres_caja cc
    LEFT JOIN usuarios u ON u.idusuarios = cc.Usuarios_idusuarios
    WHERE MONTH(cc.fecha_cierre) = '$mes' AND YEAR(cc.fecha_cierre) = '$anio'
    ORDER BY cc.fecha_cierre DESC LIMIT 1
";
$res_cierre = $conexion->consultar($query_cierre);
$cierre = $res_cierre->fetch_assoc();

$usuario = $cierre['username'] ?? 'Administrador';
$observaciones = $cierre['observaciones'] ?? 'Sin observaciones registradas.';
$fecha_cierre = $cierre['fecha_cierre'] ?? date('Y-m-d H:i:s');

// ============================
// CONFIGURAR DOMPDF
// ============================
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$logo_path = '../assets/img/logo.png'; // Ruta al logo

// ============================
// PLANTILLA HTML
// ============================
$html = "
<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 13px;
        color: #333;
        margin: 20px;
    }
    h1, h2, h3 {
        color: #52658F;
        text-align: center;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #52658F;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .logo { width: 120px; }
    .resumen {
        margin-top: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 15px;
        background-color: #f9f9f9;
    }
    .resumen h3 {
        background-color: #52658F;
        color: white;
        padding: 8px;
        border-radius: 6px;
        text-align: center;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    th {
        background-color: #52658F;
        color: white;
    }
    .total {
        font-weight: bold;
        background-color: #e6e6e6;
    }
    .grafico {
        text-align: center;
        margin-top: 25px;
    }
    .footer {
        margin-top: 40px;
        text-align: center;
        font-size: 12px;
        color: #777;
    }
</style>
</head>
<body>

<div class='header'>
    <img src='$logo_path' class='logo'>
    <div>
        <h2>Reporte de Cierre de Caja</h2>
        <p><strong>Periodo:</strong> " . ucfirst(strftime('%B', mktime(0,0,0,$mes,1))) . " $anio</p>
        <p><strong>Generado:</strong> " . date('d/m/Y H:i') . "</p>
    </div>
</div>

<div class='resumen'>
    <h3>Resumen del Balance</h3>
    <table>
        <tr><th>Ingresos Totales</th><td>$" . number_format($total_ingresos, 2, ',', '.') . "</td></tr>
        <tr><th>Egresos Totales</th><td>$" . number_format($total_egresos, 2, ',', '.') . "</td></tr>
        <tr class='total'>
            <th>Balance del Mes</th>
            <td>$" . number_format($balance, 2, ',', '.') . "</td>
        </tr>
    </table>
</div>";

if($chart_img){
    $html .= "
    <div class='grafico'>
        <h3>Gráfico del Balance</h3>
        <img src='$chart_img' style='width:90%; border:1px solid #ccc; border-radius:8px;'>
    </div>";
}

$html .= "
<div class='resumen'>
    <h3>Datos del Cierre</h3>
    <table>
        <tr><th>Fecha de Cierre</th><td>$fecha_cierre</td></tr>
        <tr><th>Usuario Responsable</th><td>$usuario</td></tr>
        <tr><th>Observaciones</th><td>$observaciones</td></tr>
    </table>
</div>

<div class='footer'>
    <p>© " . date('Y') . " - Sistema Car Sales | Reporte generado automáticamente.</p>
</div>

</body>
</html>
";

// ============================
// GENERAR PDF
// ============================
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("cierre_caja_{$anio}_{$mes}.pdf", ["Attachment" => false]);
exit;
?>
