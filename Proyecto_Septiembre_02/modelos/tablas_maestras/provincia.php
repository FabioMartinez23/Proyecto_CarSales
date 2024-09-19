<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/paginacion.php');

class Provincias extends Paginacion{
    private $idprovincias;
    private $descripcion;
    private $paises_idpaises;


    public function __construct($idprovincias='', $descripcion='', $paises_idpaises='') {
        $this->idprovincias = $idprovincias;
        $this->descripcion = $descripcion;
        $this->paises_idpaises = $paises_idpaises;
    }

    public function agregar_provincia(){
        $conexion = new Conexion();
        $query = "INSERT INTO provincias (descripcion,paises_idpaises) VALUES('$this->descripcion','$this->paises_idpaises')";
        return $conexion->insertar($query);
    }

    public function actualizar_provincia(){
        $conexion = new Conexion();
        $query = "UPDATE provincias SET descripcion = '$this->descripcion', paises_idpaises = '$this->paises_idpaises' WHERE idprovincias = '$this->idprovincias'";
        return $conexion->actualizar($query);
    }

    public function eliminar_provincia(){
        $conexion = new Conexion();
        $query = "UPDATE provincias SET activo = 0 WHERE idprovincias = '$this->idprovincias'";
        return $conexion->actualizar($query);
    }

    
    public function validar_provincia(){
        $conexion = new Conexion();
        $query = "SELECT * FROM provincias WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_provincia(){
        $conexion = new Conexion();
        $query = "SELECT * FROM provincias WHERE activo_provincia = 1";
        return $conexion->consultar($query);
    }

    public function traer_provincia_id($idmodelos){
        $conexion = new Conexion();
        $query = "SELECT * FROM provincias WHERE idprovincias = '$idmodelos'";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_provincia(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM provincias WHERE activo_provincia = 1";
        return $conexion->consultar($query);
    }

    public function traer_provincias_paginacion(){
            $conexion = new Conexion();
            $query = "SELECT provincias.*, provincias.descripcion as nombre_provincia, paises.*, paises.descripcion as nombre_pais FROM provincias INNER JOIN paises on provincias.paises_idpaises = paises.idpaises WHERE activo_provincia = 1 LIMIT $this->pagina_actual,$this->paginacion";
            return $conexion->consultar($query);
    }

    /**
     * Get the value of idprovincias
     */ 
    public function getIdprovincias()
    {
        return $this->idprovincias;
    }

    /**
     * Set the value of idprovincias
     *
     * @return  self
     */ 
    public function setIdprovincias($idprovincias)
    {
        $this->idprovincias = $idprovincias;

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
     * Get the value of paises_idpaises
     */ 
    public function getPaises_idpaises()
    {
        return $this->paises_idpaises;
    }

    /**
     * Set the value of paises_idpaises
     *
     * @return  self
     */ 
    public function setPaises_idpaises($paises_idpaises)
    {
        $this->paises_idpaises = $paises_idpaises;

        return $this;
    }
}

?>