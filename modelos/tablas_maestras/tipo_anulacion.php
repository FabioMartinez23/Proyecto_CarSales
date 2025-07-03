<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class Tipo_Anulaciones {
    private $idtipo_anulacion;
    private $descripcion;

    public function __construct($idtipo_anulacion='', $descripcion='') {
        $this->idtipo_anulacion = $idtipo_anulacion;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_anulacion() {
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_anulacion (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function modificar_tipo_anulacion() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_anulacion SET descripcion = '$this->descripcion' WHERE idtipo_anulacion = '$this->idtipo_anulacion'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_anulacion() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_anulacion SET activo_tipo_anulacion = 0 WHERE idtipo_anulacion = '$this->idtipo_anulacion'";
        return $conexion->actualizar($query);
    }

    public function validar_tipo_anulacion() {
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_anulacion WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_tipo_anulacion() {
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_anulacion WHERE activo_tipo_anulacion = 1";
        return $conexion->consultar($query);
    }

    public function traer_tipo_anulacion_id($idtipo_anulacion) {
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_anulacion WHERE idtipo_anulacion = $idtipo_anulacion";
        return $conexion->consultar($query);
    }

    /**
     * Get the value of idtipo_anulacion
     */ 
    public function getIdtipo_anulacion()
    {
        return $this->idtipo_anulacion;
    }

    /**
     * Set the value of idtipo_anulacion
     *
     * @return  self
     */ 
    public function setIdtipo_anulacion($idtipo_anulacion)
    {
        $this->idtipo_anulacion = $idtipo_anulacion;

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