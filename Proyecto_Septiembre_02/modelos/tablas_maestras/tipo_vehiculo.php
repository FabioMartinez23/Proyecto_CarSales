<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');



class Tipo_Vehiculos {
    private $idtipo_vehiculos;
    private $nombre;

    public function __construct($idtipo_vehiculos='', $nombre='') {
        $this->idtipo_vehiculos = $idtipo_vehiculos;
        $this->nombre = $nombre;
    }

    public function agregar_tipo_vehiculo() {
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_vehiculos (nombre) VALUES ('$this->nombre')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_vehiculo() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_vehiculos SET nombre = '$this->nombre' WHERE idtipo_vehiculos = '$this->idtipo_vehiculos'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_vehiculo() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_vehiculos SET activo_tipo = 0 WHERE idtipo_vehiculos = '$this->idtipo_vehiculos'";
        return $conexion->actualizar($query);
    }

    public function validar_tipo_vehiculo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_vehiculos WHERE nombre = '$this->nombre'";
        return $conexion->consultar($query);
    }

    public function traer_tipo_vehiculo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_vehiculos WHERE activo_tipo = 1";
        return $conexion->consultar($query);
    }

    public function traer_tipo_vehiculo_id($idtipo_vehiculos){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_vehiculos WHERE idtipo_vehiculos = '$idtipo_vehiculos'";
        return $conexion->consultar($query);
    }


    /**
     * Get the value of idtipo_vehiculos
     */ 
    public function getIdtipo_vehiculos()
    {
        return $this->idtipo_vehiculos;
    }

    /**
     * Set the value of idtipo_vehiculos
     *
     * @return  self
     */ 
    public function setIdtipo_vehiculos($idtipo_vehiculos)
    {
        $this->idtipo_vehiculos = $idtipo_vehiculos;

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
}