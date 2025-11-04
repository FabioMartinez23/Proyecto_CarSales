<?php
require_once('../../modelos/documentaciones.php');

header('Content-Type: application/json');

// Recibir parámetros POST
$vehiculo_id = $_POST['vehiculo_id'] ?? null;
$tipo_doc_id = $_POST['tipo_doc_id'] ?? null;
$estado_doc = isset($_POST['estado_doc']) ? intval($_POST['estado_doc']) : 0;

if (!$vehiculo_id || !$tipo_doc_id) {
    echo json_encode(['success' => false, 'error' => 'Faltan parámetros']);
    exit;
}

$documentacion = new Documentacion();

// Verificar si ya existe un registro para este vehículo y tipo
$docs_existente = $documentacion->traer_doc_por_vehiculo_tipo($vehiculo_id, $tipo_doc_id);

if ($docs_existente && $docs_existente->num_rows > 0) {
    // Actualizar solo estado_doc
    $doc = $docs_existente->fetch_assoc();
    $documentacion->setIdDocumentaciones($doc['idDocumentaciones']);
    $documentacion->setEstado_doc($estado_doc);
    $documentacion->setURL_descripcion(''); // vacío hasta digitalización
    $documentacion->setDigitalizado(0);
    $documentacion->setVehiculos_idvehiculos($vehiculo_id);
    $documentacion->setTipo_documentacion_idtipo_documentacion($tipo_doc_id);

    $documentacion->actualizar_estado();
} else {
    // Insertar nuevo registro con estado_doc
    $documentacion->setVehiculos_idvehiculos($vehiculo_id);
    $documentacion->setTipo_documentacion_idtipo_documentacion($tipo_doc_id);
    $documentacion->setEstado_doc($estado_doc);
    $documentacion->setURL_descripcion(''); // vacío
    $documentacion->setDigitalizado(0);

    $documentacion->agregar_img_doc();
}

echo json_encode(['success' => true]);