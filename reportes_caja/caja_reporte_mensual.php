<?php
require_once('../modelos/conexion.php');
require_once('../modelos/caja.php');

use Dompdf\Dompdf;
use Dompdf\Options;

require_once('../vendor/autoload.php'); // composer require dompdf/dompdf

// ============================
// PARÁMETROS
// ============================
$anio      = $_GET['anio'] ?? date('Y');
$mes       = $_GET['mes'] ?? date('m');
$chart_img = $_GET['chart'] ?? null; // base64 del gráfico (opcional)

$caja      = new Caja();
$conexion  = new Conexion();

// ============================
// BALANCE DEL MES (GENERAL)
// ============================
$query = "
    SELECT 
        DATE_FORMAT(fecha_movimiento, '%M %Y') AS periodo,
        SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS total_ingresos,
        SUM(CASE WHEN tipo = 'egreso'  THEN monto ELSE 0 END) AS total_egresos,
        (SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) - 
         SUM(CASE WHEN tipo = 'egreso'  THEN monto ELSE 0 END)) AS balance
    FROM caja_movimientos
    WHERE YEAR(fecha_movimiento) = '$anio' 
      AND MONTH(fecha_movimiento) = '$mes'
      AND activo_movimiento = 1
";
$resultado = $conexion->consultar($query);
$datos     = $resultado->fetch_assoc();

$total_ingresos = $datos['total_ingresos'] ?? 0;
$total_egresos  = $datos['total_egresos'] ?? 0;
$balance        = $datos['balance'] ?? 0;

// ============================
// DETALLE POR MÉTODO DE PAGO
// ============================
$query_metodos = "
    SELECT 
        COALESCE(tp.descripcion, 'Sin especificar') AS metodo_pago,
        SUM(CASE WHEN cm.tipo = 'ingreso' THEN cm.monto ELSE 0 END) AS total_ingresos,
        SUM(CASE WHEN cm.tipo = 'egreso'  THEN cm.monto ELSE 0 END) AS total_egresos,
        (SUM(CASE WHEN cm.tipo = 'ingreso' THEN cm.monto ELSE 0 END) -
         SUM(CASE WHEN cm.tipo = 'egreso'  THEN cm.monto ELSE 0 END)) AS balance
    FROM caja_movimientos cm
    LEFT JOIN tipo_pago tp 
        ON tp.idtipo_pago = cm.tipo_pago_idtipo_pago
    WHERE YEAR(cm.fecha_movimiento) = '$anio'
      AND MONTH(cm.fecha_movimiento) = '$mes'
      AND cm.activo_movimiento = 1
    GROUP BY COALESCE(tp.descripcion, 'Sin especificar')
    ORDER BY metodo_pago ASC
";
$res_metodos = $conexion->consultar($query_metodos);

$metodos_pago = [];
while ($row = $res_metodos->fetch_assoc()) {
    $metodos_pago[] = $row;
}

// ============================
// CIERRE Y USUARIO
// ============================
$query_cierre = "
    SELECT cc.*, u.username
    FROM cierres_caja cc
    LEFT JOIN usuarios u ON u.idusuarios = cc.Usuarios_idusuarios
    WHERE MONTH(cc.fecha_cierre) = '$mes' 
      AND YEAR(cc.fecha_cierre) = '$anio'
    ORDER BY cc.fecha_cierre DESC 
    LIMIT 1
";
$res_cierre   = $conexion->consultar($query_cierre);
$cierre       = $res_cierre->fetch_assoc();

$usuario       = $cierre['username']       ?? 'Administrador';
$observaciones = $cierre['observaciones']  ?? 'Sin observaciones registradas.';
$fecha_cierre  = $cierre['fecha_cierre']   ?? date('Y-m-d H:i:s');

$usuario_html       = htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8');
$observaciones_html = htmlspecialchars($observaciones, ENT_QUOTES, 'UTF-8');
$fecha_cierre_html  = htmlspecialchars($fecha_cierre, ENT_QUOTES, 'UTF-8');

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
setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');

$nombre_mes = strftime('%B', mktime(0, 0, 0, (int)$mes, 1));

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
    h1, h2, h3, h4 {
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
        page-break-inside: avoid;
    }
    .resumen h3 {
        background-color: #52658F;
        color: white;
        padding: 8px;
        border-radius: 6px;
        text-align: center;
        margin-top: 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 6px 8px;
        text-align: center;
    }
    th {
        background-color: #52658F;
        color: white;
        font-size: 12px;
    }
    .total {
        font-weight: bold;
        background-color: #e6e6e6;
    }
    .grafico {
        text-align: center;
        margin-top: 25px;
        page-break-inside: avoid;
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
        <p><strong>Periodo:</strong> " . ucfirst($nombre_mes) . " $anio</p>
        <p><strong>Generado:</strong> " . date('d/m/Y H:i') . "</p>
    </div>
</div>

<div class='resumen'>
    <h3>Resumen del Balance General</h3>
    <table>
        <tr>
            <th>Ingresos Totales</th>
            <td>$" . number_format($total_ingresos, 2, ',', '.') . "</td>
        </tr>
        <tr>
            <th>Egresos Totales</th>
            <td>$" . number_format($total_egresos, 2, ',', '.') . "</td>
        </tr>
        <tr class='total'>
            <th>Balance del Mes</th>
            <td>$" . number_format($balance, 2, ',', '.') . "</td>
        </tr>
    </table>
</div>
";

// ============================
// GRÁFICO (SI VIENE EN BASE64)
// ============================
if ($chart_img) {
    $html .= "
    <div class='grafico'>
        <h3>Gráfico del Balance</h3>
        <img src='$chart_img' style='width:90%; border:1px solid #ccc; border-radius:8px;'>
    </div>";
}

// ============================
// DETALLE POR MÉTODO DE PAGO
// ============================
if (count($metodos_pago) > 0) {
    $html .= "
    <div class='resumen'>
        <h3>Detalle por Método de Pago</h3>
        <table>
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th>Ingresos</th>
                    <th>Egresos</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>";
    
    foreach ($metodos_pago as $m) {
        $metodo   = htmlspecialchars($m['metodo_pago'], ENT_QUOTES, 'UTF-8');
        $ing      = $m['total_ingresos'] ?? 0;
        $egr      = $m['total_egresos']  ?? 0;
        $bal      = $m['balance']        ?? 0;

        $html .= "
                <tr>
                    <td>$metodo</td>
                    <td>$" . number_format($ing, 2, ',', '.') . "</td>
                    <td>$" . number_format($egr, 2, ',', '.') . "</td>
                    <td>$" . number_format($bal, 2, ',', '.') . "</td>
                </tr>";
    }

    $html .= "
            </tbody>
        </table>
    </div>";
}

// ============================
// DATOS DEL CIERRE
// ============================
$html .= "
<div class='resumen'>
    <h3>Datos del Cierre</h3>
    <table>
        <tr>
            <th>Fecha de Cierre</th>
            <td>$fecha_cierre_html</td>
        </tr>
        <tr>
            <th>Usuario Responsable</th>
            <td>$usuario_html</td>
        </tr>
        <tr>
            <th>Observaciones</th>
            <td>$observaciones_html</td>
        </tr>
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
