<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$caja = new Caja();

// ================================
// Filtros
// ================================
$hoy = date('Y-m-d');
$primer_dia_anio = date('Y-01-01');

$desde    = $_GET['desde']    ?? $primer_dia_anio;
$hasta    = $_GET['hasta']    ?? $hoy;
$tipo_mov = $_GET['tipo_mov'] ?? 'todos'; // ingresos | egresos | todos

// Traer movimientos para el período y tipo seleccionado
$result = $caja->reporte_movimientos($desde, $hasta, $tipo_mov);

// Pasar a array para reusar
$movimientos = [];
$total_ingresos = 0;
$total_egresos  = 0;

if ($result && $result->num_rows > 0) {
    while ($m = $result->fetch_assoc()) {
        $movimientos[] = $m;

        $monto = (float)$m['monto'];
        if ($m['tipo_movimiento'] === 'ingreso') {
            $total_ingresos += $monto;
        } elseif ($m['tipo_movimiento'] === 'egreso') {
            $total_egresos += $monto;
        }
    }
}

$total_neto = $total_ingresos - $total_egresos;

// ================================
// Datos para gráfico (por fecha)
// ================================
$labels = [];
$data_ingresos = [];
$data_egresos  = [];

// agrupamos por día (o podrías agrupar por mes si querés)
$agrupado = [];

foreach ($movimientos as $m) {
    $fecha = substr($m['fecha_movimiento'], 0, 10); // YYYY-MM-DD
    if (!isset($agrupado[$fecha])) {
        $agrupado[$fecha] = ['ingresos' => 0, 'egresos' => 0];
    }

    $monto = (float)$m['monto'];
    if ($m['tipo_movimiento'] === 'ingreso') {
        $agrupado[$fecha]['ingresos'] += $monto;
    } elseif ($m['tipo_movimiento'] === 'egreso') {
        $agrupado[$fecha]['egresos'] += $monto;
    }
}

foreach ($agrupado as $fecha => $datos) {
    $labels[]        = date('d/m/Y', strtotime($fecha));
    $data_ingresos[] = round($datos['ingresos'], 2);
    $data_egresos[]  = round($datos['egresos'], 2);
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Movimientos de caja</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-cash-register me-2"></i> Movimientos de Caja
            </h2>
            <p class="text-muted mb-0">
                Resumen de <strong>ingresos y egresos</strong> de caja para el período seleccionado.
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
            <input type="hidden" name="page" value="reporte_caja_mensual">

            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Tipo de movimiento</label>
                <select name="tipo_mov" class="form-select">
                    <option value="todos"    <?= $tipo_mov === 'todos' ? 'selected' : '' ?>>Ingresos y egresos</option>
                    <option value="ingresos" <?= $tipo_mov === 'ingresos' ? 'selected' : '' ?>>Solo ingresos</option>
                    <option value="egresos"  <?= $tipo_mov === 'egresos' ? 'selected' : '' ?>>Solo egresos</option>
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
              action="controladores/reportes/reporte_caja_pdf.controlador.php"
              method="POST" target="_blank" class="mt-3">

            <!-- Envío los mismos filtros que se están usando -->
            <input type="hidden" name="desde"    value="<?= htmlspecialchars($desde) ?>">
            <input type="hidden" name="hasta"    value="<?= htmlspecialchars($hasta) ?>">
            <input type="hidden" name="tipo_mov" value="<?= htmlspecialchars($tipo_mov) ?>">

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
                <div class="kpi-label">Total ingresos</div>
                <div class="kpi-value">$<?= number_format($total_ingresos, 2, ',', '.'); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">Total egresos</div>
                <div class="kpi-value">$<?= number_format($total_egresos, 2, ',', '.'); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Neto (ingresos - egresos)</div>
                <div class="kpi-value">$<?= number_format($total_neto, 2, ',', '.'); ?></div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if (count($labels) > 0): ?>
            <canvas id="cajaChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se encontraron movimientos en el período seleccionado.
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
                    <th class="text-center">Tipo</th>
                    <th class="text-end">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($movimientos) > 0): ?>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($m['fecha_movimiento'])); ?></td>
                            <td><?= htmlspecialchars($m['descripcion']); ?></td>
                            <td class="text-center text-capitalize"><?= htmlspecialchars($m['tipo_movimiento']); ?></td>
                            <td class="text-end">
                                $<?= number_format((float)$m['monto'], 2, ',', '.'); ?>
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
        </table>
    </div>
</div>

<?php if (count($labels) > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('cajaChart').getContext('2d');

    const labels      = <?= json_encode($labels); ?>;
    const dataIngresos = <?= json_encode($data_ingresos); ?>;
    const dataEgresos  = <?= json_encode($data_egresos); ?>;

    // Instancia global para luego usarla al generar el PDF
    window.cajaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Ingresos',
                    data: dataIngresos,
                    yAxisID: 'y1'
                },
                {
                    type: 'bar',
                    label: 'Egresos',
                    data: dataEgresos,
                    yAxisID: 'y1'
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
    const btnPdf      = document.getElementById('btn-exportar-pdf');
    const formPdf     = document.getElementById('form-exportar-pdf');
    const inputChartImg = document.getElementById('chart_img');

    if (btnPdf && formPdf && inputChartImg) {
        btnPdf.addEventListener('click', function () {
            if (window.cajaChart) {
                const imgBase64 = window.cajaChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>
