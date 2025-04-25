<?php

require_once('conexion.php');

class RegistroCliente{
    private $idregistro_clientes;
    private $usuarios_idusuarios;

    public function __construct($idregistro_clientes='', $usuarios_idusuarios='') {
        $this->idregistro_clientes = $idregistro_clientes;
        $this->usuarios_idusuarios = $usuarios_idusuarios;
    }

    public function agregar_cliente(){
        $conexion = new Conexion();
        $query = "INSERT INTO registro_clientes (usuarios_idusuarios) VALUES ('$this->usuarios_idusuarios')";
        return $conexion->insertar($query);
    }

    public function traer_clientes(){
        $conexion = new Conexion();
        $query = "SELECT * FROM registro_clientes";
        return $conexion->consultar($query);
    }

    public function traer_cliente_por_id($idusuarios){
        $conexion = new Conexion();
        $query = "SELECT * FROM registro_clientes WHERE Usuarios_idusuarios = $idusuarios";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }

    /**
     * Get the value of idregistro_clientes
     */ 
    public function getIdregistro_clientes()
    {
        return $this->idregistro_clientes;
    }

    /**
     * Set the value of idregistro_clientes
     *
     * @return  self
     */ 
    public function setIdregistro_clientes($idregistro_clientes)
    {
        $this->idregistro_clientes = $idregistro_clientes;

        return $this;
    }

    /**
     * Get the value of usuarios_idusuarios
     */ 
    public function getUsuarios_idusuarios()
    {
        return $this->usuarios_idusuarios;
    }

    /**
     * Set the value of usuarios_idusuarios
     *
     * @return  self
     */ 
    public function setUsuarios_idusuarios($usuarios_idusuarios)
    {
        $this->usuarios_idusuarios = $usuarios_idusuarios;

        return $this;
    }
}