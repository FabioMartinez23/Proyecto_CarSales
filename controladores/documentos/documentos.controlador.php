<?php
ini_set('display_errors', 1);
require_once('../../modelos/documentaciones.php');
require_once('../../modelos/conexion.php');
require_once('../../modelos/tablas_maestras/tipo_documentacion.php');
require_once('../../modelos/vehiculos.php');

if (isset($_POST['action'])) {
    $documentos_controlador = new DocumentosControlador();

    switch ($_POST['action']) {
        case 'guardar':
            $documentos_controlador->guardar(); // Guarda imágenes y documentos
            break;

        case 'guardar_imagenes':
            $documentos_controlador->guardarImagenes(); // Solo imágenes
            break;

        case 'guardar_documentos':
            $documentos_controlador->guardarDocumentos(); // Solo documentos
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

        // ✅ Obtener los IDs dinámicos de los tipos de documentación
        $idtipo_imagen = $this->obtenerIdTipoDocumentacion('imagen');
        $idtipo_doc = $this->obtenerIdTipoDocumentacion('documento');

        // ✅ Procesar ambos tipos (imágenes y documentos)
        $mensajes = array_merge(
            $this->procesarArchivos($_FILES['imagen_vehiculo'] ?? null, $idvehiculos, 'img', $idtipo_imagen),
            $this->procesarArchivos($_FILES['documentos_vehiculo'] ?? null, $idvehiculos, 'doc', $idtipo_doc)
        );

        if ($this->contieneErrores($mensajes)) $status = 'error';

        $this->redireccionar($mensajes, $status);
    }

    /* -------------------------------------------------------------------------- */
    /*                            Solo guardar imágenes                            */
    /* -------------------------------------------------------------------------- */
    public function guardarImagenes() {
        $idvehiculos = $_POST['vehiculos_idvehiculos'];

        // ✅ Buscar dinámicamente el ID de tipo_documentacion para "imagen"
        $idtipo_documentacion = $this->obtenerIdTipoDocumentacion('imagen');

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

        // ❌ No llamar a $this->obtenerIdTipoDocumentacion('documento')

        if (!$idtipo_documentacion) {
            return; // o mostrar error si no se envió el tipo
        }

        $mensajes = $this->procesarArchivos(
            $_FILES['documentos_vehiculo'] ?? null,
            $idvehiculos,
            'doc',
            $idtipo_documentacion
        );

        $status = $this->contieneErrores($mensajes) ? 'error' : 'success';
        $this->redireccionar($mensajes, $status);
    }

    /* -------------------------------------------------------------------------- */
    /*                      🔍 Obtener ID tipo_documentacion                       */
    /* -------------------------------------------------------------------------- */
    private function obtenerIdTipoDocumentacion($descripcion) {
        $conexion = new Conexion();

        // Buscar si ya existe un tipo con esa descripción
        $queryTipo = "SELECT idtipo_documentacion FROM tipo_documentacion WHERE descripcion = '$descripcion' LIMIT 1";
        $resultado = $conexion->consultar($queryTipo);

        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return $fila['idtipo_documentacion'];
        } else {
            // Si no existe, lo creamos automáticamente
            $queryInsert = "INSERT INTO tipo_documentacion (descripcion) VALUES ('$descripcion')";
            $conexion->insertar($queryInsert);

            // Volver a consultar para obtener el nuevo ID
            $nuevo = $conexion->consultar($queryTipo);
            $filaNuevo = $nuevo->fetch_assoc();
            return $filaNuevo['idtipo_documentacion'];
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                            Métodos reutilizables                            */
    /* -------------------------------------------------------------------------- */

    /** Procesa múltiples archivos (imágenes o documentos) */
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

    /** Guarda un archivo individual (imagen o documento) */
    private function guardarArchivo($archivo, $idvehiculos, $tipo, $idtipo_documentacion) {
        $documentacion = new Documentacion();
        $conexion = new Conexion();

        $uploadDir = ($tipo === 'img') ? '../../uploads/img/' : '../../uploads/doc/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

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

        /* -------------------------------------------------------------------------- */
        /* 🔍 1️⃣ Busco si existe documentación física (estado_doc = 1, sin URL)       */
        /* -------------------------------------------------------------------------- */
        $queryFisico = "
            SELECT idDocumentaciones, URL_descripcion, digitalizado 
            FROM Documentaciones 
            WHERE vehiculos_idvehiculos = '$idvehiculos'
            AND tipo_documentacion_idtipo_documentacion = '$idtipo_documentacion'
            AND estado_doc = 1
            LIMIT 1
        ";
        $resultado = $conexion->consultar($queryFisico);
        $mensaje = '';

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();

            // ⚙️ Caso A: documento físico encontrado sin URL (digitalizarlo)
            if (empty($row['URL_descripcion']) && $row['digitalizado'] == 0) {
                $idDoc = $row['idDocumentaciones'];

                $queryUpdate = "
                    UPDATE Documentaciones
                    SET URL_descripcion = '$targetFilePath',
                        digitalizado = 1
                    WHERE idDocumentaciones = '$idDoc'
                ";
                $ok = $conexion->insertar($queryUpdate);

                $mensaje = ($ok !== false)
                    ? "Documento físico actualizado correctamente (ahora digitalizado)."
                    : "Error al actualizar documento físico.";
            } 
            // ⚙️ Caso B: ya tiene URL → crear nuevo (solo imágenes o adicionales)
            else {
                $documentacion->setURL_descripcion($targetFilePath);
                $documentacion->setVehiculos_idvehiculos($idvehiculos);
                $documentacion->setEstado_doc(1);
                $documentacion->setDigitalizado(1);
                $documentacion->setTipo_documentacion_idtipo_documentacion($idtipo_documentacion);

                if ($documentacion->agregar_img_doc()) {
                    $mensaje = ($tipo === 'img')
                        ? "Nueva imagen guardada correctamente."
                        : "Nuevo documento guardado correctamente.";
                } else {
                    $mensaje = "Error al insertar nueva documentación.";
                }
            }
        } 
        else {
            // ⚙️ Caso C: no existe nada → insertar nuevo registro normal
            $documentacion->setURL_descripcion($targetFilePath);
            $documentacion->setVehiculos_idvehiculos($idvehiculos);
            $documentacion->setEstado_doc(1);
            $documentacion->setDigitalizado(1);
            $documentacion->setTipo_documentacion_idtipo_documentacion($idtipo_documentacion);

            if ($documentacion->agregar_img_doc()) {
                $mensaje = ($tipo === 'img')
                    ? "Imagen guardada correctamente."
                    : "Documento guardado correctamente.";
            } else {
                $mensaje = "Error al guardar documentación.";
            }
        }

        // Verifica documentación completa del vehículo
        $this->verificarYActualizarEstadoVehiculo($idvehiculos);
        return $mensaje;
    }



    /** Verifica si hay errores en los mensajes */
    private function contieneErrores($mensajes) {
        foreach ($mensajes as $msg) {
            if (stripos($msg, 'error') !== false) {
                return true;
            }
        }
        return false;
    }

    /** Redirecciona con mensajes y estado */
    private function redireccionar($mensajes, $status) {
        $accion = $_POST['action'] ?? '';

        switch ($accion) {
            case 'guardar':
                $page = 'listado_vehiculos';
                break;
            case 'guardar_imagenes':
            case 'guardar_documentos':
                $page = 'listado_falta_documentacion';
                break;
            default:
                $page = 'listado_vehiculos';
                break;
        }

        $mensaje = implode(" | ", $mensajes);
        header("Location: ../../index.php?page={$page}&mensaje=" . urlencode($mensaje) . "&status={$status}");
        exit();
    }

    /** Verifica si el vehículo ya tiene todo digitalizado y actualiza su estado */
    private function verificarYActualizarEstadoVehiculo($idvehiculos) {
        $tipoDoc = new Tipo_Documentacion();
        $faltantes = $tipoDoc->mostrar_tipos_faltantes($idvehiculos);

        // ✅ Si no hay documentos faltantes → cambiar estado a “disponible”
        if ($faltantes->num_rows === 0) {
            $vehiculo = new Vehiculos();
            $vehiculo->actualizar_disponible($idvehiculos, 'disponible');
        }
    }
}
?>
