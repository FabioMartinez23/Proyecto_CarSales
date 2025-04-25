<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class Tipo_Domicilios {
    private $idtipo_domicilio;
    private $descripcion;

    public function __construct($idtipo_domicilio='', $descripcion='') {
        $this->idtipo_domicilio = $idtipo_domicilio;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_domicilio() {
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_domicilio (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_domicilio() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_domicilio SET descripcion = '$this->descripcion' WHERE idtipo_domicilio = '$this->idtipo_domicilio'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_domicilio() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_domicilio SET activo_tipo = 0 WHERE idtipo_domicilio = '$this->idtipo_domicilio'";
        return $conexion->actualizar($query);
    }

    public function validar_tipo_domicilio(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_domicilio WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_tipo_domicilio(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_domicilio WHERE activo_tipo = 1";
        return $conexion->consultar($query);
    }

    public function traer_tipo_domicilio_id($idTipo_domicilio){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_domicilio WHERE idtipo_domicilio = '$idTipo_domicilio'";
        return $conexion->consultar($query);
    }


    /**
     * Get the value of idtipo_domicilio
     */ 
    public function getIdtipo_domicilio()
    {
        return $this->idtipo_domicilio;
    }

    /**
     * Set the value of idtipo_domicilio
     *
     * @return  self
     */ 
    public function setIdtipo_domicilio($idtipo_domicilio)
    {
        $this->idtipo_domicilio = $idtipo_domicilio;

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