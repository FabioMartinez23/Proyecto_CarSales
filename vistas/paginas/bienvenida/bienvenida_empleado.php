<main class="dashboard-main my-4">

    <!-- ENCABEZADO -->
    <header class="dashboard-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="dashboard-title mb-1">Panel del Vendedor</h2>
            <p class="dashboard-subtitle mb-0">
                Resumen de mis vehículos, ventas y comisiones.
            </p>
        </div>
        <div class="dashboard-meta text-end">
            <span class="dashboard-tag">Empleado</span>
        </div>
    </header>

    <!-- TARJETAS RESUMEN -->
    <section class="stat-grid">

        <article class="stat-item">
            <div class="stat-label">Vehículos Disponibles</div>
            <div class="stat-value"><?= $total_vehiculos_count ?></div>
        </article>

        <article class="stat-item">
            <div class="stat-label">Mis Ventas Concretadas</div>
            <div class="stat-value"><?= $ventas_empleado ?></div>
        </article>

        <article class="stat-item">
            <div class="stat-label">Mis Ventas Anuladas</div>
            <div class="stat-value"><?= $ventas_anuladas_empleado ?></div>
        </article>

        <article class="stat-item">
            <div class="stat-label">Mis Comisiones Mensuales</div>
            <div class="stat-value stat-value--money">
                $<?= number_format($mis_ganancias_mes, 2, ',', '.') ?>
            </div>
        </article>

    </section>

    <!-- GRÁFICO DE VENTAS DEL MES -->
    <section class="charts-layout mt-4">
        <div class="charts-row charts-row--full">
            <div class="chart-wrapper">
                <div class="chart-header">
                    <h5 class="chart-title mb-0">Mis Ventas del Mes</h5>
                </div>
                <canvas id="chartEmpleado" height="110"></canvas>
            </div>
        </div>
    </section>

</main>

<script>
fetch("controladores/ventas/ventas.empleado.controlador.php", {
    method: "POST",
    body: new URLSearchParams({
        action: "ventas_empleado_mes",
        idusuario: <?= $_SESSION['idusuarios'] ?>
    })
})
.then(r => r.text())
.then(texto => {
    console.log("RESPUESTA RAW:", texto);

    const data = JSON.parse(texto);

    new Chart(document.getElementById('chartEmpleado'), {
        type: 'bar',
        data: {
            labels: data.dias,
            datasets: [{
                label: "Mis Ventas del Mes",
                data: data.ventas,
                backgroundColor: "rgba(13,110,253,0.7)",
                borderColor: "rgba(13,110,253,1)",
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
