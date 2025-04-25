<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class Tipo_Contactos {
    private $idtipo_contacto;
    private $descripcion;

    public function __construct($idtipo_contacto='', $descripcion='') {
        $this->idtipo_contacto = $idtipo_contacto;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_contacto() {
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_contacto (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_contacto() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_contacto SET descripcion = '$this->descripcion' WHERE idtipo_contacto = '$this->idtipo_contacto'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_contacto() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_contacto SET activo_tipo = 0 WHERE idtipo_contacto = '$this->idtipo_contacto'";
        return $conexion->actualizar($query);
    }

    public function validar_tipo_contacto(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_contacto WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_tipo_contacto(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_contacto WHERE activo_tipo = 1";
        return $conexion->consultar($query);
    }

    public function traer_tipo_contacto_id($idtipo_contacto){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_contacto WHERE idtipo_contacto = '$idtipo_contacto'";
        return $conexion->consultar($query);
    }


    /**
     * Get the value of idtipo_contacto
     */ 
    public function getIdtipo_contacto()
    {
        return $this->idtipo_contacto;
    }

    /**
     * Set the value of idtipo_contacto
     *
     * @return  self
     */ 
    public function setIdtipo_contacto($idtipo_contacto)
    {
        $this->idtipo_contacto = $idtipo_contacto;

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