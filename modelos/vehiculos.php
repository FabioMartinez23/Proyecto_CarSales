<?php
require_once('conexion.php');
require_once('paginacion.php');

class Vehiculos extends Paginacion{

    private $_con = null;              // 🔹 conexión compartida opcional
    private $conexion_externa = false; // 🔹 indica si viene desde transacción

    private $idvehiculos;
    private $patente;
    private $chasis;
    private $motor;
    private $año;
    private $kilometraje;
    private $modelos_idmodelos;
    private $colores_idcolores;
    private $tipo_vehiculos_idtipo_vehiculos;
    private $estado_vehiculo_idestado_vehiculo;

    // ===========================================================
    // CONSTRUCTOR — AGREGO $conn SOLO PARA LOS MÉTODOS NECESARIOS
    // ===========================================================
    public function __construct(
        $idvehiculos = '', $patente = '', $chasis = '', $motor = '',
        $año = '', $kilometraje = '', $modelos_idmodelos = '',
        $colores_idcolores = '', $tipo_vehiculos_idtipo_vehiculos = '',
        $estado_vehiculo_idestado_vehiculo = '', $conn = null
    ) {
        $this->idvehiculos = $idvehiculos;
        $this->patente = $patente;
        $this->chasis = $chasis;
        $this->motor = $motor;
        $this->año = $año;
        $this->kilometraje = $kilometraje;
        $this->modelos_idmodelos = $modelos_idmodelos;
        $this->colores_idcolores = $colores_idcolores;
        $this->tipo_vehiculos_idtipo_vehiculos = $tipo_vehiculos_idtipo_vehiculos;
        $this->estado_vehiculo_idestado_vehiculo = $estado_vehiculo_idestado_vehiculo;

        // 🔹 si me pasás una conexión (transacción), no abro otra
        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    // ===========================================================
    // MÉTODOS AUXILIARES — SOLO PARA LOS QUE TIENEN TRANSACCIÓN
    // ===========================================================
    private function getConexion() {
        if ($this->conexion_externa && $this->_con instanceof mysqli) {
            return $this->_con; // 🟩 usa la conexión de la venta
        }

        // 🟩 métodos normales: usan conexión estándar
        $conexion = new Conexion();
        $conexion->conectar();
        return $conexion->_con;
    }

    private function cerrarConexion($con) {
        if (!$this->conexion_externa && $con instanceof mysqli) {
            $con->close();
        }
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

    public function eliminar_vehiculo_venta() {
        $con = $this->getConexion();

        $query = "UPDATE vehiculos 
                SET activo_vehiculo = 0, 
                    estado_vehiculo_idestado_vehiculo = (
                        SELECT idestado_vehiculo 
                        FROM estado_vehiculo 
                        WHERE estado_vehiculo = 'vendido' 
                        LIMIT 1
                    )
                WHERE idvehiculos = '$this->idvehiculos'";

        $resultado = $con->query($query);

        $this->cerrarConexion($con);
        return $resultado;
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

    public function traer_vehiculo_por_id($idvehiculos)
    {
        $idvehiculos = intval($idvehiculos);

        $con = new Conexion();
        $con->conectar();         // ← SIN ESTO tu conexión está NULL
        $db = $con->_con; 

        $query = "
            SELECT 
                v.*,
                m.nombre AS nombre_marca,
                mo.nombre AS nombre_modelo,
                c.descripcion AS nombre_color,
                tv.nombre AS nombre_tipo_vehiculo,
                ev.estado_vehiculo AS estado,

                -- Precio tomado (último)
                (
                    SELECT pv.precio
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'tomado'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS precio_tomado,

                -- Fecha del precio tomado
                (
                    SELECT pv.fecha_precio
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'tomado'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS fecha_tomado,

                -- Precio público (último)
                (
                    SELECT pv.precio
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'publico'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS precio_publico,

                (
                    SELECT pv.fecha_precio
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'publico'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS fecha_publico

            FROM vehiculos v
            INNER JOIN modelos mo ON v.modelos_idmodelos = mo.idmodelos
            INNER JOIN marcas m ON mo.marcas_idmarcas = m.idmarcas
            INNER JOIN colores c ON v.colores_idcolores = c.idcolores
            INNER JOIN tipo_vehiculos tv ON v.tipo_vehiculos_idtipo_vehiculos = tv.idtipo_vehiculos
            LEFT JOIN estado_vehiculo ev ON v.estado_vehiculo_idestado_vehiculo = ev.idestado_vehiculo
            WHERE v.idvehiculos = $idvehiculos
            LIMIT 1
        ";

        $res = $db->query($query);

        return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    }

    public function traer_vehiculo_por_id_modificar($idvehiculos)
    {
        $idvehiculos = intval($idvehiculos);

        $conexion = new Conexion();

        $query = "
            SELECT 
                v.*,
                m.*,
                m.nombre AS nombre_marca,
                mo.nombre AS nombre_modelo,
                c.descripcion AS nombre_color,
                tv.nombre AS nombre_tipo_vehiculo,
                ev.estado_vehiculo AS estado,

                -- Precio tomado (último)
                (
                    SELECT pv.precio
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'tomado'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS precio_tomado,

                -- Fecha precio tomado
                (
                    SELECT pv.fecha_precio
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'tomado'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS fecha_tomado,

                -- Precio público (último)
                (
                    SELECT pv.precio
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'publico'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS precio_publico,

                -- Fecha precio público
                (
                    SELECT pv.fecha_precio
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'publico'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS fecha_publico

            FROM vehiculos v
            INNER JOIN modelos mo ON v.modelos_idmodelos = mo.idmodelos
            INNER JOIN marcas m ON mo.marcas_idmarcas = m.idmarcas
            INNER JOIN colores c ON v.colores_idcolores = c.idcolores
            INNER JOIN tipo_vehiculos tv ON v.tipo_vehiculos_idtipo_vehiculos = tv.idtipo_vehiculos
            LEFT JOIN estado_vehiculo ev ON v.estado_vehiculo_idestado_vehiculo = ev.idestado_vehiculo

            WHERE v.idvehiculos = $idvehiculos
            LIMIT 1
        ";

        $res = $conexion->consultar($query);

        return ($res && $res->num_rows > 0) 
            ? $res->fetch_assoc()
            : null;
    }


    public function buscar_vehiculo($buscador, $estado = null){
        $conexion = new Conexion();
        
        $query = "
        SELECT 
            vehiculos.*, 
            marcas.nombre AS nombre_marca, 
            modelos.nombre AS nombre_modelo,
            tipo_vehiculos.nombre AS nombre_tipo, 
            colores.descripcion AS nombre_color,
            estado_vehiculo.estado_vehiculo AS nombre_estado,

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
        LEFT JOIN estado_vehiculo
            ON vehiculos.estado_vehiculo_idestado_vehiculo = estado_vehiculo.idestado_vehiculo

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

        WHERE 
            (vehiculos.patente LIKE '%$buscador%' 
            OR marcas.nombre LIKE '%$buscador%' 
            OR modelos.nombre LIKE '%$buscador%' 
            OR vehiculos.anio LIKE '%$buscador%')
            AND vehiculos.activo_vehiculo = 1
    ";

        
        if ($estado) {
            $query .= " AND estado_vehiculo.estado_vehiculo = '$estado'";
        }
        
        $query .= " ORDER BY vehiculos.idvehiculos";
        
        return $conexion->consultar($query);
    }


    public function traer_vehiculos_por_patente_json($patente){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*,colores.idcolores, colores.descripcion as nombre_color, marcas.idmarcas, marcas.nombre as nombre_marca,modelos.idmodelos, modelos.nombre as nombre_modelo,tipo_vehiculos.idtipo_vehiculos, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM vehiculos INNER JOIN modelos on vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE patente = '$patente' AND activo_vehiculo = 1";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null; 
    }

    public function traer_vehiculo_por_patente_ventas($patente){
        $conexion = new Conexion();
        $query = "SELECT *, 
                        marcas.idmarcas, marcas.nombre AS nombre_marca,
                        modelos.idmodelos, modelos.nombre AS nombre_modelo,
                        tipo_vehiculos.idtipo_vehiculos, tipo_vehiculos.nombre AS nombre_tipo_vehiculo
                FROM vehiculos
                INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos
                INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas
                INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos
                INNER JOIN estado_vehiculo ON vehiculos.estado_vehiculo_idestado_vehiculo = estado_vehiculo.idestado_vehiculo
                INNER JOIN precios_vehiculos ON vehiculos.idvehiculos = precios_vehiculos.vehiculos_idvehiculos
                INNER JOIN tipo_precios ON precios_vehiculos.tipo_precios_idtipo_precios = tipo_precios.idtipo_precios
                INNER JOIN compras ON vehiculos.idvehiculos = compras.vehiculo_idvehiculo
                WHERE patente = '$patente' 
                AND activo_vehiculo = 1 
                AND activo_precio = 1
                AND tipo_precios.descripcion = 'publico'
                AND estado_vehiculo.estado_vehiculo = 'disponible'";

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
    
    public function traer_vehiculos_por_estado($idestado = null, $inicio = 0, $cantidad = 50) {
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*, 
                        colores.descripcion AS nombre_color, 
                        marcas.nombre AS nombre_marca, 
                        modelos.nombre AS nombre_modelo, 
                        tipo_vehiculos.nombre AS nombre_tipo,
                        estado_vehiculo.descripcion_estado AS nombre_estado
                FROM vehiculos
                INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos
                INNER JOIN colores ON vehiculos.colores_idcolores = colores.idcolores
                INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas
                INNER JOIN tipo_vehiculos ON vehiculos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos
                INNER JOIN estado_vehiculo ON vehiculos.estado_vehiculo_idestado_vehiculo = estado_vehiculo.idestado_vehiculo
                WHERE vehiculos.activo_vehiculo = 1";

        if (!empty($idestado)) {
            $query .= " AND vehiculos.estado_vehiculo_idestado_vehiculo = '$idestado'";
        }

        $query .= " ORDER BY vehiculos.idvehiculos DESC LIMIT $inicio, $cantidad";

        return $conexion->consultar($query);
    }

    public function contarVehiculosConFaltante() {
        $conexion = new Conexion();
        $query = "SELECT COUNT(DISTINCT v.idvehiculos) AS faltantes
                FROM vehiculos v
                LEFT JOIN estado_vehiculo e ON v.estado_vehiculo_idestado_vehiculo = e.idestado_vehiculo
                WHERE e.estado_vehiculo = 'falta_documento' 
                    OR e.estado_vehiculo IS NULL";
        
        $resultado = $conexion->consultar($query);
        $fila = $resultado->fetch_assoc();
        return $fila['faltantes'];
    }

    public function contarVehiculosDisponibles() {
        $conexion = new Conexion();
        $query = "SELECT COUNT(*) AS disponibles
                FROM vehiculos LEFT JOIN estado_vehiculo e ON vehiculos.estado_vehiculo_idestado_vehiculo = e.idestado_vehiculo
                WHERE e.estado_vehiculo = 'disponible' AND vehiculos.activo_vehiculo = 1";
        $resultado = $conexion->consultar($query);
        $fila = $resultado->fetch_assoc();
        return $fila['disponibles'];
    }

    public function contarVehiculosSinDigitar() {
        $conexion = new Conexion();
        $query = "SELECT COUNT(*) AS sin_digitar
                FROM vehiculos LEFT JOIN estado_vehiculo e ON vehiculos.estado_vehiculo_idestado_vehiculo = e.idestado_vehiculo
                WHERE e.estado_vehiculo = 'falta_digitalizacion' AND vehiculos.activo_vehiculo = 1";
        $resultado = $conexion->consultar($query);
        $fila = $resultado->fetch_assoc();
        return $fila['sin_digitar'];
    }

    public function actualizar_estado($vehiculos_idvehiculos, $estado_vehiculo_idestado_vehiculo){
        $con = $this->getConexion();

        $query = "UPDATE vehiculos 
                  SET estado_vehiculo_idestado_vehiculo = '$estado_vehiculo_idestado_vehiculo' 
                  WHERE idvehiculos = '$vehiculos_idvehiculos'";

        $resultado = $con->query($query);

        $this->cerrarConexion($con);
        return $resultado;
    }

    public function actualizar_disponible($idvehiculos, $estado_nombre) {
        $conexion = new Conexion();

        // Buscar ID del estado correspondiente
        $query = "SELECT idestado_vehiculo FROM estado_vehiculo WHERE estado_vehiculo = '$estado_nombre' LIMIT 1";
        $result = $conexion->consultar($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $idEstado = $row['idestado_vehiculo'];

            // Actualizar el estado del vehículo
            $update = "UPDATE vehiculos 
                    SET estado_vehiculo_idestado_vehiculo = '$idEstado'
                    WHERE idvehiculos = '$idvehiculos'";
            return $conexion->insertar($update);
        }
        return false;
    }


    /* RESTAURAR EL ESTADO DEL VEHICULO VENDIDO */

    public function restaurar_estado_disponible() {
        $con = $this->getConexion();
        $id = $this->idvehiculos;

        $query = "
            UPDATE vehiculos
            SET estado_vehiculo_idestado_vehiculo = 1
            WHERE idvehiculos = $id
        ";

        $ok = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $ok;
    }

    public function reporte_stock_envejecido($edad_minima = 0)
    {
        $conexion = new Conexion();

        // Nos aseguramos de que sea número
        $edad_minima = (int)$edad_minima;

        // Usamos fecha_alta como fecha de ingreso al stock
        $query = "
            SELECT 
                v.idvehiculos,
                v.patente,
                v.anio,
                m.nombre  AS marca,
                mo.nombre AS modelo,
                v.fecha_alta AS fecha_ingreso,
                DATEDIFF(CURDATE(), v.fecha_alta) AS dias_en_stock
            FROM vehiculos v
            INNER JOIN modelos mo 
                ON mo.idmodelos = v.modelos_idmodelos
            INNER JOIN marcas m 
                ON m.idmarcas = mo.marcas_idmarcas
            LEFT JOIN estado_vehiculo ev
                ON ev.idestado_vehiculo = v.estado_vehiculo_idestado_vehiculo
            WHERE 
                v.activo_vehiculo = 1
                -- si querés solo disponibles, descomentá esta línea:
                -- AND ev.estado_vehiculo = 'disponible'
            HAVING dias_en_stock >= $edad_minima
            ORDER BY dias_en_stock DESC
        ";

        return $conexion->consultar($query);
    }


    public function reporte_vehiculos_por_estado($solo_activos = true)
    {
        $conexion = new Conexion();

        // Filtro base
        $where = "WHERE 1=1";

        // Si querés solo activos, filtramos por activo_vehiculo
        if ($solo_activos) {
            $where .= " AND v.activo_vehiculo = 1";
        }

        $query = "
            SELECT 
                COALESCE(e.estado_vehiculo, 'Sin estado') AS estado,
                COUNT(*) AS cantidad,
                AVG(DATEDIFF(CURDATE(), v.fecha_alta)) AS dias_promedio
            FROM vehiculos v
            LEFT JOIN estado_vehiculo e 
                ON v.estado_vehiculo_idestado_vehiculo = e.idestado_vehiculo
            $where
            GROUP BY estado
            ORDER BY cantidad DESC
        ";

        return $conexion->consultar($query);
    }


    public function reporte_stock_estado()
    {
        $conexion = new Conexion();

        $query = "
            SELECT
                COALESCE(e.estado_vehiculo, 'Sin estado') AS estado,
                COUNT(*) AS cantidad
            FROM vehiculos v
            LEFT JOIN estado_vehiculo e
                ON v.estado_vehiculo_idestado_vehiculo = e.idestado_vehiculo
            WHERE v.activo_vehiculo = 1
            AND (
                    e.estado_vehiculo IN (
                        'disponible',
                        'falta_documento',
                        'falta_digitalizacion',
                        'taller'
                    )
                    OR e.estado_vehiculo IS NULL
            )
            GROUP BY estado
            ORDER BY cantidad DESC
        ";

        return $conexion->consultar($query);
    }


    // AUDITORIA //

    public function traer_por_id($idvehiculos)
    {
        $idvehiculos = intval($idvehiculos);

        $conexion = new Conexion();
        $query = "SELECT * FROM vehiculos WHERE idvehiculos = $idvehiculos LIMIT 1";

        $resultado = $conexion->consultar($query);

        if ($resultado && $resultado->num_rows > 0) {
            return $resultado->fetch_assoc(); // array asociativo con v.*
        }

        return null;
    }


    public function traer_vehiculos_disponibles_publico()
    {
        $conexion = new Conexion();

        $query = "
            SELECT 
                v.idvehiculos,
                v.patente,
                v.anio,
                m.nombre  AS marca,
                mo.nombre AS modelo,
                c.descripcion AS color,
                tv.nombre AS tipo_vehiculo,

                -- Precio público (último)
                precios_publico.precio       AS precio_publico,
                precios_publico.fecha_precio AS fecha_publico

            FROM vehiculos v
            INNER JOIN modelos mo 
                ON v.modelos_idmodelos = mo.idmodelos
            INNER JOIN marcas m 
                ON mo.marcas_idmarcas = m.idmarcas
            INNER JOIN colores c 
                ON v.colores_idcolores = c.idcolores
            INNER JOIN tipo_vehiculos tv 
                ON v.tipo_vehiculos_idtipo_vehiculos = tv.idtipo_vehiculos
            LEFT JOIN estado_vehiculo e
                ON v.estado_vehiculo_idestado_vehiculo = e.idestado_vehiculo

            -- JOIN para precio PÚBLICO (último)
            LEFT JOIN precios_vehiculos AS precios_publico 
                ON precios_publico.vehiculos_idvehiculos = v.idvehiculos
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
                    WHERE p2.vehiculos_idvehiculos = v.idvehiculos
                    AND tp2.descripcion = 'publico'
                )

            WHERE 
                v.activo_vehiculo = 1
                AND (e.estado_vehiculo = 'disponible' OR e.estado_vehiculo IS NULL)

            ORDER BY m.nombre, mo.nombre, v.anio DESC
        ";

        return $conexion->consultar($query);
    }


    public function marcar_reservado_credito()
    {
        $con = $this->getConexion();

        $id = (int)$this->idvehiculos;

        // 7 = Reservado Credito
        $sql = "
            UPDATE vehiculos
            SET 
                estado_vehiculo_idestado_vehiculo = 7,
                activo_vehiculo = 1
            WHERE idvehiculos = $id
        ";

        return $con->query($sql);
    }

    public function marcar_disponible()
    {
        $con = $this->getConexion();

        $id = (int)$this->idvehiculos;

        // 1 = disponible
        $sql = "
            UPDATE vehiculos
            SET 
                estado_vehiculo_idestado_vehiculo = 1,
                activo_vehiculo = 1
            WHERE idvehiculos = $id
        ";

        return $con->query($sql);
    }

            /**
         * Cambia el estado del vehículo a "taller"
         * usando el idestado_vehiculo correspondiente en la tabla estado_vehiculo.
         */
        public function enviar_a_taller()
        {
            $con = $this->getConexion();
            $id  = (int) $this->idvehiculos;

            // Buscar el ID del estado 'taller'
            $sqlEstado = "
                SELECT idestado_vehiculo 
                FROM estado_vehiculo 
                WHERE estado_vehiculo = 'taller'
                LIMIT 1
            ";
            $resEstado = $con->query($sqlEstado);

            if (!$resEstado || $resEstado->num_rows === 0) {
                $this->cerrarConexion($con);
                return false; // no se encontró el estado "taller"
            }

            $row      = $resEstado->fetch_assoc();
            $idEstado = (int) $row['idestado_vehiculo'];

            // Actualizar el vehículo
            $sql = "
                UPDATE vehiculos
                SET estado_vehiculo_idestado_vehiculo = $idEstado
                WHERE idvehiculos = $id
            ";

            $ok = $con->query($sql);

            $this->cerrarConexion($con);
            return $ok;
        }



            /**
         * Marca el estado del vehículo según un nombre lógico
         * ('disponible', 'falta_documento', 'falta_digitalizacion', 'taller', etc.)
         * buscando el idestado_vehiculo en la tabla estado_vehiculo.
         */
        public function marcar_estado_por_documentacion($estado_nombre)
        {
            $con = $this->getConexion();
            $id  = (int) $this->idvehiculos;

            // Sanitizo el nombre del estado
            $estado_nombre = $con->real_escape_string($estado_nombre);

            // Busco el ID del estado
            $sqlEstado = "
                SELECT idestado_vehiculo
                FROM estado_vehiculo
                WHERE estado_vehiculo = '$estado_nombre'
                LIMIT 1
            ";
            $resEstado = $con->query($sqlEstado);

            if (!$resEstado || $resEstado->num_rows === 0) {
                $this->cerrarConexion($con);
                return false; // estado no encontrado
            }

            $row      = $resEstado->fetch_assoc();
            $idEstado = (int) $row['idestado_vehiculo'];

            // Actualizo el vehículo
            $sql = "
                UPDATE vehiculos
                SET estado_vehiculo_idestado_vehiculo = $idEstado
                WHERE idvehiculos = $id
            ";

            $ok = $con->query($sql);

            $this->cerrarConexion($con);
            return $ok;
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

    /**
     * Get the value of estado_vehiculo_idestado_vehiculo
     */ 
    public function getEstado_vehiculo_idestado_vehiculo()
    {
        return $this->estado_vehiculo_idestado_vehiculo;
    }

    /**
     * Set the value of estado_vehiculo_idestado_vehiculo
     *
     * @return  self
     */ 
    public function setEstado_vehiculo_idestado_vehiculo($estado_vehiculo_idestado_vehiculo)
    {
        $this->estado_vehiculo_idestado_vehiculo = $estado_vehiculo_idestado_vehiculo;

        return $this;
    }
}