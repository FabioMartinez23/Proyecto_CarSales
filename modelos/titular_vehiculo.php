<?php

require_once('conexion.php');

class Titular_Vehiculo{
    private $idtitular_vehiculo;
    private $vehiculos_idvehiculos;
    private $Personas_idpersonas;

    public function __construct($idtitular_vehiculo='', $vehiculos_idvehiculos='', $Personas_idpersonas='') {
        $this->idtitular_vehiculo = $idtitular_vehiculo;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
        $this->Personas_idpersonas = $Personas_idpersonas;
    }

    public function agregar_titular(){
        $conexion = new Conexion();
        $query = "INSERT INTO titular_vehiculo (vehiculos_idvehiculos, Personas_idpersonas) VALUES ('$this->vehiculos_idvehiculos', '$this->Personas_idpersonas')";
        $this->idtitular_vehiculo = $conexion->insertar($query);
        return $this->idtitular_vehiculo;
    }

    public function traer_titular(){
        $conexion = new Conexion();
        $query = "SELECT * FROM titular_vehiculo";
        return $conexion->consultar($query);
    }

    public function traer_titular_por_id($idpersonas){
        $conexion = new Conexion();
        $query = "SELECT * FROM titular_vehiculo WHERE Personas_idpersonas = $idpersonas";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }


    /**
     * Get the value of idtitular_vehiculo
     */ 
    public function getIdtitular_vehiculo()
    {
        return $this->idtitular_vehiculo;
    }

    /**
     * Set the value of idtitular_vehiculo
     *
     * @return  self
     */ 
    public function setIdtitular_vehiculo($idtitular_vehiculo)
    {
        $this->idtitular_vehiculo = $idtitular_vehiculo;

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

    /**
     * Get the value of Personas_idPersonas
     */ 
    public function getPersonas_idpersonas()
    {
        return $this->Personas_idpersonas;
    }

    /**
     * Set the value of Personas_idPersonas
     *
     * @return  self
     */ 
    public function setPersonas_idpersonas($Personas_idpersonas)
    {
        $this->Personas_idpersonas = $Personas_idpersonas;

        return $this;
    }
}