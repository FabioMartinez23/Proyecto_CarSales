<?php
require_once '../../modelos/conexion.php';
require_once '../../modelos/documentaciones.php';

header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => 'Solicitud inválida.'];

if (isset($_POST['idDocumentacion'])) {
    $id = intval($_POST['idDocumentacion']);

    // Crear instancia y setear ID
    $documento = new Documentacion();
    $documento->setIdDocumentaciones($id);

    // Ejecutar eliminación
    if ($documento->eliminar_img_doc()) {
        $response = [
            'success' => true,
            'message' => 'Archivo eliminado correctamente.'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'No se pudo eliminar el archivo.'
        ];
    }
}

echo json_encode($response);
exit;
?>
