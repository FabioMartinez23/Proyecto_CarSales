<?php

class Documento{
    private $iddocumentos;
    private $valor;
    private $Tipo_documento_idTipo_documento;
    private $Personas_idPersonas;

    public function __construct($iddocumentos='', $valor='', $Tipo_documento_idTipo_documento='', $Personas_idPersonas='') {
        $this->iddocumentos = $iddocumentos;
        $this->valor = $valor;
        $this->Tipo_documento_idTipo_documento = $Tipo_documento_idTipo_documento;
        $this->Personas_idPersonas = $Personas_idPersonas;
    }

    public function agregar_documento(){
        $conexion = new Conexion();
        $query = "INSERT INTO documentos (valor,Tipo_documento_idTipo_documento,Personas_idPersonas) VALUES ('$this->valor', '$this->Tipo_documento_idTipo_documento', '$this->Personas_idPersonas')";
        return $conexion->insertar($query);
    }

    public function modificar_documento(){
        $conexion = new Conexion();
        $query = "UPDATE documentos SET valor = '$this->valor', Tipo_documento_idTipo_documento = '$this->Tipo_documento_idTipo_documento' WHERE iddocumentos = '$this->iddocumentos'";
        return $conexion->actualizar($query);
    }

    public function eliminar_documento(){
        $conexion = new Conexion();
        $query = "DELETE FROM documento WHERE iddocumentos  = '$this->iddocumentos'";
        return $conexion->insertar($query);
    }

    public function traer_documento_por_id($Personas_idPersonas){
        $conexion = new Conexion();
        $query = "SELECT * FROM documentos INNER JOIN Tipo_documento on documentos.Tipo_documento_idTipo_documento = Tipo_documento.idTipo_documento WHERE Personas_idPersonas = $Personas_idPersonas";
        return $conexion->consultar($query);
    }

    public function traer_documento_por_id_json($Personas_idPersonas){
        $conexion = new Conexion();
        $query = "SELECT * FROM documentos INNER JOIN Tipo_documento on documentos.Tipo_documento_idTipo_documento = Tipo_documento.idTipo_documento WHERE Personas_idPersonas = $Personas_idPersonas";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null; 
    }

    /**
     * Get the value of iddocumentos
     */ 
    public function getIddocumentos()
    {
        return $this->iddocumentos;
    }

    /**
     * Set the value of iddocumentos
     *
     * @return  self
     */ 
    public function setIddocumentos($iddocumentos)
    {
        $this->iddocumentos = $iddocumentos;

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
     * Get the value of Tipo_documento_idTipo_documento
     */ 
    public function getTipo_documento_idTipo_documento()
    {
        return $this->Tipo_documento_idTipo_documento;
    }

    /**
     * Set the value of Tipo_documento_idTipo_documento
     *
     * @return  self
     */ 
    public function setTipo_documento_idTipo_documento($Tipo_documento_idTipo_documento)
    {
        $this->Tipo_documento_idTipo_documento = $Tipo_documento_idTipo_documento;

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