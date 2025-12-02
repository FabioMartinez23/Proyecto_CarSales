<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/vehiculos.php");

$vehiculoModel = new Vehiculos();

// ================================
// Filtros
// ================================
$edad_minima = isset($_GET['edad_minima']) ? (int)$_GET['edad_minima'] : 60;

// Traer vehículos envejecidos
$result = $vehiculoModel->reporte_stock_envejecido($edad_minima);

// Pasar a array para reusar
$vehiculos = [];
$total_dias = 0;
$max_dias   = 0;

if ($result && $result->num_rows > 0) {
    while ($v = $result->fetch_assoc()) {
        $vehiculos[] = $v;
        $dias = (int)$v['dias_en_stock'];

        $total_dias += $dias;
        if ($dias > $max_dias) {
            $max_dias = $dias;
        }
    }
}

$cantidad_vehiculos = count($vehiculos);
$promedio_dias = $cantidad_vehiculos > 0
    ? $total_dias / $cantidad_vehiculos
    : 0;

// ================================
// Datos para gráfico: rangos de días
// ================================
$rangos = [
    '0-30'   => 0,
    '31-60'  => 0,
    '61-90'  => 0,
    '91-180' => 0,
    '180+'   => 0,
];

foreach ($vehiculos as $v) {
    $dias = (int)$v['dias_en_stock'];

    if ($dias <= 30) {
        $rangos['0-30']++;
    } elseif ($dias <= 60) {
        $rangos['31-60']++;
    } elseif ($dias <= 90) {
        $rangos['61-90']++;
    } elseif ($dias <= 180) {
        $rangos['91-180']++;
    } else {
        $rangos['180+']++;
    }
}

$labels_rangos = array_keys($rangos);
$data_rangos   = array_values($rangos);
?>

<link rel="stylesheet" href="assets/css/reporte_base.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Stock envejecido</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-hourglass-half me-2"></i> Stock envejecido
            </h2>
            <p class="text-muted mb-0">
                Detecta vehículos con <strong>muchos días en stock</strong> para tomar decisiones comerciales (bajar precio, promociones, etc.).
            </p>
        </div>
        <div class="reporte-ventas-badge">
            <span class="badge rounded-pill bg-report-main">
                Filtrando desde <?= (int)$edad_minima ?> días en stock
            </span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="reporte-filtros mb-4">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="reporte_stock_envejecido">

            <div class="col-md-4">
                <label class="form-label">Edad mínima en stock (días)</label>
                <input 
                    type="number" 
                    name="edad_minima" 
                    class="form-control" 
                    min="0" 
                    value="<?= htmlspecialchars($edad_minima); ?>">
            </div>

            <div class="col-md-4 text-md-start">
                <button type="submit" class="report-btn w-100 mt-2 mt-md-0">
                    <i class="fa-solid fa-filter me-2"></i> Aplicar filtros
                </button>
            </div>
        </form>

        <!-- BOTÓN EXPORTAR PDF (form aparte) -->
        <form id="form-exportar-pdf"
              action="controladores/reportes/reporte_stock_envejecido_pdf.controlador.php"
              method="POST" target="_blank" class="mt-3">

            <!-- Envío los mismos filtros que se están usando -->
            <input type="hidden" name="edad_minima" value="<?= htmlspecialchars($edad_minima) ?>">

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
                <div class="kpi-label">Vehículos en stock envejecido</div>
                <div class="kpi-value"><?= $cantidad_vehiculos; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">Promedio de días en stock</div>
                <div class="kpi-value">
                    <?= number_format($promedio_dias, 1, ',', '.'); ?> días
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Máximo de días en stock</div>
                <div class="kpi-value">
                    <?= (int)$max_dias; ?> días
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if ($cantidad_vehiculos > 0): ?>
            <canvas id="stockEnvejecidoChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se encontraron vehículos con al menos <?= (int)$edad_minima ?> días en stock.
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabla detalle -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle reporte-tabla">
            <thead class="table-dark">
                <tr>
                    <th>Vehículo</th>
                    <th>Patente</th>
                    <th>Año</th>
                    <th class="text-center">Fecha ingreso</th>
                    <th class="text-center">Días en stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($cantidad_vehiculos > 0): ?>
                    <?php foreach ($vehiculos as $v): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']); ?>
                            </td>
                            <td><?= htmlspecialchars($v['patente']); ?></td>
                            <td><?= htmlspecialchars($v['anio']); ?></td>
                            <td class="text-center">
                                <?= date('d/m/Y', strtotime($v['fecha_ingreso'])); ?>
                            </td>
                            <td class="text-center">
                                <?= (int)$v['dias_en_stock']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted p-4">
                            No hay información para mostrar con el filtro actual.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($cantidad_vehiculos > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('stockEnvejecidoChart').getContext('2d');

    const labelsRangos = <?= json_encode($labels_rangos); ?>;
    const dataRangos   = <?= json_encode($data_rangos); ?>;

    // Instancia global para luego usarla al generar el PDF
    window.stockEnvejecidoChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labelsRangos,
            datasets: [
                {
                    type: 'bar',
                    label: 'Cantidad de vehículos por rango de días',
                    data: dataRangos,
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
    const btnPdf        = document.getElementById('btn-exportar-pdf');
    const formPdf       = document.getElementById('form-exportar-pdf');
    const inputChartImg = document.getElementById('chart_img');

    if (btnPdf && formPdf && inputChartImg) {
        btnPdf.addEventListener('click', function () {
            if (window.stockEnvejecidoChart) {
                const imgBase64 = window.stockEnvejecidoChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>
