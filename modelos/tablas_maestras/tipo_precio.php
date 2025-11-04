<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php'); 

class Tipo_Precios{
    private $idtipo_precios;
    private $descripcion;

    public function __construct($idtipo_precios = '', $descripcion = '') {
        $this->idtipo_precios = $idtipo_precios;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_precio(){
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_precios (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_precio(){
        $conexion = new Conexion();
        $query = "UPDATE tipo_precios SET descripcion = '$this->descripcion' WHERE idtipo_precios = '$this->idtipo_precios'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_precio(){
        $conexion = new Conexion();
        $query = "UPDATE tipo_precios SET activo_tipo_precio = 0 WHERE idtipo_precios = '$this->idtipo_precios'";
        return $conexion->actualizar($query);
    }

    public function mostrar_tipo_precio(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_precios WHERE activo_tipo_precio = 1";
        return $conexion->consultar($query);
    }

    public function obtenerIdPorDescripcion($descripcion) {
        $conexion = new Conexion();
        $query = "SELECT idtipo_precios FROM tipo_precios WHERE descripcion = '$descripcion' AND activo_tipo_precio = 1";
        $resultado = $conexion->consultar($query);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc(); // ✅ convierte el objeto en array
            return $row['idtipo_precios'];
        }

        return null; // Retorna null si no se encuentra
    }

    /**
     * Get the value of idtipo_precios
     */ 
    public function getIdtipo_precios()
    {
        return $this->idtipo_precios;
    }

    /**
     * Set the value of idtipo_precios
     *
     * @return  self
     */ 
    public function setIdtipo_precios($idtipo_precios)
    {
        $this->idtipo_precios = $idtipo_precios;

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