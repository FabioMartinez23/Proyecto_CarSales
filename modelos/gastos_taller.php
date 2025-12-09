<?php
require_once('conexion.php');

class Gastos_Taller
{
    private $_con;
    private $conexion_externa = false;

    public function __construct($conn = null)
    {
        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        } else {
            $conexion = new Conexion();
            $conexion->conectar();
            $this->_con = $conexion->_con;
        }
    }

    private function cerrarConexion()
    {
        if (!$this->conexion_externa && $this->_con instanceof mysqli) {
            $this->_con->close();
        }
    }

    public function registrar_gasto($fecha_gasto, $descripcion, $monto, $proveedor, $comprobante, $usuario_carga, $tipo_gasto_id, $idvehiculos_taller)
    {
        $fecha_gasto        = $this->_con->real_escape_string($fecha_gasto);
        $descripcion        = $descripcion ? "'" . $this->_con->real_escape_string($descripcion) . "'" : "NULL";
        $proveedor          = $proveedor ? "'" . $this->_con->real_escape_string($proveedor) . "'" : "NULL";
        $comprobante        = $comprobante ? "'" . $this->_con->real_escape_string($comprobante) . "'" : "NULL";
        $usuario_carga      = $usuario_carga ? (int)$usuario_carga : "NULL";
        $tipo_gasto_id      = (int)$tipo_gasto_id;
        $idvehiculos_taller = (int)$idvehiculos_taller;
        $monto              = (float)$monto;

        $sql = "
            INSERT INTO gastos_taller
                (fecha_gasto, descripcion, monto, proveedor, comprobante_numero, usuario_carga,
                 tipo_gasto_idtipo_gasto, vehiculos_taller_idvehiculos_taller, anulado)
            VALUES
                ('$fecha_gasto', $descripcion, $monto, $proveedor, $comprobante,
                 $usuario_carga, $tipo_gasto_id, $idvehiculos_taller, 0)
        ";

        $ok = $this->_con->query($sql);
        if (!$ok) {
            return false;
        }

        return $this->_con->insert_id;
    }

    public function traer_gastos_por_vehiculo_taller($idvehiculos_taller)
    {
        $id = (int)$idvehiculos_taller;

        $sql = "
            SELECT 
                gt.*,
                tg.descripcion AS tipo_gasto
            FROM gastos_taller gt
            INNER JOIN tipo_gasto tg 
                ON gt.tipo_gasto_idtipo_gasto = tg.idtipo_gasto
            WHERE gt.vehiculos_taller_idvehiculos_taller = $id
              AND gt.anulado = 0
            ORDER BY gt.fecha_gasto DESC, gt.idgastos_taller DESC
        ";

        return $this->_con->query($sql);
    }

    public function traer_por_id($idgasto)
    {
        $id = (int)$idgasto;
        $sql = "
            SELECT 
                gt.*,
                tg.descripcion AS tipo_gasto
            FROM gastos_taller gt
            INNER JOIN tipo_gasto tg 
                ON gt.tipo_gasto_idtipo_gasto = tg.idtipo_gasto
            WHERE gt.idgastos_taller = $id
            LIMIT 1
        ";

        $res = $this->_con->query($sql);
        return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    }

    public function marcar_anulado($idgasto)
    {
        $id = (int)$idgasto;
        $sql = "UPDATE gastos_taller SET anulado = 1 WHERE idgastos_taller = $id";
        return $this->_con->query($sql);
    }

    public function __destruct()
    {
        $this->cerrarConexion();
    }
}
