<?php
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ============================
// VERIFICACIONES DE SESIÓN
// ============================
if (!isset($_SESSION['username']) || !isset($_SESSION['idusuarios']) || !isset($_SESSION['descripcion'])) {
    header('location: ../index.php?page=login&mensaje=Debes iniciar sesión primero.&status=warning');
    exit();
}

// Configuración local
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain');
$fecha_actual = utf8_encode(strftime('%A, %d de %B de %Y'));

// ============================
// CONTADORES GENERALES
// ============================
$cantidad_vehiculos = new Vehiculos();
$total_vehiculos_count = $cantidad_vehiculos->contarVehiculosDisponibles();

$cantidad_vehiculos_sin_digitar = new Vehiculos();
$total_vehiculos_sin_digitar_count = $cantidad_vehiculos_sin_digitar->contarVehiculosSinDigitar();

$cantidad_vehiculos_sin_documentacion = new Vehiculos();
$total_vehiculos_sin_documentacion_count = $cantidad_vehiculos_sin_documentacion->contarVehiculosConFaltante();

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

$cantidad_anuladas = new VenderVehiculo();
$result_cant_anuladas = $cantidad_anuladas->cantidad_ventas_anuladas();
foreach($result_cant_anuladas as $total_anuladas){
    $total_anuladas_count = $total_anuladas['total'];
}

// ============================
// CAJA AUTOMÁTICA MENSUAL
// ============================
$caja = new Caja();
$idusuario = $_SESSION['idusuarios'];
$caja_actual = $caja->verificar_o_abrir_caja_mensual($idusuario);

// ============================
// BALANCE MENSUAL (para gráficos)
// ============================
$balance_mensual = $caja->traer_balance_mensual(); // ✅ método real
$labels_mes = [];
$ingresos_mes = [];
$egresos_mes = [];
$balance_mes = [];

while ($fila = $balance_mensual->fetch_assoc()) {
    $labels_mes[] = $fila['periodo'];
    $ingresos_mes[] = (float)$fila['total_ingresos'];
    $egresos_mes[] = (float)$fila['total_egresos'];
    $balance_mes[] = (float)$fila['balance_mensual'];
}

// convertir a JSON
$labels_mes_json = json_encode($labels_mes);
$ingresos_mes_json = json_encode($ingresos_mes);
$egresos_mes_json = json_encode($egresos_mes);
$balance_mes_json = json_encode($balance_mes);

// Calcular el último balance
$ultimo_balance = end($balance_mes);
?>

<header class="dashboard-header">
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
            echo 'Error al cargar los datos del usuario.';
        }
    ?>
</header>

