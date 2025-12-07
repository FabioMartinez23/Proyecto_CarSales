<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$compra = new ComprarVehiculo();
$resultado_compra = $compra->traer_compra_por_id($_GET['idcompra']);

?>

<div class="detalle-container hacer_padding">

<?php if ($resultado_compra) { ?>

    <!-- Título Principal -->
    <div class="detalle-header">
        <h1>Ingreso de Vehículo Nº <?= $resultado_compra['idcompras']; ?></h1>
        <p class="detalle-subtitle">
            <?= $resultado_compra['nombre_marca'] . " " . $resultado_compra['nombre_modelo'] ?>
            (<?= $resultado_compra['anio']; ?>)
        </p>
    </div>

    <!-- SECCIÓN CLIENTE -->
    <div class="detalle-box">
        <h2 class="detalle-box-title">Datos del Cliente</h2>
        <div class="detalle-row"><span>Nombre:</span> <?= $resultado_compra['nombre']." ".$resultado_compra['apellido']; ?></div>
        <div class="detalle-row"><span>DNI:</span> <?= $resultado_compra['valor_documento']; ?></div>
        <div class="detalle-row"><span>Teléfono:</span> <?= $resultado_compra['valor_contacto']; ?></div>
        <div class="detalle-row">
            <span>Dirección:</span>
            <?= $resultado_compra['nombre_domicilio'] ?> - Barrio <?= $resultado_compra['nombre_barrio'] ?>,
            <?= $resultado_compra['nombre_localidad'] ?>, <?= $resultado_compra['nombre_provincia'] ?>
        </div>
    </div>

    <!-- SECCIÓN VEHÍCULO -->
    <div class="detalle-box">
        <h2 class="detalle-box-title">Datos del Vehículo</h2>
        <div class="detalle-row"><span>Marca:</span> <?= $resultado_compra['nombre_marca']; ?></div>
        <div class="detalle-row"><span>Modelo:</span> <?= $resultado_compra['nombre_modelo']; ?></div>
        <div class="detalle-row"><span>Año:</span> <?= $resultado_compra['anio']; ?></div>
        <div class="detalle-row"><span>Color:</span> <?= $resultado_compra['nombre_descripcion']; ?></div>
    </div>

    <!-- SECCIÓN COMPRA -->
    <div class="detalle-box">
        <h2 class="detalle-box-title">Datos del Ingreso</h2>
        <div class="detalle-row">
            <span>Fecha de Ingreso:</span>
            <?php
                $fecha_original = new DateTime($resultado_compra['fecha_compra']);
                echo $fecha_original->format('d-m-Y');
            ?>
        </div>
        <div class="detalle-row"><span>Monto:</span> $<?= number_format($resultado_compra['precio'], 0, ',', '.'); ?></div>
        <div class="detalle-row"><span>Observaciones:</span> <?= $resultado_compra['observacion']; ?></div>
    </div>

    <!-- BOTONES -->
    <div class="detalle-buttons">
        <a href="index.php?page=listado_compras" class="btn-volver">Volver</a>
        <a href="reportes_pdf/acuse_ingreso_consignacion.php?idcompra=<?= $_GET['idcompra']; ?>" target="_blank" class="btn-descargar">
            Descargar Constancia de Ingreso en Consignación
        </a>
    </div>

<?php 
} else {
    echo "<div class='alert alert-danger'>No se encontró la compra.</div>";
}
?>

</div>

