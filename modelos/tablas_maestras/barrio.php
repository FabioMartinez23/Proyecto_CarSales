<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/paginacion.php');

class Barrios extends Paginacion{
    private $idbarrios;
    private $descripcion;
    private $localidades_idlocalidades;


    public function __construct($idbarrios='', $descripcion='', $localidades_idlocalidades='') {
        $this->idbarrios = $idbarrios;
        $this->descripcion = $descripcion;
        $this->localidades_idlocalidades = $localidades_idlocalidades;
    }

    public function agregar_barrio(){
        $conexion = new Conexion();
        $query = "INSERT INTO barrios (descripcion,localidades_idlocalidades) VALUES('$this->descripcion','$this->localidades_idlocalidades')";
        return $conexion->insertar($query);
    }

    public function actualizar_barrio(){
        $conexion = new Conexion();
        $query = "UPDATE barrios SET descripcion = '$this->descripcion', localidades_idlocalidades = '$this->localidades_idlocalidades' WHERE idbarrios = '$this->idbarrios'";
        return $conexion->actualizar($query);
    }

    public function eliminar_barrio(){
        $conexion = new Conexion();
        $query = "UPDATE barrios SET activo_barrio = 0 WHERE idbarrios = '$this->idbarrios'";
        return $conexion->actualizar($query);
    }

    
    public function validar_barrio(){
        $conexion = new Conexion();
        $query = "SELECT * FROM barrios WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_barrio(){
        $conexion = new Conexion();
        $query = "SELECT * FROM barrios WHERE activo_barrio = 1";
        return $conexion->consultar($query);
    }

    public function traer_barrio_id($idbarrios){
        $conexion = new Conexion();
        $query = "SELECT * FROM barrios WHERE idbarrios = '$idbarrios'";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_barrio(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM barrios WHERE activo_barrio = 1";
        return $conexion->consultar($query);
    }

    public function traer_barrios_paginacion(){
            $conexion = new Conexion();
            $query = "SELECT barrios.*, barrios.descripcion as nombre_barrio, localidades.*, localidades.descripcion as nombre_localidad FROM barrios INNER JOIN localidades on barrios.localidades_idlocalidades = localidades.idlocalidades WHERE activo_barrio = 1 LIMIT $this->pagina_actual,$this->paginacion";
            return $conexion->consultar($query);
    }

    public function traer_barrios_por_localidad($localidades_idlocalidades){
        $conexion = new Conexion();
        $query = "SELECT barrios.*, barrios.descripcion as nombre_barrio, localidades.descripcion as nombre_localidad FROM barrios INNER JOIN localidades on barrios.localidades_idlocalidades = localidades.idlocalidades WHERE localidades_idlocalidades = $localidades_idlocalidades";
        return $conexion->consultar($query);
    }


    /**
     * Get the value of idbarrios
     */ 
    public function getIdbarrios()
    {
        return $this->idbarrios;
    }

    /**
     * Set the value of idbarrios
     *
     * @return  self
     */ 
    public function setIdbarrios($idbarrios)
    {
        $this->idbarrios = $idbarrios;

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
     * Get the value of localidades_idlocalidades
     */ 
    public function getLocalidades_idlocalidades()
    {
        return $this->localidades_idlocalidades;
    }

    /**
     * Set the value of localidades_idlocalidades
     *
     * @return  self
     */ 
    public function setLocalidades_idlocalidades($localidades_idlocalidades)
    {
        $this->localidades_idlocalidades = $localidades_idlocalidades;

        return $this;
    }
}