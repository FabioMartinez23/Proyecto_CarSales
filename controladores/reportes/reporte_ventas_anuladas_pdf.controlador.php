<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../vendor/autoload.php'); // Dompdf
require_once('../../modelos/vender_vehiculos.php');
require_once('../../modelos/usuarios.php');

use Dompdf\Dompdf;
use Dompdf\Options;

// ================================
// Datos sistema / empresa
// ================================
$nombre_sistema    = "CarSales - Gestión de Vehículos Usados";
$nombre_empresa    = "Arenales S.A.";
$direccion_empresa = "Arenales 1815 - Formosa Cap., Argentina";
$telefono_empresa  = "+54 362 400-0000";

// ================================
// Usuario logueado
// ================================
$usuario_nombre   = "Usuario";
$usuario_apellido = "";
$usuario_perfil   = "Sin perfil";

if (isset($_SESSION['idusuarios'])) {
    $usuarioModel = new Usuario();
    $datos = $usuarioModel->traerDatosUsuario($_SESSION['idusuarios']);

    if ($datos && $datos->num_rows > 0) {
        $u = $datos->fetch_assoc();
        $usuario_nombre   = $u['nombre']  ?? "Usuario";
        $usuario_apellido = $u['apellido'] ?? "";
        $usuario_perfil   = $u['perfil']  ?? "Sin perfil";
    }
}

$usuario_nombre_completo = trim($usuario_nombre . " " . $usuario_apellido);

// ================================
// Logo en base64
// ================================
$logoBase64 = '';
$logoPath = '../../assets/img/LOGO-Modificado.png';

if (file_exists($logoPath)) {
    $logoData = file_get_contents($logoPath);
    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
}

// ================================
// Datos del POST
// ================================
$hoy             = date('Y-m-d');
$primer_dia_anio = date('Y-01-01');

$desde    = $_POST['desde'] ?? $primer_dia_anio;
$hasta    = $_POST['hasta'] ?? $hoy;
$chartImg = $_POST['chart_img'] ?? null;

$desde_txt = $desde ? date('d/m/Y', strtotime($desde)) : 'Sin definir';
$hasta_txt = $hasta ? date('d/m/Y', strtotime($hasta)) : 'Sin definir';

// ================================
// Traer datos del modelo
// ================================
$ventasModel = new VenderVehiculo();
$result = $ventasModel->reporte_ventas_anuladas_detalle($desde, $hasta);

$filas           = [];
$total_anuladas  = 0;
$total_monto     = 0;

// para gráfico si quisieras reconstruirlo también
$agrupado = [];

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $filas[] = $r;

        $total_anuladas++;
        $total_monto += (float)$r['precio_venta'];

        if (!empty($r['fecha_anulacion'])) {
            $periodo = date('Y-m', strtotime($r['fecha_anulacion']));
            $periodo_legible = date('m/Y', strtotime($r['fecha_anulacion']));
            if (!isset($agrupado[$periodo])) {
                $agrupado[$periodo] = [
                    'label'        => $periodo_legible,
                    'cantidad'     => 0,
                    'total_monto'  => 0
                ];
            }
            $agrupado[$periodo]['cantidad']++;
            $agrupado[$periodo]['total_monto'] += (float)$r['precio_venta'];
        }
    }
}

$ticket_promedio = $total_anuladas > 0
    ? $total_monto / $total_anuladas
    : 0;

// ================================
// Armar filas HTML de la tabla
// ================================
$filas_html = '';

if (count($filas) > 0) {
    foreach ($filas as $f) {
        $fecha_venta  = $f['fecha_venta'] ? date('d/m/Y', strtotime($f['fecha_venta'])) : '-';
        $fecha_anula  = $f['fecha_anulacion'] ? date('d/m/Y', strtotime($f['fecha_anulacion'])) : '-';
        $vehiculo     = htmlspecialchars($f['marca'] . ' ' . $f['modelo'] . ' - ' . $f['patente']);
        $cliente      = htmlspecialchars($f['cliente']);
        $vendedor     = htmlspecialchars($f['vendedor']);
        $monto        = (float)$f['precio_venta'];

        $filas_html .= '
            <tr>
                <td style="padding:4px; border:1px solid #ccc;">' . $fecha_venta . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $fecha_anula . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $vehiculo . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $cliente . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $vendedor . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:right;">
                    $ ' . number_format($monto, 2, ',', '.') . '
                </td>
            </tr>
        ';
    }
} else {
    $filas_html .= '
        <tr>
            <td colspan="6" style="padding:8px; border:1px solid #ccc; text-align:center; color:#777;">
                No se encontraron ventas anuladas en el período seleccionado.
            </td>
        </tr>
    ';
}

