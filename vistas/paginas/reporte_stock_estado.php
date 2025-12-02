<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/vehiculos.php");

$vehiculos = new Vehiculos();

// ================================
// Datos del modelo
// ================================
$result = $vehiculos->reporte_stock_estado(); // <- NUEVA FUNCIÓN

// Pasar a array
$estados = [];
$total_stock = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $estados[] = $r;
        $total_stock += (int)$r['cantidad'];
    }
}

// Datos para gráfico
$labels   = [];
$dataCant = [];

foreach ($estados as $e) {
    $labels[]   = $e['estado'];
    $dataCant[] = (int)$e['cantidad'];
}
?>

<link rel="stylesheet" href="assets/css/reporte_ventas_periodo.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Stock por estado</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-warehouse me-2"></i> Stock por estado
            </h2>
            <p class="text-muted mb-0">
                Distribución del <strong>stock actual</strong> de vehículos según su estado operativo.
            </p>
        </div>
        <div class="reporte-ventas-badge">
            <span class="badge rounded-pill bg-report-main">
                Vehículos en stock: <?= $total_stock; ?>
            </span>
        </div>
    </div>

    <!-- (No hace falta filtro de fechas acá, es foto actual) -->

    <!-- Botón Exportar PDF -->
    <div class="mb-3">
        <form id="form-exportar-pdf"
              action="controladores/reportes/reporte_stock_estado_pdf.controlador.php"
              method="POST" target="_blank">

            <!-- Aquí guardamos la imagen del gráfico -->
            <input type="hidden" name="chart_img" id="chart_img">

            <button type="button" id="btn-exportar-pdf" class="btn btn-danger">
                <i class="fa fa-file-pdf me-1"></i> Exportar a PDF
            </button>
        </form>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <?php
        // Mapeo rápido por estado (tal cual viene de la BD)
        $map = [];
        foreach ($estados as $e) {
            $map[$e['estado']] = (int)$e['cantidad'];
        }

        // Tus valores reales en estado_vehiculo:
        // 'disponible', 'falta_documento', 'falta_digitalizacion', 'taller', 'vendido', 'Baja consignación'
        // En este reporte solo tomamos los que son stock: disponible / faltas / taller
        $disp  = $map['disponible']          ?? 0;
        $taller = $map['taller']             ?? 0;
        $fDoc  = $map['falta_documento']     ?? 0;
        $fDig  = $map['falta_digitalizacion'] ?? 0;
        ?>

        <div class="col-md-3">
            <div class="kpi-card kpi-ventas">
                <div class="kpi-label">Disponibles</div>
                <div class="kpi-value"><?= $disp; ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">En taller</div>
                <div class="kpi-value"><?= $taller; ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Falta documentación</div>
                <div class="kpi-value"><?= $fDoc; ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Falta digitalización</div>
                <div class="kpi-value"><?= $fDig; ?></div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if (count($labels) > 0): ?>
            <canvas id="stockEstadoChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se encontraron vehículos en stock para mostrar.
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabla detalle -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle reporte-tabla">
            <thead class="table-dark">
                <tr>
                    <th>Estado</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">% sobre el stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($estados) > 0): ?>
                    <?php foreach ($estados as $e): 
                        $cant = (int)$e['cantidad'];
                        $porc = $total_stock > 0 ? ($cant * 100 / $total_stock) : 0;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($e['estado']); ?></td>
                            <td class="text-center"><?= $cant; ?></td>
                            <td class="text-center"><?= number_format($porc, 1, ',', '.'); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted p-4">
                            No hay información para mostrar.
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
    const ctx = document.getElementById('stockEstadoChart').getContext('2d');

    const labels   = <?= json_encode($labels); ?>;
    const dataCant = <?= json_encode($dataCant); ?>;

    window.stockEstadoChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Cantidad de vehículos',
                    data: dataCant,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
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

    // Exportar PDF
    const btnPdf        = document.getElementById('btn-exportar-pdf');
    const formPdf       = document.getElementById('form-exportar-pdf');
    const inputChartImg = document.getElementById('chart_img');

    if (btnPdf && formPdf && inputChartImg) {
        btnPdf.addEventListener('click', function () {
            if (window.stockEstadoChart) {
                const imgBase64 = window.stockEstadoChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>