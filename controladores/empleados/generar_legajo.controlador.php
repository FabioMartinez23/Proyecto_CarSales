<?php
session_start();
require_once('../../modelos/conexion.php');
require_once('../../modelos/empleados.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idusuario = $_POST['idempleados'] ?? null; // ID del usuario
    $idtipo_puesto = $_POST['idtipo_puesto'] ?? null;

    if (!$idusuario || !$idtipo_puesto) {
        header("Location: ../../index.php?page=listado_empleados&mensaje=Faltan datos para generar legajo&status=error");
        exit;
    }

    $empleado = new Empleado();

    // Traer último legajo
    $ultimo = $empleado->obtenerUltimoLegajo();
    if ($ultimo) {
        $numero = (int)substr($ultimo, 3); // Ej: AAA025 → 25
        $nuevo_legajo = "000" . str_pad($numero + 1, 3, "0", STR_PAD_LEFT);
    } else {
        $nuevo_legajo = "000001";
    }

    // Verificar si ya existe fila en empleados para este usuario
    $idempleado_existente = $empleado->traerEmpleadoPorUsuario($idusuario); // Debe devolver idempleados o null

    if ($idempleado_existente) {
        // Actualizar
        $empleado->actualizarLegajoYPuesto($idempleado_existente, $nuevo_legajo, $idtipo_puesto);
    } else {
        // Insertar
        $empleado->asignarLegajoYPuesto($idusuario, $nuevo_legajo, $idtipo_puesto);
    }

    header("Location: ../../index.php?page=listado_empleados&mensaje=Legajo generado con éxito&status=success");
    exit;
}

