<?php
require_once('conexion.php');

class AuditoriaEdiciones
{
    private $_con = null;
    private $conexion_externa = false;

    private $idauditoria_ediciones;
    private $tabla;
    private $id_registro;
    private $modulo;
    private $accion;
    private $datos_anteriores;
    private $datos_nuevos;
    private $descripcion;
    private $fecha;
    private $idusuario;
    private $ip;
    private $user_agent;

    // ===========================================================
    // CONSTRUCTOR (compatible con conexión compartida)
    // ===========================================================
    public function __construct(
        $idauditoria_ediciones = '',
        $tabla = '',
        $id_registro = '',
        $modulo = '',
        $accion = 'UPDATE',
        $datos_anteriores = null,
        $datos_nuevos = null,
        $descripcion = null,
        $fecha = '',
        $idusuario = null,
        $ip = null,
        $user_agent = null,
        $conn = null
    ) {
        $this->idauditoria_ediciones = $idauditoria_ediciones;
        $this->tabla                  = $tabla;
        $this->id_registro            = $id_registro;
        $this->modulo                 = $modulo;
        $this->accion                 = $accion;
        $this->datos_anteriores       = $datos_anteriores;
        $this->datos_nuevos           = $datos_nuevos;
        $this->descripcion            = $descripcion;
        $this->fecha                  = $fecha;
        $this->idusuario              = $idusuario;
        $this->ip                     = $ip;
        $this->user_agent             = $user_agent;

        // Si se pasa conexión externa (transacción), la usamos
        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    // ===========================================================
    // Manejo de conexión (misma lógica que Caja/Vehiculos/etc.)
    // ===========================================================
    private function getConexion()
    {
        if ($this->conexion_externa && $this->_con instanceof mysqli) {
            return $this->_con;
        }

        $conexion = new Conexion();
        $conexion->conectar();
        return $conexion->_con;
    }

    private function cerrarConexion($con)
    {
        if (!$this->conexion_externa && $con instanceof mysqli) {
            $con->close();
        }
    }

    // ===========================================================
    // Registrar auditoría genérica
    // ===========================================================
    /**
     * Registra una auditoría de edición.
     *
     * @param string       $tabla            Nombre de la tabla afectada (ej: 'vehiculos')
     * @param int          $id_registro      ID del registro afectado en esa tabla
     * @param string|null  $modulo           Módulo lógico (ej: 'Vehículos', 'Precios', 'Personas')
     * @param string       $accion           'UPDATE' | 'DELETE' | 'INSERT'
     * @param array|string $datos_anteriores Array asociativo con datos viejos o JSON string
     * @param array|string $datos_nuevos     Array asociativo con datos nuevos o JSON string
     * @param string|null  $descripcion      Texto libre (ej: 'Se modificó el precio público')
     * @param int|null     $idusuario        ID de usuario que hizo el cambio (si es null, toma de $_SESSION)
     *
     * @return bool true si insertó OK, false en caso contrario
     */
    public function registrar(
        $tabla,
        $id_registro,
        $modulo = null,
        $accion = 'UPDATE',
        $datos_anteriores = null,
        $datos_nuevos = null,
        $descripcion = null,
        $idusuario = null
    ) {
        $con = $this->getConexion();

        // Tomar idusuario de sesión si no viene por parámetro
        if ($idusuario === null && isset($_SESSION['idusuarios'])) {
            $idusuario = (int)$_SESSION['idusuarios'];
        }

        // IP y User Agent desde $_SERVER
        $ip         = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;

        // Normalizar acción
        $accion = strtoupper($accion);
        if (!in_array($accion, ['UPDATE', 'DELETE', 'INSERT'])) {
            $accion = 'UPDATE';
        }

        // Si vienen arrays, los convertimos a JSON
        if (is_array($datos_anteriores)) {
            $datos_anteriores = json_encode($datos_anteriores, JSON_UNESCAPED_UNICODE);
        }
        if (is_array($datos_nuevos)) {
            $datos_nuevos = json_encode($datos_nuevos, JSON_UNESCAPED_UNICODE);
        }

        // Escapamos las cadenas (siguiendo tu estilo, sin bind_param)
        $tabla       = $con->real_escape_string($tabla);
        $id_registro = (int)$id_registro;

        $modulo      = $modulo      !== null ? $con->real_escape_string($modulo)      : null;
        $accion      = $con->real_escape_string($accion);
        $descripcion = $descripcion !== null ? $con->real_escape_string($descripcion) : null;

        $datos_ant   = $datos_anteriores !== null ? $con->real_escape_string($datos_anteriores) : null;
        $datos_nue   = $datos_nuevos     !== null ? $con->real_escape_string($datos_nuevos)     : null;

        $ip_db       = $ip         !== null ? $con->real_escape_string($ip)         : null;
        $ua_db       = $user_agent !== null ? $con->real_escape_string($user_agent) : null;

        $idusuario_db = $idusuario !== null ? (int)$idusuario : null;

        // Armamos el INSERT respetando NULLs
        $query = "
            INSERT INTO auditoria_ediciones
            (tabla, id_registro, modulo, accion, datos_anteriores, datos_nuevos, descripcion, fecha, idusuario, ip, user_agent)
            VALUES (
                '$tabla',
                '$id_registro',
                " . ($modulo      !== null ? "'$modulo'"      : "NULL") . ",
                '$accion',
                " . ($datos_ant   !== null ? "'$datos_ant'"   : "NULL") . ",
                " . ($datos_nue   !== null ? "'$datos_nue'"   : "NULL") . ",
                " . ($descripcion !== null ? "'$descripcion'" : "NULL") . ",
                NOW(),
                " . ($idusuario_db !== null ? "'$idusuario_db'" : "NULL") . ",
                " . ($ip_db !== null ? "'$ip_db'" : "NULL") . ",
                " . ($ua_db !== null ? "'$ua_db'" : "NULL") . "
            )
        ";

        $ok = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $ok ? true : false;
    }

    // ===========================================================
    // (Opcional) método para traer auditoría por registro/tabla
    // ===========================================================
    public function traer_por_registro($tabla, $id_registro)
    {
        $con = $this->getConexion();

        $tabla       = $con->real_escape_string($tabla);
        $id_registro = (int)$id_registro;

        $query = "
            SELECT *
            FROM auditoria_ediciones
            WHERE tabla = '$tabla'
              AND id_registro = '$id_registro'
            ORDER BY fecha DESC, idauditoria_ediciones DESC
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res; // devuelve mysqli_result
    }

    // ===========================================================
    // Filtros reutilizables para listados
    // ===========================================================
    private function construir_where($con, $filtros = [])
    {
        $where = "1=1";

        // Filtro por tabla (ej: 'vehiculos', 'usuarios', 'clientes', 'documentaciones', etc.)
        if (!empty($filtros['tabla'])) {
            $tabla = $con->real_escape_string($filtros['tabla']);
            $where .= " AND a.tabla = '$tabla'";
        }

        // Filtro por módulo lógico (ej: 'Vehículos', 'Personas', 'Imágenes', etc.)
        if (!empty($filtros['modulo'])) {
            $modulo = $con->real_escape_string($filtros['modulo']);
            $where .= " AND a.modulo = '$modulo'";
        }

        // Filtro por acción (INSERT / UPDATE / DELETE)
        if (!empty($filtros['accion'])) {
            $accion = strtoupper($filtros['accion']);
            $accion = $con->real_escape_string($accion);
            $where .= " AND a.accion = '$accion'";
        }

        // Filtro por usuario
        if (!empty($filtros['idusuario'])) {
            $idusuario = (int)$filtros['idusuario'];
            $where .= " AND a.idusuario = $idusuario";
        }

        // Rango de fechas (yyyy-mm-dd)
        if (!empty($filtros['fecha_desde'])) {
            $fd = $con->real_escape_string($filtros['fecha_desde']);
            $where .= " AND DATE(a.fecha) >= '$fd'";
        }
        if (!empty($filtros['fecha_hasta'])) {
            $fh = $con->real_escape_string($filtros['fecha_hasta']);
            $where .= " AND DATE(a.fecha) <= '$fh'";
        }

        // Buscar texto libre en descripción o datos JSON
        if (!empty($filtros['buscar'])) {
            $buscar = $con->real_escape_string($filtros['buscar']);
            $where .= " AND (
                a.descripcion LIKE '%$buscar%' 
                OR a.datos_anteriores LIKE '%$buscar%'
                OR a.datos_nuevos LIKE '%$buscar%'
            )";
        }

        return $where;
    }

