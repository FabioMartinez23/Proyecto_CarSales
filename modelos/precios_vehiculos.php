<?php

require_once('conexion.php');
require_once('paginacion.php');

class PrecioVehiculo extends Paginacion{
    private $idprecios_vehiculos;
    private $precio;
    private $fecha_precio;
    private $vehiculos_idvehiculos;
    private $tipo_precios_idtipo_precios;
    private $intereses_idintereses;


    public function __construct($idprecios_vehiculos='', $precio='', $fecha_precio='', $vehiculos_idvehiculos='', $tipo_precios_idtipo_precios='', $intereses_idintereses='') {
        $this->idprecios_vehiculos = $idprecios_vehiculos;
        $this->precio = $precio;
        $this->fecha_precio = $fecha_precio;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
        $this->tipo_precios_idtipo_precios = $tipo_precios_idtipo_precios;
        $this->intereses_idintereses = $intereses_idintereses;
    }

    public function actualizar_precio() {
        $conexion = new Conexion;

        // 1️⃣ Desactivar el precio anterior
        $query1 = "UPDATE precios_vehiculos 
                SET activo_precio = 0 
                WHERE vehiculos_idvehiculos = '$this->vehiculos_idvehiculos' 
                AND activo_precio = 1";
        $conexion->insertar($query1);

        // 2️⃣ Preparar el valor del interés (puede ser NULL)
        $interesValue = !empty($this->intereses_idintereses) ? "'$this->intereses_idintereses'" : "NULL";

        // 3️⃣ Insertar el nuevo precio
        $query2 = "INSERT INTO precios_vehiculos 
                (precio, fecha_precio, vehiculos_idvehiculos, activo_precio, tipo_precios_idtipo_precios, intereses_idintereses)
                VALUES ('$this->precio', NOW(), '$this->vehiculos_idvehiculos', 1, '$this->tipo_precios_idtipo_precios', $interesValue)";
        
        return $conexion->insertar($query2);
    }


