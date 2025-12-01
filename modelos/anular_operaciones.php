<?php

require_once('conexion.php');

class AnularOperacion {

    private $_con;
    private $conexion_externa = false;

    private $idanular_operacion;
    private $fecha_anulacion;
    private $tipo_anulacion_idtipo_anulacion;
    private $entidad;          // 'venta', 'compra', etc.
    private $id_entidad;       // idventa, idcompra, etc.
    private $motivo_detalle;
    private $Usuarios_idusuarios;

    public function __construct(
        $idanular_operacion = '',
        $fecha_anulacion = '',
        $tipo_anulacion_idtipo_anulacion = '',
        $entidad = '',
        $id_entidad = '',
        $Usuarios_idusuarios = '',
        $conn = null
    ) {
        $this->idanular_operacion              = $idanular_operacion;
        $this->fecha_anulacion                 = $fecha_anulacion;
        $this->tipo_anulacion_idtipo_anulacion = $tipo_anulacion_idtipo_anulacion;
        $this->entidad                         = $entidad;
        $this->id_entidad                      = $id_entidad;
        $this->Usuarios_idusuarios             = $Usuarios_idusuarios;

        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    private function getConexion() {
        if ($this->conexion_externa && $this->_con instanceof mysqli) {
            return $this->_con;
        }
        $c = new Conexion();
        $c->conectar();
        return $c->_con;
    }

    private function cerrarConexion($c) {
        if (!$this->conexion_externa && $c instanceof mysqli) {
            $c->close();
        }
    }

    /**
     * Registrar la anulación (auditoría general).
     * NO toca ventas, compras ni vehículos: eso se maneja en la transacción del controlador.
     */
    public function registrar() {
        $con = $this->getConexion();

        $fecha   = $con->real_escape_string($this->fecha_anulacion);
        $tipo    = (int)$this->tipo_anulacion_idtipo_anulacion;
        $entidad = $con->real_escape_string($this->entidad);
        $idEnt   = (int)$this->id_entidad;
        $idUser  = (int)$this->Usuarios_idusuarios;

        $motivo  = $this->motivo_detalle 
            ? $con->real_escape_string($this->motivo_detalle) 
            : null;

        $query = "
            INSERT INTO anular_operacion 
                (fecha_anulacion, tipo_anulacion_idtipo_anulacion, entidad, id_entidad, motivo_detalle, Usuarios_idusuarios) 
            VALUES 
                ('$fecha',
                 $tipo,
                 '$entidad',
                 $idEnt,
                 " . ($motivo !== null ? "'$motivo'" : "NULL") . ",
                 $idUser)
        ";

        $ok = $con->query($query);
        $idanulacion = $ok ? $con->insert_id : null;

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $idanulacion ?: false;
    }

    public function traer_anulacion_por_id($idanulacion) {
        $con = $this->getConexion();

        $idanulacion = (int)$idanulacion;

        $query = "
            SELECT ao.*, ta.descripcion AS descripcion_tipo_anulacion
            FROM anular_operacion ao
            INNER JOIN tipo_anulacion ta 
                ON ao.tipo_anulacion_idtipo_anulacion = ta.idtipo_anulacion 
            WHERE ao.idanular_operacion = $idanulacion
        ";

        $resultado = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        if ($resultado && $resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }

        return null;
    }


    // En modelos/anular_operaciones.php

    public function contar_anulaciones($entidad = null) {
        $con = $this->getConexion();

        $where = "";
        if ($entidad) {
            $entidad = $con->real_escape_string($entidad);
            $where = "WHERE ao.entidad = '$entidad'";
        }

        $query = "
            SELECT COUNT(*) AS total
            FROM anular_operacion ao
            $where
        ";

        $res = $con->query($query);
        $fila = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : ['total' => 0];

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return (int)$fila['total'];
    }

    public function traer_anulaciones_paginacion($inicio, $cantidad, $entidad = null) {
        $con = $this->getConexion();

        $where = "";
        if ($entidad) {
            $entidad = $con->real_escape_string($entidad);
            $where = "WHERE ao.entidad = '$entidad'";
        }

        $query = "
            SELECT 
                ao.*,
                ta.descripcion AS nombre_tipo_anulacion,
                u.username,
                u.email
            FROM anular_operacion ao
            INNER JOIN tipo_anulacion ta 
                ON ao.tipo_anulacion_idtipo_anulacion = ta.idtipo_anulacion
            INNER JOIN usuarios u
                ON ao.Usuarios_idusuarios = u.idusuarios
            $where
            ORDER BY ao.fecha_anulacion DESC, ao.idanular_operacion DESC
            LIMIT $inicio, $cantidad
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res;
    }

    // =========================
    // Getters y setters
    // =========================

    public function getIdanular_operacion() {
        return $this->idanular_operacion;
    }
    public function setIdanular_operacion($idanular_operacion) {
        $this->idanular_operacion = $idanular_operacion;
        return $this;
    }

    public function getFecha_anulacion() {
        return $this->fecha_anulacion;
    }
    public function setFecha_anulacion($fecha_anulacion) {
        $this->fecha_anulacion = $fecha_anulacion;
        return $this;
    }

    public function getTipo_anulacion_idtipo_anulacion() {
        return $this->tipo_anulacion_idtipo_anulacion;
    }
    public function setTipo_anulacion_idtipo_anulacion($tipo_anulacion_idtipo_anulacion) {
        $this->tipo_anulacion_idtipo_anulacion = $tipo_anulacion_idtipo_anulacion;
        return $this;
    }

    public function getEntidad() {
        return $this->entidad;
    }
    public function setEntidad($entidad) {
        $this->entidad = $entidad;
        return $this;
    }

    public function getId_entidad() {
        return $this->id_entidad;
    }
    public function setId_entidad($id_entidad) {
        $this->id_entidad = $id_entidad;
        return $this;
    }

    public function getMotivo_detalle() {
        return $this->motivo_detalle;
    }
    public function setMotivo_detalle($motivo_detalle) {
        $this->motivo_detalle = $motivo_detalle;
        return $this;
    }

    public function getUsuarios_idusuarios() {
        return $this->Usuarios_idusuarios;
    }
    public function setUsuarios_idusuarios($Usuarios_idusuarios) {
        $this->Usuarios_idusuarios = $Usuarios_idusuarios;
        return $this;
    }
}
