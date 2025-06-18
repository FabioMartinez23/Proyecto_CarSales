<?php
require_once('../modelos/comprar_vehiculos.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start(); // Inicia el buffer de salida

$image_path = '../assets/img/LOGO-Modificado.png'; // Ruta del logo
$image_data = base64_encode(file_get_contents($image_path)); // Convierte la imagen a base64
$image_src = 'data:image/png;base64,' . $image_data; // Prefijo necesario para las imágenes

$compra = new ComprarVehiculo();
$resultado_compra = $compra->traer_compra_por_id($_GET['idcompra']);

if ($resultado_compra) { 
?>

    <div class="container mt-5">

        <div style="text-align: right; margin-top: -50px; margin-right: 10px;">
            <img src="<?= $image_src ?>" alt="Car Sales Logo" style="max-width: 80px;">
        </div>

        <h1 class="text-center">Detalles de Compra - Nro <?=$resultado_compra['idcompras']; ?></h1>

        <h2>Datos del Cliente</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Nombre:</strong> <?=$resultado_compra['nombre']." ".$resultado_compra['apellido']; ?></li>
            <li class="list-group-item"><strong>DNI:</strong> <?=$resultado_compra['valor_documento']; ?></li>
            <li class="list-group-item"><strong>Teléfono:</strong> <?=$resultado_compra['valor_contacto']; ?></li>
            <li class="list-group-item"><strong>Dirección:</strong> <?=$resultado_compra['nombre_domicilio'].' - Barrio: '.$resultado_compra['nombre_barrio'].' - Localidad: '.$resultado_compra['nombre_localidad'].' - Provincia: '.$resultado_compra['nombre_provincia']; ?></li>
        </ul>

        <h2>Datos del Auto</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Marca:</strong> <?=$resultado_compra['nombre_marca']; ?></li>
            <li class="list-group-item"><strong>Modelo:</strong> <?=$resultado_compra['nombre_modelo']; ?></li>
            <li class="list-group-item"><strong>Año:</strong> <?=$resultado_compra['anio'];?></li>
            <li class="list-group-item"><strong>Color:</strong> <?=$resultado_compra['nombre_descripcion']; ?></li>
        </ul>

        <h2>Datos de la Venta</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Fecha de Venta:</strong> <?=$resultado_compra['fecha_compra']; ?></li>
            <li class="list-group-item"><strong>Tipo de Pago:</strong> <?=$resultado_compra['nombre_pago']; ?></li>
            <li class="list-group-item"><strong>Monto:</strong> $<?=$resultado_compra['precio']; ?></li>
            <li class="list-group-item"><strong>Observaciones:</strong> <?=$resultado_compra['observacion']; ?></li>
        </ul>
    </div>
    <!-- Información adicional -->
    <div style="text-align: center; margin-top: 30px;">
        <p><strong>Fecha de generación:</strong> <?=date('d/m/Y H:i:s');?></p>
        <p><strong>Contacto:</strong> info@carsales.com</p>
        <p>© 2024 Car Sales. Todos los derechos reservados.</p>
    </div>

<?php 
} else {
    echo "<div class='alert alert-danger'>No se encontró la venta.</div>";
}

// Cierra el buffer y almacena el HTML en una variable
$html = ob_get_clean();

use Dompdf\Dompdf;

require '../vendor/autoload.php';

// Crea una nueva instancia de DOMPDF
$dompdf = new Dompdf();

// Carga el HTML en DOMPDF
$dompdf->loadHtml($html);

// Configura el tamaño de la página y la orientación
$dompdf->setPaper('A4', 'portrait');

// Renderiza el PDF
$dompdf->render();

// Muestra o descarga el PDF en el navegador
$dompdf->stream("reporte_venta_compra.pdf", array("Attachment" => false)); // `Attachment => true` para forzar descarga
?>