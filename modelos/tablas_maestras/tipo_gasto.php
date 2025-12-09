<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class Tipo_Gasto {
    private $idtipo_gasto;
    private $descripcion;
    private $aplica_en;
    private $modo_calculo;
    private $valor_calculo;

    public function __construct(
        $idtipo_gasto = '',
        $descripcion = '',
        $aplica_en = 'manual',
        $modo_calculo = 'monto_fijo',
        $valor_calculo = 0.00
    ) {
        $this->idtipo_gasto  = $idtipo_gasto;
        $this->descripcion   = $descripcion;
        $this->aplica_en     = $aplica_en;
        $this->modo_calculo  = $modo_calculo;
        $this->valor_calculo = $valor_calculo;
    }

    /* ============================
       CRUD BÁSICO
       ============================ */

    public function agregar_tipo_gasto() {
        $conexion = new Conexion();
        $query = "
            INSERT INTO tipo_gasto 
                (descripcion, aplica_en, modo_calculo, valor_calculo, activo_gasto) 
            VALUES 
                ('$this->descripcion', '$this->aplica_en', '$this->modo_calculo', '$this->valor_calculo', 1)
        ";
        return $conexion->insertar($query);
    }

    public function actualizar_tipo_gasto() {
        $conexion = new Conexion();
        $query = "
            UPDATE tipo_gasto 
            SET 
                descripcion   = '$this->descripcion',
                aplica_en     = '$this->aplica_en',
                modo_calculo  = '$this->modo_calculo',
                valor_calculo = '$this->valor_calculo'
            WHERE idtipo_gasto = '$this->idtipo_gasto'
        ";
        return $conexion->actualizar($query);
    }

    // Soft delete
    public function eliminar_tipo_gasto() {
        $conexion = new Conexion();
        $query = "
            UPDATE tipo_gasto 
            SET activo_gasto = 0 
            WHERE idtipo_gasto = '$this->idtipo_gasto'
        ";
        return $conexion->actualizar($query);
    }

    public function traer_tipo_gasto() {
        $conexion = new Conexion();
        $query = "
            SELECT * 
            FROM tipo_gasto 
            WHERE activo_gasto = 1
            ORDER BY descripcion ASC
        ";
        return $conexion->consultar($query);
    }

    public function traer_tipo_gasto_id($idtipo_gasto) {
        $conexion = new Conexion();
        $query = "
            SELECT * 
            FROM tipo_gasto 
            WHERE idtipo_gasto = '$idtipo_gasto'
            LIMIT 1
        ";
        $resultado = $conexion->consultar($query);
        if ($resultado && $resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }

    /* ============================
       GETTERS / SETTERS
       ============================ */

    public function getIdtipo_gasto() {
        return $this->idtipo_gasto;
    }

    public function setIdtipo_gasto($idtipo_gasto) {
        $this->idtipo_gasto = $idtipo_gasto;
        return $this;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getAplica_en() {
        return $this->aplica_en;
    }

    public function setAplica_en($aplica_en) {
        $this->aplica_en = $aplica_en;
        return $this;
    }

    public function getModo_calculo() {
        return $this->modo_calculo;
    }

    public function setModo_calculo($modo_calculo) {
        $this->modo_calculo = $modo_calculo;
        return $this;
    }

    public function getValor_calculo() {
        return $this->valor_calculo;
    }

    public function setValor_calculo($valor_calculo) {
        $this->valor_calculo = $valor_calculo;
        return $this;
    }
}
