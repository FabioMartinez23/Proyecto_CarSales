<div class="container mt-5 hacer_padding">
    <h1 class="text-center mb-4">Estadísticas de la Concesionaria</h1>
    
    <div class="row">
        <!-- Gráfico de Usuarios por Género -->
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5 class="text-center">Usuarios por Género</h5>
                <canvas id="graficoGenero"></canvas>
                <p class="text-muted text-center mt-2">Distribución de usuarios registrados por género.</p>
            </div>
        </div>

        <!-- Gráfico de Ventas Mensuales -->
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5 class="text-center">Ventas Mensuales</h5>
                <canvas id="graficoVentasMensuales"></canvas>
                <p class="text-muted text-center mt-2">Número de ventas realizadas cada mes.</p>
            </div>
        </div>

        <!-- Gráfico de Vehículos por Tipo -->
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5 class="text-center">Vehículos por Tipo</h5>
                <canvas id="graficoVehiculosTipo"></canvas>
                <p class="text-muted text-center mt-2">Clasificación de vehículos en el inventario según su tipo.</p>
            </div>
        </div>

        <!-- Gráfico de Clientes por Rango de Edad -->
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5 class="text-center">Clientes por Rango de Edad</h5>
                <canvas id="graficoRangoEdad"></canvas>
                <p class="text-muted text-center mt-2">Distribución de clientes en distintos rangos de edad.</p>
            </div>
        </div>

        <!-- Gráfico de Simulaciones de Financiamiento Realizadas -->
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5 class="text-center">Simulaciones de Financiamiento</h5>
                <canvas id="graficoSimulaciones"></canvas>
                <p class="text-muted text-center mt-2">Número de simulaciones de financiamiento realizadas por clientes.</p>
            </div>
        </div>

        <!-- Gráfico de Documentos Digitalizados -->
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5 class="text-center">Documentos Digitalizados</h5>
                <canvas id="graficoDocumentos"></canvas>
                <p class="text-muted text-center mt-2">Número de documentos digitalizados por tipo (fotos, PDFs, etc.).</p>
            </div>
        </div>
    </div>
</div>

<script>
    const ctxGenero = document.getElementById('graficoGenero').getContext('2d');
const graficoGenero = new Chart(ctxGenero, {
    type: 'pie',
    data: {
        labels: ['Femenino', 'Masculino'],
        datasets: [{
            data: [60, 40], // Datos de ejemplo
            backgroundColor: ['#ff6384', '#36a2eb'],
            hoverBackgroundColor: ['#ff4384', '#36b2eb']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: { enabled: true }
        }
    }
});


const ctxVentasMensuales = document.getElementById('graficoVentasMensuales').getContext('2d');
const graficoVentasMensuales = new Chart(ctxVentasMensuales, {
    type: 'line',
    data: {
        labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        datasets: [{
            label: 'Ventas',
            data: [15, 20, 18, 25, 30, 45, 50, 55, 60, 50, 40, 30], // Datos de ejemplo
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});


const ctxVehiculosTipo = document.getElementById('graficoVehiculosTipo').getContext('2d');
const graficoVehiculosTipo = new Chart(ctxVehiculosTipo, {
    type: 'bar',
    data: {
        labels: ['SUV', 'Sedán', 'Camioneta', 'Deportivo'],
        datasets: [{
            label: 'Cantidad',
            data: [25, 40, 15, 10], // Datos de ejemplo
            backgroundColor: ['#42a5f5', '#66bb6a', '#ffa726', '#ab47bc']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});


const ctxRangoEdad = document.getElementById('graficoRangoEdad').getContext('2d');
const graficoRangoEdad = new Chart(ctxRangoEdad, {
    type: 'bar',
    data: {
        labels: ['18-25', '26-35', '36-45', '46-60', '60+'],
        datasets: [{
            label: 'Clientes',
            data: [20, 40, 25, 15, 10], // Datos de ejemplo
            backgroundColor: '#66bb6a'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            x: { stacked: true },
            y: { beginAtZero: true }
        }
    }
});


const ctxSimulaciones = document.getElementById('graficoSimulaciones').getContext('2d');
const graficoSimulaciones = new Chart(ctxSimulaciones, {
    type: 'line',
    data: {
        labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        datasets: [{
            label: 'Simulaciones',
            data: [10, 15, 25, 20, 30, 45, 60, 55, 50, 40, 35, 20], // Datos de ejemplo
            backgroundColor: 'rgba(153, 102, 255, 0.2)',
            borderColor: 'rgba(153, 102, 255, 1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});


const ctxDocumentos = document.getElementById('graficoDocumentos').getContext('2d');
const graficoDocumentos = new Chart(ctxDocumentos, {
    type: 'doughnut',
    data: {
        labels: ['Fotos', 'PDFs', 'Otros'],
        datasets: [{
            data: [50, 30, 20], // Datos de ejemplo
            backgroundColor: ['#ffcd56', '#4bc0c0', '#ff6384'],
            hoverBackgroundColor: ['#ffb056', '#4bc0b0', '#ff5384']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: { enabled: true }
        }
    }
});

</script>
