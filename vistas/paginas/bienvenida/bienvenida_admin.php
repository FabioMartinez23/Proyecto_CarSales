<main class="dashboard-main my-4">

    <!-- ENCABEZADO -->
    <header class="dashboard-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="dashboard-title mb-1">Panel de Control</h2>
            <p class="dashboard-subtitle mb-0">
                Resumen general de vehículos, ventas y caja.
            </p>
        </div>
        <div class="dashboard-meta text-end">
            <span class="dashboard-tag">Administrador</span>
        </div>
    </header>

    <!-- TARJETAS RESUMEN -->
    <section class="stat-grid">

        <article class="stat-item stat-item--clickable"
                 onclick="window.location.href='index.php?page=listado_vehiculos'">
            <div class="stat-label">Vehículos Disponibles</div>
            <div class="stat-value"><?= $total_vehiculos_count ?></div>
        </article>

        <article class="stat-item stat-item--clickable"
                 onclick="window.location.href='index.php?page=listado_falta_documentacion&estado=falta_documento'">
            <div class="stat-label">Vehículos Sin Documentación</div>
            <div class="stat-value"><?= $total_vehiculos_sin_documentacion_count ?></div>
        </article>

        <article class="stat-item stat-item--clickable"
                 onclick="window.location.href='index.php?page=listado_falta_documentacion&estado=falta_digitalizacion'">
            <div class="stat-label">Vehículos Sin Digitalizar</div>
            <div class="stat-value"><?= $total_vehiculos_sin_digitar_count ?></div>
        </article>

        <article class="stat-item">
            <div class="stat-label">Total Usuarios</div>
            <div class="stat-value"><?= $total_usuarios_count ?></div>
        </article>

    </section>

    <section class="stat-grid mt-4">

        <article class="stat-item stat-item--clickable"
                 onclick="window.location.href='index.php?page=listado_ventas'">
            <div class="stat-label">Ventas Concretadas</div>
            <div class="stat-value"><?= $total_ventas_count ?></div>
        </article>

        <article class="stat-item">
            <div class="stat-label">Ventas Anuladas</div>
            <div class="stat-value"><?= $total_anuladas_count ?></div>
        </article>

        <article class="stat-item">
            <div class="stat-label">Balance del Mes</div>
            <div class="stat-value stat-value--money">
                $<?= number_format($ultimo_balance, 2, ',', '.') ?>
            </div>
        </article>

        <article class="stat-item stat-item--accent stat-item--clickable"
                 onclick="window.location.href='index.php?page=costos_totales'">
            <div class="stat-label">
                <i class="fa-solid fa-calculator me-1"></i> Costos Totales
            </div>
            <div class="stat-value stat-icon-only">
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </article>

    </section>

    <!-- ESTADO DE CAJA (BARRA / NAVEGADOR) -->
    <?php include 'bienvenida_estado_caja.php'; ?>

        <!-- ACCESOS RÁPIDOS -->
    <section class="quick-actions mt-5">
        <h3 class="quick-actions-title text-center mb-3">Accesos Rápidos</h3>

        <div class="quick-actions-grid">
            <button class="btn-quick"
                    data-bs-toggle="modal"
                    data-bs-target="#ventaModal">
                <i class="fa-solid fa-file-signature me-1"></i>
                Registrar Venta
            </button>

            <button class="btn-quick"
                    data-bs-toggle="modal"
                    data-bs-target="#vehiculoModal">
                <i class="fa-solid fa-car-side me-1"></i>
                Agregar Vehículo
            </button>

            <button class="btn-quick"
                    data-bs-toggle="modal"
                    data-bs-target="#clienteModal">
                <i class="fa-solid fa-magnifying-glass me-1"></i>
                Buscar Cliente
            </button>

            <button class="btn-quick btn-quick--dark"
                    onclick="window.location.href='index.php?page=costos_totales'">
                <i class="fa-solid fa-chart-pie me-1"></i>
                Costos Totales
            </button>
        </div>
    </section>

    <!-- GRÁFICOS -->
    <section class="charts-layout mt-4">

        <!-- FILA 1: DIARIO -->
        <div class="charts-row charts-row--full">
            <div class="chart-wrapper">
                <div class="chart-header">
                    <h5 class="chart-title mb-0">Movimientos Diario (Mes Actual)</h5>
                </div>
                <canvas id="chartDiario" height="110"></canvas>
            </div>
        </div>

        <!-- FILA 2: MENSUAL + ANUAL -->
        <div class="charts-row charts-row--two">
            <div class="chart-wrapper">
                <div class="chart-header">
                    <h5 class="chart-title mb-0">Balance Mensual</h5>
                </div>
                <canvas id="chartMensual" height="170"></canvas>
            </div>

            <div class="chart-wrapper">
                <div class="chart-header">
                    <h5 class="chart-title mb-0">Balance Anual</h5>
                </div>
                <canvas id="chartAnual" height="170"></canvas>
            </div>
        </div>

    </section>
</main>

<!-- ==========================
     SCRIPTS CHART.JS
=========================== -->
<script>
/* GRAFICO MENSUAL */
const labelsMensual = <?= $labels_mes_json ?>;
const ingresosMensual = <?= $ingresos_mes_json ?>;
const egresosMensual = <?= $egresos_mes_json ?>;
const balanceMensual = <?= $balance_mes_json ?>;

new Chart(document.getElementById('chartMensual'), {
    type: 'line',
    data: {
        labels: labelsMensual,
        datasets: [
            {
                label: 'Ingresos',
                data: ingresosMensual,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40,167,69,0.2)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Egresos',
                data: egresosMensual,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220,53,69,0.2)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Balance',
                data: balanceMensual,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.1)',
                fill: false,
                borderDash: [5,5],
                tension: 0.3
            }
        ]
    },
    options: {responsive: true}
});
</script>

<script>
/* GRAFICO DIARIO */
fetch("controladores/caja/caja.controlador.php", {
    method: "POST",
    body: new URLSearchParams({ action: "grafico_diario" })
})
.then(r => r.json())
.then(data => {

    new Chart(document.getElementById('chartDiario'), {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: "Ingresos",
                    data: data.ingresos,
                    backgroundColor: "rgba(25,135,84,0.7)"
                },
                {
                    label: "Egresos",
                    data: data.egresos,
                    backgroundColor: "rgba(220,53,69,0.7)"
                }
            ]
        }
    });

});
</script>

<script>
/* GRAFICO ANUAL */
fetch("controladores/caja/caja.controlador.php", {
    method: "POST",
    body: new URLSearchParams({ action: "grafico_anual" })
})
.then(r => r.json())
.then(data => {

    new Chart(document.getElementById('chartAnual'), {
        type: 'line',
        data: {
            labels: data.meses,
            datasets: [{
                label: "Balance Mensual",
                data: data.balances,
                borderColor: "#6f42c1",
                backgroundColor: "rgba(111,66,193,0.2)",
                fill: true,
                tension: 0.3
            }]
        }
    });

});
</script>
