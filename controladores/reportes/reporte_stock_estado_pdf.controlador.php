<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../vendor/autoload.php'); // Dompdf + Composer
require_once('../../modelos/vehiculos.php');

use Dompdf\Dompdf;
use Dompdf\Options;

// ================================
// 1) Datos de sistema / empresa
// ================================
$nombre_sistema   = "CarSales - Gestión de Vehículos Usados";
$nombre_empresa   = "Arenales S.A.";
$direccion_empresa = "Arenales 1815 - Formosa Cap., Argentina";
$telefono_empresa = "+54 362 400-0000";

// ================================
// 2) Datos del usuario logueado
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

        $usuario_nombre   = $u['nombre']  ?? "Usuario";
        $usuario_apellido = $u['apellido'] ?? "";
        $usuario_perfil   = $u['perfil']   ?? "Sin perfil";
    }
}

// Logo en base64 (ajustá la ruta)
$logoBase64 = '';
$logoPath   = '../../assets/img/LOGO-Modificado.png';

if (file_exists($logoPath)) {
    $logoData  = file_get_contents($logoPath);
    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
}

// ================================
// 3) Datos recibidos por POST
// ================================
$chartImg = $_POST['chart_img'] ?? null;

// ================================
// 4) Traer datos del modelo
// ================================
$vehiculos = new Vehiculos();
$result    = $vehiculos->reporte_stock_estado();

// Pasar a array
$estados = [];
$total_stock = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $estados[]    = $r;
        $total_stock += (int)$r['cantidad'];
    }
}

// Mapa por estado para KPIs
$map = [];
foreach ($estados as $e) {
    $map[$e['estado']] = (int)$e['cantidad'];
}

// Estados reales en tu tabla:
// 'disponible', 'falta_documento', 'falta_digitalizacion', 'taller', 'vendido', 'Baja consignación'
$disp   = $map['disponible']           ?? 0;
$taller = $map['taller']               ?? 0;
$fDoc   = $map['falta_documento']      ?? 0;
$fDig   = $map['falta_digitalizacion'] ?? 0;

// ================================
// 5) Armar filas de la tabla
// ================================
$filas_html = '';

if (count($estados) > 0) {
    foreach ($estados as $e) {
        $estado = htmlspecialchars($e['estado']);
        $cant   = (int)$e['cantidad'];
        $porc   = $total_stock > 0 ? ($cant * 100 / $total_stock) : 0;

        $filas_html .= '
            <tr>
                <td style="padding:4px; border:1px solid #ccc;">' . $estado . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">' . $cant . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">
                    ' . number_format($porc, 1, ',', '.') . ' %
                </td>
            </tr>
        ';
    }
} else {
    $filas_html .= '
        <tr>
            <td colspan="3" style="padding:8px; border:1px solid #ccc; text-align:center; color:#777;">
                No se encontraron vehículos en stock para mostrar.
            </td>
        </tr>
    ';
}

// ================================
// 6) HTML del PDF
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
            <div><strong>Usuario:</strong> ' . htmlspecialchars($usuario_nombre . " " . $usuario_apellido) . '</div>
            <div><strong>Perfil:</strong> ' . htmlspecialchars($usuario_perfil) . '</div>
        </td>
    </tr>
</table>
';

// TÍTULO
$html .= '
<div class="titulo-reporte">
    <h2 class="text-main">Reporte de Stock por Estado de Vehículos</h2>
    <div class="subtitulo">
        Foto actual del stock al <strong>' . $fecha_generacion . '</strong>
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
            <td class="kpi-label">Total vehículos en stock:</td>
            <td class="kpi-value">' . $total_stock . '</td>
        </tr>
        <tr>
            <td class="kpi-label">Disponibles:</td>
            <td class="kpi-value">' . $disp . '</td>
        </tr>
        <tr>
            <td class="kpi-label">En taller:</td>
            <td class="kpi-value">' . $taller . '</td>
        </tr>
        <tr>
            <td class="kpi-label">Falta documentación:</td>
            <td class="kpi-value">' . $fDoc . '</td>
        </tr>
        <tr>
            <td class="kpi-label">Falta digitalización:</td>
            <td class="kpi-value">' . $fDig . '</td>
        </tr>
    </table>
</div>
';

// TABLA DETALLE
$html .= '
<table class="tabla-detalle">
    <thead>
        <tr>
            <th>Estado</th>
            <th>Cantidad</th>
            <th>% sobre el stock</th>
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
// 7) Generar PDF con Dompdf
// ================================
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');

// Horizontal o vertical, como prefieras
$dompdf->setPaper('A4', 'landscape');

$dompdf->render();
$dompdf->stream('reporte_stock_estado.pdf', ['Attachment' => false]);
exit;
