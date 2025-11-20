<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$venta = new VenderVehiculo();
$resultado_venta = $venta->traer_venta_por_id($_GET['idventa']);

$parte_pago = new VentaFormaPago();
$result_parte_pago = $parte_pago->traer_vehiculo_forma_pago($_GET['idventa']);

?>

<div class="detalle-container hacer_padding">

<?php if ($resultado_venta) { ?>

    <!-- HEADER -->
    <div class="detalle-header">
        <h1>Venta Nº <?= $resultado_venta['idventas']; ?></h1>
        <p class="detalle-subtitle">
            <?= $resultado_venta['nombre_marca'] . " " . $resultado_venta['nombre_modelo'] ?>
            (<?= $resultado_venta['anio']; ?>)
        </p>
    </div>


    <!-- SECCIÓN CLIENTE -->
    <div class="detalle-box">
        <h2 class="detalle-box-title">Datos del Cliente</h2>

        <div class="detalle-row"><span>Nombre:</span> <?= $resultado_venta['nombre']." ".$resultado_venta['apellido']; ?></div>
        <div class="detalle-row"><span>DNI:</span> <?= $resultado_venta['valor_documento']; ?></div>
        <div class="detalle-row"><span>Teléfono:</span> <?= $resultado_venta['valor_contacto']; ?></div>

        <div class="detalle-row">
            <span>Dirección:</span>
            <?= $resultado_venta['nombre_domicilio'] ?> - Barrio <?= $resultado_venta['nombre_barrio'] ?>,
            <?= $resultado_venta['nombre_localidad'] ?>, <?= $resultado_venta['nombre_provincia'] ?>
        </div>
    </div>


    <!-- SECCIÓN VEHÍCULO -->
    <div class="detalle-box">
        <h2 class="detalle-box-title">Datos del Vehículo</h2>

        <div class="detalle-row"><span>Marca:</span> <?= $resultado_venta['nombre_marca']; ?></div>
        <div class="detalle-row"><span>Modelo:</span> <?= $resultado_venta['nombre_modelo']; ?></div>
        <div class="detalle-row"><span>Año:</span> <?= $resultado_venta['anio']; ?></div>
        <div class="detalle-row"><span>Color:</span> <?= $resultado_venta['nombre_descripcion']; ?></div>
    </div>


    <!-- SECCIÓN VENTA -->
    <div class="detalle-box">
        <h2 class="detalle-box-title">Datos de la Venta</h2>

        <div class="detalle-row">
            <span>Fecha:</span>
            <?php
                $fecha = new DateTime($resultado_venta['fecha_venta']);
                echo $fecha->format('d-m-Y');
            ?>
        </div>

        <div class="detalle-row"><span>Tipo de Pago:</span> <?= $resultado_venta['nombre_pago']; ?></div>

        <div class="detalle-row">
            <span>Monto Total:</span> $<?= number_format($resultado_venta['precio'], 0, ',', '.'); ?>
        </div>

        <div class="detalle-row"><span>Observaciones:</span> <?= $resultado_venta['observacion']; ?></div>
    </div>


    <!-- SECCIÓN PARTE DE PAGO -->
    <?php if (!empty($result_parte_pago)) { ?>

        <div class="detalle-box">
            <h2 class="detalle-box-title">Vehículo Entregado en Parte de Pago</h2>

            <div class="detalle-row"><span>Marca:</span> <?= $result_parte_pago['nombre_marca']; ?></div>
            <div class="detalle-row"><span>Modelo:</span> <?= $result_parte_pago['nombre_modelo']; ?></div>
            <div class="detalle-row"><span>Tipo:</span> <?= $result_parte_pago['nombre_tipo_vehiculo']; ?></div>

            <div class="detalle-row">
                <span>Valor Tasado:</span> 
                $<?= number_format($result_parte_pago['precio'], 0, ',', '.'); ?>
            </div>
        </div>

        <!-- DIFERENCIA A PAGAR -->
        <?php
            $total_venta = floatval($resultado_venta['precio']);
            $valor_parte = floatval($result_parte_pago['precio']);
            $total_pagar = $total_venta - $valor_parte;
        ?>

        <div class="detalle-box">
            <h2 class="detalle-box-title">Diferencia a Abonar</h2>

            <div class="detalle-row">
                <span>Total a Pagar:</span>
                <span style="font-weight:700; color:#B00020;">
                    $<?= number_format($total_pagar, 0, ',', '.'); ?>
                </span>
            </div>

            <div class="detalle-row">
                <span style="font-weight:600;">Detalle:</span>
                Precio venta $<?= number_format($total_venta, 0, ',', '.') ?> -
                Parte de pago $<?= number_format($valor_parte, 0, ',', '.') ?>
            </div>
        </div>

    <?php } ?>


    <!-- BOTONES -->
    <div class="detalle-buttons">
        <a href="index.php?page=listado_ventas" class="btn-volver">Volver</a>

        <a href="reportes_pdf/detalle_venta.php?idventa=<?= $_GET['idventa']; ?>" 
           target="_blank" 
           class="btn-descargar">
            Descargar Boleto Compra/Venta
        </a>
    </div>

<?php 
} else {
    echo "<div class='alert alert-danger'>No se encontró la venta.</div>";
}
?>

</div>
