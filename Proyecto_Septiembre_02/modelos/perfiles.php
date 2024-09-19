<?php

require_once('conexion.php');

class Perfil{
    private $idperfiles;
    private $descripcion;

    public function __construct($idperfiles='',$descripcion='') {
        $this->idperfiles = $idperfiles;
        $this->descripcion = $descripcion;
    }

    public function traer_perfiles(){
        $conexion = new Conexion();
        $query = "SELECT DISTINCT * FROM perfiles WHERE activo_perfil = 1;";
        return $conexion->consultar($query);
    }

    public function traer_perfil($idperfiles){
        $conexion = new Conexion();
        $query = "SELECT descripcion FROM perfiles WHERE idperfiles = '$idperfiles'";
        return $conexion->consultar($query);
    }

    public function guardar(){
        $conexion = new Conexion();
        $query = "INSERT INTO perfiles(descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_perfil() {
        $conexion = new Conexion();
        $query = "UPDATE perfiles SET descripcion = '$this->descripcion' WHERE idpperfiles = '$this->idperfiles'";
        return $conexion->actualizar($query);
    }

    public function eliminar_perfil() {
        $conexion = new Conexion();
        $query = "UPDATE perfiles SET activo_perfiles = 0 WHERE idperfiles = '$this->idperfiles'";
        return $conexion->actualizar($query);
    }

    public function validar_perfil(){
        $conexion = new Conexion();
        $query = "SELECT * FROM perfiles WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    /**
     * Get the value of idperfiles
     */ 
    public function getIdperfiles()
    {
        return $this->idperfiles;
    }

    /**
     * Set the value of idperfiles
     *
     * @return  self
     */ 
    public function setIdperfiles($idperfiles)
    {
        $this->idperfiles = $idperfiles;

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