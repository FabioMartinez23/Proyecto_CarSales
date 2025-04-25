<?php

require_once('conexion.php');

class Financiamiento{
    private $idfinanciamientos;
    private $estado;
    private $fecha_financiamiento;
    private $vehiculos_idvehiculos;
    private $cantidades_cuotas_idcantidades_cuotas;
    private $registro_clientes_idregistro_clientes;

    public function __construct($idfinanciamientos='', $estado='', $fecha_financiamiento='', $vehiculos_idvehiculos='', $cantidades_cuotas_idcantidades_cuotas='', $registro_clientes_idregistro_clientes='') {
        $this->idfinanciamientos = $idfinanciamientos;
        $this->estado = $estado;
        $this->fecha_financiamiento = $fecha_financiamiento;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
        $this->cantidades_cuotas_idcantidades_cuotas = $cantidades_cuotas_idcantidades_cuotas;
        $this->registro_clientes_idregistro_clientes = $registro_clientes_idregistro_clientes;
    }

    public function agregar_financiamiento(){
        $conexion = new Conexion();
        $query = "INSERT INTO financiamientos (estado, fecha_financiamiento, vehiculos_idvehiculos, cantidades_cuotas_idcantidades_cuotas, registro_clientes_idregistro_clientes) VALUES ('$this->estado', CURDATE(), '$this->vehiculos_idvehiculos', '$this->cantidades_cuotas_idcantidades_cuotas', '$this->registro_clientes_idregistro_clientes')";
        return $conexion->insertar($query);
    }

    public function traer_todos_los_financiamientos(){
        $conexion = new Conexion();
        $query = "";
        return $conexion->consultar($query);
    }
}


?>