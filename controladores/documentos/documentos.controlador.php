<?php
ini_set('display_errors', 1);

require_once('../../modelos/documentaciones.php');
require_once('../../modelos/conexion.php');
require_once('../../modelos/tablas_maestras/tipo_documentacion.php');
require_once('../../modelos/vehiculos.php');

/* ========================================================================== */
/*                                  RUTEO                                     */
/* ========================================================================== */
if (isset($_POST['action'])) {

    $c = new DocumentosControlador();

    switch ($_POST['action']) {

        case 'guardar':
            $c->guardar();
        break;

        case 'guardar_imagenes':
            $c->guardarImagenes();
        break;

        case 'guardar_documentos':
            $c->guardarDocumentos();
        break;
    }
}

/* ========================================================================== */
/*                          CONTROLADOR DOCUMENTOS                             */
/* ========================================================================== */
class DocumentosControlador {

    /* ====================================================================== */
    /*                     GUARDAR IMÁGENES + DOCUMENTOS                       */
    /* ====================================================================== */
    public function guardar() {

        $idveh = $_POST['vehiculos_idvehiculos'] ?? null;
        if (!$idveh) return;

        $mensajes = [];

        // 1) Tipo "imagen"
        $idtipo_imagen = $this->obtenerIdTipoDocumentacion('imagen');

        // Procesar imágenes
        $mensajes = array_merge(
            $this->procesarArchivos($_FILES['imagen_vehiculo'] ?? null, $idveh, 'img', $idtipo_imagen),

            // 2) Procesar documentos PDF por tipo_documentación
            $this->procesarDocumentosPorTipo($_FILES['documentos_vehiculo_tipo'] ?? null, $idveh)
        );

        $status = $this->contieneErrores($mensajes) ? 'error' : 'success';
        $this->redireccionar($mensajes, $status, $idveh);
    }

    /* ====================================================================== */
    /*                           SOLO IMÁGENES                                 */
    /* ====================================================================== */
    public function guardarImagenes() {

        $idveh = $_POST['vehiculos_idvehiculos'] ?? null;
        if (!$idveh) return;

        $idtipo_imagen = $this->obtenerIdTipoDocumentacion('imagen');

        $mensajes = $this->procesarArchivos(
            $_FILES['imagen_vehiculo'] ?? null,
            $idveh,
            'img',
            $idtipo_imagen
        );

        $status = $this->contieneErrores($mensajes) ? 'error' : 'success';
        $this->redireccionar($mensajes, $status, $idveh);
    }

    /* ====================================================================== */
    /*                       SOLO DOCUMENTOS PDF                               */
    /* ====================================================================== */
    public function guardarDocumentos() {

        $idveh = $_POST['vehiculos_idvehiculos'] ?? null;
        if (!$idveh) return;

        $mensajes = $this->procesarDocumentosPorTipo(
            $_FILES['documentos_vehiculo_tipo'] ?? null,
            $idveh
        );

        $status = $this->contieneErrores($mensajes) ? 'error' : 'success';
        $this->redireccionar($mensajes, $status, $idveh);
    }

    /* ====================================================================== */
    /*       Procesar PDF según documentos_vehiculo_tipo[idTipoDoc]           */
    /* ====================================================================== */
    private function procesarDocumentosPorTipo($archivos, $idveh) {

        $mensajes = [];

        if (!$archivos || !isset($archivos['name'])) {
            return [];
        }

        foreach ($archivos['name'] as $idTipoDoc => $nombreArchivo) {

            if ($nombreArchivo === '' || $archivos['error'][$idTipoDoc] !== 0) {
                continue;
            }

            $archivo = [
                'name'     => $nombreArchivo,
                'tmp_name' => $archivos['tmp_name'][$idTipoDoc],
                'error'    => $archivos['error'][$idTipoDoc]
            ];

            $mensajes[] = $this->guardarArchivo($archivo, $idveh, 'doc', $idTipoDoc);
        }

        return $mensajes;
    }

    /* ====================================================================== */
    /*      Procesar muchas imágenes (solo tipo imagen)                       */
    /* ====================================================================== */
    private function procesarArchivos($archivos, $idveh, $tipo, $idtipo_documentacion) {

        $mensajes = [];
        if (!$archivos || !isset($archivos['name'])) return [];

        foreach ($archivos['name'] as $i => $nombre) {

            if ($nombre === '' || $archivos['error'][$i] !== 0) continue;

            $archivo = [
                'name'     => $nombre,
                'tmp_name' => $archivos['tmp_name'][$i],
                'error'    => $archivos['error'][$i]
            ];

            $mensajes[] = $this->guardarArchivo($archivo, $idveh, $tipo, $idtipo_documentacion);
        }

        return $mensajes;
    }

    /* ====================================================================== */
    /*               Obtener id de "imagen" o "documento"                     */
    /* ====================================================================== */
    private function obtenerIdTipoDocumentacion($descripcion) {

        $conexion = new Conexion();

        $query = "SELECT idtipo_documentacion 
                  FROM tipo_documentacion 
                  WHERE descripcion = '$descripcion' LIMIT 1";

        $res = $conexion->consultar($query);

        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc()['idtipo_documentacion'];
        }

        // Si no existe → crear
        $conexion->insertar("INSERT INTO tipo_documentacion (descripcion) VALUES ('$descripcion')");

