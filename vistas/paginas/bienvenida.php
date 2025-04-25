<?php
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cantidad_vehiculos = new Vehiculos();
$result_cant_vehiculos = $cantidad_vehiculos->traer_cantidad_vehiculo();
foreach($result_cant_vehiculos as $total_vehiculos){
    $total_vehiculos_count = $total_vehiculos['total'];
}

$cantidad_usuarios = new Usuario();
$result_cant_usuarios = $cantidad_usuarios->traer_cantidad_usuario();
foreach($result_cant_usuarios as $total_usuarios){
    $total_usuarios_count = $total_usuarios['total'];
}

$cantidad_ventas = new VenderVehiculo();
$result_cant_ventas = $cantidad_ventas->traer_cantidad_ventas();
foreach($result_cant_ventas as $total_ventas){
    $total_ventas_count = $total_ventas['total'];
}

// Verifica si la sesión contiene las variables necesarias
if (!isset($_SESSION['username']) || !isset($_SESSION['idusuarios']) || !isset($_SESSION['descripcion'])) {
    header('location: ../index.php?page=login&mensaje=Debes iniciar sesión primero.&status=warning');
    exit();
}

// Configurar la zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configurar el idioma en español
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain');

// Obtener la fecha en español
$fecha_actual = utf8_encode(strftime('%A, %d de %B de %Y')); // utf8_encode codifica la fechas para que puedan aceptar acentos o ñ


?>
<header class="dashboard-header">
    <h1>
        <?php 
            $usuario = new Usuario();
            $idusuario = $_SESSION['idusuarios'];
            $resultado = $usuario->traer_usuarios_y_personas($idusuario);

            if (!empty($resultado) && isset($_SESSION['username'])) {
                foreach($resultado as $datos){
                    echo '<h1>¡Bienvenido, ' . $datos['nombre'] . ' ' . $datos['apellido'].'!</h1>';
                    echo '<p>Hoy es: <span id="currentDate">' . ucfirst($fecha_actual) . '</span></p>';
                }
            } else {
                echo 'Error';
            }
        ?>
    </h1>
</header>

<div class="hacer_padding">
    <?php 
if($_SESSION['descripcion'] == 'Administrador' || $_SESSION['descripcion'] == 'Empleado'){
    echo '<main class="my-4">
        <!-- Resumen principal -->
        <section class="stats d-flex justify-content-between">
            <div class="card text-center p-3" onclic=\"windows.location.href="index.php?page=listado_vehiculos"\">
                <h2>'.$total_vehiculos_count.'</h2>
                <p>Vehículos Disponibles</p>
            </div>
            <div class="card text-center p-3">
                <h2>'.$total_ventas_count.'</h2>
                <p>Ventas Concretadas</p>
            </div>
            <div class="card text-center p-3">
                <h2>87.5%</h2>
                <p>Reportes y Estadísticas</p>
            </div>
            <div class="card text-center p-3">
                <h2>'.$total_usuarios_count.'</h2>
                <p>Total Usuarios Registrados</p>
            </div>
        </section>

        <section class="stats d-flex justify-content-between">
            <div class="card text-center p-3">
                <h2>150</h2>
                <p>Vehículos en Reparación</p>
            </div>
            <div class="card text-center p-3">
                <h2>$5.000.000</h2>
                <p>Ingresos del Mes</p>
            </div>
            <div class="card text-center p-3">
                <h2>42</h2>
                <p>Nuevos Clientes del Mes</p>
            </div>
            <div class="card text-center p-3">
                <h2>Ford Focus</h2>
                <p>Vehículo Más Popular del Mes</p>
            </div>
        </section>

        <!-- Gráficos -->
        <section class="charts row mt-4">
            <div class="col-md-6">
                <canvas id="barChart"></canvas>
            </div>
            <div class="col-md-4">
                <canvas id="pieChart"></canvas>
            </div>
        </section>
        <section class="charts row mt-4">
            <div class="col-md-6">
                <canvas id="lineChart"></canvas>
            </div>
        </section>

        <!-- Acciones rápidas -->
        <h3 class="text-center">Accesos Rápidos</h3>
        <section class="quick-actions d-flex justify-content-around mt-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ventaModal">Registrar Nueva Venta</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehiculoModal">Agregar Vehículo</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clienteModal">Consultar Clientes</button>
        </section>
    </main>
    </div>';
}
?>

<script>
    // Inicializa gráficos con Chart.js
    const barChart = new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: ['Ford', 'Chevrolet', 'Toyota', 'Nissan', 'Honda'],
            datasets: [{
                label: 'Cantidad Vendida',
                data: [15, 12, 10, 8, 5],
                backgroundColor: ['#003366', '#00509e', '#0074d9', '#0099ff', '#33ccff']
            }]
        }
    });

    const pieChart = new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: ['Clientes Nuevos', 'Clientes Frecuentes'],
            datasets: [{
                data: [30, 70],
                backgroundColor: ['#00509e', '#33ccff']
            }]
        }
    });

    const lineChart = new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
            datasets: [{
                label: 'Ventas Mensuales',
                data: [10000, 12000, 15000, 20000, 25000, 30000],
                borderColor: '#003366',
                fill: false
            }]
        }
    });
</script>

