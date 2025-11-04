<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/paginacion.php');

class Estado_Vehiculo extends Paginacion{
    private $idestado_vehiculo;
    private $estado_vehiculo;

    public function __construct($idestado_vehiculo='', $estado_vehiculo='') {
        $this->idestado_vehiculo = $idestado_vehiculo;
        $this->estado_vehiculo = $estado_vehiculo;
    }

    public function agregar_estado_vehiculo(){
        $conexion = new Conexion();
        $query = "INSERT INTO estado_vehiculo (estado_vehiculo) VALUES('$this->estado_vehiculo')";
        return $conexion->insertar($query);
    }

    public function actualizar_estado_vehiculo(){
        $conexion = new Conexion();
        $query = "UPDATE estado_vehiculo SET estado_vehiculo = '$this->estado_vehiculo' WHERE idestado_vehiculo = '$this->idestado_vehiculo'";
        return $conexion->actualizar($query);
    }

    public function eliminar_estado_vehiculo(){
        $conexion = new Conexion();
        $query = "UPDATE estado_vehiculo SET activo_estado = 0 WHERE idestado_vehiculo = '$this->idestado_vehiculo'";
        return $conexion->actualizar($query);
    }

    
    public function validar_estado_vehiculo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM estado_vehiculo WHERE estado_vehiculo = '$this->estado_vehiculo' AND activo_estado = 1";
        return $conexion->consultar($query);
    }

    public function traer_estado_vehiculo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM estado_vehiculo WHERE activo_estado = 1";
        return $conexion->consultar($query);
    }

    public function traer_estado_vehiculo_id($idestado_vehiculo){
        $conexion = new Conexion();
        $query = "SELECT * FROM estado_vehiculo WHERE idestado_vehiculo = '$idestado_vehiculo'";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_estado_vehiculo(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM estado_vehiculo WHERE activo_estado = 1";
        return $conexion->consultar($query);
    }

    public function obtenerIdPorEstado($estado_vehiculo) {
        $conexion = new Conexion();
        $query = "SELECT idestado_vehiculo, estado_vehiculo 
                FROM estado_vehiculo 
                WHERE estado_vehiculo = '$estado_vehiculo' 
                AND activo_estado = 1 
                LIMIT 1";
        
        $resultado = $conexion->consultar($query); // mysqli_result

        // Tomar el primer registro directamente
        if ($fila = $resultado->fetch_assoc()) {
            return $fila['idestado_vehiculo'];
        }

        return null; // si no encontró nada
    }

    /**
     * Get the value of idestado_vehiculo
     */ 
    public function getIdestado_vehiculo()
    {
        return $this->idestado_vehiculo;
    }

    /**
     * Set the value of idestado_vehiculo
     *
     * @return  self
     */ 
    public function setIdestado_vehiculo($idestado_vehiculo)
    {
        $this->idestado_vehiculo = $idestado_vehiculo;

        return $this;
    }



    /**
     * Get the value of estado_vehiculo
     */ 
    public function getEstado_vehiculo()
    {
        return $this->estado_vehiculo;
    }

    /**
     * Set the value of estado_vehiculo
     *
     * @return  self
     */ 
    public function setEstado_vehiculo($estado_vehiculo)
    {
        $this->estado_vehiculo = $estado_vehiculo;

        return $this;
    }
}