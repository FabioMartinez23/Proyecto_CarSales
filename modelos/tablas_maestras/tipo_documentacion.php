<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class Tipo_Documentacion {
    private $idtipo_documentacion;
    private $descripcion;

    public function __construct($idtipo_documentacion = '', $descripcion = '') {
        $this->idtipo_documentacion = $idtipo_documentacion;
        $this->descripcion = $descripcion;
    }

    public function agregar_tipo_documentacion(){
        $conexion = new Conexion();
        $query = "INSERT INTO tipo_documentacion (descripcion) VALUES ('$this->descripcion')";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_documentacion(){
        $conexion = new Conexion();
        $query = "UPDATE tipo_documentacion SET descripcion = '$this->descripcion' WHERE idtipo_documentacion = '$this->idtipo_documentacion'";
        return $conexion->actualizar($query);
    }

    public function eliminar_tipo_documentacion(){
        $conexion = new Conexion();
        $query = "UPDATE tipo_documentacion SET activo_tipo_doc = 0 WHERE idtipo_documentacion = '$this->idtipo_documentacion'";
        return $conexion->actualizar($query);
    }

    public function mostrar_tipo_documentacion(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_documentacion WHERE activo_tipo_doc = 1";
        return $conexion->consultar($query);
    }

    public function mostrar_tipo_doc(){
        $conexion = new Conexion();
        $query = "SELECT * FROM tipo_documentacion WHERE descripcion != 'imagen'";
        return $conexion->consultar($query);
    }

    public function traer_todos_los_tipos() {
        $conexion = new Conexion();
        $query = "SELECT idtipo_documentacion, descripcion 
                FROM tipo_documentacion 
                WHERE activo_tipo_doc = 1 
                AND descripcion != 'imagen'";
        $resultado = $conexion->consultar($query);

        $tipos = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $tipos[] = $fila;
            }
        }

        return $tipos;
    }
    
    public function mostrar_tipos_faltantes($vehiculos_idvehiculos) {
        $conexion = new Conexion();
        $query = "
            SELECT td.idtipo_documentacion, td.descripcion
            FROM tipo_documentacion td
            WHERE td.activo_tipo_doc = 1 
            AND td.descripcion <> 'imagen'
            AND td.idtipo_documentacion NOT IN (
                SELECT d.tipo_documentacion_idtipo_documentacion
                FROM Documentaciones d
                WHERE d.vehiculos_idvehiculos = $vehiculos_idvehiculos
                AND d.digitalizado = 1
            )
            ORDER BY td.descripcion ASC
        ";
        return $conexion->consultar($query);
    }

    public function obtenerIdPorDescripcion($descripcion) {
        $conexion = new Conexion();
        $query = "SELECT idtipo_documentacion FROM tipo_documentacion WHERE descripcion = '$descripcion' LIMIT 1";
        $resultado = $conexion->consultar($query);

        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return $fila['idtipo_documentacion'];
        }
        return null;
    }

    
    public function traer_por_id($id) {
        $conexion = new Conexion();

        $query = "SELECT idtipo_documentacion, descripcion
                  FROM tipo_documentacion
                  WHERE idtipo_documentacion = '$id'
                  LIMIT 1";

        $res = $conexion->consultar($query);

        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc();
        }

        return null;
    }

    /**
     * Get the value of idtipo_documentacion
     */ 
    public function getIdtipo_documentacion()
    {
        return $this->idtipo_documentacion;
    }

    /**
     * Set the value of idtipo_documentacion
     *
     * @return  self
     */ 
    public function setIdtipo_documentacion($idtipo_documentacion)
    {
        $this->idtipo_documentacion = $idtipo_documentacion;

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