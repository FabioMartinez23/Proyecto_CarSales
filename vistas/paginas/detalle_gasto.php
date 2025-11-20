<?php
session_start();

$gasto = new GastoGeneral();

$id = $_GET['id'] ?? 0;

$info = $gasto->traer_gasto_por_id_detalle($id);

if (!$info || $info->num_rows == 0) {
    echo "<div class='alert alert-danger'>Gasto no encontrado.</div>";
    exit;
}

$data = $info->fetch_assoc();
?>

<link rel="stylesheet" href="assets/css/gastos.css">

<div class="gastos-container">

    <div class="gastos-box">

        <h4 class="gastos-title">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i> Detalle del Gasto
        </h4>

        <!-- DATOS PRINCIPALES -->
        <div class="mb-4">
            <h5 class="mb-3">Información general</h5>
            
            <p><strong>ID gasto:</strong> <?= $data['idgastos_generales'] ?></p>
            <p><strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($data['fecha_gasto'])) ?></p>
            <p><strong>Descripción:</strong> <?= $data['descripcion'] ?></p>
            <p><strong>Monto:</strong> <span class="badge bg-danger">$<?= number_format($data['monto'], 2) ?></span></p>
            <p><strong>Tipo de gasto:</strong> <?= $data['tipo_gasto'] ?></p>
            <p><strong>Usuario:</strong> <?= $data['username'] ?></p>
        </div>

        <!-- ORIGEN -->
        <div class="mb-4 gastos-dynamic-box">
            <h5 class="mb-3">Origen del gasto</h5>
            <p><strong>Origen:</strong> <?= ucfirst($data['origen']) ?></p>

            <?php if ($data['origen'] == 'vehiculo') { ?>
                <p><strong>Vehículo:</strong></p>
                <ul>
                    <li><strong>Patente:</strong> <?= $data['patente'] ?></li>
                    <li><strong>Marca:</strong> <?= $data['marca'] ?></li>
                    <li><strong>Modelo:</strong> <?= $data['modelo'] ?></li>
                </ul>
            <?php } ?>

            <?php if ($data['origen'] == 'venta') { ?>
                <p><strong>Venta asociada:</strong></p>
                <ul>
                    <li><strong>ID Venta:</strong> <?= $data['venta_numero'] ?></li>
                    <li><strong>Vehículo:</strong> <?= $data['venta_marca'].' '.$data['venta_modelo'].' ('.$data['venta_patente'].')' ?></li>
                </ul>
            <?php } ?>

            <?php if ($data['origen'] == 'empleado') { ?>
                <p><strong>Empleado:</strong></p>
                <ul>
                    <li><strong>ID:</strong> <?= $data['empleados_idempleados'] ?></li>
                    <li><strong>Legajo:</strong> <?= $data['legajo'] ?></li>
                    <li><strong>Puesto:</strong> <?= $data['puesto'] ?></li>
                </ul>
            <?php } ?>

            <?php if ($data['origen'] == 'general') { ?>
                <p>No tiene relación con vehículo, venta o empleado.</p>
            <?php } ?>
        </div>

        <!-- MOVIMIENTO DE CAJA -->
        <div class="mb-4">
            <h5 class="mb-3">Movimiento de Caja Asociado</h5>

            <?php if ($data['referencia_movimiento']) { ?>

                <?php
                require_once("modelos/caja.php");
                $caja = new Caja();
                $mov = $caja->traer_movimiento_por_id($data['referencia_movimiento']);
                ?>

                <?php if ($mov && $mov->num_rows > 0) {
                    $m = $mov->fetch_assoc();
                ?>
                    <ul>
                        <li><strong>ID Movimiento:</strong> <?= $m['idcaja_movimientos'] ?></li>
                        <li><strong>Fecha:</strong> <?= $m['fecha_movimiento'] ?></li>
                        <li><strong>Tipo:</strong> <?= $m['tipo'] ?></li>
                        <li><strong>Monto:</strong> $<?= number_format($m['monto'], 2) ?></li>
                        <li><strong>Tipo de pago:</strong> <?= $m['tipo_pago'] ?></li>
                    </ul>

                <?php } else { ?>
                    <p class="text-muted">Movimiento no encontrado.</p>
                <?php } ?>

            <?php } else { ?>
                <p class="text-muted">Este gasto no tiene movimiento asociado.</p>
            <?php } ?>
        </div>


        <!-- VOLVER -->
        <div class="text-end">
            <a href="index.php?page=listado_gastos" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver
            </a>
        </div>

    </div>
</div>
