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

// Traer ventas anuladas detalle
$result = $ventas->reporte_ventas_anuladas_detalle($desde, $hasta);

// Pasar a array para reusar
$filas = [];
$total_anuladas = 0;
$total_monto    = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $filas[] = $r;
        $total_anuladas++;
        $total_monto += (float)$r['comision_concesionaria'];
    }
}

$ticket_promedio = $total_anuladas > 0
    ? $total_monto / $total_anuladas
    : 0;

// ================================
// Datos para el gráfico (agrupado por mes/año de ANULACIÓN)
// ================================
$agrupado = [];

foreach ($filas as $f) {
    if (empty($f['fecha_anulacion'])) {
        continue;
    }

    $fecha_raw = $f['fecha_anulacion']; // YYYY-mm-dd HH:ii:ss
    $periodo   = date('Y-m', strtotime($fecha_raw));
    $periodo_legible = date('m/Y', strtotime($fecha_raw));

    if (!isset($agrupado[$periodo])) {
        $agrupado[$periodo] = [
            'label' => $periodo_legible,
            'cantidad' => 0,
            'total_monto' => 0
        ];
    }

    $agrupado[$periodo]['cantidad']++;
    $agrupado[$periodo]['total_monto'] += (float)$f['precio_venta'];
}

$labels = [];
$data_cantidades = [];
$data_totales = [];

foreach ($agrupado as $p) {
    $labels[]         = $p['label'];
    $data_cantidades[] = (int)$p['cantidad'];
    $data_totales[] = round($p['total_monto'], 2);
}
?>

<link rel="stylesheet" href="assets/css/reporte_ventas_periodo.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ventas anuladas</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-ban me-2"></i> Ventas Anuladas
            </h2>
            <p class="text-muted mb-0">
                Análisis de las <strong>ventas anuladas</strong>, su cantidad y el impacto económico en el período seleccionado.
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
            <input type="hidden" name="page" value="reporte_ventas_anuladas">

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
              action="controladores/reportes/reporte_ventas_anuladas_pdf.controlador.php"
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
                <div class="kpi-label">Cantidad de ventas anuladas</div>
                <div class="kpi-value"><?= $total_anuladas; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">Monto total involucrado</div>
                <div class="kpi-value">$<?= number_format($total_monto, 2, ',', '.'); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Ticket promedio anulado</div>
                <div class="kpi-value">$<?= number_format($ticket_promedio, 2, ',', '.'); ?></div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if (count($labels) > 0): ?>
            <canvas id="ventasAnuladasChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se encontraron ventas anuladas en el período seleccionado.
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabla detalle -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle reporte-tabla">
            <thead class="table-dark">
                <tr>
                    <th>Fecha venta</th>
                    <th>Fecha anulación</th>
                    <th>Vehículo</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th class="text-end">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($filas) > 0): ?>
                    <?php foreach ($filas as $f): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($f['fecha_venta'])); ?></td>
                            <td><?= date('d/m/Y', strtotime($f['fecha_anulacion'])); ?></td>
                            <td>
                                <?= htmlspecialchars($f['marca'] . ' ' . $f['modelo'] . ' - ' . $f['patente']); ?>
                            </td>
                            <td><?= htmlspecialchars($f['cliente']); ?></td>
                            <td><?= htmlspecialchars($f['vendedor']); ?></td>
                            <td class="text-end">
                                $<?= number_format((float)$f['comision_concesionaria'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-4">
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
    const ctx = document.getElementById('ventasAnuladasChart').getContext('2d');

    const labels       = <?= json_encode($labels); ?>;
    const dataCant     = <?= json_encode($data_cantidades); ?>;
    const dataTotales  = <?= json_encode($data_totales); ?>;

    // Instancia global para usarla al generar el PDF
    window.ventasAnuladasChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Monto total anulado ($)',
                    data: dataTotales,
                    yAxisID: 'y1'
                },
                {
                    type: 'line',
                    label: 'Cantidad de anulaciones',
                    data: dataCant,
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
    const btnPdf       = document.getElementById('btn-exportar-pdf');
    const formPdf      = document.getElementById('form-exportar-pdf');
    const inputChartImg = document.getElementById('chart_img');

    if (btnPdf && formPdf && inputChartImg) {
        btnPdf.addEventListener('click', function () {
            if (window.ventasAnuladasChart) {
                const imgBase64 = window.ventasAnuladasChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>
