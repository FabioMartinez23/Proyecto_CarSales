<?php    
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('../../modelos/documentaciones.php');
require_once('../../modelos/tablas_maestras/tipo_documentacion.php');

if (isset($_POST['vehiculos_idvehiculos'])) {
    $vehiculos_idvehiculos = $_POST['vehiculos_idvehiculos'];
    $tipo = $_POST['tipo'] ?? 'todos';

    $documentacion = new Documentacion();
    $result = $documentacion->mostrar_img_doc_vehiculos($vehiculos_idvehiculos);

    $imagenesHTML = '';
    $documentosHTML = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $url = $row['URL_descripcion'];
            $fileType = strtolower(pathinfo($url, PATHINFO_EXTENSION));
            $urlRelative = str_replace('../../', '', $url);
            $idDocumentacion = $row['idDocumentaciones'];

            // 🖼️ Si es imagen
           if (in_array($fileType, ['jpg','jpeg','png']) && ($tipo==='imagenes'||$tipo==='todos')) {
                $imagenesHTML .= "
                    <div class='image-item' data-id='$idDocumentacion'>
                        <a href='#' onclick='ampliarImagen(\"$urlRelative\")'>
                            <img src='$urlRelative' class='img-thumbnail' alt='Imagen subida'>
                        </a>
                        <div>
                            <button type='button' class='btn btn-danger btn-sm' onclick='eliminarDocumento($idDocumentacion, this)'>Eliminar</button>
                        </div>
                    </div>
                ";
            } elseif ($fileType==='pdf' && ($tipo==='documentos'||$tipo==='todos')) {
                $documentosHTML .= "
                    <div class='document-item' data-id='$idDocumentacion'>
                        <div>
                            <a href='$urlRelative' target='_blank'>{$row['descripcion']}</a>
                        </div>
                        <div>
                            <button type='button' class='btn btn-danger btn-sm' onclick='eliminarDocumento($idDocumentacion, this)'>Eliminar</button>
                        </div>
                    </div>
                ";
            }
        }
    }

    // 🔽 Tipos faltantes
    $tipo_documentacion = new Tipo_Documentacion();
    $result_tipos_faltantes = $tipo_documentacion->mostrar_tipos_faltantes($vehiculos_idvehiculos); 
    $tiposHTML = '<option value="">Seleccione un tipo...</option>';

    if ($result_tipos_faltantes->num_rows > 0) {
        while ($row = $result_tipos_faltantes->fetch_assoc()) {
            $tiposHTML .= "<option value='{$row['idtipo_documentacion']}'>{$row['descripcion']}</option>";
        }
    } else {
        $tiposHTML .= "<option value=''>No hay tipos disponibles</option>";
    }

    echo json_encode([
        'imagenes' => $imagenesHTML,
        'documentos' => $documentosHTML,
        'tipos' => $tiposHTML
    ]);
}
