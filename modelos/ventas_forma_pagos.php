<?php

require_once('conexion.php');

class VentaFormaPago {
    private $idventas_forma_de_pago;
    private $compras_idcompras;
    private $ventas_idventas;


    public function __construct($idventas_forma_de_pago='', $compras_idcompras='', $ventas_idventas='') {
        $this->idventas_forma_de_pago = $idventas_forma_de_pago;
        $this->compras_idcompras = $compras_idcompras;
        $this->ventas_idventas = $ventas_idventas;
    }

    public function agregar_parte_de_pago(){
        $conexion = new Conexion();
        $query = "INSERT INTO ventas_forma_de_pago (compras_idcompras, ventas_idventas) VALUES ('$this->compras_idcompras', '$this->ventas_idventas')";
        return $conexion->insertar($query);
    }

    public function traer_vehiculo_forma_pago($ventas_idventas){
        $conexion = new Conexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM ventas_forma_de_pago INNER JOIN compras ON ventas_forma_de_pago.compras_idcompras = compras.idcompras INNER JOIN vehiculos ON compras.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos INNER JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos WHERE ventas_idventas = $ventas_idventas AND  activo_precio = 1";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }

    /**
     * Get the value of idventas_forma_de_pago
     */ 
    public function getIdventas_forma_de_pago()
    {
        return $this->idventas_forma_de_pago;
    }

    /**
     * Set the value of idventas_forma_de_pago
     *
     * @return  self
     */ 
    public function setIdventas_forma_de_pago($idventas_forma_de_pago)
    {
        $this->idventas_forma_de_pago = $idventas_forma_de_pago;

        return $this;
    }

    /**
     * Get the value of compras_idcompras
     */ 
    public function getCompras_idcompras()
    {
        return $this->compras_idcompras;
    }

    /**
     * Set the value of compras_idcompras
     *
     * @return  self
     */ 
    public function setCompras_idcompras($compras_idcompras)
    {
        $this->compras_idcompras = $compras_idcompras;

        return $this;
    }

    /**
     * Get the value of ventas_idventas
     */ 
    public function getVentas_idventas()
    {
        return $this->ventas_idventas;
    }

    /**
     * Set the value of ventas_idventas
     *
     * @return  self
     */ 
    public function setVentas_idventas($ventas_idventas)
    {
        $this->ventas_idventas = $ventas_idventas;

        return $this;
    }
}