        return $conexion->consultar($query)->fetch_assoc()['idtipo_documentacion'];
    }

    /* ====================================================================== */
    /*      Guardar archivo individual (imagen o PDF)                         */
    /* ====================================================================== */
    private function guardarArchivo($archivo, $idveh, $tipo, $idtipo_documentacion) {

        $conexion = new Conexion();
        $doc      = new Documentacion();

        // Carpeta
        $uploadDir = ($tipo === 'img') ? '../../uploads/img/' : '../../uploads/doc/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName   = uniqid() . "_" . basename($archivo['name']);
        $targetFile = $uploadDir . $fileName;
        $ext        = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $permitidos = ['jpg','jpeg','png','pdf'];
        if (!in_array($ext, $permitidos)) {
            return "Error: formato no permitido ($ext).";
        }

        if (!move_uploaded_file($archivo['tmp_name'], $targetFile)) {
            return "Error: no se pudo mover archivo.";
        }

        /* =============================================================== */
        /*                     IMÁGENES → SIEMPRE INSERTA                  */
        /* =============================================================== */
        if ($tipo === 'img') {

            $doc->setURL_descripcion($targetFile);
            $doc->setVehiculos_idvehiculos($idveh);
            $doc->setEstado_doc(1);
            $doc->setDigitalizado(1);
            $doc->setTipo_documentacion_idtipo_documentacion($idtipo_documentacion);
            $doc->agregar_img_doc();

            return "Imagen guardada correctamente.";
        }

        /* =============================================================== */
        /*                       PDFs → UNO POR TIPO                       */
        /* =============================================================== */

        // Buscar si ya existe físico o digital
        $q = "
            SELECT idDocumentaciones, URL_descripcion, digitalizado
            FROM Documentaciones
            WHERE vehiculos_idvehiculos = '$idveh'
            AND tipo_documentacion_idtipo_documentacion = '$idtipo_documentacion'
            LIMIT 1
        ";
        $res = $conexion->consultar($q);

        if ($res && $res->num_rows > 0) {

            $row = $res->fetch_assoc();
            $idDoc = $row['idDocumentaciones'];

            // Eliminar PDF anterior si existía
            if (!empty($row['URL_descripcion']) && file_exists($row['URL_descripcion'])) {
                @unlink($row['URL_descripcion']);
            }

            // Actualizar existente
            $update = "
                UPDATE Documentaciones
                SET URL_descripcion = '$targetFile',
                    digitalizado = 1,
                    estado_doc = 1
                WHERE idDocumentaciones = '$idDoc'
            ";
            $conexion->insertar($update);

            $this->verificarYActualizarEstadoVehiculo($idveh);
            return "Documento actualizado correctamente.";
        }

        // NO existe → crear registro nuevo
        $doc->setURL_descripcion($targetFile);
        $doc->setVehiculos_idvehiculos($idveh);
        $doc->setEstado_doc(1);
        $doc->setDigitalizado(1);
        $doc->setTipo_documentacion_idtipo_documentacion($idtipo_documentacion);
        $doc->agregar_img_doc();

        $this->verificarYActualizarEstadoVehiculo($idveh);
        return "Documento guardado correctamente.";
    }

    /* ====================================================================== */
    /*  Verificar faltantes y cambiar estado del vehículo                     */
    /* ====================================================================== */
    private function verificarYActualizarEstadoVehiculo($idveh) {

        $tipoDoc = new Tipo_Documentacion();
        $falt = $tipoDoc->mostrar_tipos_faltantes($idveh);

        if ($falt && $falt->num_rows === 0) {
            $veh = new Vehiculos();
            $veh->actualizar_disponible($idveh, 'disponible');
        }
    }

    /* ====================================================================== */
    /*                   Detectar errores en mensajes                         */
    /* ====================================================================== */
    private function contieneErrores($mensajes) {

        foreach ($mensajes as $m) {
            if (stripos($m, 'error') !== false) return true;
        }
        return false;
    }

    /* ====================================================================== */
    /*                           Redirección final                            */
    /* ====================================================================== */
    private function redireccionar($mensajes, $status, $idveh)
    {
        // -------------------------------------------
        // MENSAJE FINAL
        // -------------------------------------------
        if ($status === 'success') {
            $mensaje = "Archivos cargados correctamente.";
        } else {
            $errores = [];

            foreach ($mensajes as $m) {
                if (stripos($m, 'error') !== false) {
                    $errores[] = $m;
                }
            }

            $mensaje = empty($errores)
                ? "Hubo errores al cargar los archivos."
                : implode(" | ", $errores);
        }

        // -------------------------------------------
        // ESTADO ACTUAL DEL LISTADO
        // -------------------------------------------
        $estado_actual = $_POST['estado_actual'] ?? 'falta_documento';

        // -------------------------------------------
        // SANEAR TODO ANTES DE ARMAR LA URL
        // -------------------------------------------
        $idveh          = intval($idveh);
        $estado_actual  = urlencode($estado_actual);
        $mensaje        = urlencode($mensaje);
        $status         = urlencode($status);

        // -------------------------------------------
        // REDIRECCIÓN
        // -------------------------------------------
        $url = "../../index.php?page=listado_falta_documentacion"
            . "&estado=$estado_actual"
            . "&id_highlight=$idveh"
            . "&mensaje=$mensaje"
            . "&status=$status";

        header("Location: $url");
        exit();
    }
}
?>
