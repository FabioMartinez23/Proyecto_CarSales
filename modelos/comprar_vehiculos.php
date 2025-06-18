<?php

require_once('conexion.php');

class ComprarVehiculo{
    private $idcompras;
    private $descripcion;
    private $fecha_compra;
    private $tipo_pago_idtipo_pago;
    private $vehiculo_idvehiculo;
    private $titular_vehiculo_idtitular_vehiculo;

    public function __construct($idcompras='', $descripcion='', $fecha_compra='', $tipo_pago_idtipo_pago='', $vehiculo_idvehiculo='', $titular_vehiculo_idtitular_vehiculo='') {
        $this->idcompras = $idcompras;
        $this->descripcion = $descripcion;
        $this->fecha_compra = $fecha_compra;
        $this->tipo_pago_idtipo_pago = $tipo_pago_idtipo_pago;
        $this->vehiculo_idvehiculo = $vehiculo_idvehiculo;
        $this->titular_vehiculo_idtitular_vehiculo = $titular_vehiculo_idtitular_vehiculo;
    }

    public function agregar_compra(){
        $conexion = new Conexion();
        $query = "INSERT INTO compras (descripcion, fecha_compra, tipo_pago_idtipo_pago, vehiculo_idvehiculo, titular_vehiculo_idtitular_vehiculo) VALUES ('$this->descripcion', CURDATE(), '$this->tipo_pago_idtipo_pago', '$this->vehiculo_idvehiculo', '$this->titular_vehiculo_idtitular_vehiculo')";
        return $conexion->insertar($query);
    }

    public function traer_compras(){
        $conexion = new Conexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, compras.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM compras INNER JOIN tipo_pago ON compras.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON compras.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN titular_vehiculo ON compras.titular_vehiculo_idtitular_vehiculo = titular_vehiculo.idtitulares WHERE DATE(compras.fecha_compra) = DATE(precios_vehiculos.fecha_precio)";
        return $conexion->consultar($query);
    }

    public function traer_compra_por_id($idcompras){
        $conexion = new Conexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, compras.descripcion as observacion, tipo_pago.descripcion as nombre_pago, contactos.valor as valor_contacto, documentos.valor as valor_documento, domicilios.descripcion as nombre_domicilio, barrios.descripcion as nombre_barrio, localidades.descripcion as nombre_localidad, provincias.descripcion as nombre_provincia, colores.descripcion as nombre_descripcion FROM compras INNER JOIN tipo_pago ON compras.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON compras.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN colores ON vehiculos.colores_idcolores = colores.idcolores INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN titular_vehiculo ON compras.titular_vehiculo_idtitular_vehiculo = titular_vehiculo.idtitular_vehiculo INNER JOIN personas ON titular_vehiculo.Personas_idpersonas = personas.idpersonas INNER JOIN contactos ON contactos.Personas_idPersonas = personas.idpersonas INNER JOIN documentos ON documentos.Personas_idPersonas = personas.idpersonas INNER JOIN domicilios ON domicilios.Personas_idPersonas = personas.idpersonas INNER JOIN barrios ON domicilios.barrios_idbarrios = barrios.idbarrios INNER JOIN localidades ON barrios.localidades_idlocalidades = localidades.idlocalidades INNER JOIN provincias ON localidades.provincias_idprovincias = provincias.idprovincias WHERE idcompras = $idcompras";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }

    public function buscar_compra($buscador){
        $conexion = new Conexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, compras.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM compras INNER JOIN tipo_pago ON compras.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON compras.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN titular_vehiculo ON compras.titular_vehiculo_idtitular_vehiculo = titular_vehiculo.idtitulares INNER JOIN personas ON titular_vehiculo.Personas_idpersonas = personas.idpersonas WHERE (patente LIKE '%$buscador%' OR marcas.nombre LIKE '%$buscador%' OR modelos.nombre LIKE '%$buscador%' OR año LIKE '%$buscador%' OR personas.nombre LIKE '%$buscador%' OR personas.apellido LIKE '%$buscador%') AND DATE(compras.fecha_compra) = DATE(precios_vehiculos.fecha_precio)";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_compras(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM compras";
        return $conexion->consultar($query);
    }

    public function traer_compras_paginacion($inicio,$cantidad){
        $conexion = new Conexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, compras.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM compras INNER JOIN tipo_pago ON compras.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON compras.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN titular_vehiculo ON compras.titular_vehiculo_idtitular_vehiculo = titular_vehiculo.idtitular_vehiculo INNER JOIN personas ON titular_vehiculo.Personas_idpersonas = personas.idpersonas WHERE DATE(compras.fecha_compra) = DATE(precios_vehiculos.fecha_precio) LIMIT $inicio,$cantidad";
        return $conexion->consultar($query);
    }

    /**
     * Get the value of idcompras
     */ 
    public function getIdcompras()
    {
        return $this->idcompras;
    }

    /**
     * Set the value of idcompras
     *
     * @return  self
     */ 
    public function setIdcompras($idcompras)
    {
        $this->idcompras = $idcompras;

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
     * Get the value of fecha_compra
     */ 
    public function getFecha_compra()
    {
        return $this->fecha_compra;
    }

    /**
     * Set the value of fecha_compra
     *
     * @return  self
     */ 
    public function setFecha_compra($fecha_compra)
    {
        $this->fecha_compra = $fecha_compra;

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
}



?>