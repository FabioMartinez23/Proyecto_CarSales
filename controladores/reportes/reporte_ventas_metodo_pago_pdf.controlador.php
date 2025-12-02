<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../vendor/autoload.php');
require_once('../../modelos/vender_vehiculos.php');

use Dompdf\Dompdf;
use Dompdf\Options;

// ================================
// Datos sistema
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
    require_once('../../modelos/usuarios.php');
    $usuario = new Usuario();
    $datos   = $usuario->traerDatosUsuario($_SESSION['idusuarios']);

    if ($datos && $datos->num_rows > 0) {
        $u = $datos->fetch_assoc();
        $usuario_nombre   = $u['nombre']   ?? "Usuario";
        $usuario_apellido = $u['apellido'] ?? "";
        $usuario_perfil   = $u['perfil']   ?? "Sin perfil";
    }
}

// Logo base64
$logoBase64 = '';
$logoPath   = '../../assets/img/LOGO-Modificado.png';

if (file_exists($logoPath)) {
    $logoData  = file_get_contents($logoPath);
    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
}

// ================================
// POST: filtros + gráfico
// ================================
$hoy             = date('Y-m-d');
$primer_dia_anio = date('Y-01-01');

$desde    = $_POST['desde']    ?? $primer_dia_anio;
$hasta    = $_POST['hasta']    ?? $hoy;
$chartImg = $_POST['chart_img'] ?? null;

$desde_txt = $desde ? date('d/m/Y', strtotime($desde)) : 'Sin definir';
$hasta_txt = $hasta ? date('d/m/Y', strtotime($hasta)) : 'Sin definir';

// ================================
// Datos del modelo
// ================================
$ventas = new VenderVehiculo();
$result = $ventas->reporte_ventas_por_metodo_pago($desde, $hasta);

$metodos              = [];
$total_ventas_global  = 0;
$total_importe_global = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $metodos[] = $r;
        $total_ventas_global  += (int)$r['cantidad_ventas'];
        $total_importe_global += (float)$r['total_vendido'];
    }
}

$ticket_promedio_global = $total_ventas_global > 0
    ? $total_importe_global / $total_ventas_global
    : 0;

// ================================
// Filas de tabla
// ================================
$filas_html = '';

if (count($metodos) > 0) {
    foreach ($metodos as $m) {
        $metodo_pago    = htmlspecialchars($m['metodo_pago']);
        $cant           = (int)$m['cantidad_ventas'];
        $total          = (float)$m['total_vendido'];
        $ticket         = (float)$m['ticket_promedio'];
        $porc           = $total_ventas_global > 0 ? ($cant * 100 / $total_ventas_global) : 0;

        $filas_html .= '
            <tr>
                <td style="padding:4px; border:1px solid #ccc;">' . $metodo_pago . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">' . $cant . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:right;">
                    $ ' . number_format($total, 2, ',', '.') . '
                </td>
                <td style="padding:4px; border:1px solid #ccc; text-align:right;">
                    $ ' . number_format($ticket, 2, ',', '.') . '
                </td>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">
                    ' . number_format($porc, 1, ',', '.') . ' %
                </td>
            </tr>
        ';
    }
} else {
    $filas_html .= '
        <tr>
            <td colspan="5" style="padding:8px; border:1px solid #ccc; text-align:center; color:#777;">
                No se encontraron ventas en el período seleccionado.
            </td>
        </tr>
    ';
}

// ================================
// HTML PDF
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

// Encabezado
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
            <div><strong>Usuario:</strong> ' . htmlspecialchars($usuario_nombre . " " . $usuario_apellido) . '</div>
            <div><strong>Perfil:</strong> ' . htmlspecialchars($usuario_perfil) . '</div>
        </td>
    </tr>
</table>
';

// Título
$html .= '
<div class="titulo-reporte">
    <h2 class="text-main">Reporte de Ventas por Método de Pago</h2>
    <div class="subtitulo">
        Período: <strong>' . $desde_txt . '</strong> al <strong>' . $hasta_txt . '</strong>
    </div>
</div>
';

// Gráfico (si llegó)
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
            <td class="kpi-label">Cantidad total de ventas:</td>
            <td class="kpi-value">' . $total_ventas_global . '</td>
        </tr>
        <tr>
            <td class="kpi-label">Total vendido:</td>
            <td class="kpi-value">$ ' . number_format($total_importe_global, 2, ',', '.') . '</td>
        </tr>
        <tr>
            <td class="kpi-label">Ticket promedio global:</td>
            <td class="kpi-value">$ ' . number_format($ticket_promedio_global, 2, ',', '.') . '</td>
        </tr>
    </table>
</div>

<table class="tabla-detalle">
    <thead>
        <tr>
            <th>Método de pago</th>
            <th>Cantidad de ventas</th>
            <th>Total vendido</th>
            <th>Ticket promedio</th>
            <th>% sobre ventas</th>
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
$dompdf->stream('reporte_ventas_metodo_pago.pdf', ['Attachment' => false]);
exit;
