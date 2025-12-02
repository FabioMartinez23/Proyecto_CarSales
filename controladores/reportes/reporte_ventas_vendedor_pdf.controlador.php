<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../vendor/autoload.php'); // Dompdf + Composer
require_once('../../modelos/vender_vehiculos.php');
require_once('../../modelos/usuarios.php');

use Dompdf\Dompdf;
use Dompdf\Options;

// ================================
// 1) Datos de sistema
// ================================
$nombre_sistema    = "CarSales - Gestión de Vehículos Usados";
$nombre_empresa    = "Arenales S.A.";
$direccion_empresa = "Arenales 1815 - Formosa Cap., Argentina";
$telefono_empresa  = "+54 362 400-0000";

// ================================
// 2) Datos del usuario logueado
// ================================
$usuario_nombre   = "Usuario";
$usuario_apellido = "";
$usuario_perfil   = "Sin perfil";

if (isset($_SESSION['idusuarios'])) {
    $usuarioModel = new Usuario();
    $datosUsuario = $usuarioModel->traerDatosUsuario($_SESSION['idusuarios']);

    if ($datosUsuario && $datosUsuario->num_rows > 0) {
        $u = $datosUsuario->fetch_assoc();
        $usuario_nombre   = $u['nombre']   ?? "Usuario";
        $usuario_apellido = $u['apellido'] ?? "";
        $usuario_perfil   = $u['perfil']   ?? "Sin perfil";
    }
}

// ================================
// 3) Logo en base64
// ================================
$logoBase64 = '';
$logoPath   = '../../assets/img/LOGO-Modificado.png';

if (file_exists($logoPath)) {
    $logoData  = file_get_contents($logoPath);
    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
}

// ================================
// 4) Datos del POST (filtros + gráfico)
// ================================
$hoy = date('Y-m-d');
$primer_dia_anio = date('Y-01-01');

$desde    = $_POST['desde']    ?? $primer_dia_anio;
$hasta    = $_POST['hasta']    ?? $hoy;
$chartImg = $_POST['chart_img'] ?? null;

$desde_txt = $desde ? date('d/m/Y', strtotime($desde)) : 'Sin definir';
$hasta_txt = $hasta ? date('d/m/Y', strtotime($hasta)) : 'Sin definir';

// ================================
// 5) Traer datos desde VenderVehiculo
// ================================
$ventas = new VenderVehiculo();
$result = $ventas->reporte_ventas_por_vendedor($desde, $hasta);

// Pasar a array + KPIs
$filas = [];
$total_ventas_global  = 0;
$total_importe_global = 0;
$mejor_vendedor       = null;
$mejor_vendedor_monto = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $filas[] = $r;

        $cant  = (int)$r['cantidad_ventas'];
        $total = (float)$r['total_vendido'];

        $total_ventas_global  += $cant;
        $total_importe_global += $total;

        if ($total > $mejor_vendedor_monto) {
            $mejor_vendedor_monto = $total;
            $mejor_vendedor       = $r['vendedor'];
        }
    }
}

$ticket_promedio_global = $total_ventas_global > 0
    ? $total_importe_global / $total_ventas_global
    : 0;

// ================================
// 6) Armar filas de la tabla
// ================================
$filas_html = '';

if (count($filas) > 0) {
    foreach ($filas as $f) {
        $vendedor        = htmlspecialchars($f['vendedor']);
        $cantidad_ventas = (int)$f['cantidad_ventas'];
        $total_vendido   = (float)$f['total_vendido'];
        $ticket_promedio = (float)$f['ticket_promedio'];

        $filas_html .= '
            <tr>
                <td style="padding:4px; border:1px solid #ccc;">' . $vendedor . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">' . $cantidad_ventas . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:right;">
                    $ ' . number_format($total_vendido, 2, ',', '.') . '
                </td>
                <td style="padding:4px; border:1px solid #ccc; text-align:right;">
                    $ ' . number_format($ticket_promedio, 2, ',', '.') . '
                </td>
            </tr>
        ';
    }
} else {
    $filas_html .= '
        <tr>
            <td colspan="4" style="padding:8px; border:1px solid #ccc; text-align:center; color:#777;">
                No se encontraron ventas para el período seleccionado.
            </td>
        </tr>
    ';
}

// ================================
// 7) HTML del PDF
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

        .text-main { color: #52658F; }

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
        .separador {
            margin-top: 8px;
            margin-bottom: 4px;
            font-weight: bold;
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
    $html .= '<img src="' . $logoBase64 . '" style="max-width:80px; height:auto;">';
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

// TÍTULO DEL REPORTE
$html .= '
    <div class="titulo-reporte">
        <h2 class="text-main">Reporte de Ventas por Vendedor</h2>
        <div class="subtitulo">
            Período: <strong>' . $desde_txt . '</strong> al <strong>' . $hasta_txt . '</strong>
        </div>
    </div>
';

// GRÁFICO (si vino desde la vista)
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
                <td class="kpi-label">Total de ventas:</td>
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
            <tr>
                <td class="kpi-label">Mejor vendedor:</td>
                <td class="kpi-value">' . ($mejor_vendedor ? htmlspecialchars($mejor_vendedor) : '—') . '</td>
            </tr>
        </table>
    </div>

    <div class="separador">Detalle por vendedor</div>

    <table class="tabla-detalle">
        <thead>
            <tr>
                <th>Vendedor</th>
                <th>Cantidad de ventas</th>
                <th>Total vendido</th>
                <th>Ticket promedio</th>
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
// 8) Generar PDF con Dompdf
// ================================
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');

// Podés usar portrait si lo querés vertical
$dompdf->setPaper('A4', 'landscape');

$dompdf->render();
$dompdf->stream('reporte_ventas_por_vendedor.pdf', ['Attachment' => false]);
exit;
