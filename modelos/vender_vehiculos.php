<?php

require_once('conexion.php');

class VenderVehiculo {
    private $_con;
    private $conexion_externa = false;

    private $idventas;
    private $descripcion;
    private $precio_venta;
    private $fecha_venta;
    private $tipo_pago_idtipo_pago;
    private $vehiculo_idvehiculo;
    private $registro_clientes_idregistro_clientes;
    private $empleados_idempleados;
    private $titular_vehiculo_idtitular_vehiculo;

    // ===========================================================
    // 🔹 CONSTRUCTOR — recibe conexión externa si viene de transacción
    // ===========================================================
    public function __construct(
        $idventas = '', $descripcion = '', $fecha_venta = '',
        $tipo_pago_idtipo_pago = '', $vehiculo_idvehiculo = '',
        $registro_clientes_idregistro_clientes = '',
        $empleados_idempleados = '', $titular_vehiculo_idtitular_vehiculo = '',
        $precio_venta = '',
        $conn = null
    ) {
        $this->idventas = $idventas;
        $this->descripcion = $descripcion;
        $this->fecha_venta = $fecha_venta;
        $this->tipo_pago_idtipo_pago = $tipo_pago_idtipo_pago;
        $this->vehiculo_idvehiculo = $vehiculo_idvehiculo;
        $this->registro_clientes_idregistro_clientes = $registro_clientes_idregistro_clientes;
        $this->empleados_idempleados = $empleados_idempleados;
        $this->titular_vehiculo_idtitular_vehiculo = $titular_vehiculo_idtitular_vehiculo;
        $this->precio_venta = $precio_venta;

        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    // ===========================================================
    // 🔹 Obtener conexión sin romper transacciones
    // ===========================================================
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

    // ===========================================================
    // MÉTODOS PRINCIPALES
    // ===========================================================
    public function agregar_venta() {
        $con = $this->getConexion();

        $query = "
            INSERT INTO ventas 
            (descripcion,  precio_venta, fecha_venta, tipo_pago_idtipo_pago, vehiculo_idvehiculo, 
             registro_clientes_idregistro_clientes, empleados_idempleados, titular_vehiculo_idtitular_vehiculo)
            VALUES (
                '$this->descripcion',
                '$this->precio_venta',
                CURDATE(),
                '$this->tipo_pago_idtipo_pago',
                '$this->vehiculo_idvehiculo',
                '$this->registro_clientes_idregistro_clientes',
                '$this->empleados_idempleados',
                '$this->titular_vehiculo_idtitular_vehiculo'
            )
        ";

        $ok = $con->query($query);
        return $ok ? $con->insert_id : null;
    }


    public function traer_ventas() {
        $con = $this->getConexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas";
        return $con->query($query);
    }

    public function traer_venta_por_id($idventas) {
        $con = $this->getConexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago, contactos.valor as valor_contacto, documentos.valor as valor_documento, domicilios.descripcion as nombre_domicilio, barrios.descripcion as nombre_barrio, localidades.descripcion as nombre_localidad, provincias.descripcion as nombre_provincia, colores.descripcion as nombre_descripcion FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON tipo_vehiculos.idtipo_vehiculos = vehiculos.tipo_vehiculos_idtipo_vehiculos INNER JOIN colores ON vehiculos.colores_idcolores = colores.idcolores INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas INNER JOIN contactos ON contactos.Personas_idPersonas = personas.idpersonas INNER JOIN documentos ON documentos.Personas_idPersonas = personas.idpersonas INNER JOIN domicilios ON domicilios.Personas_idPersonas = personas.idpersonas INNER JOIN barrios ON domicilios.barrios_idbarrios = barrios.idbarrios INNER JOIN localidades ON barrios.localidades_idlocalidades = localidades.idlocalidades INNER JOIN provincias ON localidades.provincias_idprovincias = provincias.idprovincias WHERE idventas = $idventas AND activo_precio = 1";

        $res = $con->query($query);
        return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    }

    public function traer_venta_para_anular($idventas) {
        $con = $this->getConexion();
        $query = "SELECT * FROM ventas WHERE idventas = $idventas";
        $res = $con->query($query);
        $fila = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
        $this->cerrarConexion($con);
        return $fila;
    }


public function buscar_ventas($buscador){
        $con = $this->getConexion();
        $query = " SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas INNER JOIN tipo_precios ON precios_vehiculos.tipo_precios_idtipo_precios = tipo_precios.idtipo_precios WHERE activo_precio = 1 AND tipo_precios.descripcion = 'publico'  AND estado_venta = 'Realizada' AND patente LIKE '%$buscador%' OR marcas.nombre LIKE '%$buscador%' OR modelos.nombre LIKE '%$buscador%' OR anio LIKE '%$buscador%' OR personas.nombre LIKE '%$buscador%' OR personas.apellido LIKE '%$buscador%'";
        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }


    public function traer_ventas_paginacion($inicio, $cantidad){
        $con = $this->getConexion();
        $query = "SELECT *,ventas.idventas, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago, MAX(pv.precio) as precio_actual, personas.nombre, personas.apellido FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos pv ON pv.vehiculos_idvehiculos = vehiculos.idvehiculos AND pv.activo_precio = 1 INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas WHERE estado_venta = 'Realizada' GROUP BY ventas.idventas LIMIT $inicio,$cantidad";
        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }

    public function traer_cantidad_ventas(){
        $con = $this->getConexion();
        $query = "SELECT count(*) as total FROM ventas WHERE estado_venta = 'Realizada'";
        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }

    public function cantidad_ventas_anuladas(){
        $con = $this->getConexion();
        $query = "SELECT count(*) as total FROM ventas WHERE estado_venta = 'Anulada'";
        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }


    /* ===========================================================
    VENTAS DEL EMPLEADO POR DÍA DEL MES (para gráfico)
    =========================================================== */
    public function ventas_empleado_mes($idusuario) {
        $con = $this->getConexion();

        $query = "
            SELECT 
                DATE(v.fecha_venta) AS dia,
                COUNT(*) AS cantidad
            FROM ventas v
            WHERE v.empleados_idempleados = (
                SELECT idempleados 
                FROM empleados 
                WHERE Usuarios_idusuarios = '$idusuario'
            )
            AND DATE_FORMAT(v.fecha_venta, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
            AND v.estado_venta = 'Realizada'
            GROUP BY DATE(v.fecha_venta)
            ORDER BY dia ASC
        ";

        return $con->query($query);
    }


    /* ===========================================================
    TOTAL DE VENTAS DEL EMPLEADO
    =========================================================== */
    public function ventas_empleado_total($idusuario) {
        $con = $this->getConexion();

        $query = "
            SELECT COUNT(*) AS total
            FROM ventas
            WHERE empleados_idempleados = (
                SELECT idempleados 
                FROM empleados 
                WHERE Usuarios_idusuarios = '$idusuario'
            )
            AND estado_venta = 'Realizada'
        ";

        return $con->query($query)->fetch_assoc();
    }


    /* ===========================================================
    TOTAL DE VENTAS ANULADAS DEL EMPLEADO
    =========================================================== */
    public function ventas_empleado_anuladas($idusuario) {
        $con = $this->getConexion();

        $query = "
            SELECT COUNT(*) AS total
            FROM ventas
            WHERE empleados_idempleados = (
                SELECT idempleados 
                FROM empleados 
                WHERE Usuarios_idusuarios = '$idusuario'
            )
            AND estado_venta = 'Anulada'
        ";

        return $con->query($query)->fetch_assoc();
    }


    /* ANULAR LA VENTA */

    public function anular_venta_estado($idventa) {
        $con = $this->getConexion();

        $query = "
            UPDATE ventas 
            SET estado_venta = 'Anulada',
                fecha_anulacion = NOW()
            WHERE idventas = $idventa
        ";

        $ok = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $ok;
    }


    public function traer_venta_por_vehiculo($idvehiculo) {
        $con = $this->getConexion();

        $query = "
            SELECT *
            FROM ventas
            WHERE vehiculo_idvehiculo = '$idvehiculo'
            LIMIT 1
        ";

        $res = $con->query($query);

        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc();
        }

        return null;
    }

    /* ===========================================================
    REPORTE: VENTAS POR PERÍODO (AGRUPADAS POR MES/AÑO)
    =========================================================== */
    public function reporte_ventas_por_periodo($desde = null, $hasta = null) {
        $con = $this->getConexion();

        $filtro = " WHERE 1=1 ";

        if (!empty($desde)) {
            $filtro .= " AND DATE(ventas.fecha_venta) >= '$desde'";
        }

        if (!empty($hasta)) {
            $filtro .= " AND DATE(ventas.fecha_venta) <= '$hasta'";
        }

        $query = "
            SELECT 
                DATE_FORMAT(ventas.fecha_venta, '%Y-%m')   AS periodo,
                DATE_FORMAT(ventas.fecha_venta, '%m/%Y')   AS periodo_legible,
                COUNT(*)                                   AS cantidad_ventas,
                SUM(precios_vehiculos.precio)              AS total_vendido,
                AVG(precios_vehiculos.precio)              AS ticket_promedio
            FROM ventas
            INNER JOIN precios_vehiculos 
                ON precios_vehiculos.vehiculos_idvehiculos = ventas.vehiculo_idvehiculo
            AND DATE(precios_vehiculos.fecha_precio) = DATE(ventas.fecha_venta)
            $filtro
            GROUP BY DATE_FORMAT(ventas.fecha_venta, '%Y-%m')
            ORDER BY DATE_FORMAT(ventas.fecha_venta, '%Y-%m') ASC
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res;
    }


    /**
     * Get the value of idventas
     */ 
    public function getIdventas()
    {
        return $this->idventas;
    }

    /**
     * Set the value of idventas
     *
     * @return  self
     */ 
    public function setIdventas($idventas)
    {
        $this->idventas = $idventas;

        return $this;
    }

    /**
     * Get the value of descripcion
     */ 
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     *
     * @return  self
     */ 
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get the value of fecha_venta
     */ 
    public function getFecha_venta()
    {
        return $this->fecha_venta;
    }

    /**
     * Set the value of fecha_venta
     *
     * @return  self
     */ 
    public function setFecha_venta($fecha_venta)
    {
        $this->fecha_venta = $fecha_venta;

        return $this;
    }

    /**
     * Get the value of tipo_pago_idtipo_pago
     */ 
    public function getTipo_pago_idtipo_pago()
    {
        return $this->tipo_pago_idtipo_pago;
    }

    /**
     * Set the value of tipo_pago_idtipo_pago
     *
     * @return  self
     */ 
    public function setTipo_pago_idtipo_pago($tipo_pago_idtipo_pago)
    {
        $this->tipo_pago_idtipo_pago = $tipo_pago_idtipo_pago;

        return $this;
    }

    /**
     * Get the value of vehiculo_idvehiculo
     */ 
    public function getVehiculo_idvehiculo()
    {
        return $this->vehiculo_idvehiculo;
    }

    /**
     * Set the value of vehiculo_idvehiculo
     *
     * @return  self
     */ 
    public function setVehiculo_idvehiculo($vehiculo_idvehiculo)
    {
        $this->vehiculo_idvehiculo = $vehiculo_idvehiculo;

        return $this;
    }



    /**
     * Get the value of registro_clientes_idregistro_clientes
     */ 
    public function getRegistro_clientes_idregistro_clientes()
    {
        return $this->registro_clientes_idregistro_clientes;
    }

    /**
     * Set the value of registro_clientes_idregistro_clientes
     *
     * @return  self
     */ 
    public function setRegistro_clientes_idregistro_clientes($registro_clientes_idregistro_clientes)
    {
        $this->registro_clientes_idregistro_clientes = $registro_clientes_idregistro_clientes;

        return $this;
    }

    /**
     * Get the value of empleados_idempleados
     */ 
    public function getEmpleados_idempleados()
    {
        return $this->empleados_idempleados;
    }

    /**
     * Set the value of empleados_idempleados
     *
     * @return  self
     */ 
    public function setEmpleados_idempleados($empleados_idempleados)
    {
        $this->empleados_idempleados = $empleados_idempleados;

        return $this;
    }

    /**
     * Get the value of titular_vehiculo_idtitular_vehiculo
     */ 
    public function getTitular_vehiculo_idtitular_vehiculo()
    {
        return $this->titular_vehiculo_idtitular_vehiculo;
    }

    /**
     * Set the value of titular_vehiculo_idtitular_vehiculo
     *
     * @return  self
     */ 
    public function setTitular_vehiculo_idtitular_vehiculo($titular_vehiculo_idtitular_vehiculo)
    {
        $this->titular_vehiculo_idtitular_vehiculo = $titular_vehiculo_idtitular_vehiculo;

        return $this;
    }

    /**
     * Get the value of precio_venta
     */ 
    public function getPrecio_venta()
    {
        return $this->precio_venta;
    }

    /**
     * Set the value of precio_venta
     *
     * @return  self
     */ 
    public function setPrecio_venta($precio_venta)
    {
        $this->precio_venta = $precio_venta;

        return $this;
    }
}
?>