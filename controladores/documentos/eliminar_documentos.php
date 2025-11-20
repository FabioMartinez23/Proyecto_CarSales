<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

require_once '../../modelos/conexion.php';
require_once '../../modelos/documentaciones.php';

$response = ['success' => false, 'message' => 'Solicitud inválida.'];

// Validar ID
if (!isset($_POST['idDocumentacion'])) {
    echo json_encode($response);
    exit;
}

$id = intval($_POST['idDocumentacion']);

$documento = new Documentacion();
$documento->setIdDocumentaciones($id);

// Ejecutar eliminación
$eliminado = $documento->eliminar_img_doc();

if ($eliminado) {
    $response = [
        'success' => true,
        'message' => 'Archivo eliminado correctamente.'
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'No se pudo eliminar el archivo desde la BD.'
    ];
}

echo json_encode($response);
exit;
?>


