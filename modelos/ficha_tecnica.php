<?php 

require_once('conexion.php');

class Fichas_Tenicas{
    private $idficha_tecnica;
    private $vencimiento_bateria;
    private $vencimiento_service;
    private $vencimiento_RTO;
    private $vehiculos_idvehiculos;
    private $carroceria_idcarroceria;
    private $cristales_idcristales;
    private $neumaticos_idneumaticos;

    public function __construct($idficha_tecnica='', $vencimiento_bateria='', $vencimiento_service='', $vencimiento_RTO='', $vehiculos_idvehiculos='', $carroceria_idcarroceria='', $cristales_idcristales='', $neumaticos_idneumaticos='') {
        $this->idficha_tecnica = $idficha_tecnica;
        $this->vencimiento_bateria =$vencimiento_bateria;
        $this->vencimiento_service = $vencimiento_service;
        $this->vencimiento_RTO = $vencimiento_RTO;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
        $this->carroceria_idcarroceria = $carroceria_idcarroceria;
        $this->cristales_idcristales = $cristales_idcristales;
        $this->neumaticos_idneumaticos = $neumaticos_idneumaticos;
    }



    public function agregar_ficha_tecnica(){
        $conexion = new Conexion();
        $query = "INSERT INTO ficha_tecnica (vencimiento_bateria, vencimiento_service, vencimiento_RTO, vehiculos_idvehiculos, carroceria_idcarroceria, cristales_idcristales, neumaticos_idneumaticos) VALUES ('$this->vencimiento_bateria', '$this->vencimiento_service', '$this->vencimiento_RTO', '$this->vehiculos_idvehiculos', '$this->carroceria_idcarroceria', '$this->cristales_idcristales', '$this->neumaticos_idneumaticos')";
        return $conexion->insertar($query);
    }
    

    public function actualizar_ficha_tecnica() {
        $query = "UPDATE ficha_tecnica SET 
                    carroceria_idcarroceria = '$this->carroceria_idcarroceria',
                    neumaticos_idneumaticos = '$this->neumaticos_idneumaticos',
                    cristales_idcristales = '$this->cristales_idcristales',
                    vencimiento_rto = '$this->vencimiento_RTO',
                    vencimiento_bateria = '$this->vencimiento_bateria',
                    vencimiento_service = '$this->vencimiento_service',
                WHERE vehiculos_idvehiculos = '$this->vehiculos_idvehiculos'";
        
        $conexion = new Conexion();
        return $conexion->consultar($query);
    }

    public function traer_fichas_tecnicas(){
        $conexion = new Conexion();
        $query = "SELECT ficha_tecnica.*, vehiculos.*, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo FROM ficha_tecnica INNER JOIN vehiculos on ficha_tecnica.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN modelos on modelos.idmodelos = vehiculos.modelos_idmodelos INNER JOIN marcas on marcas.idmarcas = modelos.marcas_idmarcas";
        return $conexion->consultar($query);
    }


    public function traer_fichas_tecnica_id($vehiculos_idvehiculos){
        $conexion = new Conexion();
        $query = "SELECT ficha_tecnica.*, vehiculos.*, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo FROM ficha_tecnica INNER JOIN vehiculos on ficha_tecnica.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN modelos on modelos.idmodelos = vehiculos.modelos_idmodelos INNER JOIN marcas on marcas.idmarcas = modelos.marcas_idmarcas WHERE idvehiculos = $vehiculos_idvehiculos";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null; 
    }

    public function buscar_por_vehiculo($vehiculo_id) {
        $conexion = new Conexion();
        $query = "SELECT * FROM ficha_tecnica WHERE vehiculos_idvehiculos = $vehiculo_id LIMIT 1";
        $resultado = $conexion->consultar($query);
        return $resultado->num_rows > 0;
    }

    /**
     * Get the value of idficha_tecnica
     */ 
    public function getIdficha_tecnica()
    {
        return $this->idficha_tecnica;
    }

    /**
     * Set the value of idficha_tecnica
     *
     * @return  self
     */ 
    public function setIdficha_tecnica($idficha_tecnica)
    {
        $this->idficha_tecnica = $idficha_tecnica;

        return $this;
    }


    /**
     * Get the value of vencimiento_bateria
     */ 
    public function getVencimiento_bateria()
    {
        return $this->vencimiento_bateria;
    }

    /**
     * Set the value of vencimiento_bateria
     *
     * @return  self
     */ 
    public function setVencimiento_bateria($vencimiento_bateria)
    {
        $this->vencimiento_bateria = $vencimiento_bateria;

        return $this;
    }


    /**
     * Get the value of vencimiento_service
     */ 
    public function getVencimiento_service()
    {
        return $this->vencimiento_service;
    }

    /**
     * Set the value of vencimiento_service
     *
     * @return  self
     */ 
    public function setVencimiento_service($vencimiento_service)
    {
        $this->vencimiento_service = $vencimiento_service;

        return $this;
    }


    /**
     * Get the value of vencimiento_RTO
     */ 
    public function getVencimiento_RTO()
    {
        return $this->vencimiento_RTO;
    }

    /**
     * Set the value of vencimiento_RTO
     *
     * @return  self
     */ 
    public function setVencimiento_RTO($vencimiento_RTO)
    {
        $this->vencimiento_RTO = $vencimiento_RTO;

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
     * Get the value of carroceria_idcarroceria
     */ 
    public function getCarroceria_idcarroceria()
    {
        return $this->carroceria_idcarroceria;
    }

    /**
     * Set the value of carroceria_idcarroceria
     *
     * @return  self
     */ 
    public function setCarroceria_idcarroceria($carroceria_idcarroceria)
    {
        $this->carroceria_idcarroceria = $carroceria_idcarroceria;

        return $this;
    }

    /**
     * Get the value of cristales_idcristales
     */ 
    public function getCristales_idcristales()
    {
        return $this->cristales_idcristales;
    }

    /**
     * Set the value of cristales_idcristales
     *
     * @return  self
     */ 
    public function setCristales_idcristales($cristales_idcristales)
    {
        $this->cristales_idcristales = $cristales_idcristales;

        return $this;
    }

    /**
     * Get the value of neumaticos_idneumaticos
     */ 
    public function getNeumaticos_idneumaticos()
    {
        return $this->neumaticos_idneumaticos;
    }

    /**
     * Set the value of neumaticos_idneumaticos
     *
     * @return  self
     */ 
    public function setNeumaticos_idneumaticos($neumaticos_idneumaticos)
    {
        $this->neumaticos_idneumaticos = $neumaticos_idneumaticos;

        return $this;
    }
}

?>