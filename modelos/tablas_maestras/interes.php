<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class Intereses{
    private $idintereses;
    private $descripcion;
    private $porcentaje;
    private $activo_interes;

    public function __construct($idintereses = null, $descripcion = null, $porcentaje = null, $activo_interes = null) {
        $this->idintereses = $idintereses;
        $this->descripcion = $descripcion;
        $this->porcentaje = $porcentaje;
        $this->activo_interes = $activo_interes;
    }

    public function agregar_interes(){
        $conexion = new Conexion();
        $query = "INSERT INTO intereses (descripcion, porcentaje) VALUES('$this->descripcion', '$this->porcentaje')";
        return $conexion->insertar($query);
    }

    public function actualizar_interes(){
        $conexion = new Conexion();
        $query = "UPDATE intereses SET descripcion = '$this->descripcion', porcentaje = '$this->porcentaje' WHERE idintereses = '$this->idintereses'";
        return $conexion->actualizar($query);
    }

    public function eliminar_interes(){
        $conexion = new Conexion();
        $query = "UPDATE intereses SET activo_interes = 0 WHERE idintereses = '$this->idintereses'";
        return $conexion->actualizar($query);
    }

    public function traer_interes(){
        $conexion = new Conexion();
        $query = "SELECT * FROM intereses WHERE descripcion != '0%' AND activo_interes = 1";
        return $conexion->consultar($query);
    }

    public function traer_interes_id($idintereses){
        $conexion = new Conexion();
        $query = "SELECT * FROM intereses WHERE idintereses = '$idintereses'";
        return $conexion->consultar($query);
    }

    public function obtenerPorId($idintereses) {
        $conexion = new Conexion();
        $query = "SELECT * FROM intereses WHERE idintereses = '$idintereses'";
        $resultado = $conexion->consultar($query);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc(); // ✅ Convierte el objeto en array
            return $row;
        }

        return null; // No se encontró el registro
    }
    

    /**
     * Get the value of idintereses
     */ 
    public function getIdintereses()
    {
        return $this->idintereses;
    }

    /**
     * Set the value of idintereses
     *
     * @return  self
     */ 
    public function setIdintereses($idintereses)
    {
        $this->idintereses = $idintereses;

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

    /**
     * Get the value of porcentaje
     */ 
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set the value of porcentaje
     *
     * @return  self
     */ 
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get the value of activo_interes
     */ 
    public function getActivo_interes()
    {
        return $this->activo_interes;
    }

    /**
     * Set the value of activo_interes
     *
     * @return  self
     */ 
    public function setActivo_interes($activo_interes)
    {
        $this->activo_interes = $activo_interes;

        return $this;
    }
}