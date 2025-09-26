<?php
// notificacion_trigger.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php'; // Ajusta la ruta según tu estructura

use Pusher\Pusher;

// Configuración Pusher
$options = [
    'cluster' => 'us2',
    'useTLS' => true
];

$pusher = new Pusher(
    'c28c5dc6a68db65e2590', // APP_KEY
    '90c5eb55b40bc7f10daa', // APP_SECRET
    '2054403',              // APP_ID
    $options
);

// Obtener datos por POST
$tipo = $_POST['tipo'] ?? 'default';
$mensaje = $_POST['mensaje'] ?? 'Notificación de prueba';

// Opcional: podés mandar más detalles si querés
$detalles = $_POST['detalles'] ?? [];

// Datos a enviar
$data = [
    'tipo'    => $tipo,
    'mensaje' => $mensaje,
    'fecha'   => date('Y-m-d H:i:s'),
    'detalles'=> $detalles
];

// Disparar el evento al canal "notificaciones"
try {
    $pusher->trigger('notificaciones', 'nuevo-evento', $data);

    echo json_encode([
        'success' => true,
        'data'    => $data
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}
