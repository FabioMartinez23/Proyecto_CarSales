<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/paginacion.php');


class Colores extends Paginacion{
    private $idcolores;
    private $descripcion;

    public function __construct($idcolores='', $descripcion='') {
        $this->idcolores = $idcolores;
        $this->descripcion = $descripcion;
    }

    public function agregar_color() {
        $conexion = new Conexion();
        $query = "INSERT INTO colores (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_color() {
        $conexion = new Conexion();
        $query = "UPDATE colores SET descripcion = '$this->descripcion' WHERE idcolores = '$this->idcolores'";
        return $conexion->actualizar($query);
    }

    public function eliminar_color() {
        $conexion = new Conexion();
        $query = "UPDATE colores SET activo_marca = 0 WHERE idcolores = '$this->idcolores'";
        return $conexion->actualizar($query);
    }

    public function validar_color(){
        $conexion = new Conexion();
        $query = "SELECT * FROM colores WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_color(){
        $conexion = new Conexion();
        $query = "SELECT * FROM colores WHERE activo_color = 1";
        return $conexion->consultar($query);
    }

    public function traer_color_id($idcolores){
        $conexion = new Conexion();
        $query = "SELECT * FROM colores WHERE idcolores = '$idcolores'";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_color(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM colores WHERE activo_color = 1";
        return $conexion->consultar($query);
    }

    public function traer_colores_paginacion(){
            $conexion = new Conexion();
            $query = "SELECT * FROM colores WHERE activo_color = 1 LIMIT $this->pagina_actual,$this->paginacion";
            return $conexion->consultar($query);
    }


    /**
     * Get the value of idcolores
     */ 
    public function getIdcolores()
    {
        return $this->idcolores;
    }

    /**
     * Set the value of idcolores
     *
     * @return  self
     */ 
    public function setIdcolores($idcolores)
    {
        $this->idcolores = $idcolores;

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
}