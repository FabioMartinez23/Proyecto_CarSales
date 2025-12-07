<?php
// ⚠ IMPORTANTE: que NO haya espacios ni líneas antes de este <?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Dompdf\Dompdf;

// RUTAS SEGURAS SEGÚN CARPETA ACTUAL
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../modelos/comprar_vehiculos.php';

$idcompra = $_GET['idcompra'] ?? null;

if (!$idcompra) {
    die("Falta parámetro idcompra");
}

$compraModelo = new ComprarVehiculo();
$compra = $compraModelo->traer_compra_para_acuse($idcompra);

if (!$compra) {
    die("No se encontró la consignación (idcompra = " . htmlspecialchars($idcompra) . ")");
}

// Traer documentaciones del vehículo
$docs = $compraModelo->traer_documentaciones_por_vehiculo($compra['idvehiculos']);

// ---------------------------------------------------------------------
// PREPARAR DATOS
// ---------------------------------------------------------------------
$dompdf = new Dompdf();

$fechaHoy       = new DateTime();
$fechaIngreso   = new DateTime($compra['fecha_compra']);

$fechaHoyStr    = $fechaHoy->format('d/m/Y');
$fechaIngresoStr= $fechaIngreso->format('d/m/Y');

// Datos titular
$apellido          = $compra['apellido']          ?? '';
$nombre            = $compra['nombre']            ?? '';
$valor_documento   = $compra['valor_documento']   ?? '';
$valor_contacto    = $compra['valor_contacto']    ?? '';
$nombre_domicilio  = $compra['nombre_domicilio']  ?? '';
$nombre_barrio     = $compra['nombre_barrio']     ?? '';
$nombre_localidad  = $compra['nombre_localidad']  ?? '';
$nombre_provincia  = $compra['nombre_provincia']  ?? '';

// Datos vehículo
$nombre_marca      = $compra['nombre_marca']      ?? '';
$nombre_modelo     = $compra['nombre_modelo']     ?? '';
$anio              = $compra['anio']              ?? '';
$color             = $compra['nombre_descripcion']?? '';
$patente           = $compra['patente']           ?? '';
$precio            = $compra['precio']            ?? 0;
$observacion       = $compra['observacion']       ?? '';

// Ficha técnica
$kilometros        = $compra['kilometraje']            ?? '________________';
$n_motor           = $compra['motor']                  ?? '________________';
$n_chasis          = $compra['chasis']                 ?? '________________';
$combustible       = '________________'; // enganchar luego si tenés tabla específica

$vto_bateria       = $compra['vencimiento_bateria']    ?? null;
$vto_service       = $compra['vencimiento_service']    ?? null;
$vto_rto           = $compra['vencimiento_RTO']        ?? null;

$vto_bateria_str   = $vto_bateria ? (new DateTime($vto_bateria))->format("d/m/Y") : "________________";
$vto_service_str   = $vto_service ? (new DateTime($vto_service))->format("d/m/Y") : "________________";
$vto_rto_str       = $vto_rto     ? (new DateTime($vto_rto))->format("d/m/Y")     : "________________";

$desc_carroceria   = $compra['descripcion_carroceria'] ?? '';
$desc_cristales    = $compra['descripcion_cristales']  ?? '';
$desc_neumaticos   = $compra['descripcion_neumaticos'] ?? '';

// ---------------------------------------------------------------------
// ARMAR FILAS DE DOCUMENTACIONES
// ---------------------------------------------------------------------
$filasDocs = '';

if ($docs && $docs->num_rows > 0) {
    while ($row = $docs->fetch_assoc()) {

        // traducimos estado_doc y digitalizado
        $estado       = ($row['estado_doc'] == 1) ? 'En buen estado' : 'Observaciones / Revisar';
        $digitalizado = ($row['digitalizado'] == 1) ? 'Sí' : 'No';

        $filasDocs .= '
            <tr>
                <td>'.htmlspecialchars($row['tipo_documento']).'</td>
                <td>'.$estado.' - Digitalizado: '.$digitalizado.'</td>
            </tr>
        ';
    }
} else {
    $filasDocs = '
        <tr>
            <td colspan="2">No hay documentación registrada para este vehículo.</td>
        </tr>
    ';
}

