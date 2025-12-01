<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// PAGINACIÓN
$pagina   = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$porPagina = 15;  // cantidad de vehículos por página
$inicio = ($pagina - 1) * $porPagina;

$reporte = new ReportesCostos();

// Filtros GET
$desde      = $_GET['desde'] ?? '';
$hasta      = $_GET['hasta'] ?? '';
$estado     = $_GET['estado'] ?? '';
$vehiculo   = $_GET['vehiculo'] ?? '';

// Datos principales del reporte
$listaCompleta = $reporte->traer_costos_totales($desde, $hasta, $estado, $vehiculo);

// Total de registros encontrados
$totalRegistros = count($listaCompleta);

// Recortar para la tabla actual
$data = array_slice($listaCompleta, $inicio, $porPagina);
$kpis            = $reporte->traer_kpis_costos($desde, $hasta, $estado, $vehiculo);
$categorias      = $reporte->traer_gastos_por_categoria($desde, $hasta);
$grafico_mensual = $reporte->traer_costos_mensuales($desde, $hasta);
?>

    <!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Costo Totales</li>
    </ol>
</nav>

<div class="container my-4 hacer_padding">

    <h1 class="mb-4 text-center fw-bold">📊 Reporte de Costos Totales</h1>

    <!-- ============================================================
         FILTROS DE BÚSQUEDA
    ============================================================ -->
    <form class="row g-3 mb-4 p-3 bg-light rounded shadow-sm" method="GET">
        <input type="hidden" name="page" value="costos_totales">

        <div class="col-md-3">
            <label class="form-label fw-bold">Desde</label>
            <input type="date" name="desde" class="form-control" value="<?= $desde ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Hasta</label>
            <input type="date" name="hasta" class="form-control" value="<?= $hasta ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Estado del Vehículo</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="disponible"        <?= $estado=='disponible' ? 'selected' : '' ?>>Disponible</option>
                <option value="falta_documento"   <?= $estado=='falta_documento' ? 'selected' : '' ?>>Falta Doc</option>
                <option value="falta_digitalizacion" <?= $estado=='falta_digitalizacion' ? 'selected' : '' ?>>Sin Digitalizar</option>
                <option value="vendido"           <?= $estado=='vendido' ? 'selected' : '' ?>>Vendido</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Patente / ID Vehículo</label>
            <input type="text" name="vehiculo" class="form-control" value="<?= $vehiculo ?>" placeholder="AA123BB / 35">
        </div>

        <div class="col-md-12 text-end mt-2">
            <button class="btn btn-primary">
                <i class="fa-solid fa-filter"></i> Filtrar Resultados
            </button>
        </div>
    </form>
    <div class="mb-3 text-end">
        <a href="export/export_costos_totales_excel.php?desde=<?= $desde ?>&hasta=<?= $hasta ?>&estado=<?= $estado ?>&vehiculo=<?= $vehiculo ?>" 
        class="btn btn-success me-2">
            <i class="fa-solid fa-file-excel"></i> Excel
        </a>

        <a href="export/export_costos_totales_pdf.php?desde=<?= $desde ?>&hasta=<?= $hasta ?>&estado=<?= $estado ?>&vehiculo=<?= $vehiculo ?>"
        class="btn btn-danger">
            <i class="fa-solid fa-file-pdf"></i> PDF
        </a>
    </div>

    <!-- ============================================================
         KPIS PRINCIPALES
    ============================================================ -->
    <div class="row text-center mb-4">

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm p-3 border-0">
                <h4 class="fw-bold">$<?= number_format($kpis['total_gastos'],0,',','.') ?></h4>
                <p class="text-muted">Gastos Totales</p>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm p-3 border-0">
                <h4 class="fw-bold">$<?= number_format($kpis['costo_promedio'],0,',','.') ?></h4>
                <p class="text-muted">Costo Promedio por Vehículo</p>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm p-3 border-0">
                <h4 class="fw-bold"><?= $kpis['cantidad_vehiculos'] ?></h4>
                <p class="text-muted">Vehículos Analizados</p>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm p-3 border-0">
                <?php 
                    $rentColor = $kpis['rentabilidad_promedio'] >= 0 ? 'green' : 'red';
                ?>
                <h4 class="fw-bold" style="color: <?= $rentColor ?>">
                    <?= number_format($kpis['rentabilidad_promedio'],1) ?>%
                </h4>
                <p class="text-muted">Rentabilidad Promedio</p>
            </div>
        </div>

    </div>

    <!-- ============================================================
         GRÁFICOS
    ============================================================ -->
    <div class="row mb-5">

        <div class="col-md-6 mb-4">
            <h5 class="text-center fw-bold">📌 Gastos por Categoría</h5>
            <canvas id="graficoCategorias"></canvas>
        </div>

        <div class="col-md-6 mb-4">
            <h5 class="text-center fw-bold">📆 Evolución Mensual de Costos</h5>
            <canvas id="graficoMensual"></canvas>
        </div>

    </div>

    <?php
    $hayVehiculosSinPrecio = false;

    // Revisamos si hay vehículos sin precio público
    foreach ($data as $item) {
        if ($item['precio_publico'] === null || $item['precio_publico'] == 0) {
            $hayVehiculosSinPrecio = true;
            break;
        }
    }
    ?>
    <?php if ($hayVehiculosSinPrecio): ?>
        <div class="alert alert-warning text-center fw-bold">
            ⚠ Hay vehículos sin <strong>Precio Público</strong>.  
            No se puede calcular el margen ni la rentabilidad correctamente.
        </div>
    <?php endif; ?>

    <!-- ============================================================
         TABLA DETALLADA POR VEHÍCULO
    ============================================================ -->
    <h3 class="mb-3 fw-bold">📋 Detalle por Vehículo</h3>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>Patente</th>
                    <th>Precio Tomado</th>
                    <th>Total Gastos</th>
                    <th>Costo Final</th>
                    <th>Precio Público</th>
                    <th>Margen</th>
                    <th>% Rentabilidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($data)) : ?>
                    <tr><td colspan="9" class="text-center text-muted">No se encontraron resultados.</td></tr>
                <?php endif; ?>

                <?php foreach ($data as $v): ?>

                <?php
                    $rent = floatval($v['rentabilidad']);
                    $color = $rent >= 0 ? 'success' : 'danger';
                ?>

                <tr class="text-center">
                    <td><?= $v['patente'] ?></td>
                    <td>$<?= number_format($v['precio_tomado'],0,',','.') ?></td>
                    <td>$<?= number_format($v['gastos'],0,',','.') ?></td>
                    <td>$<?= number_format($v['costo_final'],0,',','.') ?></td>

                    <!-- PRECIO PÚBLICO -->
                    <td>
                        <?php if ($v['precio_publico'] === null || $v['precio_publico'] == 0): ?>
                            <span class="badge bg-danger">Falta cargar</span>
                        <?php else: ?>
                            $<?= number_format($v['precio_publico'],0,',','.') ?>
                        <?php endif; ?>
                    </td>

                    <!-- MARGEN -->
                    <td>
                        <?php if ($v['precio_publico'] === null || $v['precio_publico'] == 0): ?>
                            <span class="text-muted">—</span>
                        <?php else: ?>
                            $<?= number_format($v['margen'],0,',','.') ?>
                        <?php endif; ?>
                    </td>

                    <!-- RENTABILIDAD -->
                    <td>
                        <?php if ($v['rentabilidad'] === null || $v['precio_publico'] == 0): ?>
                            <span class="text-muted">—</span>
                        <?php else: ?>
                            <?php
                                $rcolor = $v['rentabilidad'] >= 0 ? 'green' : 'red';
                            ?>
                            <strong style="color: <?= $rcolor ?>">
                                <?= number_format($v['rentabilidad'],1) ?>%
                            </strong>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="index.php?page=costo_vehiculo&idvehiculo=<?= $v['idvehiculos'] ?>" 
                        class="btn btn-sm btn-outline-primary">
                            Ver Costos
                        </a>
                    </td>
                </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
            $totalPaginas = ceil($totalRegistros / $porPagina);

            if ($totalPaginas > 1):
            ?>
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center">

                    <!-- ANTERIOR -->
                    <li class="page-item <?= ($pagina <= 1 ? 'disabled' : '') ?>">
                        <a class="page-link" 
                        href="index.php?page=costos_totales&pagina=<?= $pagina-1 ?>&desde=<?= $desde ?>&hasta=<?= $hasta ?>&estado=<?= $estado ?>&vehiculo=<?= $vehiculo ?>">
                        Anterior
                        </a>
                    </li>

                    <!-- NÚMEROS -->
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <li class="page-item <?= ($pagina == $i ? 'active' : '') ?>">
                            <a class="page-link" 
                            href="index.php?page=costos_totales&pagina=<?= $i ?>&desde=<?= $desde ?>&hasta=<?= $hasta ?>&estado=<?= $estado ?>&vehiculo=<?= $vehiculo ?>">
                            <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- SIGUIENTE -->
                    <li class="page-item <?= ($pagina >= $totalPaginas ? 'disabled' : '') ?>">
                        <a class="page-link" 
                        href="index.php?page=costos_totales&pagina=<?= $pagina+1 ?>&desde=<?= $desde ?>&hasta=<?= $hasta ?>&estado=<?= $estado ?>&vehiculo=<?= $vehiculo ?>">
                        Siguiente
                        </a>
                    </li>

                </ul>
            </nav>
        <?php endif; ?>
    </div>

</div>

<!-- ============================================================
     SCRIPTS PARA GRÁFICOS
============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// GASTOS POR CATEGORÍA
new Chart(document.getElementById("graficoCategorias"), {
    type: "pie",
    data: {
        labels: <?= json_encode(array_column($categorias, 'categoria')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($categorias, 'total')) ?>,
            backgroundColor: ["#3754AA","#4BC0C0","#FF6384","#FF9F40","#8E44AD"],
        }]
    }
});

// GASTOS MENSUALES
new Chart(document.getElementById("graficoMensual"), {
    type: "line",
    data: {
        labels: <?= json_encode(array_column($grafico_mensual, 'mes')) ?>,
        datasets: [{
            label: "Costos Mensuales",
            data: <?= json_encode(array_column($grafico_mensual, 'total')) ?>,
            borderColor: "#333",
            backgroundColor: "rgba(51,51,51,0.3)",
            fill: true,
            tension: 0.3
        }]
    }
});
</script>
