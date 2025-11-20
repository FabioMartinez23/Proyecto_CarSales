<?php 

require_once('conexion.php');

class Documentacion{
    private $idDocumentaciones;
    private $URL_descripcion;
    private $estado_doc;
    private $digitalizado;
    private $fecha_registro;
    private $vehiculos_idvehiculos;
    private $tipo_documentacion_idtipo_documentacion;

    public function __construct($idDocumentaciones='', $URL_descripcion='', $estado_doc='', $digitalizado='', $fecha_registro='', $vehiculos_idvehiculos='', $tipo_documentacion_idtipo_documentacion='') {
        $this->idDocumentaciones = $idDocumentaciones;
        $this->URL_descripcion = $URL_descripcion;
        $this->estado_doc = $estado_doc;
        $this->digitalizado = $digitalizado;
        $this->fecha_registro = $fecha_registro;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
        $this->tipo_documentacion_idtipo_documentacion = $tipo_documentacion_idtipo_documentacion;
    }

    public function agregar_img_doc(){
        $conexion = new Conexion();
        $query = "INSERT INTO Documentaciones (URL_descripcion, estado_doc, digitalizado, vehiculos_idvehiculos, tipo_documentacion_idtipo_documentacion) VALUES ('$this->URL_descripcion', '$this->estado_doc', '$this->digitalizado', '$this->vehiculos_idvehiculos', '$this->tipo_documentacion_idtipo_documentacion')";
        return $conexion->insertar($query);
    }

    public function agregar_doc_fisica() {
        $conexion = new Conexion();
        
        // Inserta un registro de documentación física sin URL ni digitalizado
        $query = "INSERT INTO Documentaciones 
                    (URL_descripcion, vehiculos_idvehiculos, tipo_documentacion_idtipo_documentacion, estado_doc, digitalizado) 
                VALUES 
                    ('', '$this->vehiculos_idvehiculos', '$this->tipo_documentacion_idtipo_documentacion', '$this->estado_doc', 0)";
        
        return $conexion->insertar($query);
    }

    public function guardar_documentacion() {
        $conexion = new Conexion();

        // Valores limpios y seguros
        $url = $this->URL_descripcion ?? '';
        $estado = $this->estado_doc ?? 1;
        $digitalizado = $this->digitalizado ?? 1;
        $vehiculo = $this->vehiculos_idvehiculos ?? 0;
        $tipo = $this->tipo_documentacion_idtipo_documentacion ?? null;

        if (empty($vehiculo) || empty($tipo)) {
            return false; // No se puede guardar sin vehículo o tipo
        }

        $query = "
            INSERT INTO Documentaciones 
            (URL_descripcion, estado_doc, digitalizado, vehiculos_idvehiculos, tipo_documentacion_idtipo_documentacion) 
            VALUES ('$url', '$estado', '$digitalizado', '$vehiculo', '$tipo')
        ";

        return $conexion->insertar($query);
    }

    public function contar_no_digitalizados($vehiculo_id) {
        $conexion = new Conexion();
        $query = "SELECT COUNT(*) AS total 
                FROM documentaciones 
                WHERE vehiculos_idvehiculos = '$vehiculo_id'
                AND digitalizado = 0";

        $resultado = $conexion->consultar($query);

        // Si es un objeto mysqli_result, convertimos correctamente
        if ($resultado instanceof mysqli_result) {
            $fila = $resultado->fetch_assoc();
            return $fila ? (int)$fila['total'] : 0;
        }

        // Si ya devuelve array (según tu configuración), lo tratamos como tal
        if (is_array($resultado) && !empty($resultado)) {
            return (int)$resultado[0]['total'];
        }

        return 0;
    }

    public function mostrar_img_doc_vehiculos($vehiculos_idvehiculos){
        $conexion = new Conexion();
        $query = "SELECT * FROM Documentaciones INNER JOIN tipo_documentacion ON Documentaciones.tipo_documentacion_idtipo_documentacion = tipo_documentacion.idtipo_documentacion WHERE vehiculos_idvehiculos = $vehiculos_idvehiculos";
        return $conexion->consultar($query);
    }

    public function mostrar_img_vehiculos($vehiculos_idvehiculos){
        $conexion = new Conexion();
        $query = "SELECT * FROM Documentaciones 
                WHERE vehiculos_idvehiculos = $vehiculos_idvehiculos 
                AND (URL_descripcion LIKE '%.jpg' OR URL_descripcion LIKE '%.jpeg' OR URL_descripcion LIKE '%.png')";
        return $conexion->consultar($query);
    }


    function obtenerImagenAleatoria($idvehiculos) {
        $conexion = new Conexion();
        $query = "SELECT URL_descripcion FROM Documentaciones WHERE vehiculos_idvehiculos = $idvehiculos 
                AND (URL_descripcion LIKE '%.jpg' OR URL_descripcion LIKE '%.png') ORDER BY RAND() LIMIT 1";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null; 
        
    }

