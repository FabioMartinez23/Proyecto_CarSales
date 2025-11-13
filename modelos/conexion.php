<?php
require_once(__DIR__ . '/../config/configuracion.php');

class Conexion {
    public $_con;
    private $servidor;
    private $usuario;
    private $password;
    private $base_datos;
    private $conexion_externa = false; // 🔹 Detecta si la conexión fue inyectada

    public function __construct($conn = null) {
        $this->servidor = DB_SERVIDOR;
        $this->usuario = DB_USUARIO;
        $this->password = DB_PASSWORD;
        $this->base_datos = DB_NOMBRE;

        // Si viene conexión externa → la utilizamos
        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    /* ======================================================
       CONECTAR
    ====================================================== */
    public function conectar() {

        // Si estás usando conexión externa, NO ABRIR otra
        if ($this->conexion_externa) {
            return;
        }

        if ($this->_con instanceof mysqli) {
            return; // ya conectada
        }

        $this->_con = new mysqli($this->servidor, $this->usuario, $this->password, $this->base_datos);
        $this->_con->set_charset("utf8mb4");

        if ($this->_con->connect_error) {
            if (DB_THROW_EXCEPTIONS) {
                throw new Exception("Error de conexión: " . $this->_con->connect_error);
            } else {
                die("Error de conexión: " . $this->_con->connect_error);
            }
        }
    }

    /* ======================================================
       DESCONECTAR
    ====================================================== */
    public function desconectar() {

        // ❗ No cerrar si la conexión viene desde una transacción externa
        if ($this->conexion_externa) {
            return;
        }

        if ($this->_con) {
            $this->_con->close();
            $this->_con = null;
        }
    }

    /* ======================================================
       CONSULTAR
    ====================================================== */
    public function consultar($query) {

        $this->conectar();
        $res = $this->_con->query($query);

        if ($res === false && DB_THROW_EXCEPTIONS) {
            throw new Exception("Error en CONSULTAR: " . $this->_con->error);
        }

        // ❗ SOLO si no es conexión externa, cerramos después
        if (!$this->conexion_externa) {
            // NO cerrar antes de usar el resultado — retornamos y luego se usará
            // PERO los mysqli_result funcionan incluso después del close.
            // Igual podemos dejarlo así.
            $this->desconectar();
        }

        return $res;
    }

    /* ======================================================
       INSERTAR
    ====================================================== */
    public function insertar($query) {

        $this->conectar();
        $res = $this->_con->query($query);

        if ($res === false) {
            if (DB_THROW_EXCEPTIONS) {
                throw new Exception("Error en INSERTAR: " . $this->_con->error);
            } else {
                echo "Error al ejecutar la consulta: " . $this->_con->error;
                if (!$this->conexion_externa) {
                    $this->desconectar();
                }
                return null;
            }
        }

        $id = $this->_con->insert_id;

        if (!$this->conexion_externa) {
            $this->desconectar();
        }

        return $id;
    }

    /* ======================================================
       ACTUALIZAR
    ====================================================== */
    public function actualizar($query) {

        $this->conectar();
        $res = $this->_con->query($query);

        if ($res === false && DB_THROW_EXCEPTIONS) {
            throw new Exception("Error en ACTUALIZAR: " . $this->_con->error);
        }

        if (!$this->conexion_externa) {
            $this->desconectar();
        }

        return $res;
    }
}
?>

