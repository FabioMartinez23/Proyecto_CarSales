<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/caja.php");

$caja = new Caja();

// ================================
// Filtros
// ================================
$hoy = date('Y-m-d');
$primer_dia_anio = date('Y-01-01');

$desde = $_GET['desde'] ?? $primer_dia_anio;
$hasta = $_GET['hasta'] ?? $hoy;

// Traer gastos para el período seleccionado
$result = $caja->traer_gastos($desde, $hasta);

// Pasar a array para reusar
$gastos = [];
$total_gastos = 0;
$cantidad_gastos = 0;

// Para gráfico por tipo de gasto
$gastos_por_tipo = [];

if ($result && $result->num_rows > 0) {
    while ($g = $result->fetch_assoc()) {
        $gastos[] = $g;

        $monto = (float)$g['monto'];
        $total_gastos += $monto;
        $cantidad_gastos++;

        $tipo_desc = $g['tipo_gasto'] ?? 'Sin tipo';

        if (!isset($gastos_por_tipo[$tipo_desc])) {
            $gastos_por_tipo[$tipo_desc] = 0;
        }
        $gastos_por_tipo[$tipo_desc] += $monto;
    }
}

$promedio_gasto = $cantidad_gastos > 0
    ? $total_gastos / $cantidad_gastos
    : 0;

// ================================
// Datos para gráfico (por tipo)
// ================================
$labels = array_keys($gastos_por_tipo);
$data_montos = array_values($gastos_por_tipo);
?>

<link rel="stylesheet" href="assets/css/reporte_base.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gastos por período</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-receipt me-2"></i> Gastos por período
            </h2>
            <p class="text-muted mb-0">
                Detalle de <strong>gastos generales</strong> registrados en el sistema, agrupados por tipo y período.
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
            <input type="hidden" name="page" value="reporte_gastos">

            <div class="col-md-4">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta); ?>">
            </div>

            <div class="col-md-4 text-md-end">
                <button type="submit" class="report-btn w-100 mt-2 mt-md-0">
                    <i class="fa-solid fa-filter me-2"></i> Aplicar filtros
                </button>
            </div>
        </form>

        <!-- BOTÓN EXPORTAR PDF (form aparte) -->
        <form id="form-exportar-pdf"
              action="controladores/reportes/reporte_gastos_pdf.controlador.php"
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
                <div class="kpi-label">Cantidad de gastos</div>
                <div class="kpi-value"><?= $cantidad_gastos; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">Total gastado</div>
                <div class="kpi-value">$<?= number_format($total_gastos, 2, ',', '.'); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Gasto promedio</div>
                <div class="kpi-value">$<?= number_format($promedio_gasto, 2, ',', '.'); ?></div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if (count($labels) > 0): ?>
            <canvas id="gastosChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se encontraron gastos en el período seleccionado.
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabla detalle -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle reporte-tabla">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Tipo de gasto</th>
                    <th>Usuario</th>
                    <th class="text-end">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($gastos) > 0): ?>
                    <?php foreach ($gastos as $g): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($g['fecha_gasto'])); ?></td>
                            <td><?= htmlspecialchars($g['descripcion']); ?></td>
                            <td><?= htmlspecialchars($g['tipo_gasto'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($g['username'] ?? ''); ?></td>
                            <td class="text-end">
                                $<?= number_format((float)$g['monto'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted p-4">
                            No hay información para mostrar en este rango de fechas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (count($labels) > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('gastosChart').getContext('2d');

    const labels = <?= json_encode($labels); ?>;
    const dataMontos = <?= json_encode($data_montos); ?>;

    // Instancia global por si la necesitás para algo más
    window.gastosChart = new Chart(ctx, {
        type: 'bar', // si querés doughnut, podés cambiar a 'doughnut'
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total por tipo de gasto',
                    data: dataMontos
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
                y: {
                    beginAtZero: true
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
            if (window.gastosChart) {
                const imgBase64 = window.gastosChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>
