<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/vender_vehiculos.php");

$ventas = new VenderVehiculo();

// ================================
// Filtros por fecha
// ================================
$hoy = date('Y-m-d');
$primer_dia_anio = date('Y-01-01');

$desde = $_GET['desde'] ?? $primer_dia_anio;
$hasta = $_GET['hasta'] ?? $hoy;

// Traer resumen por cliente
$result = $ventas->reporte_clientes_frecuentes($desde, $hasta);

// Pasar el result a un array para reusar
$clientes = [];
$total_clientes = 0;
$total_ventas_global = 0;
$total_importe_global = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $clientes[] = $r;
        $total_clientes++;

        $total_ventas_global  += (int)$r['cantidad_ventas'];
        $total_importe_global += (float)$r['total_vendido'];
    }
}

$ticket_promedio_global = $total_ventas_global > 0
    ? $total_importe_global / $total_ventas_global
    : 0;

// Datos para el gráfico
$labels = [];
$data_cantidades = [];
$data_totales = [];

foreach ($clientes as $c) {
    $labels[]          = $c['cliente'];
    $data_cantidades[] = (int)$c['cantidad_ventas'];
    $data_totales[]    = round((float)$c['total_vendido'], 2);
}
?>

<link rel="stylesheet" href="assets/css/reporte_ventas_periodo.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Clientes más frecuentes</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-user-check me-2"></i> Clientes más frecuentes
            </h2>
            <p class="text-muted mb-0">
                Ranking de <strong>clientes</strong> según la cantidad de ventas realizadas en el período seleccionado.
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
            <input type="hidden" name="page" value="reporte_clientes_frecuentes">

            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta); ?>">
            </div>

            <div class="col-md-3 d-none d-md-block">
                <!-- espacio para algún filtro futuro (mín. ventas, etc.) -->
            </div>

            <div class="col-md-3 text-md-end">
                <button type="submit" class="report-btn w-100 mt-2 mt-md-0">
                    <i class="fa-solid fa-filter me-2"></i> Aplicar filtros
                </button>
            </div>
        </form>

        <!-- BOTÓN EXPORTAR PDF (form aparte) -->
        <form id="form-exportar-pdf"
              action="controladores/reportes/reporte_clientes_frecuentes_pdf.controlador.php"
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
        <div class="col-md-3">
            <div class="kpi-card kpi-ventas">
                <div class="kpi-label">Cantidad de clientes</div>
                <div class="kpi-value"><?= $total_clientes; ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-ventas">
                <div class="kpi-label">Total de ventas</div>
                <div class="kpi-value"><?= $total_ventas_global; ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">Total facturado</div>
                <div class="kpi-value">
                    $<?= number_format($total_importe_global, 2, ',', '.'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Ticket promedio global</div>
                <div class="kpi-value">
                    $<?= number_format($ticket_promedio_global, 2, ',', '.'); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if (count($clientes) > 0): ?>
            <canvas id="clientesFrecuentesChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se encontraron ventas en el período seleccionado.
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabla detalle -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle reporte-tabla">
            <thead class="table-dark">
                <tr>
                    <th>Cliente</th>
                    <th class="text-center">Cantidad de ventas</th>
                    <th class="text-end">Total vendido</th>
                    <th class="text-end">Ticket promedio</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($clientes) > 0): ?>
                    <?php foreach ($clientes as $c): ?>
                        <?php
                            $cant   = (int)$c['cantidad_ventas'];
                            $total  = (float)$c['total_vendido'];
                            $ticket = (float)$c['ticket_promedio'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($c['cliente']); ?></td>
                            <td class="text-center"><?= $cant; ?></td>
                            <td class="text-end">
                                $<?= number_format($total, 2, ',', '.'); ?>
                            </td>
                            <td class="text-end">
                                $<?= number_format($ticket, 2, ',', '.'); ?>
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
            <?php if (count($clientes) > 0): ?>
            <tfoot>
                <tr>
                    <th>Total período</th>
                    <th class="text-center"><?= $total_ventas_global; ?></th>
                    <th class="text-end">
                        $<?= number_format($total_importe_global, 2, ',', '.'); ?>
                    </th>
                    <th class="text-end">
                        $<?= number_format($ticket_promedio_global, 2, ',', '.'); ?>
                    </th>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php if (count($clientes) > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('clientesFrecuentesChart').getContext('2d');

    const labels        = <?= json_encode($labels); ?>;
    const dataCantidades = <?= json_encode($data_cantidades); ?>;
    const dataTotales    = <?= json_encode($data_totales); ?>;

    // Instancia global para luego usarla al generar el PDF
    window.clientesFrecuentesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Cantidad de ventas',
                    data: dataCantidades,
                    yAxisID: 'y1'
                },
                {
                    type: 'line',
                    label: 'Total vendido ($)',
                    data: dataTotales,
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
            if (window.clientesFrecuentesChart) {
                const imgBase64 = window.clientesFrecuentesChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>
