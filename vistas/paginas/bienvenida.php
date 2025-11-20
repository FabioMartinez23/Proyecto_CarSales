<?php
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ============================
// VERIFICACION DE SESIÓN
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
// CARGA DE DATOS DEL USUARIO
// ============================
$usuario = new Usuario();
$idusuario = $_SESSION['idusuarios'];
$resultado = $usuario->traer_usuarios_y_personas($idusuario);

// Encabezado
echo '<header class="dashboard-header">';

if (!empty($resultado)) {
    foreach ($resultado as $datos) {
        echo '<h1>¡Bienvenido, ' . $datos['nombre'] . ' ' . $datos['apellido'] . '!</h1>';
        echo '<p>Hoy es: <span id="currentDate">' . ucfirst($fecha_actual) . '</span></p>';
    }
}
echo '</header>';

// =====================================
// DATOS GENERALES PARA ADMIN / EMPLEADO
// =====================================
$cantidad_vehiculos = new Vehiculos();
$total_vehiculos_count = $cantidad_vehiculos->contarVehiculosDisponibles();

// Vehículos sin digitalizar
$cantidad_vehiculos_sin_digitar = new Vehiculos();
$total_vehiculos_sin_digitar_count = $cantidad_vehiculos_sin_digitar->contarVehiculosSinDigitar();

// Vehículos sin documentación
$cantidad_vehiculos_sin_documentacion = new Vehiculos();
$total_vehiculos_sin_documentacion_count = $cantidad_vehiculos_sin_documentacion->contarVehiculosConFaltante();

// Cantidad usuarios
$cantidad_usuarios = new Usuario();
$result_cant_usuarios = $cantidad_usuarios->traer_cantidad_usuario();
foreach ($result_cant_usuarios as $u) {
    $total_usuarios_count = $u['total'];
}

// Ventas
$cantidad_ventas = new VenderVehiculo();
$result_cant_ventas = $cantidad_ventas->traer_cantidad_ventas();
foreach ($result_cant_ventas as $v) {
    $total_ventas_count = $v['total'];
}

// Ventas anuladas
$cantidad_anuladas = new VenderVehiculo();
$result_cant_anuladas = $cantidad_anuladas->cantidad_ventas_anuladas();
foreach ($result_cant_anuladas as $a) {
    $total_anuladas_count = $a['total'];
}

// ================================
// CAJA MENSUAL (solo Admin)
// ================================
$caja = new Caja();
$caja_actual = $caja->verificar_o_abrir_caja_mensual($idusuario);

// Balance mensual para gráfico
$balance_mensual = $caja->traer_balance_mensual();
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

$labels_mes_json = json_encode($labels_mes);
$ingresos_mes_json = json_encode($ingresos_mes);
$egresos_mes_json = json_encode($egresos_mes);
$balance_mes_json = json_encode($balance_mes);
$ultimo_balance = end($balance_mes);

// ===============================
// VALORES POR DEFECTO EMPLEADO
// ===============================
$ventas_empleado = 0;
$ventas_anuladas_empleado = 0;
$mis_ganancias_mes = 0.00;

if ($_SESSION['descripcion'] === 'Empleado') {
    // Usamos tu modelo real de ventas
    $ventasModel = new VenderVehiculo();

    // Ventas realizadas por este usuario (empleado)
    $rowVE = $ventasModel->ventas_empleado_total($idusuario);
    $ventas_empleado = isset($rowVE['total']) ? (int)$rowVE['total'] : 0;

    // Ventas anuladas por este usuario (empleado)
    $rowVA = $ventasModel->ventas_empleado_anuladas($idusuario);
    $ventas_anuladas_empleado = isset($rowVA['total']) ? (int)$rowVA['total'] : 0;

    // 2) Comisiones reales del empleado
    $comModel = new Comisiones_Ventas();

    $rowCom = $comModel->comisiones_empleado_mes($idusuario);
    $mis_ganancias_mes = isset($rowCom['total']) ? floatval($rowCom['total']) : 0.00;
}

?>

<div class="hacer_padding">

<?php

switch ($_SESSION['descripcion']) {

    case 'Administrador':
        include 'bienvenida/bienvenida_admin.php';
        break;

    case 'Empleado':
        include 'bienvenida/bienvenida_empleado.php';
        break;

    case 'Cliente':
        include 'bienvenida/bienvenida_cliente.php';
        break;

    default:
        echo "<p class='text-danger'>Error: Perfil no reconocido.</p>";
        break;
}
?>

</div>