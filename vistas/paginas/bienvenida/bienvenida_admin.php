<main class="my-4">

<!-- ==========================
     TARJETAS ADMINISTRADOR
=========================== -->
<section class="stats d-flex justify-content-between flex-wrap">
    <div class="card text-center p-3" onclick="window.location.href='index.php?page=listado_vehiculos'">
        <h2><?= $total_vehiculos_count ?></h2>
        <p>Vehículos Disponibles</p>
    </div>
    <div class="card text-center p-3" onclick="window.location.href='index.php?page=listado_falta_documentacion&estado=falta_documento'">
        <h2><?= $total_vehiculos_sin_documentacion_count ?></h2>
        <p>Vehículos Sin Documentación</p>
    </div>
    <div class="card text-center p-3" onclick="window.location.href='index.php?page=listado_falta_documentacion&estado=falta_digitalizacion'">
        <h2><?= $total_vehiculos_sin_digitar_count ?></h2>
        <p>Vehículos Sin Digitalizar</p>
    </div>
    <div class="card text-center p-3">
        <h2><?= $total_usuarios_count ?></h2>
        <p>Total Usuarios</p>
    </div>
</section>

<section class="stats d-flex justify-content-between flex-wrap mt-4">
    <div class="card text-center p-3" onclick="window.location.href='index.php?page=listado_ventas'">
        <h2><?= $total_ventas_count ?></h2>
        <p>Ventas Concretadas</p>
    </div>
    <div class="card text-center p-3">
        <h2><?= $total_anuladas_count ?></h2>
        <p>Ventas Anuladas</p>
    </div>
    <div class="card text-center p-3">
        <h2>$<?= number_format($ultimo_balance,2,',','.') ?></h2>
        <p>Balance del Mes</p>
    </div>
    <!-- ==========================
     COSTOS TOTALES — ACCESO DIRECTO
    =========================== -->
    <div class="card text-center p-3"
         style="cursor:pointer; transition:0.2s;"
         onclick="window.location.href='index.php?page=costos_totales'">
        <h2><i class="fa-solid fa-calculator"></i></h2>
        <p>Costos Totales</p>
    </div>
</section>

<!-- ==========================
     ESTADO DE CAJA
=========================== -->
<?php include 'bienvenida_estado_caja.php'; ?>

<!-- ==========================
     GRÁFICOS
=========================== -->
<section class="charts row mt-4">

    <!-- Diario -->
    <div class="col-md-12">
        <h5 class="text-center fw-bold">Movimientos Diario (Mes Actual)</h5>
        <canvas id="chartDiario" height="110"></canvas>
    </div>

    <!-- Mensual -->
    <div class="col-md-6 mb-4">
        <h5 class="text-center fw-bold">Balance Mensual</h5>
        <canvas id="chartMensual" height="180"></canvas>
    </div>

    <!-- Anual -->
    <div class="col-md-6 mb-4">
        <h5 class="text-center fw-bold">Balance Anual</h5>
        <canvas id="chartAnual" height="180"></canvas>
    </div>

</section>

<!-- ACCESOS RÁPIDOS -->
<h3 class="text-center mt-5">Accesos Rápidos</h3>
<section class="quick-actions d-flex justify-content-around mt-4 flex-wrap">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ventaModal">Registrar Venta</button>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehiculoModal">Agregar Vehículo</button>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clienteModal">Buscar Cliente</button>
        <!-- COSTOS TOTALES (NUEVO) -->
    <button class="btn btn-dark"
            onclick="window.location.href='index.php?page=costos_totales'">
        <i class="fa-solid fa-chart-pie me-1"></i> Costos Totales
    </button>
</section>

</main>

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
