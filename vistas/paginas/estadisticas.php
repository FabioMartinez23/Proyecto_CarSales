<?php 
ini_set('display_errors', 1);

// Consultas para obtener datos de género
$femenino = new Persona();
$result_femenino = $femenino->cantidad_femenino()->fetch_assoc()['cantidad_femenino'];

$masculino = new Persona();
$result_masculino = $masculino->cantidad_masculino()->fetch_assoc()['cantidad_masculino'];

// Datos de ventas mensuales (de ejemplo)
$meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
$ventas_mensuales = [10, 15, 8, 20, 18, 22, 25, 30, 17, 19, 21, 14]; 
?>

<div class="container mt-4 hacer_padding">
    <div class="row justify-content-center">
        <!-- Gráfico de género -->
        <div class="col-md-4 text-center gf-sexo">
            <h3 class="text-center">Estadísticas de Género de Usuarios</h3>
            <canvas id="genderChart" style="max-width: 300px; max-height: 300px;"></canvas> <!-- Tamaño uniforme -->
            <p class="mt-2">
                Masculino: <span id="masculinoCount"><?php echo $result_masculino; ?></span> <br>
                Femenino: <span id="femeninoCount"><?php echo $result_femenino; ?></span>
            </p>
        </div>

        <!-- Gráfico de ventas mensuales (barras verticales) -->
        <div class="col-md-4 text-center gf-ventas">
            <h3>Ventas Mensuales</h3>
            <canvas id="monthlySalesChart" style="max-width: 500px; max-height: 500px;"></canvas> <!-- Tamaño uniforme -->
            <p class="mt-2">
                Enero: <span id=""><?php echo $ventas_mensuales[0]; ?></span> <br>
                Febrero: <span id=""><?php echo $ventas_mensuales[1]; ?></span> <br>
                Marzo: <span id=""><?php echo $ventas_mensuales[2]; ?></span> <br>
                Abril: <span id=""><?php echo $ventas_mensuales[3]; ?></span> <br>
                Mayo: <span id=""><?php echo $ventas_mensuales[4]; ?></span> <br>
                Junio: <span id=""><?php echo $ventas_mensuales[5]; ?></span> <br>
                Julio: <span id=""><?php echo $ventas_mensuales[6]; ?></span> <br>
                Agosto: <span id=""><?php echo $ventas_mensuales[7]; ?></span> <br>
                Septiembre: <span id=""><?php echo $ventas_mensuales[8]; ?></span> <br>
                Octubre: <span id=""><?php echo $ventas_mensuales[9]; ?></span> <br>
                Noviembre: <span id=""><?php echo $ventas_mensuales[10]; ?></span> <br>
                Diciembre: <span id=""><?php echo $ventas_mensuales[11]; ?></span> <br>
            </p>
        </div>
    </div>
</div>

<script>
    // Datos de género
    const masculinoCount = <?php echo $result_masculino; ?>;
    const femeninoCount = <?php echo $result_femenino; ?>;

    // Gráfico de torta para género
    const ctx = document.getElementById("genderChart").getContext("2d");
    new Chart(ctx, {
        type: "pie",
        data: {
            labels: ["Masculino", "Femenino"],
            datasets: [{
                data: [masculinoCount, femeninoCount],
                backgroundColor: ["#007bff", "#ff6384"],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: "top" },
            },
        }
    });
</script>

<script>
    // Datos de ventas mensuales
    const labels = <?php echo json_encode($meses); ?>;
    const ventasMensuales = <?php echo json_encode($ventas_mensuales); ?>;

    // Gráfico de barras verticales para ventas mensuales
    const monthlySalesCtx = document.getElementById("monthlySalesChart").getContext("2d");
    new Chart(monthlySalesCtx, {
        type: 'bar', // Cambiado a 'bar' para gráfico de barras
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas por Mes',
                data: ventasMensuales,
                borderColor: '#007bff',
                backgroundColor: '#007bff', // Barra de color uniforme
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 40, // Máximo en el eje Y
                    stepSize: 10, // Intervalos de 10 en 10
                    title: { display: true, text: 'Cantidad de Ventas' }
                },
                x: {
                    title: { display: true, text: 'Meses' }
                }
            }
        }
    });
</script>


