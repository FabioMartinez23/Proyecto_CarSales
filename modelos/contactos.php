<?php

require_once('conexion.php');

class Contacto{
    private $idcontactos;
    private $valor;
    private $tipo_contactos_idtipo_contactos;
    private $Personas_idPersonas;

    public function __construct($idcontactos='', $valor='', $tipo_contactos_idtipo_contactos='', $Personas_idPersonas='') {
        $this->idcontactos = $idcontactos;
        $this->valor = $valor;
        $this->tipo_contactos_idtipo_contactos = $tipo_contactos_idtipo_contactos;
        $this->Personas_idPersonas = $Personas_idPersonas;
    }

    public function agregar_contato(){
        $conexion = new Conexion();
        $query = "INSERT INTO contactos (valor,tipo_contactos_idtipo_contactos,Personas_idPersonas) VALUES ('$this->valor','$this->tipo_contactos_idtipo_contactos','$this->Personas_idPersonas')";
        return $conexion->insertar($query);
    }

    public function modificar_contacto(){
        $conexion = new Conexion();
        $query = "UPDATE contactos SET valor = '$this->valor', tipo_contactos_idtipo_contactos = '$this->tipo_contactos_idtipo_contactos' WHERE idcontactos = '$this->idcontactos';";
        return $conexion->actualizar($query);
    }

    public function consultar_contacto($Personas_idPersonas){
        $conexion = new Conexion();
        $query = "SELECT * FROM contactos INNER JOIN tipo_contacto on contactos.tipo_contactos_idtipo_contactos = tipo_contacto.idtipo_contacto WHERE Personas_idPersonas = '$Personas_idPersonas'";
        return $conexion->consultar($query);
    }

    public function consultar_contacto_json($Personas_idPersonas){
        $conexion = new Conexion();
        $query = "SELECT * FROM contactos INNER JOIN tipo_contacto on contactos.tipo_contactos_idtipo_contactos = tipo_contacto.idtipo_contacto WHERE Personas_idPersonas = $Personas_idPersonas";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null; 
    }
    


    /**
     * Get the value of idcontactos
     */ 
    public function getIdcontactos()
    {
        return $this->idcontactos;
    }

    /**
     * Set the value of idcontactos
     *
     * @return  self
     */ 
    public function setIdcontactos($idcontactos)
    {
        $this->idcontactos = $idcontactos;

        return $this;
    }

    /**
     * Get the value of valor
     */ 
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set the value of valor
     *
     * @return  self
     */ 
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get the value of tipo_contactos_idtipo_contactos
     */ 
    public function getTipo_contactos_idtipo_contactos()
    {
        return $this->tipo_contactos_idtipo_contactos;
    }

    /**
     * Set the value of tipo_contactos_idtipo_contactos
     *
     * @return  self
     */ 
    public function setTipo_contactos_idtipo_contactos($tipo_contactos_idtipo_contactos)
    {
        $this->tipo_contactos_idtipo_contactos = $tipo_contactos_idtipo_contactos;

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
}

?>