    public function traer_doc_vehiculos($vehiculos_idvehiculos){
        $conexion = new Conexion();
        $query = "SELECT * FROM Documentaciones INNER JOIN tipo_documentacion ON Documentaciones.tipo_documentacion_idtipo_documentacion = tipo_documentacion.idtipo_documentacion WHERE vehiculos_idvehiculos = $vehiculos_idvehiculos";
        return $conexion->consultar($query);
    }

    public function eliminar_img_doc(){
        $conexion = new Conexion();
        $query = "DELETE FROM Documentaciones WHERE idDocumentaciones = '$this->idDocumentaciones'";
        return $conexion->actualizar($query); // O ejecutar($query)
    }

    public function traer_doc_completa_vehiculo($vehiculos_idvehiculos) {
        $conexion = new Conexion();
        $query = "
            SELECT 
                td.idtipo_documentacion,
                td.descripcion AS tipo_doc,

                d.idDocumentaciones,
                d.estado_doc,
                d.URL_descripcion,
                d.digitalizado

            FROM tipo_documentacion td

            LEFT JOIN Documentaciones d 
                ON d.tipo_documentacion_idtipo_documentacion = td.idtipo_documentacion
                AND d.vehiculos_idvehiculos = $vehiculos_idvehiculos

            WHERE td.descripcion != 'imagen'

            ORDER BY td.idtipo_documentacion ASC
        ";

        return $conexion->consultar($query);
    }

    public function traer_doc_por_vehiculo_tipo($vehiculo_id, $tipo_doc_id) {
        $conexion = new Conexion();
        $query = "SELECT * FROM Documentaciones 
                WHERE vehiculos_idvehiculos = '$vehiculo_id' 
                AND tipo_documentacion_idtipo_documentacion = '$tipo_doc_id'
                LIMIT 1";
        return $conexion->consultar($query);
    }

    public function actualizar_estado() {
        $conexion = new Conexion();
        
        // Solo actualiza el estado_doc, URL_descripcion queda vacía, digitalizado = 0
        $query = "UPDATE Documentaciones SET 
                    estado_doc = '$this->estado_doc',
                    URL_descripcion = '$this->URL_descripcion',
                    digitalizado = '$this->digitalizado'
                WHERE idDocumentaciones = '$this->idDocumentaciones'";
        $resultado = $conexion->insertar($query);
        return $resultado;
    }

    public function traer_todos_los_docs() {
        $conexion = new Conexion();
        $query = "SELECT * FROM Documentaciones INNER JOIN tipo_documentacion ON Documentaciones.tipo_documentacion_idtipo_documentacion = tipo_documentacion.idtipo_documentacion INNER JOIN vehiculos ON Documentaciones.vehiculos_idvehiculos = vehiculos.idvehiculos";
        return $conexion->consultar($query);
    }

    /**
     * Get the value of idDocumentaciones
     */ 
    public function getIdDocumentaciones()
    {
        return $this->idDocumentaciones;
    }

    /**
     * Set the value of idDocumentaciones
     *
     * @return  self
     */ 
    public function setIdDocumentaciones($idDocumentaciones)
    {
        $this->idDocumentaciones = $idDocumentaciones;

        return $this;
    }

    /**
     * Get the value of URL_descripcion
     */ 
    public function getURL_descripcion()
    {
        return $this->URL_descripcion;
    }

    /**
     * Set the value of URL_descripcion
     *
     * @return  self
     */ 
    public function setURL_descripcion($URL_descripcion)
    {
        $this->URL_descripcion = $URL_descripcion;

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
     * Get the value of tipo_documentacion_idtipo_documentacion
     */ 
    public function getTipo_documentacion_idtipo_documentacion()
    {
        return $this->tipo_documentacion_idtipo_documentacion;
    }

    /**
     * Set the value of tipo_documentacion_idtipo_documentacion
     *
     * @return  self
     */ 
    public function setTipo_documentacion_idtipo_documentacion($tipo_documentacion_idtipo_documentacion)
    {
        $this->tipo_documentacion_idtipo_documentacion = $tipo_documentacion_idtipo_documentacion;

        return $this;
    }

    /**
     * Get the value of estado_doc
     */ 
    public function getEstado_doc()
    {
        return $this->estado_doc;
    }

    /**
     * Set the value of estado_doc
     *
     * @return  self
     */ 
    public function setEstado_doc($estado_doc)
    {
        $this->estado_doc = $estado_doc;

        return $this;
    }

    /**
     * Get the value of digitalizado
     */ 
    public function getDigitalizado()
    {
        return $this->digitalizado;
    }

    /**
     * Set the value of digitalizado
     *
     * @return  self
     */ 
    public function setDigitalizado($digitalizado)
    {
        $this->digitalizado = $digitalizado;

        return $this;
    }

    /**
     * Get the value of fecha_registro
     */ 
    public function getFecha_registro()
    {
        return $this->fecha_registro;
    }

    /**
     * Set the value of fecha_registro
     *
     * @return  self
     */ 
    public function setFecha_registro($fecha_registro)
    {
        $this->fecha_registro = $fecha_registro;

        return $this;
    }
}


?>