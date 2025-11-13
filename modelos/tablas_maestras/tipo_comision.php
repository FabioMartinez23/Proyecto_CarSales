<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class Tipo_Comisiones {
    private $idtipo_comision;
    private $descripcion;

    public function __construct($idtipo_comision='', $descripcion='') {
        $this->idtipo_comision = $idtipo_comision;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_comision() {
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_comision (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_comision() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_comision SET descripcion = '$this->descripcion' WHERE idtipo_comision = '$this->idtipo_comision'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_comision() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_comision SET activo_tipo = 0 WHERE idtipo_comision = '$this->idtipo_comision'";
        return $conexion->actualizar($query);
    }

    public function mostrar_tipo_comision() {
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_comision WHERE activo_tipo = 1";
        return $conexion->consultar($query);
    }

    /**
     * Get the value of idtipo_comision
     */ 
    public function getIdtipo_comision()
    {
        return $this->idtipo_comision;
    }

    /**
     * Set the value of idtipo_comision
     *
     * @return  self
     */ 
    public function setIdtipo_comision($idtipo_comision)
    {
        $this->idtipo_comision = $idtipo_comision;

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