<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../vendor/autoload.php'); // Dompdf + Composer
require_once('../../modelos/vehiculos.php');
require_once('../../modelos/usuarios.php');

use Dompdf\Dompdf;
use Dompdf\Options;

// ================================
// 1) Datos de sistema / empresa
// ================================
$nombre_sistema   = "CarSales - Gestión de Vehículos Usados";
$nombre_empresa   = "Arenales S.A.";
$direccion_empresa = "Arenales 1815 - Formosa Cap., Argentina";
$telefono_empresa  = "+54 362 400-0000";

// ================================
// 2) Datos del usuario logueado
// ================================
$usuario_nombre   = "Usuario";
$usuario_apellido = "";
$usuario_perfil   = "Sin perfil";

// Si tenés guardado el id del usuario en la sesión
if (isset($_SESSION['idusuarios'])) {
    $usuarioModel = new Usuario();
    $datos = $usuarioModel->traerDatosUsuario($_SESSION['idusuarios']);

    if ($datos && $datos->num_rows > 0) {
        $u = $datos->fetch_assoc();

        // Ajustá estos índices al resultado real de traerDatosUsuario
        $usuario_nombre   = $u['nombre']  ?? "Usuario";
        $usuario_apellido = $u['apellido'] ?? "";
        $usuario_perfil   = $u['perfil']  ?? "Sin perfil";
    }
}

$usuario_nombre_completo = trim($usuario_nombre . " " . $usuario_apellido);

// ================================
// 3) Logo en base64
// ================================
$logoBase64 = '';
$logoPath = '../../assets/img/LOGO-Modificado.png'; // ajustá si tu logo está en otra ruta

if (file_exists($logoPath)) {
    $logoData = file_get_contents($logoPath);
    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
}

// ================================
// 4) Capturo datos del POST
// ================================
$edad_minima = isset($_POST['edad_minima']) ? (int)$_POST['edad_minima'] : 60;
$chartImg    = $_POST['chart_img'] ?? null;

// ================================
// 5) Traigo datos del modelo
// ================================
$vehiculoModel = new Vehiculos();
$result = $vehiculoModel->reporte_stock_envejecido($edad_minima);

// Paso a array para reutilizar
$vehiculos   = [];
$total_dias  = 0;
$max_dias    = 0;

if ($result && $result->num_rows > 0) {
    while ($v = $result->fetch_assoc()) {
        $vehiculos[] = $v;
        $dias = (int)$v['dias_en_stock'];

        $total_dias += $dias;
        if ($dias > $max_dias) {
            $max_dias = $dias;
        }
    }
}

$cantidad_vehiculos = count($vehiculos);
$promedio_dias = $cantidad_vehiculos > 0
    ? $total_dias / $cantidad_vehiculos
    : 0;

// ================================
// 6) Armo filas de la tabla
// ================================
$filas_html = '';

if ($cantidad_vehiculos > 0) {
    foreach ($vehiculos as $v) {
        $marca_modelo = htmlspecialchars($v['marca'] . ' ' . $v['modelo']);
        $patente      = htmlspecialchars($v['patente']);
        $anio         = htmlspecialchars($v['anio']);
        $fecha_ing    = $v['fecha_ingreso'] ? date('d/m/Y', strtotime($v['fecha_ingreso'])) : '-';
        $dias_stock   = (int)$v['dias_en_stock'];

        $filas_html .= '
            <tr>
                <td style="padding:4px; border:1px solid #ccc;">' . $marca_modelo . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $patente . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">' . $anio . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">' . $fecha_ing . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">' . $dias_stock . '</td>
            </tr>
        ';
    }
} else {
    $filas_html .= '
        <tr>
            <td colspan="5" style="padding:8px; border:1px solid #ccc; text-align:center; color:#777;">
                No se encontraron vehículos con al menos ' . (int)$edad_minima . ' días en stock.
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

        /* Colores CarSales aproximados */
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
        .small-info {
            font-size: 9px;
            text-align: right;
            margin-bottom: 5px;
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

// ENCABEZADO CON LOGO + DATOS EMPRESA + USUARIO
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

// TÍTULO DEL REPORTE
$html .= '
    <div class="titulo-reporte">
        <h2 class="text-main">Reporte de Stock Envejecido</h2>
        <div class="subtitulo">
            Vehículos con al menos <strong>' . (int)$edad_minima . '</strong> días en stock.
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

// KPIs globales
$html .= '
    <div class="kpi-container">
        <table class="kpi-table">
            <tr>
                <td class="kpi-label">Vehículos en stock envejecido:</td>
                <td class="kpi-value">' . $cantidad_vehiculos . '</td>
            </tr>
            <tr>
                <td class="kpi-label">Promedio de días en stock:</td>
                <td class="kpi-value">' . number_format($promedio_dias, 1, ',', '.') . ' días</td>
            </tr>
            <tr>
                <td class="kpi-label">Máximo de días en stock:</td>
                <td class="kpi-value">' . (int)$max_dias . ' días</td>
            </tr>
        </table>
    </div>

    <div class="separador">Detalle de vehículos en stock envejecido</div>

    <table class="tabla-detalle">
        <thead>
            <tr>
                <th>Vehículo</th>
                <th>Patente</th>
                <th>Año</th>
                <th>Fecha ingreso</th>
                <th>Días en stock</th>
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
// 8) Genero el PDF con Dompdf
// ================================
$options = new Options();
$options->set('isRemoteEnabled', true); // permite imágenes base64
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');

// Podés cambiar a "portrait" si querés vertical
$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

// Mostrar en el navegador (sin forzar descarga)
$dompdf->stream('reporte_stock_envejecido.pdf', ['Attachment' => false]);
exit;
