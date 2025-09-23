<?php
require_once('../modelos/vender_vehiculos.php');
require_once('../modelos/ventas_forma_pagos.php');
require_once('../modelos/usuarios.php'); // Para traer datos del empleado

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();

use Dompdf\Dompdf;
require '../vendor/autoload.php';

$image_path = '../assets/img/LOGO-Modificado.png'; 
$image_data = base64_encode(file_get_contents($image_path)); 
$image_src = 'data:image/png;base64,' . $image_data; 

// Traer datos de la venta
$venta = new VenderVehiculo();
$resultado_venta = $venta->traer_venta_por_id($_GET['idventa']);

// Traer datos parte de pago (si corresponde)
$parte_pago = new VentaFormaPago();
$result_parte_pago = $parte_pago->traer_vehiculo_forma_pago($_GET['idventa']);

// Traer datos del empleado vendedor (según sesión)
session_start();
$id_empleado = $_SESSION['idusuarios']; 
$usuario = new Usuario();
$resultado_empleado = $usuario->traer_usuario_por_id($id_empleado);

if ($resultado_venta && $resultado_empleado) { 
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Boleto de Compra Venta Automotor</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; margin: 25px; }
    h1 { text-align: center; font-size: 18px; text-decoration: underline; margin-bottom: 15px; }
    .logo { text-align: right; margin-top: -40px; }
    .section { margin-top: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 5px; }
    td { padding: 4px; vertical-align: top; }
    .vehiculo { border: 1px solid #000; margin-top: 10px; }
    .vehiculo td { border: 1px solid #000; }
    .legal { font-size: 10px; text-align: justify; margin-top: 20px; }
    .observ { border: 1px solid #000; padding: 6px; min-height: 50px; margin-top: 5px; }
    .firmas-bloque { margin-top: 40px; }
    .firmas-tabla { width: 100%; margin-top: 10px; }
    .firmas-tabla td { width: 50%; vertical-align: top; padding: 5px; }
    .firma-linea { margin-top: 40px; text-align: center; }
    .firma-linea span { display: inline-block; border-top: 1px solid #000; padding-top: 2px; width: 200px; }
  </style>
</head>
<body>

  <div class="logo">
    <img src="<?= $image_src ?>" alt="Car Sales Logo" style="max-width: 80px;">
  </div>

  <h1>BOLETO DE COMPRA VENTA AUTOMOTOR</h1>

  <p>
    Conste en el presente que entre el Señor: <?= $resultado_empleado['nombre'].' '.$resultado_empleado['apellido']; ?> como <strong>VENDEDOR</strong> 
    y el Señor: <?= $resultado_venta['nombre'].' '.$resultado_venta['apellido']; ?> como <strong>COMPRADOR</strong> 
    se conviene lo siguiente:
  </p>

  <p>
    El Señor: <?= $resultado_empleado['nombre'].' '.$resultado_empleado['apellido']; ?> vende un: 
    <strong>VEHÍCULO AUTOMOTOR</strong> en las condiciones vistas.
  </p>

  <table class="vehiculo">
    <tr>
      <td>Marca: <?= $resultado_venta['nombre_marca']; ?></td>
      <td>Modelo: <?= $resultado_venta['nombre_modelo']; ?></td>
      <td>Tipo: <?= $resultado_venta['nombre_tipo']; ?></td>
      <td>Año: <?= $resultado_venta['anio']; ?></td>
    </tr>
    <tr>
      <td>Motor Nº: <?= $resultado_venta['motor']; ?></td>
      <td>Chasis Nº: <?= $resultado_venta['chasis']; ?></td>
      <td>Dominio: <?= $resultado_venta['patente']; ?></td>
      <td></td>
    </tr>
  </table>

  <p>
    En la suma de pesos: $<?= $resultado_venta['precio']; ?><br>
    Pagaderos de la siguiente forma: <?= $resultado_venta['nombre_pago']; ?>
  </p>

  <div class="legal">
    Esta unidad se entrega en el estado de uso en que se encuentra y que el comprador declara conocer, al igual que todo
    lo concerniente a la marca, modelo, números de motor y/o chasis del referido vehículo, que ha sido revisado y
    constatado y acepta de plena conformidad, haciéndose responsable civil y criminalmente, a partir de la fecha y hora de
    efectuada esta venta por cualquier accidente, daño y/o perjuicio que pudiera ocasionar el vehículo que es recibido en
    este acto con su documentación completa y al día. El comprador se compromete a efectuar la correspondiente
    transferencia de dominio del vehículo dentro de los _____ días de la fecha, de acuerdo a lo establecido al respecto por
    la ley 22.977 y sus normas complementarias, interpretativas y/o complementarias, estando a su exclusivo cargo la
    totalidad de los gastos que demande la misma y los trámites y gestiones pertinentes, incluyendo la firma del formulario
    08 o el que a tales fines lo subsista y/o reemplace y/o el otorgamiento de los poderes, todos ello en forma directa con
    el titular dominial. Transcurrido dicho plazo sin que realizara la transferencia el vendedor no se responsabiliza por los
    inconvenientes de cualquier índole que pudieran existir anteriores o posteriores a la fecha, que imposibilitan la
    efectivización de dicho trámite, incluyendo embargos y/o prendas o medidas judiciales de cualquier tipo sobre el
    vehículo, al igual que deudas emergentes de patentes municipales y/o multas. Con absoluta conformidad del Comprador.
  </div>

    <?php
    $fechaVenta = new DateTime($resultado_venta['fecha_venta']);
    $dia = $fechaVenta->format('d');
    $anio = $fechaVenta->format('Y');

    // Array de meses en español
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    $mesTexto = $meses[(int)$fechaVenta->format('m')];
    ?>
    <p>
    En: Formosa Capital a los: <?= $dia; ?> del mes de <?= $mesTexto; ?> año: <?= $anio; ?> 
    se firman dos ejemplares del mismo tenor y a un solo efecto.
    </p>

  <div class="section">
    <strong>OBSERVACIONES:</strong>
    <div class="observ"> <?= $resultado_venta['observacion']; ?> </div>
  </div>

  <div class="firmas-bloque">
    <table class="firmas-tabla">
      <tr>
        <td><strong>COMPRADOR</strong></td>
        <td><strong>VENDEDOR</strong></td>
      </tr>
      <tr>
        <td>Nombre y Apellido: <?= $resultado_venta['nombre'].' '.$resultado_venta['apellido']; ?></td>
        <td>Nombre y Apellido: <?= $resultado_empleado['nombre'].' '.$resultado_empleado['apellido']; ?></td>
      </tr>
      <tr>
        <td>DNI: <?= $resultado_venta['valor_documento']; ?></td>
        <td>DNI: <?= $resultado_empleado['valor_documento']; ?></td>
      </tr>
      <tr>
        <td>Domicilio: <?= $resultado_venta['nombre_domicilio'].' - Barrio '.$resultado_venta['nombre_barrio']; ?></td>
        <td>Domicilio: <?= $resultado_empleado['nombre_domicilio']; ?></td>
      </tr>
      <tr>
        <td>Localidad: <?= $resultado_venta['nombre_localidad'].', '.$resultado_venta['nombre_provincia']; ?></td>
        <td>Localidad: <?= $resultado_empleado['nombre_localidad'].', '.$resultado_empleado['nombre_provincia']; ?></td>
      </tr>
      <tr>
        <td>Teléfono: <?= $resultado_venta['valor_contacto']; ?></td>
        <td>Teléfono: <?= $resultado_empleado['valor_contacto']; ?></td>
      </tr>
    </table>

    <div class="firma-linea">
      <span>Firma</span>
      <span style="margin-left:200px;">Firma</span>
    </div>
  </div>

  <div style="text-align:center; margin-top:10px; font-size:11px;">
    <p><strong>Fecha de generación:</strong> <?= date('d/m/Y H:i:s'); ?></p>
    <p><strong>Contacto:</strong> info@carsales.com</p>
    <p>© 2024 Car Sales. Todos los derechos reservados.</p>
  </div>

</body>
</html>
<?php
} else {
  echo "<div class='alert alert-danger'>No se encontró la compra.</div>";
}

$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("boleto_compra_venta.pdf", array("Attachment" => false));
?>
