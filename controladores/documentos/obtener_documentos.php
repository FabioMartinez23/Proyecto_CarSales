<?php    
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('../../modelos/documentaciones.php');
require_once('../../modelos/tablas_maestras/tipo_documentacion.php');

header('Content-Type: application/json; charset=utf-8');

/* ============================================================
   1️⃣ Validar entrada
============================================================ */
if (!isset($_POST['vehiculos_idvehiculos'])) {
    echo json_encode(['error' => 'ID de vehículo no recibido.']);
    exit;
}

$vehiculos_idvehiculos = (int) $_POST['vehiculos_idvehiculos'];
$tipo = $_POST['tipo'] ?? 'todos';

/* ============================================================
   2️⃣ Traer documentación existente del vehículo
      (imágenes + PDFs)
============================================================ */
$documentacion = new Documentacion();
$result = $documentacion->mostrar_img_doc_vehiculos($vehiculos_idvehiculos);

$imagenesHTML   = '';
$documentosHTML = '';

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $url = $row['URL_descripcion'];
        if (!$url) continue;

        $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        $urlRelative = str_replace('../../', '', $url);
        $idDocumentacion = (int) $row['idDocumentaciones'];

        /* =======================================================
           🖼️ IMÁGENES
        ======================================================= */
        if (in_array($ext, ['jpg','jpeg','png']) && ($tipo === 'imagen' || $tipo === 'todos')) {

            $imagenesHTML .= "
                <div class='col-md-3 text-center mb-3' data-id='$idDocumentacion'>
                    <img src='$urlRelative' class='img-thumbnail'
                        style='cursor:pointer'
                        onclick='ampliarImagen(\"$urlRelative\")'>

                    <br>

                    <button type='button' 
                            class='btn btn-danger btn-sm'
                            onclick='eliminarDocumento($idDocumentacion, this)'>
                        Eliminar
                    </button>
                </div>
            ";
        }

        /* =======================================================
           📄 DOCUMENTOS PDF
        ======================================================= */
        if ($ext === 'pdf' && ($tipo === 'documentos' || $tipo === 'todos')) {

            // YA TENÉS DESCRIPCIÓN EN EL JOIN DEL MODELO
            $nombreTipo = !empty($row['descripcion']) 
                ? htmlspecialchars($row['descripcion']) 
                : 'Documento PDF';

            $documentosHTML .= "
                <div class='col-md-6 mb-3' data-id='$idDocumentacion'>
                    <div class='p-2 border rounded bg-light d-flex justify-content-between align-items-center'>
                        <a href='$urlRelative' target='_blank' class='fw-bold text-primary'>
                            <i class='fa-solid fa-file-pdf me-1 text-danger'></i> $nombreTipo
                        </a>

                        <button type='button' 
                                class='btn btn-danger btn-sm'
                                onclick='eliminarDocumento($idDocumentacion, this)'>
                            Eliminar
                        </button>
                    </div>
                </div>
            ";
        }
    }
}

/* ============================================================
   3️⃣ Tipos de documentación faltantes → inputs para subir PDFs
============================================================ */
$tipo_documentacion = new Tipo_Documentacion();
$tipos_faltantes = $tipo_documentacion->mostrar_tipos_faltantes($vehiculos_idvehiculos);

$tiposHTML = '';

if ($tipos_faltantes && $tipos_faltantes->num_rows > 0) {

    while ($row = $tipos_faltantes->fetch_assoc()) {

        $idTipo = (int) $row['idtipo_documentacion'];
        $desc   = htmlspecialchars($row['descripcion']);

        $tiposHTML .= "
            <div class='col-md-4 mb-3'>
                <div class='border rounded p-2 bg-light h-100'>
                    <label class='fw-semibold d-block'>$desc</label>
                    <input type='file' 
                           name='documentos_vehiculo_tipo[$idTipo]'
                           class='form-control form-control-sm mt-2' 
                           accept='.pdf'>
                </div>
            </div>
        ";
    }

} else {

    $tiposHTML .= "
        <div class='col-12 text-center text-muted'>
            No hay documentos pendientes para subir.
        </div>
    ";
}

/* ============================================================
   4️⃣ Respuesta JSON
============================================================ */
echo json_encode([
    'imagenes'   => $imagenesHTML,
    'documentos' => $documentosHTML,
    'tipos'      => $tiposHTML
]);
exit;



