<?php
require_once('../modelos/anular_operaciones.php');
require_once('../modelos/ventas_forma_pagos.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start(); // Inicia el buffer de salida

$image_path = '../assets/img/LOGO-Modificado.png'; // Ruta del logo
$image_data = base64_encode(file_get_contents($image_path)); // Convierte la imagen a base64
$image_src = 'data:image/png;base64,' . $image_data; // Prefijo necesario para las imágenes

/*$parte_pago = new VentaFormaPago();
$result_parte_pago = $parte_pago->traer_vehiculo_forma_pago($_GET['idventa']);*/

$anulacion = new AnularOperacion();
$resultado_anulacion = $anulacion->traer_venta_anulada_id($_GET['idanulacion']);

if ($resultado_anulacion) { 
?>

    <div class="container mt-5">
        <div style="text-align: right; margin-top: -50px; margin-right: 10px;">
            <img src="<?= $image_src ?>" alt="Car Sales Logo" style="max-width: 80px;">
        </div>
        <h1 class="text-center">Detalles de Anulación de la Venta - Nro <?=$resultado_anulacion['ventas_idventas']; ?></h1>
        <ul class="list-group mb-4">
            <li class="list-group-item">
                <strong>Fecha de Anulación:</strong> 
                <?php
                    $fecha_original = new DateTime($resultado_anulacion['fecha_anulacion']);
                    echo $fecha_original->format('d-m-Y');
                ?>
            </li>
            <li class="list-group-item"><strong>Motivo de la Anulación:</strong> <?=$resultado_anulacion['descripcion']; ?></li>
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
