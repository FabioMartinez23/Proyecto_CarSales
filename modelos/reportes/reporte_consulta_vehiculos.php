<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/conexion.php');

class ReporteConsultaVehiculo {
    private $idreporte_consulta_vehiculo;
    private $clicks;
    private $fecha_consulta;
    private $vehiculos_idvehiculos;

    public function __construct($idreporte_consulta_vehiculo='', $clicks='', $fecha_consulta='', $vehiculos_idvehiculos='') {
        $this->idreporte_consulta_vehiculo = $idreporte_consulta_vehiculo;
        $this->clicks = $clicks;
        $this->fecha_consulta = $fecha_consulta;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
    }

    public function verificar_fecha_existente() {
        try {
            $conexion = new Conexion();
            $query = "SELECT * FROM reporte_consulta_vehiculo 
                      WHERE fecha_consulta = '$this->fecha_consulta' 
                      AND vehiculos_idvehiculos = '$this->vehiculos_idvehiculos'";
    
            // Ejecutar consulta
            $resultado = $conexion->consultar($query);
    
            // Procesar resultado
            if ($resultado && $resultado->num_rows > 0) {
                return $resultado->fetch_assoc(); // Devuelve el primer registro como un array asociativo
            } else {
                return null; // No hay registros
            }
        } catch (Exception $e) {
            error_log("Error en verificar_fecha_existente: " . $e->getMessage());
            return null;
        }
    }
    
    

    public function guardar_clicks() {
        try {
            $conexion = new Conexion();
    
            // Verificar si ya existe un registro para la fecha y el vehículo
            $registro_existente = $this->verificar_fecha_existente();
    
            if ($registro_existente) {
                // Si ya existe, decodificar clicks existentes y agregar nuevos
                $id = $registro_existente['idreporte_consulta_vehiculo'];
                $clicks_existentes = json_decode($registro_existente['clicks'], true) ?? [];
                $clicks_existentes[] = 1; // Agregar un nuevo click
                $clicks_actualizados = json_encode($clicks_existentes);
    
                $query = "UPDATE reporte_consulta_vehiculo 
                          SET clicks = '$clicks_actualizados' 
                          WHERE idreporte_consulta_vehiculo = '$id'";
            } else {
                // Si no existe, insertar un nuevo registro con el primer click
                $clicks_actuales = json_encode([1]); // Iniciar la lista con un click
                $query = "INSERT INTO reporte_consulta_vehiculo(clicks, fecha_consulta, vehiculos_idvehiculos) 
                          VALUES ('$clicks_actuales', '$this->fecha_consulta', '$this->vehiculos_idvehiculos')";
            }
    
            // Registrar la consulta para depuración
            error_log("Consulta SQL: $query");
    
            // Ejecutar la consulta
            $resultado = $conexion->insertar($query);
            if ($resultado) {
                return true; // Si la consulta fue exitosa
            } else {
                throw new Exception("Error al ejecutar la consulta SQL.");
            }
    
        } catch (Exception $e) {
            // Manejo de errores
            error_log("Error al guardar el clic: " . $e->getMessage());
            return false;
        }
    }
    
    

    public function mostrar_vehiculo_consultado(){
        $conexion = new Conexion();
        $query = "";
    }



    /**
     * Get the value of idreporte_consulta_vehiculo
     */ 
    public function getIdreporte_consulta_vehiculo()
    {
        return $this->idreporte_consulta_vehiculo;
    }

    /**
     * Set the value of idreporte_consulta_vehiculo
     *
     * @return  self
     */ 
    public function setIdreporte_consulta_vehiculo($idreporte_consulta_vehiculo)
    {
        $this->idreporte_consulta_vehiculo = $idreporte_consulta_vehiculo;

        return $this;
    }

    /**
     * Get the value of clicks
     */ 
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * Set the value of clicks
     *
     * @return  self
     */ 
    public function setClicks($clicks)
    {
        $this->clicks = $clicks;

        return $this;
    }

    /**
     * Get the value of fecha_consulta
     */ 
    public function getFecha_consulta()
    {
        return $this->fecha_consulta;
    }

    /**
     * Set the value of fecha_consulta
     *
     * @return  self
     */ 
    public function setFecha_consulta($fecha_consulta)
    {
        $this->fecha_consulta = $fecha_consulta;

        return $this;
    }

    /**
     * Get the value of vehiculos_idvehiculos
     */ 
    public function getVehiculos_idvehiculos()
    {
        return $this->vehiculos_idvehiculos;
    }

    /**
     * Set the value of vehiculos_idvehiculos
     *
     * @return  self
     */ 
    public function setVehiculos_idvehiculos($vehiculos_idvehiculos)
    {
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;

        return $this;
    }
}
