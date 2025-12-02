<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/vender_vehiculos.php");

$ventas = new VenderVehiculo();

// ================================
// Filtros por fecha
// ================================
$hoy            = date('Y-m-d');
$primer_dia_anio = date('Y-01-01');

$desde = $_GET['desde'] ?? $primer_dia_anio;
$hasta = $_GET['hasta'] ?? $hoy;

// Traer datos agrupados por método de pago
$result = $ventas->reporte_ventas_por_metodo_pago($desde, $hasta);

// Pasar a array
$metodos              = [];
$total_ventas_global  = 0;
$total_importe_global = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $metodos[] = $r;
        $total_ventas_global  += (int)$r['cantidad_ventas'];
        $total_importe_global += (float)$r['total_vendido'];
    }
}

$ticket_promedio_global = $total_ventas_global > 0
    ? $total_importe_global / $total_ventas_global
    : 0;

// Datos para gráfico
$labels       = [];
$dataCant     = [];
$dataImporte  = [];

foreach ($metodos as $m) {
    $labels[]      = $m['metodo_pago'];
    $dataCant[]    = (int)$m['cantidad_ventas'];
    $dataImporte[] = round((float)$m['total_vendido'], 2);
}
?>

<link rel="stylesheet" href="assets/css/reporte_ventas_periodo.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ventas por método de pago</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-credit-card me-2"></i> Ventas por método de pago
            </h2>
            <p class="text-muted mb-0">
                Distribución de las <strong>ventas realizadas</strong> según el método de pago elegido por los clientes.
            </p>
        </div>
        <div class="reporte-ventas-badge">
            <span class="badge rounded-pill bg-report-main">
                Período: <?= date('d/m/Y', strtotime($desde)); ?> al <?= date('d/m/Y', strtotime($hasta)); ?>
            </span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="reporte-filtros mb-4">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="reporte_ventas_metodo_pago">

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

        <!-- BOTÓN EXPORTAR PDF -->
        <form id="form-exportar-pdf"
              action="controladores/reportes/reporte_ventas_metodo_pago_pdf.controlador.php"
              method="POST" target="_blank" class="mt-3">

            <!-- Mismos filtros -->
            <input type="hidden" name="desde" value="<?= htmlspecialchars($desde); ?>">
            <input type="hidden" name="hasta" value="<?= htmlspecialchars($hasta); ?>">

            <!-- Imagen del gráfico -->
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
                <div class="kpi-label">Cantidad total de ventas</div>
                <div class="kpi-value"><?= $total_ventas_global; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">Total vendido</div>
                <div class="kpi-value">$<?= number_format($total_importe_global, 2, ',', '.'); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Ticket promedio global</div>
                <div class="kpi-value">$<?= number_format($ticket_promedio_global, 2, ',', '.'); ?></div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if (count($labels) > 0): ?>
            <canvas id="metodoPagoChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se registran ventas en el período seleccionado.
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabla detalle -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle reporte-tabla">
            <thead class="table-dark">
                <tr>
                    <th>Método de pago</th>
                    <th class="text-center">Cantidad de ventas</th>
                    <th class="text-end">Total vendido</th>
                    <th class="text-end">Ticket promedio</th>
                    <th class="text-center">% sobre ventas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($metodos) > 0): ?>
                    <?php foreach ($metodos as $m): 
                        $cant = (int)$m['cantidad_ventas'];
                        $total = (float)$m['total_vendido'];
                        $ticket = (float)$m['ticket_promedio'];
                        $porc = $total_ventas_global > 0 ? ($cant * 100 / $total_ventas_global) : 0;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($m['metodo_pago']); ?></td>
                            <td class="text-center"><?= $cant; ?></td>
                            <td class="text-end">$<?= number_format($total, 2, ',', '.'); ?></td>
                            <td class="text-end">$<?= number_format($ticket, 2, ',', '.'); ?></td>
                            <td class="text-center"><?= number_format($porc, 1, ',', '.'); ?>%</td>
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
    const ctx = document.getElementById('metodoPagoChart').getContext('2d');

    const labels      = <?= json_encode($labels); ?>;
    const dataCant    = <?= json_encode($dataCant); ?>;
    const dataImporte = <?= json_encode($dataImporte); ?>;

    window.metodoPagoChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Cantidad de ventas',
                    data: dataCant,
                    yAxisID: 'y1'
                },
                {
                    type: 'bar',
                    label: 'Total vendido',
                    data: dataImporte,
                    yAxisID: 'y2'
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
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad'
                    }
                },
                y2: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Monto'
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

    // Exportar PDF
    const btnPdf        = document.getElementById('btn-exportar-pdf');
    const formPdf       = document.getElementById('form-exportar-pdf');
    const inputChartImg = document.getElementById('chart_img');

    if (btnPdf && formPdf && inputChartImg) {
        btnPdf.addEventListener('click', function () {
            if (window.metodoPagoChart) {
                const imgBase64 = window.metodoPagoChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>
