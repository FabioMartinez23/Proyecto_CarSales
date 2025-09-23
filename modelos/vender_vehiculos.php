<?php

require_once('conexion.php');

class VenderVehiculo{
    private $idventas;
    private $descripcion;
    private $fecha_venta;
    private $tipo_pago_idtipo_pago;
    private $vehiculo_idvehiculo;
    private $registro_clientes_idregistro_clientes;
    private $empleados_idempleados;

    public function __construct($idventas='', $descripcion='', $fecha_venta='', $tipo_pago_idtipo_pago='', $vehiculo_idvehiculo='', $registro_clientes_idregistro_clientes='', $empleados_idempleados='') {
        $this->idventas = $idventas;
        $this->descripcion = $descripcion;
        $this->fecha_venta = $fecha_venta;
        $this->tipo_pago_idtipo_pago = $tipo_pago_idtipo_pago;
        $this->vehiculo_idvehiculo = $vehiculo_idvehiculo;
        $this->registro_clientes_idregistro_clientes = $registro_clientes_idregistro_clientes;
        $this->empleados_idempleados = $empleados_idempleados;
    }

    public function agregar_venta(){
        $conexion = new Conexion();
        $query = "INSERT INTO ventas (descripcion, fecha_venta, tipo_pago_idtipo_pago, vehiculo_idvehiculo, registro_clientes_idregistro_clientes, empleados_idempleados) VALUES ('$this->descripcion', CURDATE(), '$this->tipo_pago_idtipo_pago', '$this->vehiculo_idvehiculo' , '$this->registro_clientes_idregistro_clientes', '$this->empleados_idempleados')";
        return $conexion->insertar($query);
    }

    public function traer_ventas(){
        $conexion = new Conexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas";
        return $conexion->consultar($query);
    }

    public function traer_venta_por_id($idventas){
        $conexion = new Conexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago, contactos.valor as valor_contacto, documentos.valor as valor_documento, domicilios.descripcion as nombre_domicilio, barrios.descripcion as nombre_barrio, localidades.descripcion as nombre_localidad, provincias.descripcion as nombre_provincia, colores.descripcion as nombre_descripcion FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON tipo_vehiculos.idtipo_vehiculos = vehiculos.tipo_vehiculos_idtipo_vehiculos INNER JOIN colores ON vehiculos.colores_idcolores = colores.idcolores INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas INNER JOIN contactos ON contactos.Personas_idPersonas = personas.idpersonas INNER JOIN documentos ON documentos.Personas_idPersonas = personas.idpersonas INNER JOIN domicilios ON domicilios.Personas_idPersonas = personas.idpersonas INNER JOIN barrios ON domicilios.barrios_idbarrios = barrios.idbarrios INNER JOIN localidades ON barrios.localidades_idlocalidades = localidades.idlocalidades INNER JOIN provincias ON localidades.provincias_idprovincias = provincias.idprovincias WHERE idventas = $idventas AND activo_precio = 1";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }

    public function traer_venta_para_anular($idventas){
        $conexion = new Conexion();
        $query = "SELECT * FROM ventas WHERE idventas = $idventas";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }


    public function buscar_ventas($buscador){
        $conexion = new Conexion();
        $query = " SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas WHERE activo_precio = 1 AND estado_venta = 'Realizada' AND patente LIKE '%$buscador%' OR marcas.nombre LIKE '%$buscador%' OR modelos.nombre LIKE '%$buscador%' OR año LIKE '%$buscador%' OR personas.nombre LIKE '%$buscador%' OR personas.apellido LIKE '%$buscador%'";
        return $conexion->consultar($query);
    }


    public function traer_ventas_paginacion($inicio, $cantidad){
        $conexion = new Conexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas WHERE activo_precio = 1 AND estado_venta = 'Realizada' LIMIT $inicio,$cantidad";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_ventas(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM ventas WHERE estado_venta = 'Realizada'";
        return $conexion->consultar($query);
    }

    public function cantidad_ventas_anuladas(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM ventas WHERE estado_venta = 'Anulada'";
        return $conexion->consultar($query);
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
}



?>