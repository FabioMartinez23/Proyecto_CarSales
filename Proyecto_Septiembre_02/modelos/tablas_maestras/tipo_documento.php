<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');



class Tipo_Documentos {
    private $idTipo_documento;
    private $descripcion;

    public function __construct($idTipo_documento='', $descripcion='') {
        $this->idTipo_documento = $idTipo_documento;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_documento() {
        $conexion = new Conexion();
        $query = "INSERT INTO Tipo_documento (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_documento() {
        $conexion = new Conexion();
        $query = "UPDATE Tipo_documento SET descripcion = '$this->descripcion' WHERE idTipo_documento = '$this->idTipo_documento'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_documento() {
        $conexion = new Conexion();
        $query = "UPDATE Tipo_documento SET activo_tipo = 0 WHERE idTipo_documento = '$this->idTipo_documento'";
        return $conexion->actualizar($query);
    }

    public function validar_tipo_documento(){
        $conexion = new Conexion();
        $query = "SELECT * FROM Tipo_documento WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_tipo_documento(){
        $conexion = new Conexion();
        $query = "SELECT * FROM Tipo_documento WHERE activo_tipo = 1";
        return $conexion->consultar($query);
    }

    public function traer_tipo_documento_id($idTipo_documento){
        $conexion = new Conexion();
        $query = "SELECT * FROM Tipo_documento WHERE idTipo_documento = '$idTipo_documento'";
        return $conexion->consultar($query);
    }


    /**
     * Get the value of idTipo_documento
     */ 
    public function getIdTipo_documento()
    {
        return $this->idTipo_documento;
    }

    /**
     * Set the value of idTipo_documento
     *
     * @return  self
     */ 
    public function setIdTipo_documento($idTipo_documento)
    {
        $this->idTipo_documento = $idTipo_documento;

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