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

    // Perfil del usuario (Administrador / Empleado / Cliente)
    $perfil_nombre = $resultado_usuario['nombre_perfil'] ?? 'Sin perfil';
    $perfil_slug   = strtolower(str_replace(' ', '-', $perfil_nombre));

    // Mostrar datos laborales para Administrador y Empleado
    $perfil_normalizado = strtolower($perfil_nombre);
    $tieneDatosLaborales = in_array($perfil_normalizado, ['administrador', 'empleado']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuario</title>
    <style>
        /* ===========================
           CONFIGURACIÓN GENERAL PDF
           =========================== */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333333;
            margin: 0;
            padding: 0;
        }

        .pdf-wrapper {
            max-width: 720px;
            margin: 20px auto;
            padding: 20px 28px;
            border: 1px solid #dddddd;
            border-radius: 8px;
        }

        /* ===========================
           ENCABEZADO
           =========================== */
        .pdf-header {
            position: relative;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .pdf-logo {
            position: absolute;
            right: 0;
            top: 0;
        }

        .pdf-logo img {
            max-width: 90px;
        }

        .pdf-title-block {
            text-align: left;
            padding-right: 110px; /* espacio para logo */
        }

        .pdf-subtitle {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            color: #888888;
            margin: 0 0 4px;
        }

        .pdf-title {
            font-size: 20px;
            margin: 0 0 4px;
            font-weight: 700;
            color: #222222;
        }

        .pdf-user-name {
            font-size: 13px;
            color: #555555;
            margin: 0 0 4px;
        }

        /* Badge de perfil */
        .pdf-role-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border: 1px solid transparent;
            margin-top: 6px;
        }

        .role-administrador {
            background-color: #fdecea;
            color: #b02a37;
            border-color: #f5c2c7;
        }

        .role-empleado {
            background-color: #e7f1ff;
            color: #0b5ed7;
            border-color: #b6d4fe;
        }

        .role-cliente {
            background-color: #e9f7ef;
            color: #157347;
            border-color: #badbcc;
        }

        /* ===========================
           SECCIONES
           =========================== */
        .section {
            margin-top: 18px;
        }

        .section-title {
            background-color: #f3f3f3;
            border: 1px solid #dddddd;
            padding: 6px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 13px;
            color: #333333;
            margin: 0 0 6px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .detail-table th,
        .detail-table td {
            padding: 5px 8px;
            vertical-align: top;
        }

        .detail-table th {
            width: 35%;
            font-weight: 600;
            color: #444444;
            text-align: left;
        }

        .detail-table td {
            width: 65%;
            color: #333333;
        }

        /* rayitas sutiles entre filas */
        .detail-table tr + tr th,
        .detail-table tr + tr td {
            border-top: 1px solid #eeeeee;
        }

        /* ===========================
           FOOTER
           =========================== */
        .pdf-footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 11px;
            color: #777777;
        }

        .pdf-footer p {
            margin: 2px 0;
        }

        .pdf-footer strong {
            color: #555555;
        }
    </style>
</head>
<body>

<div class="pdf-wrapper">

    <!-- ENCABEZADO -->
    <header class="pdf-header">
        <div class="pdf-logo">
            <img src="<?= $image_src ?>" alt="Car Sales Logo">
        </div>
        <div class="pdf-title-block">
            <p class="pdf-subtitle">Reporte de usuario</p>
            <h1 class="pdf-title">Detalle de Usuario</h1>
            <p class="pdf-user-name">
                <?= htmlspecialchars($resultado_usuario['apellido'] . ' ' . $resultado_usuario['nombre']); ?>
            </p>

            <!-- Badge de perfil -->
            <span class="pdf-role-badge role-<?= $perfil_slug; ?>">
                <?= htmlspecialchars($perfil_nombre); ?>
            </span>
        </div>
    </header>

    <!-- DATOS PERSONALES -->
    <section class="section">
        <h2 class="section-title">Datos Personales</h2>
        <table class="detail-table">
            <tr>
                <th>Perfil</th>
                <td><?= htmlspecialchars($perfil_nombre); ?></td>
            </tr>
            <tr>
                <th>Nombre</th>
                <td><?= htmlspecialchars($resultado_usuario['nombre']); ?></td>
            </tr>
            <tr>
                <th>Apellido</th>
                <td><?= htmlspecialchars($resultado_usuario['apellido']); ?></td>
            </tr>
            <tr>
                <th>DNI</th>
                <td><?= htmlspecialchars($resultado_usuario['valor_documento']); ?></td>
            </tr>
            <tr>
                <th>Fecha de Nacimiento</th>
                <td><?= htmlspecialchars($resultado_usuario['fecha_nacimiento']); ?></td>
            </tr>
            <tr>
                <th>Sexo</th>
                <td><?= htmlspecialchars($resultado_usuario['nombre_tipo_sexo']); ?></td>
            </tr>
        </table>
    </section>

    <!-- DATOS LABORALES (ADMIN / EMPLEADO) -->
    <?php if ($tieneDatosLaborales): ?>
    <section class="section">
        <h2 class="section-title">Datos Laborales</h2>
        <table class="detail-table">
            <tr>
                <th>Legajo</th>
                <td><?= htmlspecialchars($resultado_usuario['legajo'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Puesto</th>
                <td><?= htmlspecialchars($resultado_usuario['nombre_puesto'] ?? ''); ?></td>
            </tr>
        </table>
    </section>
    <?php endif; ?>

    <!-- DATOS DE CONTACTO -->
    <section class="section">
        <h2 class="section-title">Datos de Contacto</h2>
        <table class="detail-table">
            <tr>
                <th>Tipo de Contacto</th>
                <td><?= htmlspecialchars($resultado_usuario['nombre_tipo_contacto']); ?></td>
            </tr>
            <tr>
                <th>Contacto</th>
                <td><?= htmlspecialchars($resultado_usuario['valor_contacto']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($resultado_usuario['email']); ?></td>
            </tr>
        </table>
    </section>

    <!-- DATOS DE DOMICILIO -->
    <section class="section">
        <h2 class="section-title">Datos de Domicilio</h2>
        <table class="detail-table">
            <tr>
                <th>Tipo de Domicilio</th>
                <td><?= htmlspecialchars($resultado_usuario['nombre_tipo_domicilio']); ?></td>
            </tr>
            <tr>
                <th>Dirección</th>
                <td>
                    <?= htmlspecialchars(
                        $resultado_usuario['nombre_domicilio'] .
                        ' - Barrio ' . $resultado_usuario['nombre_barrio'] .
                        ' - ' . $resultado_usuario['nombre_localidad'] .
                        ' - ' . $resultado_usuario['nombre_provincia']
                    ); ?>
                </td>
            </tr>
        </table>
    </section>

    <!-- FOOTER -->
    <footer class="pdf-footer">
        <p><strong>Fecha de generación:</strong> <?= date('d/m/Y H:i:s'); ?></p>
        <p><strong>Contacto:</strong> info@carsales.com</p>
        <p>© <?= date('Y'); ?> Car Sales. Todos los derechos reservados.</p>
    </footer>

</div>

</body>
</html>

<?php 
} else {
    echo "<p>No se encontró ningún usuario.</p>";
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

// Mostrar en el navegador (Attachment => true para forzar descarga)
$dompdf->stream("Reporte_Usuario.pdf", array("Attachment" => false));
?>
