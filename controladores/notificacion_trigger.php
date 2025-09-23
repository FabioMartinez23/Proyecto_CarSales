<?php
require __DIR__ . '/../vendor/autoload.php'; // Ajusta la ruta según tu proyecto

$options = [
    'cluster' => 'us2',
    'useTLS' => true
];

$pusher = new Pusher\Pusher(
    'c28c5dc6a68db65e2590',
    '90c5eb55b40bc7f10daa',
    '2054403',
    $options
);

$tipo = $_POST['tipo'] ?? 'default';
$mensaje = $_POST['mensaje'] ?? 'Notificación de prueba';

$data = [
    'tipo' => $tipo,
    'mensaje' => $mensaje,
    'fecha' => date('Y-m-d H:i:s')
];

$pusher->trigger('notificaciones', 'nuevo-evento', $data);

echo json_encode(['success' => true, 'data' => $data]);