// ================================
// HTML del PDF
// ================================
$fecha_generacion = date('d/m/Y H:i');

$html = '
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }
        h1, h2, h3 {
            margin: 0;
            padding: 0;
        }

        .bg-main {
            background-color: #52658F;
            color: #ffffff;
        }
        .text-main {
            color: #52658F;
        }
        .border-main {
            border-color: #52658F;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 4px 6px;
        }
        .header-logo {
            width: 80px;
        }
        .header-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
        }
        .header-subtitle {
            font-size: 11px;
        }
        .header-info {
            font-size: 10px;
            text-align: right;
        }

        .titulo-reporte {
            text-align: center;
            margin: 6px 0;
        }
        .titulo-reporte h2 {
            font-size: 14px;
        }
        .subtitulo {
            font-size: 11px;
            margin-top: 2px;
        }

        .kpi-container {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kpi-table td {
            padding: 6px 8px;
            font-size: 11px;
        }
        .kpi-label {
            font-weight: bold;
        }
        .kpi-value {
            text-align: right;
        }

        .tabla-detalle {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .tabla-detalle thead th {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 11px;
        }
        .tabla-detalle tbody td {
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 30px;
            right: 30px;
            font-size: 9px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
';

// ENCABEZADO
$html .= '
    <table class="header-table">
        <tr>
            <td class="header-logo">
';

if (!empty($logoBase64)) {
    $html .= '
                <img src="' . $logoBase64 . '" style="max-width:80px; height:auto;">
    ';
}

$html .= '
            </td>
            <td class="header-title">
                <div>' . htmlspecialchars($nombre_sistema) . '</div>
                <div class="header-subtitle">' . htmlspecialchars($nombre_empresa) . '</div>
                <div class="header-subtitle">' . htmlspecialchars($direccion_empresa) . ' - ' . htmlspecialchars($telefono_empresa) . '</div>
            </td>
            <td class="header-info">
                <div><strong>Fecha:</strong> ' . $fecha_generacion . '</div>
                <div><strong>Usuario:</strong> ' . htmlspecialchars($usuario_nombre_completo) . '</div>
                <div><strong>Perfil:</strong> ' . htmlspecialchars($usuario_perfil) . '</div>
            </td>
        </tr>
    </table>
';

// TÍTULO
$html .= '
    <div class="titulo-reporte">
        <h2 class="text-main">Reporte de Ventas Anuladas</h2>
        <div class="subtitulo">
            Período: <strong>' . $desde_txt . '</strong> al <strong>' . $hasta_txt . '</strong>
        </div>
    </div>
';

// GRÁFICO (si viene)
if (!empty($chartImg)) {
    $html .= '
    <div style="text-align:center; margin: 10px 0 5px 0;">
        <img src="' . $chartImg . '" style="max-width: 100%; height: auto;">
    </div>
    ';
}

// KPIs
$html .= '
    <div class="kpi-container">
        <table class="kpi-table">
            <tr>
                <td class="kpi-label">Cantidad de ventas anuladas:</td>
                <td class="kpi-value">' . $total_anuladas . '</td>
            </tr>
            <tr>
                <td class="kpi-label">Monto total involucrado:</td>
                <td class="kpi-value">$ ' . number_format($total_monto, 2, ',', '.') . '</td>
            </tr>
            <tr>
                <td class="kpi-label">Ticket promedio anulado:</td>
                <td class="kpi-value">$ ' . number_format($ticket_promedio, 2, ',', '.') . '</td>
            </tr>
        </table>
    </div>

    <table class="tabla-detalle">
        <thead>
            <tr>
                <th>Fecha venta</th>
                <th>Fecha anulación</th>
                <th>Vehículo</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            ' . $filas_html . '
        </tbody>
    </table>

    <div class="footer">
        ' . htmlspecialchars($nombre_empresa) . ' - Sistema de Gestión: CarSales | Reporte generado automáticamente, no requiere firma.
    </div>
</body>
</html>
';

// ================================
// Generar PDF
// ================================
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('reporte_ventas_anuladas.pdf', ['Attachment' => false]);
exit;
