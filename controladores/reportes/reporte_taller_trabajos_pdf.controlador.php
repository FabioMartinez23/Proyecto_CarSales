<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../vendor/autoload.php'); // Dompdf + Composer
require_once('../../modelos/vehiculos_taller.php');

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
//  Datos del usuario logueado
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

// Logo en base64 (ajustar ruta si cambia)
$logoBase64 = '';
$logoPath   = '../../assets/img/LOGO-Modificado.png';

if (file_exists($logoPath)) {
    $logoData   = file_get_contents($logoPath);
    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
}

// ================================
// 2) Datos recibidos por POST
// ================================
$hoy            = date('Y-m-d');
$primer_dia_anio = date('Y-01-01');

$fecha_desde   = $_POST['fecha_desde']   ?? $primer_dia_anio;
$fecha_hasta   = $_POST['fecha_hasta']   ?? $hoy;
$estado_taller = $_POST['estado_taller'] ?? '';      // en_proceso / finalizado / ''
$patente       = $_POST['patente']       ?? '';
$chartImg      = $_POST['chart_img']     ?? null;

// Normalizar para mostrar
$desde_txt = $fecha_desde ? date('d/m/Y', strtotime($fecha_desde)) : 'Sin definir';
$hasta_txt = $fecha_hasta ? date('d/m/Y', strtotime($fecha_hasta)) : 'Sin definir';

$estado_txt = 'Todos';
if ($estado_taller === 'en_proceso') {
    $estado_txt = 'En proceso';
} elseif ($estado_taller === 'finalizado') {
    $estado_txt = 'Finalizados';
}

// ================================
// 3) Traigo datos del modelo
// ================================
$vt       = new Vehiculos_Taller();
$filtros  = [
    'fecha_desde'   => $fecha_desde,
    'fecha_hasta'   => $fecha_hasta,
    'estado_taller' => $estado_taller,
    'patente'       => $patente
];

$result = $vt->reporte_trabajos_taller($filtros);

// Paso a array para reusar
$trabajos              = [];
$total_trabajos        = 0;
$total_gastos_global   = 0;
$trabajos_en_proceso   = 0;
$trabajos_finalizados  = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $trabajos[] = $r;

        $total_trabajos++;
        $total_gastos_global += (float)($r['total_gastos_taller'] ?? 0);

        if ($r['estado_taller'] === 'en_proceso') {
            $trabajos_en_proceso++;
        } elseif ($r['estado_taller'] === 'finalizado') {
            $trabajos_finalizados++;
        }
    }
}

$gasto_promedio_trabajo = $total_trabajos > 0
    ? $total_gastos_global / $total_trabajos
    : 0;

// ================================
// 4) Armo filas de la tabla
// ================================
$filas_html = '';

if (count($trabajos) > 0) {
    foreach ($trabajos as $t) {
        $id_taller   = (int)$t['idvehiculos_taller'];
        $pat         = htmlspecialchars($t['patente']);
        $vehiculo    = htmlspecialchars($t['nombre_marca'] . ' ' . $t['nombre_modelo'] . ' ' . $t['anio']);
        $color       = htmlspecialchars($t['nombre_color']);
        $estado      = htmlspecialchars($t['estado_taller']);

        $fecha_ing   = $t['fecha_ingreso'] 
                        ? date('d/m/Y H:i', strtotime($t['fecha_ingreso'])) 
                        : '-';

        $fecha_sal   = $t['fecha_salida']
                        ? date('d/m/Y H:i', strtotime($t['fecha_salida']))
                        : '-';

        $motivo      = htmlspecialchars($t['motivo'] ?? '');
        $trab_real   = htmlspecialchars($t['trabajo_realizado'] ?? '');
        $total_gasto = (float)($t['total_gastos_taller'] ?? 0);

        $filas_html .= '
            <tr>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">' . $id_taller . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $fecha_ing . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $fecha_sal . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $pat . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $vehiculo . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $color . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:center;">' . ucfirst($estado) . '</td>
                <td style="padding:4px; border:1px solid #ccc; text-align:right;">
                    $ ' . number_format($total_gasto, 2, ',', '.') . '
                </td>
                <td style="padding:4px; border:1px solid #ccc;">' . $motivo . '</td>
                <td style="padding:4px; border:1px solid #ccc;">' . $trab_real . '</td>
            </tr>
        ';
    }
} else {
    $filas_html .= '
        <tr>
            <td colspan="10" style="padding:8px; border:1px solid #ccc; text-align:center; color:#777;">
                No se encontraron trabajos de taller para el criterio seleccionado.
            </td>
        </tr>
    ';
}

// ================================
// 5) HTML del PDF
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

// TÍTULO REPORTE
$html .= '
<div class="titulo-reporte">
    <h2 class="text-main">Reporte de Trabajos de Taller</h2>
    <div class="subtitulo">
        Período: <strong>' . $desde_txt . '</strong> al <strong>' . $hasta_txt . '</strong><br>
        Estado: <strong>' . htmlspecialchars($estado_txt) . '</strong>' . 
        (!empty($patente) ? ' | Patente contiene: <strong>' . htmlspecialchars($patente) . '</strong>' : '') . '
    </div>
</div>
';

// GRÁFICO (si vino)
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
            <td class="kpi-label">Cantidad de trabajos de taller:</td>
            <td class="kpi-value">' . $total_trabajos . '</td>
        </tr>
        <tr>
            <td class="kpi-label">Total de gastos de taller:</td>
            <td class="kpi-value">$ ' . number_format($total_gastos_global, 2, ',', '.') . '</td>
        </tr>
        <tr>
            <td class="kpi-label">Gasto promedio por trabajo:</td>
            <td class="kpi-value">$ ' . number_format($gasto_promedio_trabajo, 2, ',', '.') . '</td>
        </tr>
        <tr>
            <td class="kpi-label">Trabajos en proceso:</td>
            <td class="kpi-value">' . $trabajos_en_proceso . '</td>
        </tr>
        <tr>
            <td class="kpi-label">Trabajos finalizados:</td>
            <td class="kpi-value">' . $trabajos_finalizados . '</td>
        </tr>
    </table>
</div>

<div class="separador">Detalle de trabajos de taller</div>

<table class="tabla-detalle">
    <thead>
        <tr>
            <th># Taller</th>
            <th>Fecha ingreso</th>
            <th>Fecha salida</th>
            <th>Patente</th>
            <th>Vehículo</th>
            <th>Color</th>
            <th>Estado</th>
            <th>Total gastos</th>
            <th>Motivo ingreso</th>
            <th>Trabajo realizado</th>
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
// 6) Genero el PDF con Dompdf
// ================================
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();
$dompdf->stream('reporte_trabajos_taller.pdf', ['Attachment' => false]);
exit;
