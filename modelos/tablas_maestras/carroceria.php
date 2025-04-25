<?php

class Carrocerias{
    private $idcarroceria;
    private $descripcion_carroceria;
    

    public function __construct($idcarroceria='', $descripcion_carroceria='') {
        $this->idcarroceria = $idcarroceria;
        $this->descripcion_carroceria = $descripcion_carroceria;
    }

    public function agregar_carroceria(){
        $conexion = new Conexion();
        $query = "INSERT INTO carroceria (descripcion_carroceria) VALUES ('$this->descripcion_carroceria')";
        return $conexion->insertar($query);
    }

    public function modificar_carroceria(){
        $conexion =  new Conexion();
        $query = "UPDATE carroceria SET descripcion_carroceria = '$this->descripcion_carroceria' WHERE idcarroceria = '$this->idcarroceria'";
        return $conexion->actualizar($query);
    }

    public function eliminar_carroceria(){
        $conexion = new Conexion();
        $query = "UPDATE carroceria SET activo_carroceria = 0 WHERE idcarroceria = '$this->idcarroceria'";
        return $conexion->actualizar($query);
    }

    public function traer_carroceria(){
        $conexion = new Conexion();
        $query = "SELECT * FROM carroceria WHERE activo_carroceria = 1";
        return $conexion->consultar($query);
    }
}


?>