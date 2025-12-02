<?php

ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/vehiculos.php");

$vehiculos = new Vehiculos();

// ================================
// Filtros
// ================================
$solo_activos = isset($_GET['solo_activos'])
    ? (bool)$_GET['solo_activos']
    : true;

// Traer resumen por estado
$result = $vehiculos->reporte_vehiculos_por_estado($solo_activos);

// Pasar a array para reusar
$estados = [];
$total_vehiculos = 0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $estados[] = $r;
        $total_vehiculos += (int)$r['cantidad'];
    }
}

// Datos para gráfico
$labels = [];
$data_cantidades = [];
$data_dias_prom = [];

foreach ($estados as $e) {
    $labels[]        = $e['estado'];
    $data_cantidades[] = (int)$e['cantidad'];
    $data_dias_prom[]  = round((float)$e['dias_promedio'], 1);
}
?>

<link rel="stylesheet" href="assets/css/reporte_ventas_periodo.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vehículos por estado</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-traffic-light me-2"></i> Vehículos por estado
            </h2>
            <p class="text-muted mb-0">
                Distribución de los <strong>vehículos por estado operativo</strong> (disponible, falta documentación, baja, etc.).
            </p>
        </div>
        <div class="reporte-ventas-badge">
            <span class="badge rounded-pill bg-report-main">
                Total vehículos analizados: <?= $total_vehiculos; ?>
            </span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="reporte-filtros mb-4">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="reporte_vehiculos_estado">

            <div class="col-md-6">
                <label class="form-label">Alcance</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="solo_activos" name="solo_activos" value="1"
                        <?= $solo_activos ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="solo_activos">
                        Solo vehículos activos (excluye eliminados físicamente)
                    </label>
                </div>
            </div>

            <div class="col-md-3"></div>

            <div class="col-md-3 text-md-end">
                <button type="submit" class="report-btn w-100 mt-2 mt-md-0">
                    <i class="fa-solid fa-filter me-2"></i> Aplicar filtros
                </button>
            </div>
        </form>

        <!-- BOTÓN EXPORTAR PDF -->
        <form id="form-exportar-pdf"
              action="controladores/reportes/reporte_vehiculos_estado_pdf.controlador.php"
              method="POST" target="_blank" class="mt-3">

            <input type="hidden" name="solo_activos" value="<?= $solo_activos ? 1 : 0; ?>">

            <!-- imagen del gráfico -->
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
                <div class="kpi-label">Total de vehículos</div>
                <div class="kpi-value"><?= $total_vehiculos; ?></div>
            </div>
        </div>

        <?php
        // Buscamos algunos estados clave si existen
        $map_estados = [];
        foreach ($estados as $e) {
            $map_estados[$e['estado']] = (int)$e['cantidad'];
        }

        $disp   = $map_estados['Disponible']           ?? ($map_estados['disponible'] ?? 0);
        $faltaD = $map_estados['Falta documentación']  ?? ($map_estados['falta_documento'] ?? 0);
        $baja   = $map_estados['Baja']                 ?? ($map_estados['baja'] ?? 0);
        ?>

        <div class="col-md-4">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">Vehículos disponibles</div>
                <div class="kpi-value"><?= $disp; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Con falta de documentación / digitalización</div>
                <div class="kpi-value"><?= $faltaD; ?></div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if (count($labels) > 0): ?>
            <canvas id="vehiculosEstadoChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se encontraron vehículos para el criterio seleccionado.
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
                    <th class="text-center">% sobre el total</th>
                    <th class="text-end">Días promedio en stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($estados) > 0): ?>
                    <?php foreach ($estados as $e): 
                        $cant = (int)$e['cantidad'];
                        $porc = $total_vehiculos > 0 ? ($cant * 100 / $total_vehiculos) : 0;
                        $dias = round((float)$e['dias_promedio'], 1);
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($e['estado']); ?></td>
                            <td class="text-center"><?= $cant; ?></td>
                            <td class="text-center"><?= number_format($porc, 1, ',', '.'); ?>%</td>
                            <td class="text-end"><?= $dias; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted p-4">
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
    const ctx = document.getElementById('vehiculosEstadoChart').getContext('2d');

    const labels       = <?= json_encode($labels); ?>;
    const dataCant     = <?= json_encode($data_cantidades); ?>;
    const dataDiasProm = <?= json_encode($data_dias_prom); ?>;

    // Instancia global para PDF
    window.vehiculosEstadoChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Cantidad de vehículos',
                    data: dataCant,
                    yAxisID: 'y1'
                },
                {
                    type: 'line',
                    label: 'Días promedio en stock',
                    data: dataDiasProm,
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

    // Exportar a PDF (capturar gráfico como base64)
    const btnPdf       = document.getElementById('btn-exportar-pdf');
    const formPdf      = document.getElementById('form-exportar-pdf');
    const inputChartImg = document.getElementById('chart_img');

    if (btnPdf && formPdf && inputChartImg) {
        btnPdf.addEventListener('click', function () {
            if (window.vehiculosEstadoChart) {
                const imgBase64 = window.vehiculosEstadoChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>
