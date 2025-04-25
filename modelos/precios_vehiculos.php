<?php

require_once('conexion.php');
require_once('paginacion.php');

class PrecioVehiculo extends Paginacion{
    private $idprecios_vehiculos;
    private $precio;
    private $fecha_precio;
    private $vehiculos_idvehiculos;

    public function __construct($idprecios_vehiculos='', $precio='', $fecha_precio='', $vehiculos_idvehiculos='') {
        $this->idprecios_vehiculos = $idprecios_vehiculos;
        $this->precio = $precio;
        $this->fecha_precio = $fecha_precio;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
    }

    public function actualizar_precio(){
        $conexion = new Conexion;
        $query = "INSERT INTO precios_vehiculos (precio, fecha_precio, vehiculos_idvehiculos) VALUES ('$this->precio', NOW(), '$this->vehiculos_idvehiculos')";
        return $conexion->insertar($query);
    }

    public function modificar_precio(){
        $conexion = new Conexion();
        $query = "UPDATE precios_vehiculos SET precio = '$this->precio' WHERE vehiculos_idvehiculos = '$this->vehiculos_idvehiculos'";
        return $conexion->actualizar($query);
    }

    public function eliminar_precio(){
        $conexion = new Conexion();
        $query = "UPDATE precios_vehiculos SET activo_precio = 0 WHERE vehiculos_idvehiculos = '$this->vehiculos_idvehiculos'";
        return $conexion->actualizar($query);
    }

    public function traer_los_precios(){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*, precios_vehiculos.precio, precios_vehiculos.fecha_precio, marcas.nombre AS nombre_marca, modelos.nombre AS nombre_modelo, tipo_vehiculos.nombre AS nombre_tipo FROM vehiculos LEFT JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos LEFT JOIN 
        (SELECT vehiculos_idvehiculos, MAX(fecha_precio) AS fecha_reciente FROM precios_vehiculos WHERE fecha_precio <= NOW() GROUP BY vehiculos_idvehiculos)
        AS ultimos ON precios_vehiculos.vehiculos_idvehiculos = ultimos.vehiculos_idvehiculos AND precios_vehiculos.fecha_precio = ultimos.fecha_reciente INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE activo_vehiculo = 1 LIMIT $this->pagina_actual,$this->paginacion";
        return $conexion->consultar($query);
    }

    public function traer_los_vehiculos_con_precio($inicio, $cantidad){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, precios_vehiculos.precio, colores.descripcion as nombre_color FROM vehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores LEFT JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos AND precios_vehiculos.fecha_precio = (
            SELECT MAX(fecha_precio)
            FROM precios_vehiculos AS p
            WHERE p.vehiculos_idvehiculos = vehiculos.idvehiculos
            AND p.fecha_precio <= NOW()) WHERE activo_vehiculo = 1
            ORDER BY vehiculos.idvehiculos LIMIT $inicio,$cantidad";
        return $conexion->consultar($query);
    }

    
    public function traer_vehiculos_con_precios(){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, precios_vehiculos.precio, colores.descripcion as nombre_color FROM vehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores LEFT JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos AND precios_vehiculos.fecha_precio = (
            SELECT MAX(fecha_precio)
            FROM precios_vehiculos AS p
            WHERE p.vehiculos_idvehiculos = vehiculos.idvehiculos
            AND p.fecha_precio <= NOW()) WHERE activo_vehiculo = 1
            ORDER BY vehiculos.idvehiculos";
        return $conexion->consultar($query);
    }


    public function traer_precio_por_id($vehiculos_idvehiculos){
        $conexion = new Conexion();
        $query = "SELECT * FROM precios_vehiculos WHERE vehiculos_idvehiculos = $vehiculos_idvehiculos";
        return $conexion->consultar($query);
    }

