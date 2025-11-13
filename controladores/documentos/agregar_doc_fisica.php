<?php
ob_clean(); // limpia cualquier salida previa
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

require_once('../../modelos/documentaciones.php');
require_once('../../modelos/conexion.php');

if (empty($_POST['vehiculo_id']) || empty($_POST['tipo_doc_id'])) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Datos incompletos.']);
    exit;
}

$vehiculo_id = intval($_POST['vehiculo_id']);
$tipo_doc_id = intval($_POST['tipo_doc_id']);
$estado_doc = intval($_POST['estado_doc']); // 1 = entregado, 0 = faltante

try {
    $doc = new Documentacion();

    // Verificamos si ya existe el documento físico para este vehículo y tipo
    $existe = $doc->traer_doc_por_vehiculo_tipo($vehiculo_id, $tipo_doc_id);

    if ($existe && $existe->num_rows > 0) {
        // ✅ Si ya existe → actualizamos
        $fila = $existe->fetch_assoc();

        $doc->setIdDocumentaciones($fila['idDocumentaciones']);
        $doc->setEstado_doc($estado_doc);
        $doc->setURL_descripcion(''); // sin URL
        $doc->setDigitalizado(0);
        $doc->actualizar_estado();

        $accion = 'actualizado';
    } else {
        // 🆕 Si no existe → insertamos
        $doc->setVehiculos_idvehiculos($vehiculo_id);
        $doc->setTipo_documentacion_idtipo_documentacion($tipo_doc_id);
        $doc->setEstado_doc($estado_doc);
        $doc->setDigitalizado(0);
        $doc->agregar_doc_fisica();

        $accion = 'insertado';
    }

    // =====================================================
    // 🔍 Verificar si ya están TODOS los documentos físicos
    // =====================================================
    $conexion = new Conexion();

    // Total de tipos de documentación requeridos (excepto imagen)
    $query_total = "SELECT COUNT(*) AS total 
                    FROM tipo_documentacion 
                    WHERE descripcion != 'imagen'";
    $res_total = $conexion->consultar($query_total);
    $total_docs = $res_total->fetch_assoc()['total'];

    // Total de docs entregadas (estado_doc = 1)
    $query_entregadas = "SELECT COUNT(*) AS entregadas
                         FROM documentaciones 
                         WHERE vehiculos_idvehiculos = $vehiculo_id
                         AND estado_doc = 1";
    $res_entregadas = $conexion->consultar($query_entregadas);
    $entregadas = $res_entregadas->fetch_assoc()['entregadas'];

    // Variable para informar al front si se cambió el estado del vehículo
    $estado_actualizado = false;

    // Si están todas entregadas → cambiar el estado del vehículo
    if ($entregadas == $total_docs) {
        // Buscar id del estado 'falta_digitalizacion'
        $query_estado = "SELECT idestado_vehiculo 
                         FROM estado_vehiculo 
                         WHERE estado_vehiculo = 'falta_digitalizacion' 
                         LIMIT 1";
        $res_estado = $conexion->consultar($query_estado);

        if ($res_estado && $res_estado->num_rows > 0) {
            $id_estado = $res_estado->fetch_assoc()['idestado_vehiculo'];

            $query_update = "UPDATE vehiculos 
                             SET estado_vehiculo_idestado_vehiculo = $id_estado 
                             WHERE idvehiculos = $vehiculo_id";
            $conexion->insertar($query_update);
            $estado_actualizado = true;
        }
    }

    // ✅ Respuesta final al frontend
    echo json_encode([
        'status' => 'success',
        'accion' => $accion,
        'estado_actualizado' => $estado_actualizado
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
    exit;
}
?>