<div class="hacer_padding">
<?php 
if($_SESSION['descripcion'] == 'Administrador' || $_SESSION['descripcion'] == 'Empleado'){
    echo '
    <main class="my-4">
        <!-- Resumen principal -->
        <section class="stats d-flex justify-content-between flex-wrap">
            <div class="card text-center p-3" onclick="window.location.href=\'index.php?page=listado_vehiculos\'">
                <h2>'.$total_vehiculos_count.'</h2>
                <p>Vehículos Disponibles</p>
            </div>
            <div class="card text-center p-3" onclick="window.location.href=\'index.php?page=listado_falta_documentacion&estado=2\'">
                <h2>'.$total_vehiculos_sin_documentacion_count.'</h2>
                <p>Vehículos Sin Documentación</p>
            </div>
            <div class="card text-center p-3" onclick="window.location.href=\'index.php?page=listado_falta_documentacion&estado=3\'">
                <h2>'.$total_vehiculos_sin_digitar_count.'</h2>
                <p>Vehículos Que Faltan Digitalizar</p>
            </div>
            <div class="card text-center p-3">
                <h2>42</h2>
                <p>Nuevos Clientes del Mes</p>
            </div>
        </section>

        <section class="stats d-flex justify-content-between flex-wrap mt-4">
            <div class="card text-center p-3">
                <h2>'.$total_ventas_count.'</h2>
                <p>Ventas Concretadas</p>
            </div>
            <div class="card text-center p-3">
                <h2>'.$total_anuladas_count.'</h2>
                <p>Ventas Anuladas</p>
            </div>
            <div class="card text-center p-3">
                <h2>'.$total_usuarios_count.'</h2>
                <p>Total Usuarios Registrados</p>
            </div>
            <div class="card text-center p-3">
                <h2>$'.number_format($ultimo_balance, 2, ',', '.').'</h2>
                <p>Balance del Mes</p>
            </div>
        </section>

        <!-- ===========================
             TARJETA DE ESTADO DE CAJA
        =========================== -->';

        // traer datos de caja actual
        $estado_caja = 'Sin datos';
        $fecha_apertura = '-';
        $saldo_actual = 0.00;

        if ($caja_actual && $caja_actual->num_rows > 0) {
            $datos_caja = $caja_actual->fetch_assoc();
            $estado_caja = ucfirst($datos_caja['estado']);
            $fecha_apertura = date('d/m/Y H:i', strtotime($datos_caja['fecha_apertura']));
            $saldo_actual = $datos_caja['saldo_actual'];
        } else {
            $ultima_cerrada = $caja->traer_cajas_cerradas();
            if ($ultima_cerrada && $ultima_cerrada->num_rows > 0) {
                $fila = $ultima_cerrada->fetch_assoc();
                $estado_caja = 'Cerrada';
                $fecha_apertura = date('d/m/Y H:i', strtotime($fila['fecha_apertura']));
                $saldo_actual = $fila['saldo_actual'];
            }
        }

        echo '
        <section class="estado-caja my-4">
            <div class="card text-center p-4 shadow-sm" 
                 style="border-left: 6px solid '.(($estado_caja == 'Abierta') ? '#198754' : '#dc3545').';">
                <h4 class="fw-bold mb-3">
                    <i class="fa-solid fa-cash-register me-2"></i>
                    Estado de la Caja: 
                    <span class="text-'.(($estado_caja == 'Abierta') ? 'success' : 'danger').'">
                        '.strtoupper($estado_caja).'
                    </span>
                </h4>
                <p><strong>Fecha de apertura:</strong> '.$fecha_apertura.'</p>
                <p><strong>Saldo actual:</strong> $'.number_format($saldo_actual, 2, ',', '.').'</p>';

                if ($estado_caja == 'Cerrada') {
                    echo '<button class="btn btn-sm btn-outline-primary mt-2" 
                                onclick="window.location.href=\'index.php?page=caja/cierres_caja\'">
                                <i class="fa-solid fa-folder-open me-1"></i> Ver cierres de caja
                          </button>';
                } else {
                    echo '<button class="btn btn-sm btn-outline-danger mt-2" id="btnCerrarCaja">
                            <i class="fa-solid fa-lock me-1"></i> Cerrar Caja
                          </button>';
                }

        echo '</div>
        </section>

        <!-- Gráficos -->
        <section class="charts row mt-5">
            <div class="col-md-12">
                <canvas id="lineChart"></canvas>
            </div>
        </section>

        <!-- Acciones rápidas -->
        <h3 class="text-center mt-5">Accesos Rápidos</h3>
        <section class="quick-actions d-flex justify-content-around mt-4 flex-wrap">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ventaModal">Registrar Nueva Venta</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehiculoModal">Agregar Vehículo</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clienteModal">Consultar Clientes</button>
        </section>
    </main>';
}
?>
</div>

<!-- ===========================
     GRÁFICO FINANCIERO
=========================== -->
<script>
const labelsMes = <?php echo $labels_mes_json; ?>;
const ingresosMes = <?php echo $ingresos_mes_json; ?>;
const egresosMes = <?php echo $egresos_mes_json; ?>;
const balanceMes = <?php echo $balance_mes_json; ?>;

const lineChart = new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: labelsMes,
        datasets: [
            {
                label: 'Ingresos',
                data: ingresosMes,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.2)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Egresos',
                data: egresosMes,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Balance',
                data: balanceMes,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: false,
                borderDash: [5, 5],
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Evolución mensual de caja (Ingresos vs Egresos)'
            },
            legend: { position: 'bottom' }
        },
        scales: { y: { beginAtZero: true } }
    }
});

// Cierre rápido de caja por AJAX
document.addEventListener("DOMContentLoaded", () => {
    const btnCerrar = document.getElementById("btnCerrarCaja");
    if (btnCerrar) {
        btnCerrar.addEventListener("click", () => {
            Swal.fire({
                title: "¿Deseas cerrar la caja?",
                text: "Esta acción cerrará el registro mensual actual.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, cerrar",
                cancelButtonText: "Cancelar"
            }).then((r) => {
                if (r.isConfirmed) {
                    fetch("controladores/caja/caja.controlador.php", {
                        method: "POST",
                        body: new URLSearchParams({
                            action: "cerrar_caja_mensual"
                        })
                    })
                    .then(resp => resp.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.status,
                            title: data.mensaje,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire("Error", "No se pudo cerrar la caja", "error");
                        console.error(err);
                    });
                }
            });
        });
    }
});
</script>


