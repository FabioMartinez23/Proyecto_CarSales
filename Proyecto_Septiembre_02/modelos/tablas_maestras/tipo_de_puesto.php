<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');



class Tipo_De_Puestos {
    private $idtipo_de_puestos;
    private $descripcion;

    public function __construct($idtipo_de_puestos='', $descripcion='') {
        $this->idtipo_de_puestos = $idtipo_de_puestos;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_puesto() {
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_de_puestos (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_puesto() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_de_puestos SET descripcion = '$this->descripcion' WHERE idtipo_de_puestos = '$this->idtipo_de_puestos'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_puesto() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_de_puestos SET activo_tipo = 0 WHERE idtipo_de_puestos = '$this->idtipo_de_puestos'";
        return $conexion->actualizar($query);
    }

    public function validar_tipo_puesto(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_de_puestos WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_tipo_puesto(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_de_puestos WHERE activo_tipo = 1";
        return $conexion->consultar($query);
    }

    public function traer_tipo_puesto_id($idtipo_de_puestos){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_de_puestos WHERE idtipo_de_puestos = '$idtipo_de_puestos'";
        return $conexion->consultar($query);
    }


    /**
     * Get the value of idtipo_de_puestos
     */ 
    public function getIdtipo_de_puestos()
    {
        return $this->idtipo_de_puestos;
    }

    /**
     * Set the value of idtipo_de_puestos
     *
     * @return  self
     */ 
    public function setIdtipo_de_puestos($idtipo_de_puestos)
    {
        $this->idtipo_de_puestos = $idtipo_de_puestos;

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