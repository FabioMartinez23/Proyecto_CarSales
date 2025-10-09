<?php
ini_set('display_errors', 1);
require_once('../../modelos/documentaciones.php');

if (isset($_POST['action'])) {
    $documentos_controlador = new DocumentosControlador();

    switch ($_POST['action']) {
        case 'guardar':
            $documentos_controlador->guardar(); // guarda imágenes y documentos
            break;

        case 'guardar_imagenes':
            $documentos_controlador->guardarImagenes(); // solo imágenes
            break;

        case 'guardar_documentos':
            $documentos_controlador->guardarDocumentos(); // solo documentos
            break;
    }
}

class DocumentosControlador {

    /* -------------------------------------------------------------------------- */
    /*                               Acción principal                              */
    /* -------------------------------------------------------------------------- */
    public function guardar() {
        $idvehiculos = $_POST['vehiculos_idvehiculos'];
        $mensajes = [];
        $status = 'success';

        $mensajes = array_merge(
            $this->procesarArchivos($_FILES['imagen_vehiculo'] ?? null, $idvehiculos, 'img', 10), // ID fijo para "Imágenes del Vehículo"   
            $this->procesarArchivos($_FILES['documentos_vehiculo'] ?? null, $idvehiculos, 'doc')
        );

        if ($this->contieneErrores($mensajes)) $status = 'error';

        $this->redireccionar($mensajes, $status);
    }

    /* -------------------------------------------------------------------------- */
    /*                            Solo guardar imágenes                            */
    /* -------------------------------------------------------------------------- */
    public function guardarImagenes() {
        $idvehiculos = $_POST['vehiculos_idvehiculos'];
        $idtipo_documentacion = 10; // ID fijo para "Imágenes del Vehículo"
        $mensajes = $this->procesarArchivos($_FILES['imagen_vehiculo'] ?? null, $idvehiculos, 'img', $idtipo_documentacion);
        $status = $this->contieneErrores($mensajes) ? 'error' : 'success';
        $this->redireccionar($mensajes, $status);
    }

    /* -------------------------------------------------------------------------- */
    /*                           Solo guardar documentos                           */
    /* -------------------------------------------------------------------------- */
    public function guardarDocumentos() {
        $idvehiculos = $_POST['vehiculos_idvehiculos'];
        $idtipo_documentacion = $_POST['tipo_documentacion_idtipo_documentacion'] ?? null;

        $mensajes = $this->procesarArchivos($_FILES['documentos_vehiculo'] ?? null, $idvehiculos, 'doc', $idtipo_documentacion);
        $status = $this->contieneErrores($mensajes) ? 'error' : 'success';
        $this->redireccionar($mensajes, $status);
    }

    /* -------------------------------------------------------------------------- */
    /*                            Métodos reutilizables                            */
    /* -------------------------------------------------------------------------- */

    /** Procesa múltiples archivos de un tipo (img o doc) */
    private function procesarArchivos($archivos, $idvehiculos, $tipo, $idtipo_documentacion = null) {
        $mensajes = [];

        if ($archivos && isset($archivos['error'])) {
            foreach ($archivos['name'] as $i => $nombre) {
                if ($archivos['error'][$i] === 0) {
                    $archivo = [
                        'name' => $nombre,
                        'tmp_name' => $archivos['tmp_name'][$i],
                        'error' => $archivos['error'][$i]
                    ];
                    $mensajes[] = $this->guardarArchivo($archivo, $idvehiculos, $tipo, $idtipo_documentacion);
                }
            }
        }

        return $mensajes;
    }

    /** Guarda un archivo individual */
    private function guardarArchivo($archivo, $idvehiculos, $tipo, $idtipo_documentacion) {
        $documentacion = new Documentacion();
        $uploadDir = ($tipo === 'img') ? '../../uploads/img/' : '../../uploads/doc/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($archivo['name']);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($fileType, $allowedTypes)) {
            return "Error: Formato de archivo no permitido ($fileType).";
        }

        if (!move_uploaded_file($archivo['tmp_name'], $targetFilePath)) {
            return "Error: No se pudo mover el archivo al servidor.";
        }

        // Guardar en la base de datos
        $documentacion->setURL_descripcion($targetFilePath);
        $documentacion->setVehiculos_idvehiculos($idvehiculos);

        if ($idtipo_documentacion) {
            $documentacion->setTipo_documentacion_idtipo_documentacion($idtipo_documentacion);
        }

        if ($documentacion->agregar_img_doc()) {
            return ($tipo === 'img')
                ? "Imagen guardada correctamente."
                : "Documento guardado correctamente.";
        } else {
            return ($tipo === 'img')
                ? "Error al guardar la imagen en la base de datos."
                : "Error al guardar el documento en la base de datos.";
        }
    }

    /** Verifica si hay algún mensaje de error */
    private function contieneErrores($mensajes) {
        foreach ($mensajes as $msg) {
            if (stripos($msg, 'error') !== false) {
                return true;
            }
        }
        return false;
    }

    /** Redirecciona con los mensajes concatenados */
    private function redireccionar($mensajes, $status) {
        // Obtener la acción actual desde el formulario
        $accion = $_POST['action'] ?? '';

        // Determinar la página de destino según la acción
        switch ($accion) {
            case 'guardar':
                $page = 'listado_vehiculos';
                break;
            case 'guardar_imagenes':
            case 'guardar_documentos':
                $page = 'listado_falta_documentacion';
                break;
            default:
                // Si llega algo inesperado, volvemos al listado general por seguridad
                $page = 'listado_vehiculos';
                break;
        }

        // Armar el mensaje
        $mensaje = implode(" | ", $mensajes);

        // Redirigir
        header("Location: ../../index.php?page={$page}&mensaje=" . urlencode($mensaje) . "&status={$status}");
        exit();
    }
}
?>
