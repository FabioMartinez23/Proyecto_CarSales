<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$venta = new VenderVehiculo();
$resultado_venta = $venta->traer_venta_por_id($_GET['idventa']);

$parte_pago = new VentaFormaPago();
$result_parte_pago = $parte_pago->traer_vehiculo_forma_pago($_GET['idventa']);

if ($resultado_venta) { 
?>

    <div class="container mt-5 hacer_padding">
        <h1 class="text-center">Detalles de Venta - Nro <?=$resultado_venta['idventas']; ?></h1>

        <h2>Datos del Cliente</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Nombre:</strong> <?=$resultado_venta['nombre']." ".$resultado_venta['apellido']; ?></li>
            <li class="list-group-item"><strong>DNI:</strong> <?=$resultado_venta['valor_documento']; ?></li>
            <li class="list-group-item"><strong>Teléfono:</strong> <?=$resultado_venta['valor_contacto']; ?></li>
            <li class="list-group-item"><strong>Barrio:</strong> <?=$resultado_venta['nombre_barrio']; ?></li>
            <li class="list-group-item"><strong>Dirección:</strong> <?=$resultado_venta['nombre_domicilio'].' - Barrio: '.$resultado_venta['nombre_barrio'].' - Localidad: '.$resultado_venta['nombre_localidad'].' - Provincia: '.$resultado_venta['nombre_provincia']; ?></li>
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
            <li class="list-group-item">
                <strong>Fecha de Venta:</strong> 
                <?php
                    $fecha_original = new DateTime($resultado_venta['fecha_venta']);
                    echo $fecha_original->format('d-m-Y');
                ?>
            </li>
            <li class="list-group-item"><strong>Tipo de Pago:</strong> <?=$resultado_venta['nombre_pago']; ?></li>
            <li class="list-group-item"><strong>Monto:</strong> $<?=$resultado_venta['precio']; ?></li>
            <li class="list-group-item"><strong>Observaciones:</strong> <?=$resultado_venta['observacion']; ?></li>
        </ul>

            <?php if (!empty($result_parte_pago)) { ?>
        <h2>Entrega de Vehículo como Parte de Pago</h2>
            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>Marca:</strong> <?=$result_parte_pago['nombre_marca']; ?></li>
                <li class="list-group-item"><strong>Modelo:</strong> <?=$result_parte_pago['nombre_modelo']; ?></li>
                <li class="list-group-item"><strong>Tipo:</strong> <?=$result_parte_pago['nombre_tipo_vehiculo']; ?></li>
                <li class="list-group-item"><strong>Valor Tasado:</strong> $<?=$result_parte_pago['precio']; ?></li>
            </ul>
        <?php } ?>

        <?php 
            if (!empty($result_parte_pago)) {
                $total_venta = floatval(str_replace(['.', ','], ['', '.'], $resultado_venta['precio']));
                $valor_parte_pago = floatval(str_replace(['.', ','], ['', '.'], $result_parte_pago['precio']));
                $total_entrega = $total_venta - $valor_parte_pago;
        ?>
                <div class="mt-4 p-4 bg-light border rounded text-center">
                    <h3 class="text-danger">Total a Pagar (diferencia): $<?= number_format($total_entrega, 2, ',', '.'); ?></h3>
                    <small class="text-muted">(Precio de venta: $<?= number_format($total_venta, 2, ',', '.'); ?> - Parte de pago: $<?= number_format($valor_parte_pago, 2, ',', '.'); ?>)</small>
                </div>
        <?php 
            }
        ?>

        <div class="text-center">
            <a href="index.php?page=listado_ventas" class="btn btn-dark">Volver</a>
            <a href="reportes_pdf/detalle_venta.php?idventa=<?=$_GET['idventa'];?>" class="btn btn-success">Descargar en PDF</a>
        </div>
    </div>

<?php 
} else {
    echo "<div class='alert alert-danger'>No se encontró la venta.</div>";
}
?>

