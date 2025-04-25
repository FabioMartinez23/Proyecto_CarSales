<?php
require_once('../modelos/vender_vehiculos.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start(); // Inicia el buffer de salida

$image_path = '../assets/img/LOGO-Modificado.png'; // Ruta del logo
$image_data = base64_encode(file_get_contents($image_path)); // Convierte la imagen a base64
$image_src = 'data:image/png;base64,' . $image_data; // Prefijo necesario para las imágenes

$venta = new VenderVehiculo();
$resultado_venta = $venta->traer_venta_por_id($_GET['idventa']);

if ($resultado_venta) { 
?>

    <div class="container mt-5">
        <div style="text-align: right; margin-top: -50px; margin-right: 10px;">
            <img src="<?= $image_src ?>" alt="Car Sales Logo" style="max-width: 80px;">
        </div>
        <h1 class="text-center">Detalles de Venta - Nro <?=$resultado_venta['idventas']; ?></h1>

        <h2>Datos del Cliente</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Nombre:</strong> <?=$resultado_venta['nombre']." ".$resultado_venta['apellido']; ?></li>
            <li class="list-group-item"><strong>DNI:</strong> <?=$resultado_venta['valor_documento']; ?></li>
            <li class="list-group-item"><strong>Teléfono:</strong> <?=$resultado_venta['valor_contacto']; ?></li>
            <li class="list-group-item"><strong>Dirección:</strong> <?=$resultado_venta['nombre_domicilio']; ?></li>
        </ul>

        <h2>Datos del Auto</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Marca:</strong> <?=$resultado_venta['nombre_marca']; ?></li>
            <li class="list-group-item"><strong>Modelo:</strong> <?=$resultado_venta['nombre_modelo']; ?></li>
            <li class="list-group-item"><strong>Año:</strong> <?=$resultado_venta['anio'];?></li>
            <li class="list-group-item"><strong>Color:</strong> <?=$resultado_venta['nombre_descripcion']; ?></li>
        </ul>

        <h2>Datos de la Venta</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Fecha de Venta:</strong> <?=$resultado_venta['fecha_venta']; ?></li>
            <li class="list-group-item"><strong>Tipo de Pago:</strong> <?=$resultado_venta['nombre_pago']; ?></li>
            <li class="list-group-item"><strong>Monto:</strong> $<?=$resultado_venta['precio']; ?></li>
            <li class="list-group-item"><strong>Observaciones:</strong> <?=$resultado_venta['observacion']; ?></li>
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

