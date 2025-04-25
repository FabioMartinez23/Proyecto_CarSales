<?php

require_once('conexion.php');

class Modulo{
    private $perfiles_idperfiles;
    private $descripcion;
    private $modulos_idmodulos;

    public function __construct($perfiles_idperfiles='', $descripcion='') {
        $this->perfiles_idperfiles = $perfiles_idperfiles;
        $this->descripcion = $descripcion;
    }

    public function insertar(){
        
    }

    public function actualizar(){
        $conexion = new Conexion();
        $query = "DELETE FROM perfiles_modulos WHERE perfiles_idperfiles = '$this->perfiles_idperfiles'";
        $conexion->actualizar($query);
        foreach($this->modulos_idmodulos as $modulos_idmodulo){
            $query = "INSERT INTO perfiles_modulos (perfiles_idperfiles,modulos_idmodulos) VALUES('$this->perfiles_idperfiles','$modulos_idmodulo')";
            $conexion->actualizar($query);
        }
        return $this->perfiles_idperfiles;
    }

    public function traer_modulos(){
        $conexion = new Conexion();
        $query = "SELECT DISTINCT * FROM modulos WHERE activo_modulo = 1";
        return $conexion->consultar($query);
    }


    public function traer_modulos_por_perfil($perfiles_idperfiles){
        $conexion = new Conexion();
        $query = "SELECT DISTINCT * FROM modulos INNER JOIN 
        perfiles_modulos on perfiles_modulos.modulos_idmodulos = modulos.idmodulos
        WHERE perfiles_modulos.perfiles_idperfiles=".$perfiles_idperfiles;
        return $conexion->consultar($query);
    }

    public function traer_todos_modulos(){
        $conexion = new Conexion();
        $query = "SELECT DISTINCT perfiles.*, perfiles.descripcion as perfiles_nombre, modulos.*, modulos.descripcion as modulos_nombre FROM car_sales_2024.perfiles_modulos INNER JOIN perfiles on perfiles.idperfiles = perfiles_modulos.perfiles_idperfiles INNER JOIN modulos on modulos.idmodulos = perfiles_modulos.modulos_idmodulos";
        return $conexion->consultar($query);
    }




    /**
     * Get the value of perfiles_idperfiles
     */ 
    public function getPerfiles_idperfiles()
    {
        return $this->perfiles_idperfiles;
    }

    /**
     * Set the value of perfiles_idperfiles
     *
     * @return  self
     */ 
    public function setPerfiles_idperfiles($perfiles_idperfiles)
    {
        $this->perfiles_idperfiles = $perfiles_idperfiles;

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
     * Get the value of modulos_idmodulos
     */ 
    public function getModulos_idmodulos()
    {
        return $this->modulos_idmodulos;
    }

    /**
     * Set the value of modulos_idmodulos
     *
     * @return  self
     */ 
    public function setModulos_idmodulos($modulos_idmodulos)
    {
        $this->modulos_idmodulos = $modulos_idmodulos;

        return $this;
    }
}

?>