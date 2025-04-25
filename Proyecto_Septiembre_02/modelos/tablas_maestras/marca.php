<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/paginacion.php');


class Marcas extends Paginacion{
    private $idmarcas;
    private $nombre;

    public function __construct($idmarcas='', $nombre='') {
        $this->idmarcas = $idmarcas;
        $this->nombre = $nombre;
    }

    public function agregar_marca() {
        $conexion = new Conexion();
        $query = "INSERT INTO marcas (nombre) VALUES ('$this->nombre')";
        return $conexion->insertar($query);
    }

    public function actualizar_marca() {
        $conexion = new Conexion();
        $query = "UPDATE marcas SET nombre = '$this->nombre' WHERE idmarcas = '$this->idmarcas'";
        return $conexion->actualizar($query);
    }

    public function eliminar_marca() {
        $conexion = new Conexion();
        $query = "UPDATE marcas SET activo_marca = 0 WHERE idmarcas = '$this->idmarcas'";
        return $conexion->actualizar($query);
    }

    public function validar_marca(){
        $conexion = new Conexion();
        $query = "SELECT * FROM marcas WHERE nombre = '$this->nombre'";
        return $conexion->consultar($query);
    }

    public function traer_marca(){
        $conexion = new Conexion();
        $query = "SELECT * FROM marcas WHERE activo_marca = 1";
        return $conexion->consultar($query);
    }

    public function traer_marca_id($idmarcas){
        $conexion = new Conexion();
        $query = "SELECT * FROM marcas WHERE idmarcas = '$idmarcas'";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_marca(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM marcas WHERE activo_marca = 1";
        return $conexion->consultar($query);
    }

    public function traer_marcas_paginacion(){
            $conexion = new Conexion();
            $query = "SELECT * FROM marcas WHERE activo_marca = 1 LIMIT $this->pagina_actual,$this->paginacion";
            return $conexion->consultar($query);
    }




    /**
     * Get the value of idmarcas
     */ 
    public function getIdmarcas()
    {
        return $this->idmarcas;
    }

    /**
     * Set the value of idmarcas
     *
     * @return  self
     */ 
    public function setIdmarcas($idmarcas)
    {
        $this->idmarcas = $idmarcas;

        return $this;
    }

    /**
     * Get the value of nombre
     */ 
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     *
     * @return  self
     */ 
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }
}