    public function contar_auditorias($filtros = [])
    {
        $con = $this->getConexion();

        $where = $this->construir_where($con, $filtros);

        $sql = "
            SELECT COUNT(*) AS total
            FROM auditoria_ediciones a
            LEFT JOIN usuarios u ON a.idusuario = u.idusuarios
            WHERE $where
        ";

        $res = $con->query($sql);
        $total = 0;

        if ($res && $fila = $res->fetch_assoc()) {
            $total = (int)$fila['total'];
        }

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $total;
    }

    public function traer_auditorias_paginacion($inicio, $cantidad, $filtros = [])
    {
        $con = $this->getConexion();

        $inicio   = (int)$inicio;
        $cantidad = (int)$cantidad;

        $where = $this->construir_where($con, $filtros);

        $sql = "
            SELECT 
                a.*,
                u.username,
                u.email
            FROM auditoria_ediciones a
            LEFT JOIN usuarios u ON a.idusuario = u.idusuarios
            WHERE $where
            ORDER BY a.fecha DESC, a.idauditoria_ediciones DESC
            LIMIT $inicio, $cantidad
        ";

        $res = $con->query($sql);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res; // mysqli_result
    }



    // ===========================================================
    // Getters / Setters (por si los necesitás después)
    // ===========================================================
    public function getIdauditoriaEdiciones()
    {
        return $this->idauditoria_ediciones;
    }

