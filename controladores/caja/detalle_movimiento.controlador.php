<?php
session_start();
require_once('../../modelos/caja.php');

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode([
        'status'  => 'error',
        'mensaje' => 'ID de movimiento inválido.'
    ]);
    exit;
}

$id = intval($_GET['id']);

$caja = new Caja();
$mov = $caja->traer_movimiento_por_id($id);

if (!$mov) {
    echo json_encode([
        'status'  => 'error',
        'mensaje' => 'Movimiento no encontrado.'
    ]);
    exit;
}

// Formateos útiles desde PHP
$fecha_formateada = date('d/m/Y H:i', strtotime($mov['fecha_movimiento']));
$monto_formateado = '$' . number_format($mov['monto'], 2, ',', '.');

$fecha_apertura_caja = null;
if (!empty($mov['fecha_apertura'])) {
    $fecha_apertura_caja = date('d/m/Y H:i', strtotime($mov['fecha_apertura']));
}

echo json_encode([
    'status'     => 'success',
    'movimiento' => [
        'id'                    => $mov['idcaja_movimientos'],
        'fecha'                 => $mov['fecha_movimiento'],
        'fecha_formateada'      => $fecha_formateada,
        'tipo'                  => $mov['tipo'],
        'monto'                 => $mov['monto'],
        'monto_formateado'      => $monto_formateado,
        'descripcion'           => $mov['descripcion'],
        'tipo_movimiento'       => $mov['tipo_movimiento'] ?? '',
        'tipo_pago'             => $mov['tipo_pago'] ?? '',
        'referencia_tabla'      => $mov['referencia_tabla'],
        'referencia_id'         => $mov['referencia_id'],
        'caja_id'               => $mov['caja_idcaja'],
        'fecha_apertura_caja'   => $fecha_apertura_caja,
        'saldo_inicial_caja'    => $mov['saldo_inicial'] ?? null,
        'saldo_actual_caja'     => $mov['saldo_actual'] ?? null,
        'usuario'               => $mov['username'] ?? ''
    ]
]);