    public function buscar_vehiculo_precio($buscador){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, precios_vehiculos.precio, colores.descripcion as nombre_color FROM vehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores LEFT JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos AND precios_vehiculos.fecha_precio = (
            SELECT MAX(fecha_precio)
            FROM precios_vehiculos AS p
            WHERE p.vehiculos_idvehiculos = vehiculos.idvehiculos
            AND p.fecha_precio <= NOW()) WHERE 
        (patente LIKE '%$buscador%' OR marcas.nombre LIKE '%$buscador%' OR modelos.nombre LIKE '%$buscador%' OR anio LIKE '%$buscador%') AND activo_vehiculo = 1 ORDER BY vehiculos.idvehiculos";
        return $conexion->consultar($query);
    }

    public function traer_cantidad_vehiculo(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM vehiculos WHERE activo_vehiculo = 1";
        return $conexion->consultar($query);
    }

    public function traer_los_vehiculos_con_precio_filtrado($filtros, $inicio, $cantidad) {
        $conexion = new Conexion();
    
        $query = "SELECT vehiculos.*, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, precios_vehiculos.precio, colores.descripcion as nombre_color FROM vehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos
        INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos INNER JOIN colores ON vehiculos.colores_idcolores = colores.idcolores LEFT JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos AND precios_vehiculos.fecha_precio = (SELECT MAX(fecha_precio)FROM precios_vehiculos AS p WHERE p.vehiculos_idvehiculos = vehiculos.idvehiculos AND p.fecha_precio <= NOW())";
        
        // Agregar condiciones según los filtros aplicados
        $conditions = ["vehiculos.activo_vehiculo = 1"];
        
        if (!empty($filtros['marca'])) {
            $conditions[] = "marcas.nombre = '" . $filtros['marca'] . "'";
        }
        
        if (!empty($filtros['modelo'])) {
            $conditions[] = "modelos.nombre = '" . $filtros['modelo'] . "'";
        }
        
        if (!empty($filtros['color'])) {
            $conditions[] = "colores.descripcion = '" . $filtros['color'] . "'";
        }
        
        if (!empty($filtros['año'])) {
            $conditions[] = "vehiculos.año = '" . $filtros['año'] . "'";
        }
        
        if (!empty($filtros['tipo'])) {
            $conditions[] = "tipo_vehiculos.nombre = '" . $filtros['tipo'] . "'";
        }
    
        // Agregar condiciones de filtro si existen
        if ($conditions) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
    
        // Orden y paginación
        $query .= " ORDER BY vehiculos.idvehiculos";
    
        // Ejecutar la consulta
        return $conexion->consultar($query);
    }
    


    public function traer_los_vehiculos_con_precio_json($vehiculos_idvehiculos){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, precios_vehiculos.precio, colores.descripcion as nombre_color FROM vehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores LEFT JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos AND precios_vehiculos.fecha_precio = (
            SELECT MAX(fecha_precio)
            FROM precios_vehiculos AS p
            WHERE p.vehiculos_idvehiculos = vehiculos.idvehiculos
            AND p.fecha_precio <= NOW()) WHERE vehiculos_idvehiculos = $vehiculos_idvehiculos
            ORDER BY vehiculos.idvehiculos";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null; 
    }




    /**
     * Get the value of idprecios_vehiculos
     */ 
    public function getIdprecios_vehiculos()
    {
        return $this->idprecios_vehiculos;
    }

    /**
     * Set the value of idprecios_vehiculos
     *
     * @return  self
     */ 
    public function setIdprecios_vehiculos($idprecios_vehiculos)
    {
        $this->idprecios_vehiculos = $idprecios_vehiculos;

        return $this;
    }

    /**
     * Get the value of precio
     */ 
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set the value of precio
     *
     * @return  self
     */ 
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get the value of fecha_precio
     */ 
    public function getFecha_precio()
    {
        return $this->fecha_precio;
    }

    /**
     * Set the value of fecha_precio
     *
     * @return  self
     */ 
    public function setFecha_precio($fecha_precio)
    {
        $this->fecha_precio = $fecha_precio;

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