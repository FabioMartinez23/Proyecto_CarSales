<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');



class Tipo_Sexos {
    private $idtipo_sexo;
    private $descripcion;

    public function __construct($idtipo_sexo='', $descripcion='') {
        $this->idtipo_sexo = $idtipo_sexo;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_sexo() {
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_sexo (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_sexo() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_sexo SET descripcion = '$this->descripcion' WHERE idtipo_sexo = '$this->idtipo_sexo'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_sexo() {
        $conexion = new Conexion();
        $query = "UPDATE tipo_sexo SET activo_tipo = 0 WHERE idtipo_sexo = '$this->idtipo_sexo'";
        return $conexion->actualizar($query);
    }

    public function validar_tipo_sexo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_sexo WHERE descripcion = '$this->descripcion'";
        return $conexion->consultar($query);
    }

    public function traer_tipo_sexo(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_sexo WHERE activo_tipo = 1";
        return $conexion->consultar($query);
    }

    public function traer_tipo_sexo_id($idtipo_sexo){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_sexo WHERE idtipo_sexo = '$idtipo_sexo'";
        return $conexion->consultar($query);
    }

    /**
     * Get the value of idtipo_sexo
     */ 
    public function getIdtipo_sexo()
    {
        return $this->idtipo_sexo;
    }

    /**
     * Set the value of idtipo_sexo
     *
     * @return  self
     */ 
    public function setIdtipo_sexo($idtipo_sexo)
    {
        $this->idtipo_sexo = $idtipo_sexo;

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
?>