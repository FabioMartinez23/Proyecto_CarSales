<?php 
require_once('../modelos/usuarios.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start(); // Inicia el buffer de salida

$image_path = '../assets/img/LOGO-Modificado.png'; // Ruta del logo
$image_data = base64_encode(file_get_contents($image_path)); // Convierte la imagen a base64
$image_src = 'data:image/png;base64,' . $image_data; // Prefijo necesario para las imágenes

$usuario = new Usuario();
$resultado_usuario = $usuario->traer_usuario_por_id($_GET['usuario']);

if ($resultado_usuario) { 
?>

    <div class="container mt-5 hacer_padding">

        <div style="text-align: right; margin-top: -50px; margin-right: 10px;">
            <img src="<?= $image_src ?>" alt="Car Sales Logo" style="max-width: 80px;">
        </div>

        <h2 style="text-align: center;">Datos Personales</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Nombre:</strong> <?=$resultado_usuario['nombre']; ?></li>
            <li class="list-group-item"><strong>Apellido:</strong> <?=$resultado_usuario['apellido']; ?></li>
            <li class="list-group-item"><strong>DNI:</strong> <?=$resultado_usuario['valor_documento']; ?></li>
            <li class="list-group-item"><strong>Fecha de Nacimiento:</strong> <?=$resultado_usuario['fecha_nacimiento']; ?></li>
            <li class="list-group-item"><strong>Sexo:</strong> <?=$resultado_usuario['nombre_tipo_sexo']; ?></li>
        </ul>

        <h2 style="text-align: center;">Datos de Contacto</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Tipo de Contacto:</strong> <?=$resultado_usuario['nombre_tipo_contacto']; ?></li>
            <li class="list-group-item"><strong>Contacto:</strong> <?=$resultado_usuario['valor_contacto']; ?></li>
            <li class="list-group-item"><strong>Email:</strong> <?=$resultado_usuario['email']; ?></li>
        </ul>

        <h2 style="text-align: center;">Datos de Domicilio</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Tipo de Domicilio:</strong> <?=$resultado_usuario['nombre_tipo_domicilio']; ?></li>
            <li class="list-group-item"><strong>Dirección:</strong> <?=$resultado_usuario['nombre_domicilio'].' - '.$resultado_usuario['nombre_localidad'].' - '.$resultado_usuario['nombre_provincia']; ?></li>
        </ul>
        
        <!-- Información adicional -->
        <div style="text-align: center; margin-top: 30px;">
            <p><strong>Fecha de generación:</strong> <?=date('d/m/Y H:i:s');?></p>
            <p><strong>Contacto:</strong> info@carsales.com</p>
            <p>© 2024 Car Sales. Todos los derechos reservados.</p>
        </div>
    </div>

<?php 
} else {
    echo "<div class='alert alert-danger'>No se encontró ningun usuario.</div>";
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
$dompdf->stream("Reporte_Usuario.pdf", array("Attachment" => false)); // `Attachment => true` para forzar descarga 
?>
