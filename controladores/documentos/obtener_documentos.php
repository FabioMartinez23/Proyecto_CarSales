<?php

// obtener_documentos.php
require_once('../../modelos/documentaciones.php');

if (isset($_POST['vehiculos_idvehiculos'])) {
    $vehiculos_idvehiculos = $_POST['vehiculos_idvehiculos'];
    $documentacion = new Documentacion();
    $result = $documentacion->mostrar_img_doc_vehiculos($vehiculos_idvehiculos);

    $imagenesHTML = '';
    $documentosHTML = '';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $url = $row['URL_descripcion'];
            $fileType = pathinfo($url, PATHINFO_EXTENSION);
            $urlRelative = str_replace('../../', '', $url);
            $idDocumentacion = $row['idDocumentaciones'];

            // Generar HTML para imágenes con enlace de eliminación
            if (in_array(strtolower($fileType), ['jpg', 'jpeg', 'png'])) {
                $imagenesHTML .= "
                    <div class='image-item'>
                        <a href='#' onclick='ampliarImagen(\"$urlRelative\")'>
                            <img src='$urlRelative' class='img-thumbnail' alt='Imagen subida'>
                        </a>
                        <div>
                            <a href='#' onclick='eliminarDocumento($idDocumentacion)' class='btn-delete'>Eliminar</a>
                        </div>
                    </div>
                ";
            } elseif (strtolower($fileType) === 'pdf') {
                $documentosHTML .= "
                    <div class='document-item'>
                        <div>
                            <a href='$urlRelative' target='_blank'>Ver archivo PDF $idDocumentacion</a>
                        </div>
                        <div>
                            <a href='#' onclick='eliminarDocumento($idDocumentacion)' class='btn-delete'>Eliminar</a>
                        </div>
                    </div>
                ";
            }
        }
    }

    echo json_encode(['imagenes' => $imagenesHTML, 'documentos' => $documentosHTML]);
}




?>

