<?php

ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/vehiculos_taller.php");

$vtModel = new Vehiculos_Taller();

// ================================
// Filtros
// ================================
$fecha_desde    = $_GET['fecha_desde']    ?? '';
$fecha_hasta    = $_GET['fecha_hasta']    ?? '';
$estado_taller  = $_GET['estado_taller']  ?? '';
$patente        = $_GET['patente']        ?? '';

$filtros = [
    'fecha_desde'   => $fecha_desde,
    'fecha_hasta'   => $fecha_hasta,
    'estado_taller' => $estado_taller,
    'patente'       => $patente,
];

// Traer datos
$result = $vtModel->reporte_trabajos_taller($filtros);

// Pasar a array para reutilizar
$trabajos = [];
$total_trabajos = 0;
$total_gastos   = 0.0;

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $trabajos[] = $r;
        $total_trabajos++;
        $total_gastos += (float)$r['total_gastos_taller'];
    }
}

// ================================
// Datos para gráfico (por estado taller)
// ================================
$map_estados = [
    'en_proceso' => [
        'label'   => 'En proceso',
        'cantidad'=> 0,
        'gastos'  => 0.0
    ],
    'finalizado' => [
        'label'   => 'Finalizado',
        'cantidad'=> 0,
        'gastos'  => 0.0
    ]
];

foreach ($trabajos as $t) {
    $estado = $t['estado_taller'] ?? 'en_proceso';
    if (!isset($map_estados[$estado])) {
        // por si se agrega algún otro estado en el futuro
        $map_estados[$estado] = [
            'label'   => ucfirst($estado),
            'cantidad'=> 0,
            'gastos'  => 0.0
        ];
    }
    $map_estados[$estado]['cantidad']++;
    $map_estados[$estado]['gastos'] += (float)$t['total_gastos_taller'];
}

$labels = [];
$data_cantidades = [];
$data_gastos_prom = [];

foreach ($map_estados as $estado => $data) {
    if ($data['cantidad'] > 0) {
        $labels[] = $data['label'];
        $data_cantidades[] = (int)$data['cantidad'];
        $prom = $data['cantidad'] > 0 ? $data['gastos'] / $data['cantidad'] : 0;
        $data_gastos_prom[] = round($prom, 2);
    }
}

$promedio_gasto_trabajo = $total_trabajos > 0 ? $total_gastos / $total_trabajos : 0;

?>

<link rel="stylesheet" href="assets/css/reporte_ventas_periodo.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=bienvenida">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=reportes">Reportes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Histórico de trabajos de taller</li>
    </ol>
</nav>

