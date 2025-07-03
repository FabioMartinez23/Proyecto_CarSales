<?php

require_once('conexion.php');

class AnularOperacion{
    private $idanular_operacion;
    private $fecha_anulacion;
    private $tipo_anulacion_idtipo_anulacion;
    private $ventas_idventas;
    private $Usuarios_idusuarios;

    public function __construct($idanular_operacion='', $fecha_anulacion='', $tipo_anulacion_idtipo_anulacion='', $ventas_idventas='', $Usuarios_idusuarios='') {
        $this->idanular_operacion = $idanular_operacion;
        $this->fecha_anulacion = $fecha_anulacion;
        $this->tipo_anulacion_idtipo_anulacion = $tipo_anulacion_idtipo_anulacion;
        $this->ventas_idventas = $ventas_idventas;
        $this->Usuarios_idusuarios = $Usuarios_idusuarios;
    }

    public function anular_venta($idvehiculos) {
        $conexion = new Conexion();
        $query_anular = "INSERT INTO anular_operacion 
            (fecha_anulacion, tipo_anulacion_idtipo_anulacion, ventas_idventas, Usuarios_idusuarios) 
            VALUES ('$this->fecha_anulacion', '$this->tipo_anulacion_idtipo_anulacion', '$this->ventas_idventas','$this->Usuarios_idusuarios')";
        $idanulacion = $conexion->insertar($query_anular);

        if ($idanulacion) {
            // Eliminar la venta asociada
            $query_anular = "UPDATE ventas SET estado_venta = 'Anulada' WHERE idventas = '$this->ventas_idventas'";
            $conexion->insertar($query_anular);

            // Restaurar el estado del vehículo
            $query_restaurar = "UPDATE vehiculos SET activo_vehiculo = 1 WHERE idvehiculos = $idvehiculos";
            $conexion->insertar($query_restaurar);

            // Devolver el ID de anulación
            return $idanulacion;
        }
        // Si no se insertó correctamente la anulación
        return false;
    }

    public function traer_venta_anulada_id($idanulacion) {
        $conexion = new Conexion();
        $query = "SELECT * FROM anular_operacion INNER JOIN tipo_anulacion ON anular_operacion.tipo_anulacion_idtipo_anulacion = tipo_anulacion.idtipo_anulacion WHERE idanular_operacion = $idanulacion";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }


    /**
     * Get the value of idanular_operacion
     */ 
    public function getIdanular_operacion()
    {
        return $this->idanular_operacion;
    }

    /**
     * Set the value of idanular_operacion
     *
     * @return  self
     */ 
    public function setIdanular_operacion($idanular_operacion)
    {
        $this->idanular_operacion = $idanular_operacion;

        return $this;
    }

    /**
     * Get the value of fecha_anulacion
     */ 
    public function getFecha_anulacion()
    {
        return $this->fecha_anulacion;
    }

    /**
     * Set the value of fecha_anulacion
     *
     * @return  self
     */ 
    public function setFecha_anulacion($fecha_anulacion)
    {
        $this->fecha_anulacion = $fecha_anulacion;

        return $this;
    }

    /**
     * Get the value of tipo_anulacion_idtipo_anulacion
     */ 
    public function getTipo_anulacion_idtipo_anulacion()
    {
        return $this->tipo_anulacion_idtipo_anulacion;
    }

    /**
     * Set the value of tipo_anulacion_idtipo_anulacion
     *
     * @return  self
     */ 
    public function setTipo_anulacion_idtipo_anulacion($tipo_anulacion_idtipo_anulacion)
    {
        $this->tipo_anulacion_idtipo_anulacion = $tipo_anulacion_idtipo_anulacion;

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

    /**
     * Get the value of Usuarios_idusuarios
     */ 
    public function getUsuarios_idusuarios()
    {
        return $this->Usuarios_idusuarios;
    }

    /**
     * Set the value of Usuarios_idusuarios
     *
     * @return  self
     */ 
    public function setUsuarios_idusuarios($Usuarios_idusuarios)
    {
        $this->Usuarios_idusuarios = $Usuarios_idusuarios;

        return $this;
    }
}