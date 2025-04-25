<?php

require_once('conexion.php');

class Domicilios{
    private $iddomicilios;
    private $descripcion;
    private $Personas_idPersonas;
    private $barrios_idbarrios;
    private $tipo_domicilio_idtipo_domicilio;

    public function __construct($iddomicilios='', $descripcion='', $Personas_idPersonas='', $barrios_idbarrios='', $tipo_domicilio_idtipo_domicilio='') {
        $this->iddomicilios = $iddomicilios;
        $this->descripcion = $descripcion;
        $this->Personas_idPersonas = $Personas_idPersonas;
        $this->barrios_idbarrios = $barrios_idbarrios;
        $this->tipo_domicilio_idtipo_domicilio = $tipo_domicilio_idtipo_domicilio;
    }

    public function agregar_domicilio(){
        $conexion = new Conexion();
        $query = "INSERT INTO domicilios (descripcion,Personas_idPersonas,barrios_idbarrios,tipo_domicilio_idtipo_domicilio) VALUES ('$this->descripcion', '$this->Personas_idPersonas', '$this->barrios_idbarrios', '$this->tipo_domicilio_idtipo_domicilio')";
        return $conexion->insertar($query);
    }

    public function modificar_domicilio(){
        $conexion = new Conexion();
        $query = "UPDATE domicilios SET descripcion = '$this->descripcion', barrios_idbarrios= '$this->barrios_idbarrios', barrios_idbarrios = '$this->barrios_idbarrios', tipo_domicilio_idtipo_domicilio= '$this->tipo_domicilio_idtipo_domicilio' WHERE iddomicilios = '$this->iddomicilios'";
        return $conexion->actualizar($query);
    }

    public function consultar_domicilio($Personas_idPersonas){
        $conexion = new Conexion();
        $query = "SELECT *, domicilios.descripcion as nombre_domicilio, tipo_domicilio.descripcion as nombre_tipo, barrios.descripcion as nombre_barrio, localidades.descripcion as nombre_localidad, provincias.descripcion as nombre_provincia, paises.descripcion as nombre_pais  FROM domicilios INNER JOIN barrios on domicilios.barrios_idbarrios = barrios.idbarrios INNER JOIN tipo_domicilio on domicilios.tipo_domicilio_idtipo_domicilio = tipo_domicilio.idtipo_domicilio INNER JOIN localidades on barrios.localidades_idlocalidades = localidades.idlocalidades INNER JOIN provincias on localidades.provincias_idprovincias = provincias.idprovincias INNER JOIN paises on provincias.paises_idpaises = paises.idpaises WHERE Personas_idPersonas = '$Personas_idPersonas'";
        return $conexion->consultar($query);
    }

    public function consultar_domicilio_json($Personas_idPersonas){
        $conexion = new Conexion();
        $query = "SELECT *, domicilios.descripcion as nombre_domicilio, tipo_domicilio.descripcion as nombre_tipo, barrios.descripcion as nombre_barrio, localidades.descripcion as nombre_localidad, provincias.descripcion as nombre_provincia, paises.descripcion as nombre_pais  FROM domicilios INNER JOIN barrios on domicilios.barrios_idbarrios = barrios.idbarrios INNER JOIN tipo_domicilio on domicilios.tipo_domicilio_idtipo_domicilio = tipo_domicilio.idtipo_domicilio INNER JOIN localidades on barrios.localidades_idlocalidades = localidades.idlocalidades INNER JOIN provincias on localidades.provincias_idprovincias = provincias.idprovincias INNER JOIN paises on provincias.paises_idpaises = paises.idpaises WHERE Personas_idPersonas = $Personas_idPersonas";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }


    /**
     * Get the value of iddomicilio
     */ 
    public function getIddomicilios()
    {
        return $this->iddomicilios;
    }

    /**
     * Set the value of iddomicilio
     *
     * @return  self
     */ 
    public function setIddomicilios($iddomicilios)
    {
        $this->iddomicilios = $iddomicilios;

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
     * Get the value of Personas_idPersonas
     */ 
    public function getPersonas_idPersonas()
    {
        return $this->Personas_idPersonas;
    }

    /**
     * Set the value of Personas_idPersonas
     *
     * @return  self
     */ 
    public function setPersonas_idPersonas($Personas_idPersonas)
    {
        $this->Personas_idPersonas = $Personas_idPersonas;

        return $this;
    }

    /**
     * Get the value of barrios_idbarrios
     */ 
    public function getBarrios_idbarrios()
    {
        return $this->barrios_idbarrios;
    }

    /**
     * Set the value of barrios_idbarrios
     *
     * @return  self
     */ 
    public function setBarrios_idbarrios($barrios_idbarrios)
    {
        $this->barrios_idbarrios = $barrios_idbarrios;

        return $this;
    }

    /**
     * Get the value of tipo_domicilio_idtipo_domicilio
     */ 
    public function getTipo_domicilio_idtipo_domicilio()
    {
        return $this->tipo_domicilio_idtipo_domicilio;
    }

    /**
     * Set the value of tipo_domicilio_idtipo_domicilio
     *
     * @return  self
     */ 
    public function setTipo_domicilio_idtipo_domicilio($tipo_domicilio_idtipo_domicilio)
    {
        $this->tipo_domicilio_idtipo_domicilio = $tipo_domicilio_idtipo_domicilio;

        return $this;
    }
}


?>