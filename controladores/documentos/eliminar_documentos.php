<?php
require_once '../../modelos/conexion.php';
require_once '../../modelos/documentaciones.php';

$response = ['success' => false, 'message' => 'Ocurrió un error.'];

if (isset($_POST['id']) && isset($_POST['tipo'])) {
    $id = $_POST['id'];
    $tipo = $_POST['tipo'];

    if ($tipo == 'image' || $tipo == 'document') {
        // Crear una instancia de Documento y asignar el ID del documento a eliminar
        $documento = new Documento();
        $documento->setIddocumentos($id);

        // Llamar al método eliminar_documento
        if ($documento->eliminar_documento()) {
            $response['success'] = true;
            $response['message'] = 'Archivo eliminado correctamente.';
        } else {
            $response['message'] = 'No se pudo eliminar el archivo.';
        }
    } else {
        $response['message'] = 'Tipo de archivo no válido.';
    }
} else {
    $response['message'] = 'Datos incompletos.';
}

echo json_encode($response);
?>