// ---------------------------------------------------------------------
// HTML DEL PDF
// ---------------------------------------------------------------------
$html = '
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 25px;
        }

        .titulo-principal {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .subtitulo {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
        }

        .bloque {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .bloque-titulo {
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            padding-bottom: 3px;
        }

        .fila {
            margin-bottom: 3px;
        }

        .fila span.label {
            font-weight: bold;
        }

        table.detalle {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.detalle th, table.detalle td {
            border: 1px solid #000;
            padding: 4px;
        }

        table.detalle th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .clausulas {
            font-size: 10px;
            text-align: justify;
            margin-top: 10px;
        }

        .firmas {
            margin-top: 40px;
            width: 100%;
        }

        .firmas td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .firma-linea {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 2px;
            font-size: 10px;
        }

        .pie-duplicado {
            margin-top: 15px;
            font-size: 9px;
            text-align: right;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="titulo-principal">
        CONSTANCIA DE INGRESO DE VEHÍCULO EN CONSIGNACIÓN
    </div>
    <div class="subtitulo">
        Fecha de emisión: '.$fechaHoyStr.'
    </div>

    <!-- DATOS DEL TITULAR -->
    <div class="bloque">
        <div class="bloque-titulo">Datos del titular / propietario</div>
        <div class="fila"><span class="label">Apellido y Nombre:</span> '.
            htmlspecialchars($apellido." ".$nombre).'</div>
        <div class="fila"><span class="label">Documento:</span> '.
            htmlspecialchars($valor_documento).'</div>
        <div class="fila"><span class="label">Domicilio:</span> '.
            htmlspecialchars($nombre_domicilio).' - Barrio '.htmlspecialchars($nombre_barrio).
            ', '.htmlspecialchars($nombre_localidad).', '.htmlspecialchars($nombre_provincia).'</div>
        <div class="fila"><span class="label">Teléfono:</span> '.
            htmlspecialchars($valor_contacto).'</div>
    </div>

    <!-- DATOS DEL VEHÍCULO -->
    <div class="bloque">
        <div class="bloque-titulo">Datos del vehículo</div>
        <div class="fila"><span class="label">Marca:</span> '.htmlspecialchars($nombre_marca).'</div>
        <div class="fila"><span class="label">Modelo:</span> '.htmlspecialchars($nombre_modelo).'</div>
        <div class="fila"><span class="label">Año:</span> '.htmlspecialchars($anio).'</div>
        <div class="fila"><span class="label">Color:</span> '.htmlspecialchars($color).'</div>
        <div class="fila"><span class="label">Patente / Dominio:</span> '.htmlspecialchars($patente).'</div>
        <div class="fila"><span class="label">Fecha de ingreso a consignación:</span> '.$fechaIngresoStr.'</div>
    </div>

    <!-- FICHA TÉCNICA -->
    <div class="bloque">
        <div class="bloque-titulo">Datos de ficha técnica</div>
        <table class="detalle">
            <tr>
                <th>Kilometraje</th>
                <th>Nº de motor</th>
                <th>Nº de chasis</th>
                <th>Vto. Batería</th>
                <th>Vto. Service</th>
                <th>Vto. RTO</th>
            </tr>
            <tr>
                <td>'.htmlspecialchars($kilometros).'</td>
                <td>'.htmlspecialchars($n_motor).'</td>
                <td>'.htmlspecialchars($n_chasis).'</td>
                <td>'.$vto_bateria_str.'</td>
                <td>'.$vto_service_str.'</td>
                <td>'.$vto_rto_str.'</td>
            </tr>
        </table>

        <table class="detalle" style="margin-top:8px;">
            <tr>
                <th>Carrocería</th>
                <th>Cristales</th>
                <th>Neumáticos</th>
            </tr>
            <tr>
                <td>'.htmlspecialchars($desc_carroceria ?: "________________").'</td>
                <td>'.htmlspecialchars($desc_cristales ?: "________________").'</td>
                <td>'.htmlspecialchars($desc_neumaticos ?: "________________").'</td>
            </tr>
        </table>
    </div>

    <!-- DOCUMENTACIÓN ENTREGADA -->
    <div class="bloque">
        <div class="bloque-titulo">Documentación física entregada</div>
        <table class="detalle">
            <tr>
                <th>Documento</th>
                <th>Estado / Observaciones</th>
            </tr>
            '.$filasDocs.'
        </table>
    </div>

    <!-- PRECIO DE TOMA -->
    <div class="bloque">
        <div class="bloque-titulo">Precio de toma / valor de referencia</div>
        <div class="fila">
            <span class="label">Precio de toma acordado:</span>
            $'.number_format($precio, 0, ",", ".").'
        </div>
        <div class="fila">
            <span class="label">Observaciones:</span>
            '.(!empty($observacion) ? htmlspecialchars($observacion) : 'Sin observaciones adicionales').'
        </div>
    </div>

    <!-- CLAUSULAS -->
    <div class="clausulas">
        <strong>Cláusulas:</strong><br><br>
        1) El titular deja en consignación el vehículo detallado en la presente, 
        autorizando a la concesionaria a exhibirlo, ofrecerlo y gestionarle la venta a terceros,
        sin que ello implique transmisión de dominio hasta tanto se concrete la operación de venta
        y se cumplan las condiciones que las partes acuerden por separado.<br><br>

        2) El titular declara que los datos consignados del vehículo y de la documentación entregada 
        son correctos y que el vehículo se encuentra libre de todo gravamen, prenda, embargo, 
        denuncia de venta u otra medida que limite su circulación o transferencia, salvo que se hayan
        dejado expresamente asentadas en el presente.<br><br>

        3) El titular manifiesta conocer y aceptar el valor de referencia y/o precio de toma 
        indicado en este documento, renunciando a formular reclamos posteriores por diferencias 
        sobre dicho importe, salvo nuevo acuerdo por escrito entre las partes. En caso de 
        modificarse el precio de toma, dicha modificación deberá constar por escrito y con 
        la firma de ambas partes.<br><br>

        4) El presente documento se firma en constancia de la entrega del vehículo y de la 
        documentación detallada, así como de la recepción por parte de la concesionaria, 
        quedando ambas partes conformes con lo aquí expuesto.<br><br>
    </div>

    <!-- FIRMAS -->
    <table class="firmas">
        <tr>
            <td>
                <div class="firma-linea">
                    Firma del Titular / Propietario<br>
                    Aclaración: '.htmlspecialchars($apellido." ".$nombre).'<br>
                    DNI: '.htmlspecialchars($valor_documento).'
                </div>
            </td>
            <td>
                <div class="firma-linea">
                    Firma del Receptor (Concesionaria)<br>
                    Aclaración: ______________________________<br>
                    DNI: ______________________________<br>
                    Cargo: ____________________________
                </div>
            </td>
        </tr>
    </table>

    <div class="pie-duplicado">
        Documento emitido por duplicado: un ejemplar para el titular y otro para la concesionaria.
    </div>
</body>
</html>
';

// ---------------------------------------------------------------------
// RENDER PDF
// ---------------------------------------------------------------------
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Limpio cualquier salida previa (por si algún echo / BOM se escapó)
if (ob_get_length()) {
    ob_clean();
}

$dompdf->stream("acuse_ingreso_consignacion_{$idcompra}.pdf", ["Attachment" => 0]);
exit;
