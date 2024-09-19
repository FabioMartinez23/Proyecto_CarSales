<?php

class Conexion{
    private $_con;
    private $servidor;
    private $usuario;
    private $password;
    private $base_datos;

    public function __construct() {
        $this->servidor = 'localhost';
        $this->usuario = 'root';
        $this->password = 'Fabio38096227';
        $this->base_datos = 'car_sales_2024';
    }

    public function conectar(){
        $this->_con = new mysqli($this->servidor, $this->usuario, $this->password, $this->base_datos); 
    }

    public function desconectar(){
        $this->_con->close();
    }

    public function consultar($query){
        $this->conectar();
        $resultado = $this->_con->query($query);
        $this->desconectar();
        return $resultado;
    }

    public function insertar($query){
        $this->conectar();
        $this->_con->query($query);
        $id = $this->_con->insert_id;
        $this->desconectar();
        return $id;
    }

    public function actualizar($query){
        $this->conectar();
        $resultado = $this->_con->query($query);
        $this->desconectar();
        return $resultado;
    }
}

?>