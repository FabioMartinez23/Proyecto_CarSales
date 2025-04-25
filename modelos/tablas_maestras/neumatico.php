<?php

class Neumaticos{
    private $idneumaticos;
    private $descripcion_neumaticos;
    

    public function __construct($idneumaticos='', $descripcion_neumaticos='') {
        $this->idneumaticos = $idneumaticos;
        $this->descripcion_neumaticos = $descripcion_neumaticos;
    }

    public function agregar_neumatico(){
        $conexion = new Conexion();
        $query = "INSERT INTO neumaticos (descripcion_neumaticos) VALUES ('$this->descripcion_neumaticos')";
        return $conexion->insertar($query);
    }

    public function modificar_neumatico(){
        $conexion =  new Conexion();
        $query = "UPDATE neumaticos SET descripcion_neumaticos = '$this->descripcion_neumaticos' WHERE idcristales = '$this->idneumaticos'";
        return $conexion->actualizar($query);
    }

    public function eliminar_neumatico(){
        $conexion = new Conexion();
        $query = "UPDATE neumaticos SET activo_neumatico = 0 WHERE idneumaticos = '$this->idneumaticos'";
        return $conexion->actualizar($query);
    }

    public function traer_neumatico(){
        $conexion = new Conexion();
        $query = "SELECT * FROM neumaticos WHERE activo_neumatico = 1";
        return $conexion->consultar($query);
    }
}


?>