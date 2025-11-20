<main class="my-4">

<section class="stats d-flex justify-content-between flex-wrap">
    <div class="card text-center p-3">
        <h2><?= $total_vehiculos_count ?></h2>
        <p>Vehículos Disponibles</p>
    </div>
    <div class="card text-center p-3">
        <h2><?= $ventas_empleado ?></h2>
        <p>Mis Ventas Concretadas</p>
    </div>
    <div class="card text-center p-3">
        <h2><?= $ventas_anuladas_empleado ?></h2>
        <p>Mis Ventas Anuladas</p>
    </div>
    <div class="card text-center p-3">
        <h2>$<?= number_format($mis_ganancias_mes,2,',','.') ?></h2>
        <p>Mis Comisiones Mensuales</p>
    </div>
</section>

<section class="charts row mt-4">
    <div class="col-md-12">
        <h5 class="text-center fw-bold">Mis Ventas del Mes</h5>
        <canvas id="chartEmpleado" height="100"></canvas>
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

    // Convertir texto a JSON
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

