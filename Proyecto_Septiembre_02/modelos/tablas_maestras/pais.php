<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/paginacion.php');


class Paises extends Paginacion{
    private $idpaises;
    private $descripcion;

    public function __construct($idpaises='', $descripcion='') {
        $this->idpaises = $idpaises;
        $this->descripcion = $descripcion;
    }

    public function agregar_pais() {
        $conexion = new Conexion();
        $query = "INSERT INTO paises (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_pais() {
        $conexion = new Conexion();
        $query = "UPDATE paises SET descripcion = '$this->descripcion' WHERE idpaises = '$this->idpaises'";
        return $conexion->actualizar($query);
    }

    public function eliminar_pais() {
        $conexion = new Conexion();
        $query = "UPDATE paises SET activo_pais = 0 WHERE idpaises = '$this->idpaises'";
        return $conexion->actualizar($query);
    }

    public function validar_pais(){
        $conexion = new Conexion();
        $query = "SELECT * FROM paises WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_pais(){
        $conexion = new Conexion();
        $query = "SELECT * FROM paises WHERE activo_pais = 1";
        return $conexion->consultar($query);
    }

    public function traer_pais_id($idpaises){
        $conexion = new Conexion();
        $query = "SELECT * FROM paises WHERE idpaises = '$idpaises'";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_pais(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM paises WHERE activo_pais = 1";
        return $conexion->consultar($query);
    }

    public function traer_paises_paginacion(){
            $conexion = new Conexion();
            $query = "SELECT * FROM paises WHERE activo_pais = 1 LIMIT $this->pagina_actual,$this->paginacion";
            return $conexion->consultar($query);
    }


    /**
     * Get the value of idpaises
     */ 
    public function getIdpaises()
    {
        return $this->idpaises;
    }

    /**
     * Set the value of idpaises
     *
     * @return  self
     */ 
    public function setIdpaises($idpaises)
    {
        $this->idpaises = $idpaises;

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