<div class="container mt-4 hacer_padding reporte-ventas-container">

    <!-- Título + resumen -->
    <div class="reporte-ventas-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">
                <i class="fa-solid fa-screwdriver-wrench me-2"></i> Histórico de trabajos de taller
            </h2>
            <p class="text-muted mb-0">
                Detalle de los <strong>vehículos ingresados al taller</strong>, trabajos realizados y gastos asociados por período.
            </p>
        </div>
        <div class="reporte-ventas-badge">
            <span class="badge rounded-pill bg-report-main">
                Total trabajos analizados: <?= $total_trabajos; ?>
            </span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="reporte-filtros mb-4">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="reporte_taller_trabajos">

            <div class="col-md-3">
                <label for="fecha_desde" class="form-label">Fecha ingreso desde</label>
                <input type="date" 
                       class="form-control" 
                       id="fecha_desde" 
                       name="fecha_desde"
                       value="<?= htmlspecialchars($fecha_desde); ?>">
            </div>

            <div class="col-md-3">
                <label for="fecha_hasta" class="form-label">Fecha ingreso hasta</label>
                <input type="date" 
                       class="form-control" 
                       id="fecha_hasta" 
                       name="fecha_hasta"
                       value="<?= htmlspecialchars($fecha_hasta); ?>">
            </div>

            <div class="col-md-3">
                <label for="estado_taller" class="form-label">Estado taller</label>
                <select name="estado_taller" id="estado_taller" class="form-select">
                    <option value="">Todos</option>
                    <option value="en_proceso" <?= $estado_taller === 'en_proceso' ? 'selected' : ''; ?>>En proceso</option>
                    <option value="finalizado" <?= $estado_taller === 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="patente" class="form-label">Patente</label>
                <input type="text"
                       class="form-control"
                       id="patente"
                       name="patente"
                       placeholder="Ej: ABC123"
                       value="<?= htmlspecialchars($patente); ?>">
            </div>

            <div class="col-md-9"></div>

            <div class="col-md-3 text-md-end">
                <button type="submit" class="report-btn w-100 mt-2 mt-md-0">
                    <i class="fa-solid fa-filter me-2"></i> Aplicar filtros
                </button>
            </div>
        </form>

        <!-- BOTÓN EXPORTAR PDF -->
        <form id="form-exportar-pdf"
            action="controladores/reportes/reporte_taller_trabajos_pdf.controlador.php"
            method="POST"
            target="_blank"
            class="mt-3">

            <input type="hidden" name="fecha_desde"   value="<?= htmlspecialchars($fecha_desde); ?>">
            <input type="hidden" name="fecha_hasta"   value="<?= htmlspecialchars($fecha_hasta); ?>">
            <input type="hidden" name="estado_taller" value="<?= htmlspecialchars($estado_taller); ?>">
            <input type="hidden" name="patente"       value="<?= htmlspecialchars($patente); ?>">

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
                <div class="kpi-label">Total de trabajos de taller</div>
                <div class="kpi-value"><?= $total_trabajos; ?></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card kpi-importe">
                <div class="kpi-label">Gasto total en taller</div>
                <div class="kpi-value">
                    $ <?= number_format($total_gastos, 2, ',', '.'); ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card kpi-ticket">
                <div class="kpi-label">Gasto promedio por trabajo</div>
                <div class="kpi-value">
                    $ <?= number_format($promedio_gasto_trabajo, 2, ',', '.'); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="reporte-grafico mb-4">
        <?php if (count($labels) > 0): ?>
            <canvas id="tallerTrabajosChart" height="110"></canvas>
        <?php else: ?>
            <div class="alert alert-light border text-center">
                No se encontraron trabajos de taller para el criterio seleccionado.
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabla detalle -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle reporte-tabla">
            <thead class="table-dark">
                <tr>
                    <th># Taller</th>
                    <th>Fecha ingreso</th>
                    <th>Fecha salida</th>
                    <th>Patente</th>
                    <th>Vehículo</th>
                    <th>Estado taller</th>
                    <th>Trabajo realizado</th>
                    <th class="text-end">Total gastos</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($trabajos) > 0): ?>
                    <?php foreach ($trabajos as $row): ?>
                        <tr>
                            <td><?= (int)$row['idvehiculos_taller']; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['fecha_ingreso'])); ?></td>
                            <td>
                                <?php if (!empty($row['fecha_salida'])): ?>
                                    <?= date('d/m/Y H:i', strtotime($row['fecha_salida'])); ?>
                                <?php else: ?>
                                    <span class="text-muted small">En proceso</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['patente']); ?></td>
                            <td>
                                <?= htmlspecialchars($row['nombre_marca'] . ' ' . $row['nombre_modelo'] . ' ' . $row['anio']); ?>
                            </td>
                            <td>
                                <?php if ($row['estado_taller'] === 'finalizado'): ?>
                                    <span class="badge bg-success">Finalizado</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">En proceso</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 260px;">
                                <small class="text-muted">
                                    <?= nl2br(htmlspecialchars($row['trabajo_realizado'] ?? '')); ?>
                                </small>
                            </td>
                            <td class="text-end">
                                $ <?= number_format((float)$row['total_gastos_taller'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted p-4">
                            No hay información para mostrar.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <?php if (count($trabajos) > 0): ?>
            <tfoot>
                <tr class="table-light">
                    <th colspan="7" class="text-end">Total general de gastos de taller:</th>
                    <th class="text-end">
                        $ <?= number_format($total_gastos, 2, ',', '.'); ?>
                    </th>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php if (count($labels) > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('tallerTrabajosChart').getContext('2d');

    const labels       = <?= json_encode($labels); ?>;
    const dataCant     = <?= json_encode($data_cantidades); ?>;
    const dataGastoProm = <?= json_encode($data_gastos_prom); ?>;

    // Instancia global para exportar a PDF
    window.tallerTrabajosChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Cantidad de trabajos',
                    data: dataCant,
                    yAxisID: 'y1'
                },
                {
                    type: 'line',
                    label: 'Gasto promedio por trabajo ($)',
                    data: dataGastoProm,
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
    const btnPdf        = document.getElementById('btn-exportar-pdf');
    const formPdf       = document.getElementById('form-exportar-pdf');
    const inputChartImg = document.getElementById('chart_img');

    if (btnPdf && formPdf && inputChartImg) {
        btnPdf.addEventListener('click', function () {
            if (window.tallerTrabajosChart) {
                const imgBase64 = window.tallerTrabajosChart.toBase64Image();
                inputChartImg.value = imgBase64;
            }
            formPdf.submit();
        });
    }
});
</script>
<?php endif; ?>
