<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');



class Tipo_Pagos {
    private $idtipo_pago;
    private $descripcion;

    public function __construct($idtipo_pago='', $descripcion='') {
        $this->idtipo_pago = $idtipo_pago;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_pago() {
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_pago (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_pago() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_pago SET descripcion = '$this->descripcion' WHERE idtipo_pago = '$this->idtipo_pago'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_pago() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_pago SET activo_tipo = 0 WHERE idtipo_pago = '$this->idtipo_pago'";
        return $conexion->actualizar($query);
    }

    public function validar_tipo_pago(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_pago WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_tipo_pago(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_pago WHERE activo_tipo = 1";
        return $conexion->consultar($query);
    }

    public function traer_tipo_pago_id($idtipo_pago){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_pago WHERE idtipo_pago = '$idtipo_pago'";
        return $conexion->consultar($query);
    }




    /**
     * Get the value of idtipo_pago
     */ 
    public function getIdtipo_pago()
    {
        return $this->idtipo_pago;
    }

    /**
     * Set the value of idtipo_pago
     *
     * @return  self
     */ 
    public function setIdtipo_pago($idtipo_pago)
    {
        $this->idtipo_pago = $idtipo_pago;

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