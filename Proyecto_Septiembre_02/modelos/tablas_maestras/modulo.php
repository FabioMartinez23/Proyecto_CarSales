<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class Modulos{
    private $idmodulos;
    private $descripcion;

    public function __construct($idmodulos ='',$descripcion ='') {
        $this->idmodulos = $idmodulos;
        $this->descripcion = $descripcion;
    }

    public function agregar_modulo() {
        $conexion = new Conexion();
        $query = "INSERT INTO modulos (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_modulo() {
        $conexion = new Conexion();
        $query = "UPDATE modulos SET descripcion = '$this->descripcion' WHERE idmodulos = '$this->idmodulos'";
        return $conexion->actualizar($query);
    }

    public function eliminar_modulo() {
        $conexion = new Conexion();
        $query = "UPDATE modulos SET activo_modulo = 0 WHERE idmodulos = '$this->idmodulos'";
        return $conexion->actualizar($query);
    }

    public function validar_modulo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM modulos WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_modulo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM modulos WHERE activo_modulo = 1";
        return $conexion->consultar($query);
    }

    public function traer_modulos_id(){
        $conexion = new Conexion();
        $query = "SELECT DISTINCT * FROM modulos WHERE idmodulos = '$this->idmodulos'";
        return $conexion->consultar($query);
    }

    /**
     * Get the value of idmodulos
     */ 
    public function getIdmodulos()
    {
        return $this->idmodulos;
    }

    /**
     * Set the value of idmodulos
     *
     * @return  self
     */ 
    public function setIdmodulos($idmodulos)
    {
        $this->idmodulos = $idmodulos;

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

?>