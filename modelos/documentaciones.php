<?php 

require_once('conexion.php');

class Documentacion{
    private $idDocumentaciones;
    private $URL_descripcion;
    private $vehiculos_idvehiculos;

    public function __construct($idDocumentaciones='', $URL_descripcion='', $vehiculos_idvehiculos='') {
        $this->idDocumentaciones = $idDocumentaciones;
        $this->URL_descripcion = $URL_descripcion;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
    }

    public function agregar_img_doc(){
        $conexion = new Conexion();
        $query = "INSERT INTO Documentaciones (URL_descripcion, vehiculos_idvehiculos) VALUES ('$this->URL_descripcion', '$this->vehiculos_idvehiculos')";
        return $conexion->insertar($query);
    }

    public function mostrar_img_doc_vehiculos($vehiculos_idvehiculos){
        $conexion = new Conexion();
        $query = "SELECT * FROM Documentaciones  WHERE vehiculos_idvehiculos = $vehiculos_idvehiculos";
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

    public function eliminar_img($vehiculos_idvehiculos){

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
}


?>