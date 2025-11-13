<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class Comisiones_Ventas {
    private $_con;
    private $conexion_externa = false;

    private $idcomisiones_ventas;
    private $porcentaje_empleado;
    private $porcentaje_concesionaria;
    private $monto_empleado;
    private $monto_concesionaria;
    private $fecha_registro;
    private $tipo_comisiones_idtipo_comisiones;
    private $ventas_idventas;

    // ===========================================================
    // CONSTRUCTOR COMPATIBLE CON CONEXIÓN COMPARTIDA
    // ===========================================================
    public function __construct(
        $idcomisiones_ventas = '',
        $porcentaje_empleado = '',
        $porcentaje_concesionaria = '',
        $monto_empleado = '',
        $monto_concesionaria = '',
        $fecha_registro = '',
        $tipo_comisiones_idtipo_comisiones = '',
        $ventas_idventas = '',
        $conn = null
    ) {
        $this->idcomisiones_ventas = $idcomisiones_ventas;
        $this->porcentaje_empleado = $porcentaje_empleado;
        $this->porcentaje_concesionaria = $porcentaje_concesionaria;
        $this->monto_empleado = $monto_empleado;
        $this->monto_concesionaria = $monto_concesionaria;
        $this->fecha_registro = $fecha_registro;
        $this->tipo_comisiones_idtipo_comisiones = $tipo_comisiones_idtipo_comisiones;
        $this->ventas_idventas = $ventas_idventas;

        // ✅ Si se pasa conexión activa desde el controlador, la usamos
        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    // ===========================================================
    // MÉTODOS AUXILIARES
    // ===========================================================
    private function getConexion() {
        if ($this->conexion_externa && $this->_con instanceof mysqli) {
            return $this->_con;
        }
        $conexion = new Conexion();
        $conexion->conectar();
        return $conexion->_con;
    }

    private function cerrarConexion($conexion) {
        if (!$this->conexion_externa && $conexion instanceof mysqli) {
            $conexion->close();
        }
    }


    // ===========================================================
    // MÉTODOS PRINCIPALES
    // ===========================================================

    // Agregar comisión
    public function agregar_comision_venta() {
        $con = $this->getConexion();

        $query = "
            INSERT INTO comisiones_ventas 
                (porcentaje_empleado, porcentaje_concesionaria, monto_empleado, monto_concesionaria, 
                 fecha_registro, tipo_comisiones_idtipo_comisiones, ventas_idventas)
            VALUES 
                ('$this->porcentaje_empleado', '$this->porcentaje_concesionaria', 
                 '$this->monto_empleado', '$this->monto_concesionaria', 
                 NOW(), '$this->tipo_comisiones_idtipo_comisiones', '$this->ventas_idventas')
        ";

        $ok = $con->query($query);
        $id = $ok ? $con->insert_id : null;

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $id;
    }

    // Actualizar comisión
    public function actualizar_comision_venta() {
        $con = $this->getConexion();

        $query = "
            UPDATE comisiones_ventas 
            SET 
                porcentaje_empleado = '$this->porcentaje_empleado',
                porcentaje_concesionaria = '$this->porcentaje_concesionaria',
                monto_empleado = '$this->monto_empleado',
                monto_concesionaria = '$this->monto_concesionaria',
                tipo_comisiones_idtipo_comisiones = '$this->tipo_comisiones_idtipo_comisiones'
            WHERE idcomisiones_ventas = '$this->idcomisiones_ventas'
        ";

        $ok = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $ok;
    }

    // Eliminar comisión
    public function eliminar_comision_venta() {
        $con = $this->getConexion();

        $query = "DELETE FROM comisiones_ventas WHERE idcomisiones_ventas = '$this->idcomisiones_ventas'";
        $ok = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $ok;
    }

    // Mostrar todas las comisiones
    public function mostrar_comisiones_ventas() {
        $con = $this->getConexion();

        $query = "
            SELECT cv.*, tc.descripcion AS tipo_comision, v.fecha_venta 
            FROM comisiones_ventas cv
            INNER JOIN tipo_comisiones tc ON cv.tipo_comisiones_idtipo_comisiones = tc.idtipo_comisiones
            INNER JOIN ventas v ON cv.ventas_idventas = v.idventas
            ORDER BY cv.fecha_registro DESC
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res;
    }

    // Traer comisión por ID de venta
    public function traer_por_venta() {
        $con = $this->getConexion();

        $query = "
            SELECT cv.*, tc.descripcion AS tipo_comision
            FROM comisiones_ventas cv
            INNER JOIN tipo_comisiones tc ON cv.tipo_comisiones_idtipo_comisiones = tc.idtipo_comisiones
            WHERE ventas_idventas = '$this->ventas_idventas'
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res;
    }


    // ===========================================================
    // GETTERS & SETTERS
    // ===========================================================

    public function getIdcomisiones_ventas() {
        return $this->idcomisiones_ventas;
    }

    public function setIdcomisiones_ventas($idcomisiones_ventas) {
        $this->idcomisiones_ventas = $idcomisiones_ventas;
        return $this;
    }

    public function getPorcentaje_empleado() {
        return $this->porcentaje_empleado;
    }

    public function setPorcentaje_empleado($porcentaje_empleado) {
        $this->porcentaje_empleado = $porcentaje_empleado;
        return $this;
    }

    public function getPorcentaje_concesionaria() {
        return $this->porcentaje_concesionaria;
    }

    public function setPorcentaje_concesionaria($porcentaje_concesionaria) {
        $this->porcentaje_concesionaria = $porcentaje_concesionaria;
        return $this;
    }

    public function getMonto_empleado() {
        return $this->monto_empleado;
    }

    public function setMonto_empleado($monto_empleado) {
        $this->monto_empleado = $monto_empleado;
        return $this;
    }

    public function getMonto_concesionaria() {
        return $this->monto_concesionaria;
    }

    public function setMonto_concesionaria($monto_concesionaria) {
        $this->monto_concesionaria = $monto_concesionaria;
        return $this;
    }

    public function getFecha_registro() {
        return $this->fecha_registro;
    }

    public function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
        return $this;
    }

    public function getTipo_comisiones_idtipo_comisiones() {
        return $this->tipo_comisiones_idtipo_comisiones;
    }

    public function setTipo_comisiones_idtipo_comisiones($tipo_comisiones_idtipo_comisiones) {
        $this->tipo_comisiones_idtipo_comisiones = $tipo_comisiones_idtipo_comisiones;
        return $this;
    }

    public function getVentas_idventas() {
        return $this->ventas_idventas;
    }

    public function setVentas_idventas($ventas_idventas) {
        $this->ventas_idventas = $ventas_idventas;
        return $this;
    }
}
?>
