<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/paginacion.php');

class Modelos_Vehiculos extends Paginacion{
    private $idmodelos;
    private $nombre;
    private $marcas_idmarcas;
    private $tipo_vehiculos_idtipo_vehiculos;

    public function __construct($idmodelos='', $nombre='', $marcas_idmarcas='', $tipo_vehiculos_idtipo_vehiculos='') {
        $this->idmodelos = $idmodelos;
        $this->nombre = $nombre;
        $this->marcas_idmarcas = $marcas_idmarcas;
        $this->tipo_vehiculos_idtipo_vehiculos = $tipo_vehiculos_idtipo_vehiculos;
    }

    public function agregar_modelo(){
        $conexion = new Conexion();
        $query = "INSERT INTO modelos (nombre,marcas_idmarcas,tipo_vehiculos_idtipo_vehiculos) VALUES('$this->nombre','$this->marcas_idmarcas','$this->tipo_vehiculos_idtipo_vehiculos')";
        return $conexion->insertar($query);
    }

    public function actualizar_modelo(){
        $conexion = new Conexion();
        $query = "UPDATE modelos SET nombre = '$this->nombre', marcas_idmarcas = '$this->marcas_idmarcas', tipo_vehiculos_idtipo_vehiculos = '$this->tipo_vehiculos_idtipo_vehiculos' WHERE idmodelos = '$this->idmodelos'";
        return $conexion->actualizar($query);
    }

    public function eliminar_modelo(){
        $conexion = new Conexion();
        $query = "UPDATE modelos SET activo_modelo = 0 WHERE idmodelos = '$this->idmodelos'";
        return $conexion->actualizar($query);
    }
    
    public function validar_modelo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM modelos WHERE nombre = '$this->nombre'";
        return $conexion->consultar($query);
    }

    public function traer_modelo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM modelos WHERE activo_modelo = 1";
        return $conexion->consultar($query);
    }

    public function traer_modelo_id($idmodelos){
        $conexion = new Conexion();
        $query = "SELECT * FROM modelos WHERE idmodelos = '$idmodelos'";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_modelo(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM modelos WHERE activo_modelo = 1";
        return $conexion->consultar($query);
    }

    public function traer_modelos_paginacion(){
            $conexion = new Conexion();
            $query = "SELECT marcas.*, marcas.nombre as nombre_marca, modelos.*, modelos.nombre as nombre_modelo, tipo_vehiculos.*, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM modelos INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on modelos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE modelos.activo_modelo = 1 LIMIT $this->pagina_actual,$this->paginacion";
            return $conexion->consultar($query);
    }

    public function traer_modelos_por_marca($marcas_idmarcas){
        $conexion = new Conexion();
        $query = "SELECT marcas.*, marcas.nombre as nombre_marca, modelos.*, modelos.nombre as nombre_modelo, tipo_vehiculos.*, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM modelos INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on modelos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE marcas.idmarcas = '$marcas_idmarcas'";
        return $conexion->consultar($query);
    }

    public function traer_tipo_vehiculo_por_modelo(){
        $conexion = new Conexion();
        $query = "SELECT marcas.*, marcas.nombre as nombre_marca, modelos.*, modelos.nombre as nombre_modelo, tipo_vehiculos.*, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM modelos INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on modelos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE modelos.idmodelos = '$this->idmodelos'";
        return $conexion->consultar($query);
    }

    public function traer_modelos(){
        $conexion = new Conexion();
        $query = "SELECT DISTINCT marcas.*, marcas.nombre as nombre_marca, modelos.*, modelos.nombre as nombre_modelo, tipo_vehiculos.*, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM modelos INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on modelos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE activo_modelo = 1";
        return $conexion->consultar($query);
    }


    /**
     * Get the value of idmodelos
     */ 
    public function getIdmodelos()
    {
        return $this->idmodelos;
    }

    /**
     * Set the value of idmodelos
     *
     * @return  self
     */ 
    public function setIdmodelos($idmodelos)
    {
        $this->idmodelos = $idmodelos;

        return $this;
    }

    /**
     * Get the value of nombre
     */ 
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     *
     * @return  self
     */ 
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get the value of marcas_idmarcas
     */ 
    public function getMarcas_idmarcas()
    {
        return $this->marcas_idmarcas;
    }

    /**
     * Set the value of marcas_idmarcas
     *
     * @return  self
     */ 
    public function setMarcas_idmarcas($marcas_idmarcas)
    {
        $this->marcas_idmarcas = $marcas_idmarcas;

        return $this;
    }

    /**
     * Get the value of tipo_vehiculos_idtipo_vehiculos
     */ 
    public function getTipo_vehiculos_idtipo_vehiculos()
    {
        return $this->tipo_vehiculos_idtipo_vehiculos;
    }

    /**
     * Set the value of tipo_vehiculos_idtipo_vehiculos
     *
     * @return  self
     */ 
    public function setTipo_vehiculos_idtipo_vehiculos($tipo_vehiculos_idtipo_vehiculos)
    {
        $this->tipo_vehiculos_idtipo_vehiculos = $tipo_vehiculos_idtipo_vehiculos;

        return $this;
    }
}

?>