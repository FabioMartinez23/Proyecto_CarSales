<?php

class Cristales{
    private $idcristales;
    private $descripcion_cristales;
    

    public function __construct($idcristales='', $descripcion_cristales='') {
        $this->idcristales = $idcristales;
        $this->descripcion_cristales = $descripcion_cristales;
    }

    public function agregar_cristal(){
        $conexion = new Conexion();
        $query = "INSERT INTO cristales (descripcion_cristales) VALUES ('$this->descripcion_cristales')";
        return $conexion->insertar($query);
    }

    public function modificar_cristal(){
        $conexion =  new Conexion();
        $query = "UPDATE cristales SET descripcion_cristales = '$this->descripcion_cristales' WHERE idcristales = '$this->idcristales'";
        return $conexion->actualizar($query);
    }

    public function eliminar_cristal(){
        $conexion = new Conexion();
        $query = "UPDATE cristales SET activo_cristal = 0 WHERE idcristales = '$this->idcristales'";
        return $conexion->actualizar($query);
    }

    public function traer_cristal(){
        $conexion = new Conexion();
        $query = "SELECT * FROM cristales WHERE activo_cristal = 1";
        return $conexion->consultar($query);
    }
}


?>