    public function modificar_precio(){
        $conexion = new Conexion();
        $query = "UPDATE precios_vehiculos SET precio = '$this->precio', tipo_precios_idtipo_precios = '$this->tipo_precios_idtipo_precios', intereses_idintereses = '$this->intereses_idintereses' WHERE vehiculos_idvehiculos = '$this->vehiculos_idvehiculos'";
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

    public function traer_los_vehiculos_con_precio($inicio, $cantidad, $estado = null){
        $conexion = new Conexion();

        $query = "
            SELECT 
                vehiculos.*, 
                marcas.nombre AS nombre_marca, 
                modelos.nombre AS nombre_modelo,
                tipo_vehiculos.nombre AS nombre_tipo, 
                colores.descripcion AS nombre_color,

                -- Precio tomado (última fecha)
                precios_tomado.precio AS precio_tomado,
                precios_tomado.fecha_precio AS fecha_tomado,

                -- Precio público (última fecha)
                precios_publico.precio AS precio_publico,
                precios_publico.fecha_precio AS fecha_publico,

                -- Interés aplicado al precio público
                intereses_publico.descripcion AS descripcion_interes_publico,
                intereses_publico.porcentaje AS porcentaje_interes_publico

            FROM vehiculos
            INNER JOIN modelos 
                ON vehiculos.modelos_idmodelos = modelos.idmodelos
            INNER JOIN marcas 
                ON modelos.marcas_idmarcas = marcas.idmarcas
            INNER JOIN tipo_vehiculos 
                ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos
            INNER JOIN colores 
                ON vehiculos.colores_idcolores = colores.idcolores

            -- JOIN para precio TOMADO (último)
            LEFT JOIN precios_vehiculos AS precios_tomado 
                ON precios_tomado.vehiculos_idvehiculos = vehiculos.idvehiculos
                AND precios_tomado.tipo_precios_idtipo_precios = (
                    SELECT idtipo_precios 
                    FROM tipo_precios 
                    WHERE descripcion = 'tomado' 
                    LIMIT 1
                )
                AND precios_tomado.fecha_precio = (
                    SELECT MAX(p1.fecha_precio)
                    FROM precios_vehiculos p1
                    INNER JOIN tipo_precios tp1 
                        ON p1.tipo_precios_idtipo_precios = tp1.idtipo_precios
                    WHERE p1.vehiculos_idvehiculos = vehiculos.idvehiculos
                    AND tp1.descripcion = 'tomado'
                )

            -- JOIN para precio PÚBLICO (último)
            LEFT JOIN precios_vehiculos AS precios_publico 
                ON precios_publico.vehiculos_idvehiculos = vehiculos.idvehiculos
                AND precios_publico.tipo_precios_idtipo_precios = (
                    SELECT idtipo_precios 
                    FROM tipo_precios 
                    WHERE descripcion = 'publico' 
                    LIMIT 1
                )
                AND precios_publico.fecha_precio = (
                    SELECT MAX(p2.fecha_precio)
                    FROM precios_vehiculos p2
                    INNER JOIN tipo_precios tp2 
                        ON p2.tipo_precios_idtipo_precios = tp2.idtipo_precios
                    WHERE p2.vehiculos_idvehiculos = vehiculos.idvehiculos
                    AND tp2.descripcion = 'publico'
                )

            -- 🔹 Relación con tabla de intereses (solo para el precio público)
            LEFT JOIN intereses AS intereses_publico 
                ON precios_publico.intereses_idintereses = intereses_publico.idintereses

            WHERE vehiculos.activo_vehiculo = 1
        ";

        if ($estado) {
            $query .= " AND vehiculos.estado_vehiculo_idestado_vehiculo = $estado";
        }

        $query .= " ORDER BY vehiculos.idvehiculos LIMIT $inicio, $cantidad";

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

    public function traer_los_vehiculos_con_precio_filtrado($filtros, $inicio, $cantidad, $estado = null) {
        $conexion = new Conexion();

        $query = "
            SELECT 
                vehiculos.*, 
                marcas.nombre AS nombre_marca, 
                modelos.nombre AS nombre_modelo,
                tipo_vehiculos.nombre AS nombre_tipo, 
                colores.descripcion AS nombre_color,

                -- Precio tomado (última fecha)
                precios_tomado.precio AS precio_tomado,
                precios_tomado.fecha_precio AS fecha_tomado,

                -- Precio público (última fecha)
                precios_publico.precio AS precio_publico,
                precios_publico.fecha_precio AS fecha_publico,

                -- Interés aplicado al precio público
                intereses_publico.descripcion AS descripcion_interes_publico,
                intereses_publico.porcentaje AS porcentaje_interes_publico

            FROM vehiculos
            INNER JOIN modelos 
                ON vehiculos.modelos_idmodelos = modelos.idmodelos
            INNER JOIN marcas 
                ON modelos.marcas_idmarcas = marcas.idmarcas
            INNER JOIN tipo_vehiculos 
                ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos
            INNER JOIN colores 
                ON vehiculos.colores_idcolores = colores.idcolores

            -- JOIN para precio TOMADO (último)
            LEFT JOIN precios_vehiculos AS precios_tomado 
                ON precios_tomado.vehiculos_idvehiculos = vehiculos.idvehiculos
                AND precios_tomado.tipo_precios_idtipo_precios = (
                    SELECT idtipo_precios 
                    FROM tipo_precios 
                    WHERE descripcion = 'tomado' 
                    LIMIT 1
                )
                AND precios_tomado.fecha_precio = (
                    SELECT MAX(p1.fecha_precio)
                    FROM precios_vehiculos p1
                    INNER JOIN tipo_precios tp1 
                        ON p1.tipo_precios_idtipo_precios = tp1.idtipo_precios
                    WHERE p1.vehiculos_idvehiculos = vehiculos.idvehiculos
                    AND tp1.descripcion = 'tomado'
                )

            -- JOIN para precio PÚBLICO (último)
            LEFT JOIN precios_vehiculos AS precios_publico 
                ON precios_publico.vehiculos_idvehiculos = vehiculos.idvehiculos
                AND precios_publico.tipo_precios_idtipo_precios = (
                    SELECT idtipo_precios 
                    FROM tipo_precios 
                    WHERE descripcion = 'publico' 
                    LIMIT 1
                )
                AND precios_publico.fecha_precio = (
                    SELECT MAX(p2.fecha_precio)
                    FROM precios_vehiculos p2
                    INNER JOIN tipo_precios tp2 
                        ON p2.tipo_precios_idtipo_precios = tp2.idtipo_precios
                    WHERE p2.vehiculos_idvehiculos = vehiculos.idvehiculos
                    AND tp2.descripcion = 'publico'
                )

            -- Relación con intereses (solo precio público)
            LEFT JOIN intereses AS intereses_publico 
                ON precios_publico.intereses_idintereses = intereses_publico.idintereses

            WHERE vehiculos.activo_vehiculo = 1
        ";

        // ---- Filtros dinámicos sin implode ----
        if (!empty($filtros['marca'])) {
            $query .= " AND marcas.nombre = '" . $filtros['marca'] . "'";
        }

        if (!empty($filtros['modelo'])) {
            $query .= " AND modelos.nombre = '" . $filtros['modelo'] . "'";
        }

        if (!empty($filtros['color'])) {
            $query .= " AND colores.descripcion = '" . $filtros['color'] . "'";
        }

        if (!empty($filtros['año'])) {
            $query .= " AND vehiculos.año = '" . $filtros['año'] . "'";
        }

        if (!empty($filtros['tipo'])) {
            $query .= " AND tipo_vehiculos.nombre = '" . $filtros['tipo'] . "'";
        }

        if ($estado) {
            $query .= " AND vehiculos.estado_vehiculo_idestado_vehiculo = $estado";
        }

        $query .= " ORDER BY vehiculos.idvehiculos LIMIT $inicio, $cantidad";

        return $conexion->consultar($query);
    }



    


    public function traer_los_vehiculos_con_precio_json($vehiculos_idvehiculos) {
        $conexion = new Conexion();

        $query = "
            SELECT 
                vehiculos.*, 
                marcas.nombre AS nombre_marca, 
                modelos.nombre AS nombre_modelo,
                tipo_vehiculos.nombre AS nombre_tipo, 
                colores.descripcion AS nombre_color,

                -- Precio tomado (última fecha)
                precios_tomado.precio AS precio_tomado,
                precios_tomado.fecha_precio AS fecha_tomado,

                -- Precio público (última fecha)
                precios_publico.precio AS precio_publico,
                precios_publico.fecha_precio AS fecha_publico,

                -- Interés aplicado al precio público
                intereses_publico.descripcion AS descripcion_interes_publico,
                intereses_publico.porcentaje AS porcentaje_interes_publico

            FROM vehiculos
            INNER JOIN modelos 
                ON vehiculos.modelos_idmodelos = modelos.idmodelos
            INNER JOIN marcas 
                ON modelos.marcas_idmarcas = marcas.idmarcas
            INNER JOIN tipo_vehiculos 
                ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos
            INNER JOIN colores 
                ON vehiculos.colores_idcolores = colores.idcolores

            -- JOIN para precio TOMADO (último)
            LEFT JOIN precios_vehiculos AS precios_tomado 
                ON precios_tomado.vehiculos_idvehiculos = vehiculos.idvehiculos
                AND precios_tomado.tipo_precios_idtipo_precios = (
                    SELECT idtipo_precios 
                    FROM tipo_precios 
                    WHERE descripcion = 'tomado' 
                    LIMIT 1
                )
                AND precios_tomado.fecha_precio = (
                    SELECT MAX(p1.fecha_precio)
                    FROM precios_vehiculos p1
                    INNER JOIN tipo_precios tp1 
                        ON p1.tipo_precios_idtipo_precios = tp1.idtipo_precios
                    WHERE p1.vehiculos_idvehiculos = vehiculos.idvehiculos
                    AND tp1.descripcion = 'tomado'
                )

            -- JOIN para precio PÚBLICO (último)
            LEFT JOIN precios_vehiculos AS precios_publico 
                ON precios_publico.vehiculos_idvehiculos = vehiculos.idvehiculos
                AND precios_publico.tipo_precios_idtipo_precios = (
                    SELECT idtipo_precios 
                    FROM tipo_precios 
                    WHERE descripcion = 'publico' 
                    LIMIT 1
                )
                AND precios_publico.fecha_precio = (
                    SELECT MAX(p2.fecha_precio)
                    FROM precios_vehiculos p2
                    INNER JOIN tipo_precios tp2 
                        ON p2.tipo_precios_idtipo_precios = tp2.idtipo_precios
                    WHERE p2.vehiculos_idvehiculos = vehiculos.idvehiculos
                    AND tp2.descripcion = 'publico'
                )

            -- Relación con tabla de intereses (solo para el precio público)
            LEFT JOIN intereses AS intereses_publico 
                ON precios_publico.intereses_idintereses = intereses_publico.idintereses

            WHERE vehiculos.idvehiculos = $vehiculos_idvehiculos
            ORDER BY vehiculos.idvehiculos
        ";

        $resultado = $conexion->consultar($query);
        if ($resultado && $resultado->num_rows > 0) {
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

    /**
     * Get the value of tipo_precios_idtipo_precio
     */ 
    public function getTipo_precios_idtipo_precios()
    {
        return $this->tipo_precios_idtipo_precios;
    }

    /**
     * Set the value of tipo_precios_idtipo_precio
     *
     * @return  self
     */ 
    public function setTipo_precios_idtipo_precios($tipo_precios_idtipo_precios)
    {
        $this->tipo_precios_idtipo_precios = $tipo_precios_idtipo_precios;

        return $this;
    }

    /**
     * Get the value of intereses_idinteres
     */ 
    public function getIntereses_idintereses()
    {
        return $this->intereses_idintereses;
    }

    /**
     * Set the value of intereses_idinteres
     *
     * @return  self
     */ 
    public function setIntereses_idintereses($intereses_idintereses)
    {
        $this->intereses_idintereses = $intereses_idintereses;

        return $this;
    }
}


?>