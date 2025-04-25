<?php
require_once('conexion.php');
require_once('paginacion.php');

class Vehiculos extends Paginacion{
    private $idvehiculos;
    private $patente;
    private $chasis;
    private $motor;
    private $año;
    private $kilometraje;
    private $modelos_idmodelos;
    private $colores_idcolores;
    private $tipo_vehiculos_idtipo_vehiculos;

    public function __construct($idvehiculos='',$patente='',$chasis='',$motor='',$año='', $kilometraje = '', $modelos_idmodelos='',$colores_idcolores='', $tipo_vehiculos_idtipo_vehiculos='') {
        $this->idvehiculos = $idvehiculos;
        $this->patente = $patente;
        $this->chasis = $chasis;
        $this->motor = $motor;
        $this->año = $año;
        $this->kilometraje = $kilometraje;
        $this->modelos_idmodelos = $modelos_idmodelos;
        $this->colores_idcolores = $colores_idcolores;
        $this->tipo_vehiculos_idtipo_vehiculos = $tipo_vehiculos_idtipo_vehiculos;
    }

    public function agregar_vehiculo(){
        $conexion = new Conexion();
        $query = "INSERT INTO vehiculos (patente,chasis,motor,anio,kilometraje, fecha_alta, modelos_idmodelos,colores_idcolores, tipo_vehiculos_idtipo_vehiculos) VALUES ('$this->patente','$this->chasis','$this->motor','$this->año','$this->kilometraje', CURDATE(),'$this->modelos_idmodelos','$this->colores_idcolores', '$this->tipo_vehiculos_idtipo_vehiculos')";
        $this->idvehiculos = $conexion->insertar($query);
        return $this->idvehiculos;
    }

    public function actualizar_vehiculo(){
        $conexion = new Conexion();
        $query = "UPDATE vehiculos SET patente = '$this->patente', chasis = '$this->chasis', motor = '$this->motor', anio = '$this->año', kilometraje = '$this->kilometraje', modelos_idmodelos = '$this->modelos_idmodelos', colores_idcolores = '$this->colores_idcolores', tipo_vehiculos_idtipo_vehiculos = '$this->tipo_vehiculos_idtipo_vehiculos' WHERE idvehiculos = '$this->idvehiculos'";
        return $conexion->actualizar($query);
    }

    public function eliminar_vehiculo(){
        $conexion = new Conexion();
        $query = "UPDATE vehiculos SET activo_vehiculo = 0 WHERE idvehiculos = '$this->idvehiculos'";
        return $conexion->actualizar($query);
    }

    public function traer_cantidad_vehiculo(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM vehiculos WHERE activo_vehiculo = 1";
        return $conexion->consultar($query);
    }

    public function validar_patente(){
        $conexion = new Conexion();
        $query = "SELECT * FROM vehiculos WHERE patente = '$this->patente'";
        return $conexion->consultar($query);
    }

    public function traer_vehiculos($inicio, $cantidad){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*,colores.idcolores, colores.descripcion as nombre_color, marcas.idmarcas, marcas.nombre as nombre_marca,modelos.idmodelos,modelos.nombre as nombre_modelo,tipo_vehiculos.idtipo_vehiculos, tipo_vehiculos.nombre as nombre_tipo FROM vehiculos INNER JOIN modelos on vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE activo_vehiculo = 1 LIMIT $inicio,$cantidad";
        return $conexion->consultar($query);
    }

    public function traer_todos_vehiculos() {
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*,colores.idcolores, colores.descripcion as nombre_color, marcas.idmarcas, marcas.nombre as nombre_marca,modelos.idmodelos, modelos.nombre as nombre_modelo,tipo_vehiculos.idtipo_vehiculos, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM vehiculos INNER JOIN modelos on vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE activo_vehiculo = 1";
        return $conexion->consultar($query);
    }

    public function traer_vehiculos_por_id($idvehiculos){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*,colores.idcolores, colores.descripcion as nombre_color, marcas.idmarcas, marcas.nombre as nombre_marca,modelos.idmodelos, modelos.nombre as nombre_modelo,tipo_vehiculos.idtipo_vehiculos, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM vehiculos INNER JOIN modelos on vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE idvehiculos = '$idvehiculos'";
        return $conexion->consultar($query);
    }
    

    public function buscar_vehiculo($buscador){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, precios_vehiculos.precio, colores.descripcion as nombre_color FROM vehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores LEFT JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos AND precios_vehiculos.fecha_precio = (
            SELECT MAX(fecha_precio)
            FROM precios_vehiculos AS p
            WHERE p.vehiculos_idvehiculos = vehiculos.idvehiculos
            AND p.fecha_precio <= NOW()) WHERE 
        (patente LIKE '%$buscador%' OR marcas.nombre LIKE '%$buscador%' OR modelos.nombre LIKE '%$buscador%' OR anio LIKE '%$buscador%') AND activo_vehiculo = 1 ORDER BY vehiculos.idvehiculos";
        return $conexion->consultar($query);
    }

