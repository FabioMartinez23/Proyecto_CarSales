<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$compra = new ComprarVehiculo();
$resultado_compra = $compra->traer_compra_por_id($_GET['idcompra']);

if ($resultado_compra) { 
?>

    <div class="container mt-5 hacer_padding">
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

        <h2>Datos de la Compra</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item">
                <strong>Fecha de Compra:</strong> 
                <?php
                    $fecha_original = new DateTime($resultado_compra['fecha_compra']);
                    echo $fecha_original->format('d-m-Y');
                ?>
            </li>
            <li class="list-group-item"><strong>Tipo de Pago:</strong> <?=$resultado_compra['nombre_pago']; ?></li>
            <li class="list-group-item"><strong>Monto:</strong> $<?=$resultado_compra['precio']; ?></li>
            <li class="list-group-item"><strong>Observaciones:</strong> <?=$resultado_compra['observacion']; ?></li>
        </ul>

        <div class="text-center">
            <a href="index.php?page=listado_compras" class="btn btn-dark">Volver</a>
            <a href="reportes_pdf/detalle_compra.php?idcompra=<?=$_GET['idcompra'];?>" class="btn btn-success">Descargar en PDF</a>
        </div>
    </div>

<?php 
} else {
    echo "<div class='alert alert-danger'>No se encontró la compra.</div>";
}
?>
