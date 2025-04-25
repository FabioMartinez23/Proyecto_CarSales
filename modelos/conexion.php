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
        $this->base_datos = 'car_sales_11_2024';
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

    public function insertar($query) {
        // Conectar a la base de datos
        $this->conectar();
    
        // Ejecutar la consulta
        if ($this->_con->query($query)) {
            // Obtener el ID de la última inserción
            $id = $this->_con->insert_id;
        } else {
            // Si ocurre un error en la consulta, lo mostramos
            echo "Error al ejecutar la consulta: " . $this->_con->error;
            $id = null; // No se pudo obtener un ID válido
        }
    
        // Desconectar de la base de datos
        $this->desconectar();
    
        // Devolver el ID obtenido (o null si ocurrió un error)
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