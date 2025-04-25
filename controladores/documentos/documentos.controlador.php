<?php

ini_set('display_errors', 1);
require_once('../../modelos/documentaciones.php');

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'guardar') {
        $documentos_controlador = new DocumentosControlador();
        $documentos_controlador->guardar();
    }
}

class DocumentosControlador {

    public function guardar() {
        $idvehiculos = $_POST['vehiculos_idvehiculos'];
        $mensajes = [];
        $status = 'success';

        // Guardar todas las imágenes del vehículo, si se subieron
        if (isset($_FILES['imagen_vehiculo']['error'])) {
            foreach ($_FILES['imagen_vehiculo']['name'] as $index => $name) {
                if ($_FILES['imagen_vehiculo']['error'][$index] == 0) {
                    $archivoImagen = [
                        'name' => $name,
                        'tmp_name' => $_FILES['imagen_vehiculo']['tmp_name'][$index],
                        'error' => $_FILES['imagen_vehiculo']['error'][$index]
                    ];
                    $resultadoImagen = $this->guardarImagen($archivoImagen, $idvehiculos);
                    $mensajes[] = $resultadoImagen;
                    if (strpos($resultadoImagen, 'Error') !== false) {
                        $status = 'error';
                    }
                }
            }
        }

        // Guardar todos los documentos del vehículo, si se subieron
        if (isset($_FILES['documentos_vehiculo']['error'])) {
            foreach ($_FILES['documentos_vehiculo']['name'] as $index => $name) {
                if ($_FILES['documentos_vehiculo']['error'][$index] == 0) {
                    $archivoDocumento = [
                        'name' => $name,
                        'tmp_name' => $_FILES['documentos_vehiculo']['tmp_name'][$index],
                        'error' => $_FILES['documentos_vehiculo']['error'][$index]
                    ];
                    $resultadoDocumento = $this->guardarDocumento($archivoDocumento, $idvehiculos);
                    $mensajes[] = $resultadoDocumento;
                    if (strpos($resultadoDocumento, 'Error') !== false) {
                        $status = 'error';
                    }
                }
            }
        }

        // Mensaje final unificado
        $mensaje = implode(" | ", $mensajes);
        header("Location: ../../index.php?page=listado_vehiculos&mensaje=" . urlencode($mensaje) . "&status=" . $status);
        exit();
    }

    private function guardarImagen($archivo, $idvehiculos) {
        return $this->guardarArchivo($archivo, $idvehiculos, 'img');
    }

    private function guardarDocumento($archivo, $idvehiculos) {
        return $this->guardarArchivo($archivo, $idvehiculos, 'doc');
    }

    private function guardarArchivo($archivo, $idvehiculos, $tipo) {
        $documentacion = new Documentacion();

        $uploadDir = ($tipo == 'img') ? '../../uploads/img/' : '../../uploads/doc/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($archivo['name']);
        $targetFilePath = $uploadDir . $fileName;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowedTypes = array('jpg', 'jpeg', 'png', 'pdf');

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($archivo['tmp_name'], $targetFilePath)) {
                $documentacion->setURL_descripcion($targetFilePath);
                $documentacion->setVehiculos_idvehiculos($idvehiculos);

                return $documentacion->agregar_img_doc() ? 
                    (($tipo == 'img') ? "Imagen guardada correctamente." : "Documento guardado correctamente.") :
                    (($tipo == 'img') ? "Error al guardar la imagen en la base de datos." : "Error al guardar el documento en la base de datos.");
            } else {
                return "Error al mover el archivo al servidor.";
            }
        } else {
            return "Formato de archivo no permitido. Solo JPG, JPEG, PNG o PDF.";
        }
    }
}



?>
