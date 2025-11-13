<?php

require_once('conexion.php');

class RegistroCliente{

    private $_con = null;              // 🔹 Conexión externa opcional
    private $usa_conexion_externa = false;

    private $idregistro_clientes;
    private $usuarios_idusuarios;

    // ===============================================
    // 🔹 CONSTRUCTOR — permite conexión compartida
    // ===============================================
    public function __construct($idregistro_clientes = '', $usuarios_idusuarios = '', $conn = null) {

        $this->idregistro_clientes = $idregistro_clientes;
        $this->usuarios_idusuarios = $usuarios_idusuarios;

        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->usa_conexion_externa = true;
        }
    }

    // ===============================================
    // 🔹 Obtener conexión (interna o externa)
    // ===============================================
    private function getConexion() {
        if ($this->usa_conexion_externa && $this->_con instanceof mysqli) {
            return $this->_con;
        }
        // Si no hay conexión externa → conexión normal
        $conexion = new Conexion();
        $conexion->conectar();
        return $conexion->_con;
    }

    private function cerrarConexion($con) {
        if (!$this->usa_conexion_externa && $con instanceof mysqli) {
            $con->close();
        }
    }

    // ===============================================
    // 🔹 INSERTAR CLIENTE
    // ===============================================
    public function agregar_cliente(){
        $con = $this->getConexion();
        $query = "INSERT INTO registro_clientes (usuarios_idusuarios)
                  VALUES ('$this->usuarios_idusuarios')";
        $con->query($query);
        $id = $con->insert_id;
        $this->cerrarConexion($con);
        return $id;
    }

    // ===============================================
    // 🔹 TODOS LOS CLIENTES
    // ===============================================
    public function traer_clientes(){
        $con = $this->getConexion();
        $query = "SELECT * FROM registro_clientes";
        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }

    // ===============================================
    // 🔹 TRAER CLIENTE POR ID
    // ===============================================
    public function traer_cliente_por_id($idusuarios){
        $con = $this->getConexion();
        $query = "SELECT * FROM registro_clientes WHERE Usuarios_idusuarios = $idusuarios";
        $res = $con->query($query);

        $fila = null;
        if ($res && $res->num_rows > 0) {
            $fila = $res->fetch_assoc();
        }

        $this->cerrarConexion($con);
        return $fila;
    }

    // ===============================================
    // GETTERS / SETTERS (sin cambios)
    // ===============================================
    public function getIdregistro_clientes(){ return $this->idregistro_clientes; }
    public function setIdregistro_clientes($id){ $this->idregistro_clientes = $id; return $this; }

    public function getUsuarios_idusuarios(){ return $this->usuarios_idusuarios; }
    public function setUsuarios_idusuarios($id){ $this->usuarios_idusuarios = $id; return $this; }
}
