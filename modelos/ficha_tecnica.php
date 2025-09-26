<?php 

require_once('conexion.php');

class Fichas_Tenicas{
    private $idficha_tecnica;
    private $vencimiento_bateria;
    private $vencimiento_service;
    private $vencimiento_RTO;
    private $form_08;
    private $form_12;
    private $titulo_vehiculo;
    private $seguro;
    private $municipalidad;
    private $cedula_vehiculo;
    private $form_13i;
    private $informe_dominio;
    private $prenda;
    private $vehiculos_idvehiculos;
    private $carroceria_idcarroceria;
    private $cristales_idcristales;
    private $neumaticos_idneumaticos;

    public function __construct($idficha_tecnica='', $vencimiento_bateria='', $vencimiento_service='', $vencimiento_RTO='', $form_08='', $form_12='', $titulo_vehiculo='', $seguro='', $municipalidad='', $cedula_vehiculo='', $form_13i='', $informe_dominio='', $prenda='', $vehiculos_idvehiculos='', $carroceria_idcarroceria='', $cristales_idcristales='', $neumaticos_idneumaticos='') {
        $this->idficha_tecnica = $idficha_tecnica;
        $this->vencimiento_bateria =$vencimiento_bateria;
        $this->vencimiento_service = $vencimiento_service;
        $this->vencimiento_RTO = $vencimiento_RTO;
        $this->form_08 = $form_08;
        $this->form_12 = $form_12;
        $this->titulo_vehiculo = $titulo_vehiculo;
        $this->seguro = $seguro;
        $this->municipalidad = $municipalidad;
        $this->cedula_vehiculo = $cedula_vehiculo;
        $this->form_13i = $form_13i;
        $this->informe_dominio = $informe_dominio;
        $this->prenda = $prenda;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
        $this->carroceria_idcarroceria = $carroceria_idcarroceria;
        $this->cristales_idcristales = $cristales_idcristales;
        $this->neumaticos_idneumaticos = $neumaticos_idneumaticos;
    }



    public function agregar_ficha_tecnica(){
        $conexion = new Conexion();
        $query = "INSERT INTO ficha_tecnica (vencimiento_bateria, vencimiento_service, vencimiento_RTO, form_08, form_12, titulo_vehiculo, seguro, municipalidad, cedula_vehiculo, form_13i, informe_dominio, prenda, vehiculos_idvehiculos, carroceria_idcarroceria, cristales_idcristales, neumaticos_idneumaticos) VALUES ('$this->vencimiento_bateria', '$this->vencimiento_service', '$this->vencimiento_RTO', '$this->form_08', '$this->form_12', '$this->titulo_vehiculo', '$this->seguro', '$this->municipalidad', '$this->cedula_vehiculo', '$this->form_13i', '$this->informe_dominio', '$this->prenda', '$this->vehiculos_idvehiculos', '$this->carroceria_idcarroceria', '$this->cristales_idcristales', '$this->neumaticos_idneumaticos')";
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
                    form_08 = '$this->form_08',
                    form_12 = '$this->form_12',
                    titulo_vehiculo = '$this->titulo_vehiculo',
                    cedula_vehiculo = '$this->cedula_vehiculo',
                    seguro = '$this->seguro',
                    municipalidad = '$this->municipalidad',
                    informe_dominio = '$this->informe_dominio',
                    form_13i = '$this->form_13i',
                    prenda = '$this->prenda'
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
     * Get the value of form_08
     */ 
    public function getForm_08()
    {
        return $this->form_08;
    }

    /**
     * Set the value of form_08
     *
     * @return  self
     */ 
    public function setForm_08($form_08)
    {
        $this->form_08 = $form_08;

        return $this;
    }

    /**
     * Get the value of form_12
     */ 
    public function getForm_12()
    {
        return $this->form_12;
    }

    /**
     * Set the value of form_12
     *
     * @return  self
     */ 
    public function setForm_12($form_12)
    {
        $this->form_12 = $form_12;

        return $this;
    }

    /**
     * Get the value of titulo_vehiculo
     */ 
    public function getTitulo_vehiculo()
    {
        return $this->titulo_vehiculo;
    }

    /**
     * Set the value of titulo_vehiculo
     *
     * @return  self
     */ 
    public function setTitulo_vehiculo($titulo_vehiculo)
    {
        $this->titulo_vehiculo = $titulo_vehiculo;

        return $this;
    }

    /**
     * Get the value of seguro
     */ 
    public function getSeguro()
    {
        return $this->seguro;
    }

    /**
     * Set the value of seguro
     *
     * @return  self
     */ 
    public function setSeguro($seguro)
    {
        $this->seguro = $seguro;

        return $this;
    }

    /**
     * Get the value of municipalidad
     */ 
    public function getMunicipalidad()
    {
        return $this->municipalidad;
    }

    /**
     * Set the value of municipalidad
     *
     * @return  self
     */ 
    public function setMunicipalidad($municipalidad)
    {
        $this->municipalidad = $municipalidad;

        return $this;
    }

    /**
     * Get the value of cedula_vehiculo
     */ 
    public function getCedula_vehiculo()
    {
        return $this->cedula_vehiculo;
    }

    /**
     * Set the value of cedula_vehiculo
     *
     * @return  self
     */ 
    public function setCedula_vehiculo($cedula_vehiculo)
    {
        $this->cedula_vehiculo = $cedula_vehiculo;

        return $this;
    }

    /**
     * Get the value of form_13i
     */ 
    public function getForm_13i()
    {
        return $this->form_13i;
    }

    /**
     * Set the value of form_13i
     *
     * @return  self
     */ 
    public function setForm_13i($form_13i)
    {
        $this->form_13i = $form_13i;

        return $this;
    }

    /**
     * Get the value of informe_dominio
     */ 
    public function getInforme_dominio()
    {
        return $this->informe_dominio;
    }

    /**
     * Set the value of informe_dominio
     *
     * @return  self
     */ 
    public function setInforme_dominio($informe_dominio)
    {
        $this->informe_dominio = $informe_dominio;

        return $this;
    }

    /**
     * Get the value of prenda
     */ 
    public function getPrenda()
    {
        return $this->prenda;
    }

    /**
     * Set the value of prenda
     *
     * @return  self
     */ 
    public function setPrenda($prenda)
    {
        $this->prenda = $prenda;

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