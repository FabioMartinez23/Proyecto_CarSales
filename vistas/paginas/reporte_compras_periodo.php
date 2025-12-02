<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/comprar_vehiculos.php");

$compras = new ComprarVehiculo();

// ================================
// Filtros por fecha
// ================================
$hoy = date('Y-m-d');
$primer_dia_anio = date('Y-01-01');

$desde = $_GET['desde'] ?? $primer_dia_anio;
$hasta = $_GET['hasta'] ?? $hoy;

// Traer resumen de ingresos agrupados por mes/año
$result = $compras->reporte_ingresos_por_periodo($desde, $hasta);

// Pasar el result a un array para reusar
$filas = [];
$total_ingresos_global = 0;
$total_importe_global  = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $filas[] = $r;
        $total_ingresos_global += (int)$r['cantidad_ingresos'];
        $total_importe_global  += (float)$r['total_tomado'];
    }
}

$valor_promedio_global = $total_ingresos_global > 0
    ? $total_importe_global / $total_ingresos_global
    : 0;

// Datos para el gráfico
$labels = [];
$data_totales   = [];
$data_cantidades = [];

foreach ($filas as $f) {
    $labels[]         = $f['periodo_legible'];
    $data_totales[]   = round($f['total_tomado'], 2);
    $data_cantidades[] = (int)$f['cantidad_ingresos'];
}
?>

<link rel="stylesheet" href="assets/css/reporte_base.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ingresos por período</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-calendar-check me-2"></i> Ingresos por período (Mes / Año)
            </h2>
            <p class="text-muted mb-0">
                Analiza la cantidad de <strong>vehículos ingresados en consignación</strong> agrupados por mes y año.
            </p>
        </div>
        <div class="reporte-ventas-badge">
            <span class="badge rounded-pill bg-report-main">
                Período actual: <?= date('d/m/Y', strtotime($desde)); ?> al <?= date('d/m/Y', strtotime($hasta)); ?>
            </span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="reporte-filtros mb-4">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="reporte_compras_periodo">

            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Agrupación</label>
                <select class="form-select" disabled>
                    <option>Mensual (Mes/Año)</option>
                </select>
            </div>

            <div class="col-md-3 text-md-end">
                <button type="submit" class="report-btn w-100 mt-2 mt-md-0">
                    <i class="fa-solid fa-filter me-2"></i> Aplicar filtros
                </button>
            </div>
        </form>

        <!-- BOTÓN EXPORTAR PDF (form aparte) -->
        <form id="form-exportar-pdf"
              action="controladores/reportes/reporte_compras_periodo_pdf.controlador.php"
              method="POST" target="_blank" class="mt-3">

            <!-- Envío los mismos filtros que se están usando -->
            <input type="hidden" name="desde" value="<?= htmlspecialchars($desde) ?>">
            <input type="hidden" name="hasta" value="<?= htmlspecialchars($hasta) ?>">

            <!-- Acá voy a guardar la imagen del gráfico en base64 -->
            <input type="hidden" name="chart_img" id="chart_img">

            <button type="button" id="btn-exportar-pdf" class="btn btn-danger">
                <i class="fa fa-file-pdf me-1"></i> Exportar a PDF
            </button>
        </form>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="kpi-card kpi-ventas">
                <div class="kpi-label">Cantidad de ingresos</div>
                <div class="kpi-value"><?= $total_ingresos_global; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">Total tomado</div>
                <div class="kpi-value">
                    $<?= number_format($total_importe_global, 2, ',', '.'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Valor promedio por ingreso</div>
                <div class="kpi-value">
                    $<?= number_format($valor_promedio_global, 2, ',', '.'); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if (count($filas) > 0): ?>
            <canvas id="ingresosPeriodoChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se encontraron ingresos en el período seleccionado.
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabla detalle por mes/año -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle reporte-tabla">
            <thead class="table-dark">
                <tr>
                    <th>Período (Mes/Año)</th>
                    <th class="text-center">Cantidad de ingresos</th>
                    <th class="text-end">Total tomado</th>
                    <th class="text-end">Valor promedio</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($filas) > 0): ?>
                    <?php foreach ($filas as $f): ?>
                        <tr>
                            <td><?= htmlspecialchars($f['periodo_legible']); ?></td>
                            <td class="text-center"><?= (int)$f['cantidad_ingresos']; ?></td>
                            <td class="text-end">
                                $<?= number_format($f['total_tomado'], 2, ',', '.'); ?>
                            </td>
                            <td class="text-end">
                                $<?= number_format($f['valor_promedio'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted p-4">
                            No hay información para mostrar en este rango de fechas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <?php if (count($filas) > 0): ?>
            <tfoot>
                <tr>
                    <th>Total período</th>
                    <th class="text-center"><?= $total_ingresos_global; ?></th>
                    <th class="text-end">
                        $<?= number_format($total_importe_global, 2, ',', '.'); ?>
                    </th>
                    <th class="text-end">
                        $<?= number_format($valor_promedio_global, 2, ',', '.'); ?>
                    </th>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php if (count($filas) > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('ingresosPeriodoChart').getContext('2d');

    const labels        = <?= json_encode($labels); ?>;
    const dataTotales   = <?= json_encode($data_totales); ?>;
    const dataCantidades = <?= json_encode($data_cantidades); ?>;

    // Instancia global para luego usarla al generar el PDF
    window.ingresosPeriodoChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Total tomado ($)',
                    data: dataTotales,
                    yAxisID: 'y1'
                },
                {
                    type: 'line',
                    label: 'Cantidad de ingresos',
                    data: dataCantidades,
                    yAxisID: 'y2',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y1: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true
                },
                y2: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // ==== LÓGICA DEL BOTÓN EXPORTAR PDF ====
    const btnPdf        = document.getElementById('btn-exportar-pdf');
    const formPdf       = document.getElementById('form-exportar-pdf');
    const inputChartImg = document.getElementById('chart_img');

    if (btnPdf && formPdf && inputChartImg) {
        btnPdf.addEventListener('click', function () {
            if (window.ingresosPeriodoChart) {
                const imgBase64 = window.ingresosPeriodoChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>
