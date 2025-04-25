<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$venta = new VenderVehiculo();
$resultado_venta = $venta->traer_venta_por_id($_GET['idventa']);

if ($resultado_venta) { 
?>

    <div class="container mt-5 hacer_padding">
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

