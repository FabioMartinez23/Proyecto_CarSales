<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/paginacion.php');

class Localidades extends Paginacion{
    private $idlocalidades;
    private $descripcion;
    private $provincias_idprovincias;


    public function __construct($idlocalidades='', $descripcion='', $provincias_idprovincias='') {
        $this->idlocalidades = $idlocalidades;
        $this->descripcion = $descripcion;
        $this->provincias_idprovincias = $provincias_idprovincias;
    }

    public function agregar_localidad(){
        $conexion = new Conexion();
        $query = "INSERT INTO localidades (descripcion,provincias_idprovincias) VALUES('$this->descripcion','$this->provincias_idprovincias')";
        return $conexion->insertar($query);
    }

    public function actualizar_localidad(){
        $conexion = new Conexion();
        $query = "UPDATE localidades SET descripcion = '$this->descripcion', provincias_idprovincias = '$this->provincias_idprovincias' WHERE idlocalidades = '$this->idlocalidades'";
        return $conexion->actualizar($query);
    }

    public function eliminar_localidad(){
        $conexion = new Conexion();
        $query = "UPDATE localidades SET activo_localidad = 0 WHERE idlocalidades = '$this->idlocalidades'";
        return $conexion->actualizar($query);
    }

    
    public function validar_localidad(){
        $conexion = new Conexion();
        $query = "SELECT * FROM localidades WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_localidad(){
        $conexion = new Conexion();
        $query = "SELECT * FROM localidades WHERE activo_localidad = 1";
        return $conexion->consultar($query);
    }

    public function traer_localidades_por_provincia($provincias_idprovincias){
        $conexion = new Conexion();
        $query = "SELECT localidades.*, localidades.descripcion as nombre_localidad, provincias.*, provincias.descripcion as nombre_provincia FROM localidades INNER JOIN provincias on localidades.provincias_idprovincias = provincias.idprovincias WHERE provincias.idprovincias = $provincias_idprovincias";
        return $conexion->consultar($query);
    }

    public function traer_localidad_id($idlocalidades){
        $conexion = new Conexion();
        $query = "SELECT * FROM localidades WHERE idlocalidades = '$idlocalidades'";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_localidad(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM localidades WHERE activo_localidad = 1";
        return $conexion->consultar($query);
    }

    public function traer_localidades_paginacion(){
            $conexion = new Conexion();
            $query = "SELECT localidades.*, localidades.descripcion as nombre_localidad, provincias.*, provincias.descripcion as nombre_provincia FROM localidades INNER JOIN provincias on localidades.provincias_idprovincias = provincias.idprovincias WHERE activo_localidad = 1 LIMIT $this->pagina_actual,$this->paginacion";
            return $conexion->consultar($query);
    }


    /**
     * Get the value of idlocalidades
     */ 
    public function getIdlocalidades()
    {
        return $this->idlocalidades;
    }

    /**
     * Set the value of idlocalidades
     *
     * @return  self
     */ 
    public function setIdlocalidades($idlocalidades)
    {
        $this->idlocalidades = $idlocalidades;

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
     * Get the value of provincias_idprovincias
     */ 
    public function getProvincias_idprovincias()
    {
        return $this->provincias_idprovincias;
    }

    /**
     * Set the value of provincias_idprovincias
     *
     * @return  self
     */ 
    public function setProvincias_idprovincias($provincias_idprovincias)
    {
        $this->provincias_idprovincias = $provincias_idprovincias;

        return $this;
    }
}