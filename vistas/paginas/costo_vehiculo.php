<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('controladores/reportes/costos_vehiculo.controlador.php');

if (!isset($_GET['idvehiculo'])) {
    header("Location: index.php?page=listado_vehiculos&mensaje=Vehículo no especificado&status=error");
    exit;
}

$idvehiculo = intval($_GET['idvehiculo']);

$controller = new CostosVehiculoControlador();
$datos_costos = $controller->procesar($idvehiculo);

$v = new Vehiculos();
$info_vehiculo = $v->traer_vehiculo_por_id($idvehiculo);

$esVendido = $datos_costos['tiene_venta'];
$det = $datos_costos['detalle_venta'] ?? null;
?>

    <nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-glass">
            <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Dashboard</a></li>

            <?php 
            if ($_GET['e'] == 'lv') {
                echo '<li class="breadcrumb-item"><a href="index.php?page=listado_vehiculos">Listado Vehiculos</a></li>';
            } elseif ($_GET['e'] == 'lfc') {
                echo '<li class="breadcrumb-item"><a href="index.php?page=listado_falta_documentacion">Listado Falta Documentacion</a></li>';
            } elseif ($_GET['e'] == 'lvn') {
                echo '<li class="breadcrumb-item"><a href="index.php?page=listado_ventas">Listado Ventas</a></li>';
            }
             else {
                echo '<li class="breadcrumb-item"><a href="#">Costos</a></li>';
            }
            ?>

            <li class="breadcrumb-item active" aria-current="page">Costo de Vehiculo</li>
        </ol>
    </nav>

<div class="costos-container">

    <!-- ENCABEZADO -->
    <div class="costos-header">
        <h2>Costos del Vehículo</h2>

        <?php if ($info_vehiculo): ?>
            <p class="costos-subtitle">
                <strong><?= $info_vehiculo['patente'] ?></strong> · 
                <?= $info_vehiculo['nombre_marca'] . ' ' . $info_vehiculo['nombre_modelo'] ?> · 
                <?= $info_vehiculo['anio'] ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- CUADROS SUPERIORES -->
    <div class="costos-grid">

        <div class="costos-item">
            <span class="costos-label">Precio Tomado</span>
            <div class="costos-value primary">
                $<?= number_format($datos_costos['precio_tomado'], 0, ',', '.') ?>
            </div>
        </div>

        <div class="costos-item">
            <span class="costos-label">Gastos del Vehículo</span>
            <div class="costos-value danger">
                $<?= number_format($datos_costos['gastos_vehiculo'], 0, ',', '.') ?>
            </div>
        </div>

        <div class="costos-item">
            <span class="costos-label"><?= $esVendido ? 'Precio de Venta' : 'Precio Público' ?></span>
            <div class="costos-value success">
                $<?= number_format($esVendido ? $datos_costos['precio_venta'] : $datos_costos['precio_publico'], 0, ',', '.') ?>
            </div>
        </div>

        <div class="costos-item">
            <span class="costos-label"><?= $esVendido ? 'Rentabilidad Real' : 'Rentabilidad Proyectada' ?></span>

            <?php 
                $rent = $esVendido ? $det['rentabilidad_real'] : $datos_costos['rentabilidad'];
                $rcolor = $rent >= 0 ? "success" : "danger";
            ?>

            <div class="costos-value <?= $rcolor ?>">
                <?= number_format($rent, 2, ',', '.') ?>%
            </div>
        </div>

    </div>

    <!-- PROYECCIÓN O DETALLE -->
    <?php if (!$esVendido): ?>
        <div class="costos-box">
            <h4 class="costos-box-title">🔍 Proyección de Rentabilidad</h4>

            <p><strong>Costo Total:</strong> 
                $<?= number_format($datos_costos['costo_total'], 0, ',', '.') ?>
            </p>

            <p><strong>Ganancia Proyectada:</strong> 
                <span class="fw-bold <?= ($datos_costos['ganancia_proyectada'] >= 0 ? 'text-success' : 'text-danger') ?>">
                    $<?= number_format($datos_costos['ganancia_proyectada'], 0, ',', '.') ?>
                </span>
            </p>
        </div>

    <?php else: ?>

        <div class="costos-box">
            <h4 class="costos-box-title">🧾 Detalle de Rentabilidad (Venta ID <?= $det['idventa'] ?>)</h4>

            <div class="costos-detail-list">

                <div class="costos-detail">
                    <span>Costo Total Real</span>
                    <strong>$<?= number_format($det['costo_total_real'], 0, ',', '.') ?></strong>
                </div>

                <div class="costos-detail">
                    <span>Precio de Venta</span>
                    <strong>$<?= number_format($det['precio_venta'], 0, ',', '.') ?></strong>
                </div>

                <div class="costos-detail">
                    <span>Ganancia Bruta</span>
                    <strong>$<?= number_format($det['ganancia_bruta_real'], 0, ',', '.') ?></strong>
                </div>

                <div class="costos-detail">
                    <span>Comisión Empleado</span>
                    <strong>$<?= number_format($det['monto_empleado'], 0, ',', '.') ?></strong>
                </div>

                <div class="costos-detail">
                    <span>Ganancia Neta</span>
                    <strong class="text-success">
                        $<?= number_format($det['ganancia_neta_conce'], 0, ',', '.') ?>
                    </strong>
                </div>

            </div>
        </div>
    <?php endif; ?>

    <!-- TABLA DE GASTOS -->
    <?php if (!empty($datos_costos['detalle_gastos'])): ?>
        <div class="costos-box">
            <h4 class="costos-box-title">🛠 Gastos Cargados al Vehículo</h4>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos_costos['detalle_gastos'] as $g): ?>
                            <tr>
                                <td><?= $g['fecha_gasto'] ?></td>
                                <td><?= $g['tipo_gasto'] ?></td>
                                <td>$<?= number_format($g['monto'], 0, ',', '.') ?></td>
                                <td><?= $g['descripcion'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- BOTONES -->
    <div class="costos-buttons">
        <a href="index.php?page=listado_vehiculos" class="btn btn-secondary">Volver al Listado</a>
        <a href="index.php" class="btn btn-outline-primary">Dashboard</a>
    </div>

    <div class="costos-export">
        <a href="../../export/export_costo_vehiculo_excel.php?id=<?= $idvehiculo ?>" class="btn btn-success">
            <i class="fa-solid fa-file-excel"></i> Excel
        </a>
        <a href="../../export/export_costo_vehiculo_pdf.php?id=<?= $idvehiculo ?>" class="btn btn-danger">
            <i class="fa-solid fa-file-pdf"></i> PDF
        </a>
    </div>

</div>