    public function setIdauditoriaEdiciones($id)
    {
        $this->idauditoria_ediciones = $id;
        return $this;
    }

    public function getTabla()
    {
        return $this->tabla;
    }

    public function setTabla($tabla)
    {
        $this->tabla = $tabla;
        return $this;
    }

    public function getIdRegistro()
    {
        return $this->id_registro;
    }

    public function setIdRegistro($id_registro)
    {
        $this->id_registro = $id_registro;
        return $this;
    }

    public function getModulo()
    {
        return $this->modulo;
    }

    public function setModulo($modulo)
    {
        $this->modulo = $modulo;
        return $this;
    }

    public function getAccion()
    {
        return $this->accion;
    }

    public function setAccion($accion)
    {
        $this->accion = $accion;
        return $this;
    }

    public function getDatosAnteriores()
    {
        return $this->datos_anteriores;
    }

    public function setDatosAnteriores($datos_anteriores)
    {
        $this->datos_anteriores = $datos_anteriores;
        return $this;
    }

    public function getDatosNuevos()
    {
        return $this->datos_nuevos;
    }

    public function setDatosNuevos($datos_nuevos)
    {
        $this->datos_nuevos = $datos_nuevos;
        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
        return $this;
    }

    public function getIdusuario()
    {
        return $this->idusuario;
    }

    public function setIdusuario($idusuario)
    {
        $this->idusuario = $idusuario;
        return $this;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    public function getUserAgent()
    {
        return $this->user_agent;
    }

    public function setUserAgent($user_agent)
    {
        $this->user_agent = $user_agent;
        return $this;
    }
}