    public function traer_vehiculos_por_patente_json($patente){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*,colores.idcolores, colores.descripcion as nombre_color, marcas.idmarcas, marcas.nombre as nombre_marca,modelos.idmodelos, modelos.nombre as nombre_modelo,tipo_vehiculos.idtipo_vehiculos, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM vehiculos INNER JOIN modelos on vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE patente = '$patente'";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null; 
    }


    public function traer_vehiculos_filtrados($filtros, $inicio, $cantidad) {
        $query = "SELECT vehiculos.*,colores.idcolores, colores.descripcion as nombre_color, marcas.idmarcas, marcas.nombre as nombre_marca,modelos.idmodelos,modelos.nombre as nombre_modelo,tipo_vehiculos.idtipo_vehiculos, tipo_vehiculos.nombre as nombre_tipo FROM vehiculos INNER JOIN modelos on vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE 1=1"; // Base de la consulta
    
        // Agregar filtros dinámicamente
        if (!empty($filtros['marca'])) {
            $query .= " AND marcas.nombre = '" . $filtros['marca'] . "'";
        }
    
        if (!empty($filtros['modelo'])) {
            $query .= " AND modelos.nombre = '" . $filtros['modelo'] . "'";
        }
    
        if (!empty($filtros['color'])) {
            $query .= " AND colores.descripcion = '" . $filtros['color'] . "'";
        }
    
        if (!empty($filtros['anio'])) {
            $query .= " AND anio = '" . $filtros['anio'] . "'";
        }
    
        if (!empty($filtros['tipo'])) {
            $query .= " AND tipo_vehiculos.nombre = '" . $filtros['tipo'] . "'";
        }
    
        // Conectar y ejecutar la consulta
        $conexion = new Conexion();
        return $conexion->consultar($query);
    }

    public function traer_año_vehiculo(){
        $conexion = new Conexion();
        $query = "SELECT DISTINCT anio FROM vehiculos WHERE activo_vehiculo = 1 ORDER BY anio DESC";
        return $conexion->consultar($query);
    }
    

    /**
     * Get the value of idvehiculos
     */ 
    public function getIdvehiculos()
    {
        return $this->idvehiculos;
    }

    /**
     * Set the value of idvehiculos
     *
     * @return  self
     */ 
    public function setIdvehiculos($idvehiculos)
    {
        $this->idvehiculos = $idvehiculos;

        return $this;
    }

    /**
     * Get the value of patente
     */ 
    public function getPatente()
    {
        return $this->patente;
    }

    /**
     * Set the value of patente
     *
     * @return  self
     */ 
    public function setPatente($patente)
    {
        $this->patente = $patente;

        return $this;
    }

    /**
     * Get the value of chasis
     */ 
    public function getChasis()
    {
        return $this->chasis;
    }

    /**
     * Set the value of chasis
     *
     * @return  self
     */ 
    public function setChasis($chasis)
    {
        $this->chasis = $chasis;

        return $this;
    }

    /**
     * Get the value of motor
     */ 
    public function getMotor()
    {
        return $this->motor;
    }

    /**
     * Set the value of motor
     *
     * @return  self
     */ 
    public function setMotor($motor)
    {
        $this->motor = $motor;

        return $this;
    }

    /**
     * Get the value of año
     */ 
    public function getAño()
    {
        return $this->año;
    }

    /**
     * Set the value of año
     *
     * @return  self
     */ 
    public function setAño($año)
    {
        $this->año = $año;

        return $this;
    }

    /**
     * Get the value of kilometraje
     */ 
    public function getKilometraje()
    {
        return $this->kilometraje;
    }

    /**
     * Set the value of kilometraje
     *
     * @return  self
     */ 
    public function setKilometraje($kilometraje)
    {
        $this->kilometraje = $kilometraje;

        return $this;
    }

    /**
     * Get the value of modelos_idmodelos
     */ 
    public function getModelos_idmodelos()
    {
        return $this->modelos_idmodelos;
    }

    /**
     * Set the value of modelos_idmodelos
     *
     * @return  self
     */ 
    public function setModelos_idmodelos($modelos_idmodelos)
    {
        $this->modelos_idmodelos = $modelos_idmodelos;

        return $this;
    }

    /**
     * Get the value of colores_idcolores
     */ 
    public function getColores_idcolores()
    {
        return $this->colores_idcolores;
    }

    /**
     * Set the value of colores_idcolores
     *
     * @return  self
     */ 
    public function setColores_idcolores($colores_idcolores)
    {
        $this->colores_idcolores = $colores_idcolores;

        return $this;
    }

    /**
     * Get the value of tipo_vehiculos_idtipo_vehiculos
     */ 
    public function getTipo_vehiculos_idtipo_vehiculos()
    {
        return $this->tipo_vehiculos_idtipo_vehiculos;
    }

    /**
     * Set the value of tipo_vehiculos_idtipo_vehiculos
     *
     * @return  self
     */ 
    public function setTipo_vehiculos_idtipo_vehiculos($tipo_vehiculos_idtipo_vehiculos)
    {
        $this->tipo_vehiculos_idtipo_vehiculos = $tipo_vehiculos_idtipo_vehiculos;

        return $this;
    }
}