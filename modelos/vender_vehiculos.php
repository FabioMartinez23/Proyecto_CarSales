<?php

require_once('conexion.php');

class VenderVehiculo {
    private $_con;
    private $conexion_externa = false;

    private $idventas;
    private $descripcion;
    private $precio_venta;
    private $fecha_venta;
    private $tipo_pago_idtipo_pago;
    private $vehiculo_idvehiculo;
    private $registro_clientes_idregistro_clientes;
    private $empleados_idempleados;
    private $titular_vehiculo_idtitular_vehiculo;
    private $estado_venta;                // Realizada / Anulada / Pendiente crédito
    private $estado_venta_credito;        // ninguno / pendiente / aprobado / rechazado

    private $banco_credito;
    private $observacion_credito;
    private $fecha_solicitud_credito;
    private $fecha_respuesta_credito;
    private $monto_aprobado_credito;

    // ===========================================================
    // 🔹 CONSTRUCTOR — recibe conexión externa si viene de transacción
    // ===========================================================
    public function __construct(
        $idventas = '', $descripcion = '', $fecha_venta = '',
        $tipo_pago_idtipo_pago = '', $vehiculo_idvehiculo = '',
        $registro_clientes_idregistro_clientes = '',
        $empleados_idempleados = '', $titular_vehiculo_idtitular_vehiculo = '',
        $precio_venta = '',
        $conn = null
    ) {
        $this->idventas = $idventas;
        $this->descripcion = $descripcion;
        $this->fecha_venta = $fecha_venta;
        $this->tipo_pago_idtipo_pago = $tipo_pago_idtipo_pago;
        $this->vehiculo_idvehiculo = $vehiculo_idvehiculo;
        $this->registro_clientes_idregistro_clientes = $registro_clientes_idregistro_clientes;
        $this->empleados_idempleados = $empleados_idempleados;
        $this->titular_vehiculo_idtitular_vehiculo = $titular_vehiculo_idtitular_vehiculo;
        $this->precio_venta = $precio_venta;

        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    // ===========================================================
    // 🔹 Obtener conexión sin romper transacciones
    // ===========================================================
    private function getConexion() {
        if ($this->conexion_externa && $this->_con instanceof mysqli) {
            return $this->_con;
        }
        $c = new Conexion();
        $c->conectar();
        return $c->_con;
    }

    private function cerrarConexion($c) {
        if (!$this->conexion_externa && $c instanceof mysqli) {
            $c->close();
        }
    }

    // ===========================================================
    // MÉTODOS PRINCIPALES
    // ===========================================================
    public function agregar_venta() {
        $con = $this->getConexion();

        // Defaults para no romper nada si no se setean desde afuera
        $estado_venta         = $this->estado_venta ?: 'Realizada';
        $estado_venta_credito = $this->estado_venta_credito ?: 'ninguno';

        $descripcion  = $con->real_escape_string($this->descripcion);
        $precio_venta = floatval($this->precio_venta);

        // Campos de crédito opcionales (pueden ser NULL)
        $banco_credito = $this->banco_credito 
            ? "'" . $con->real_escape_string($this->banco_credito) . "'" 
            : "NULL";

        $observacion_credito = $this->observacion_credito 
            ? "'" . $con->real_escape_string($this->observacion_credito) . "'" 
            : "NULL";

        // Fecha solicitud (solo tendrá valor si la seteamos, ej. en crédito)
        $fecha_solicitud_credito = $this->fecha_solicitud_credito
            ? "'" . $con->real_escape_string($this->fecha_solicitud_credito) . "'"
            : "NULL";

        // Estos se completan después cuando el banco responda
        $fecha_respuesta_credito = $this->fecha_respuesta_credito
            ? "'" . $con->real_escape_string($this->fecha_respuesta_credito) . "'"
            : "NULL";

        $monto_aprobado_credito = ($this->monto_aprobado_credito !== null && $this->monto_aprobado_credito !== '')
            ? floatval($this->monto_aprobado_credito)
            : "NULL";

        $query = "
            INSERT INTO ventas 
            (
                descripcion,
                precio_venta,
                fecha_venta,
                tipo_pago_idtipo_pago,
                vehiculo_idvehiculo,
                registro_clientes_idregistro_clientes,
                empleados_idempleados,
                titular_vehiculo_idtitular_vehiculo,
                estado_venta,
                estado_venta_credito,
                banco_credito,
                observacion_credito,
                fecha_solicitud_credito,
                fecha_respuesta_credito,
                monto_aprobado_credito
            )
            VALUES (
                '$descripcion',
                '$precio_venta',
                NOW(),
                '$this->tipo_pago_idtipo_pago',
                '$this->vehiculo_idvehiculo',
                '$this->registro_clientes_idregistro_clientes',
                '$this->empleados_idempleados',
                '$this->titular_vehiculo_idtitular_vehiculo',
                '$estado_venta',
                '$estado_venta_credito',
                $banco_credito,
                $observacion_credito,
                $fecha_solicitud_credito,
                $fecha_respuesta_credito,
                $monto_aprobado_credito
            )
        ";

        $ok = $con->query($query);
        return $ok ? $con->insert_id : null;
    }



    public function traer_ventas() {
        $con = $this->getConexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas";
        return $con->query($query);
    }

    public function traer_venta_por_id($idventas) {
        $con = $this->getConexion();
        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, tipo_vehiculos.nombre as nombre_tipo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago, contactos.valor as valor_contacto, documentos.valor as valor_documento, domicilios.descripcion as nombre_domicilio, barrios.descripcion as nombre_barrio, localidades.descripcion as nombre_localidad, provincias.descripcion as nombre_provincia, colores.descripcion as nombre_descripcion FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos ON tipo_vehiculos.idtipo_vehiculos = vehiculos.tipo_vehiculos_idtipo_vehiculos INNER JOIN colores ON vehiculos.colores_idcolores = colores.idcolores INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas INNER JOIN contactos ON contactos.Personas_idPersonas = personas.idpersonas INNER JOIN documentos ON documentos.Personas_idPersonas = personas.idpersonas INNER JOIN domicilios ON domicilios.Personas_idPersonas = personas.idpersonas INNER JOIN barrios ON domicilios.barrios_idbarrios = barrios.idbarrios INNER JOIN localidades ON barrios.localidades_idlocalidades = localidades.idlocalidades INNER JOIN provincias ON localidades.provincias_idprovincias = provincias.idprovincias WHERE idventas = $idventas AND activo_precio = 1";

        $res = $con->query($query);
        return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    }

    public function traer_venta_para_anular($idventas) {
        $con = $this->getConexion();
        $query = "SELECT * FROM ventas WHERE idventas = $idventas";
        $res = $con->query($query);
        $fila = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
        $this->cerrarConexion($con);
        return $fila;
    }


public function buscar_ventas($buscador){
        $con = $this->getConexion();
        $query = " SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, ventas.descripcion as observacion, tipo_pago.descripcion as nombre_pago FROM ventas INNER JOIN tipo_pago ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago INNER JOIN vehiculos ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos INNER JOIN registro_clientes ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes INNER JOIN usuarios ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios INNER JOIN personas ON usuarios.personas_idpersonas = personas.idpersonas INNER JOIN tipo_precios ON precios_vehiculos.tipo_precios_idtipo_precios = tipo_precios.idtipo_precios WHERE activo_precio = 1 AND tipo_precios.descripcion = 'publico'  AND estado_venta = 'Realizada' AND patente LIKE '%$buscador%' OR marcas.nombre LIKE '%$buscador%' OR modelos.nombre LIKE '%$buscador%' OR anio LIKE '%$buscador%' OR personas.nombre LIKE '%$buscador%' OR personas.apellido LIKE '%$buscador%'";
        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }


    public function traer_ventas_paginacion($inicio, $cantidad)
    {
        $con = $this->getConexion();

        // Por seguridad, casteamos a int
        $inicio   = (int)$inicio;
        $cantidad = (int)$cantidad;

        $query = "
            SELECT 
                ventas.*, vehiculos.*,                               -- incluye estado_venta y estado_venta_credito
                marcas.nombre              AS nombre_marca,
                modelos.nombre             AS nombre_modelo,
                ventas.descripcion         AS observacion,
                tipo_pago.descripcion      AS nombre_pago,
                MAX(pv.precio)             AS precio_actual,
                personas.nombre,
                personas.apellido
            FROM ventas
            INNER JOIN tipo_pago 
                ON ventas.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago
            INNER JOIN vehiculos 
                ON ventas.vehiculo_idvehiculo = vehiculos.idvehiculos
            INNER JOIN modelos 
                ON vehiculos.modelos_idmodelos = modelos.idmodelos
            INNER JOIN marcas 
                ON modelos.marcas_idmarcas = marcas.idmarcas
            INNER JOIN precios_vehiculos pv 
                ON pv.vehiculos_idvehiculos = vehiculos.idvehiculos
            AND pv.activo_precio = 1
            INNER JOIN registro_clientes 
                ON ventas.registro_clientes_idregistro_clientes = registro_clientes.idregistro_clientes
            INNER JOIN usuarios 
                ON registro_clientes.Usuarios_idusuarios = usuarios.idusuarios
            INNER JOIN personas 
                ON usuarios.personas_idpersonas = personas.idpersonas
            WHERE ventas.estado_venta IN ('Realizada', 'Pendiente crédito')
            GROUP BY ventas.idventas
            ORDER BY ventas.fecha_venta DESC, ventas.idventas DESC
            LIMIT $inicio, $cantidad
        ";

        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }


    public function traer_cantidad_ventas()
    {
        $con = $this->getConexion();

        $query = "
            SELECT COUNT(*) AS total
            FROM ventas
            WHERE estado_venta IN ('Realizada', 'Pendiente crédito')
        ";

        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }

    public function cantidad_ventas_anuladas(){
        $con = $this->getConexion();
        $query = "SELECT count(*) as total FROM ventas WHERE estado_venta = 'Anulada'";
        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }


    /* ===========================================================
    VENTAS DEL EMPLEADO POR DÍA DEL MES (para gráfico)
    =========================================================== */
    public function ventas_empleado_mes($idusuario) {
        $con = $this->getConexion();

        $query = "
            SELECT 
                DATE(v.fecha_venta) AS dia,
                COUNT(*) AS cantidad
            FROM ventas v
            WHERE v.empleados_idempleados = (
                SELECT idempleados 
                FROM empleados 
                WHERE Usuarios_idusuarios = '$idusuario'
            )
            AND DATE_FORMAT(v.fecha_venta, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
            AND v.estado_venta = 'Realizada'
            GROUP BY DATE(v.fecha_venta)
            ORDER BY dia ASC
        ";

        return $con->query($query);
    }


    /* ===========================================================
    TOTAL DE VENTAS DEL EMPLEADO
    =========================================================== */
    public function ventas_empleado_total($idusuario) {
        $con = $this->getConexion();

        $query = "
            SELECT COUNT(*) AS total
            FROM ventas
            WHERE empleados_idempleados = (
                SELECT idempleados 
                FROM empleados 
                WHERE Usuarios_idusuarios = '$idusuario'
            )
            AND estado_venta = 'Realizada'
        ";

        return $con->query($query)->fetch_assoc();
    }


    /* ===========================================================
    TOTAL DE VENTAS ANULADAS DEL EMPLEADO
    =========================================================== */
    public function ventas_empleado_anuladas($idusuario) {
        $con = $this->getConexion();

        $query = "
            SELECT COUNT(*) AS total
            FROM ventas
            WHERE empleados_idempleados = (
                SELECT idempleados 
                FROM empleados 
                WHERE Usuarios_idusuarios = '$idusuario'
            )
            AND estado_venta = 'Anulada'
        ";

        return $con->query($query)->fetch_assoc();
    }


    /* ANULAR LA VENTA */

    public function anular_venta_estado($idventa) {
        $con = $this->getConexion();

        $query = "
            UPDATE ventas 
            SET estado_venta = 'Anulada',
                fecha_anulacion = NOW()
            WHERE idventas = $idventa
        ";

        $ok = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $ok;
    }


    public function traer_venta_por_vehiculo($idvehiculo) {
        $con = $this->getConexion();

        $query = "
            SELECT *
            FROM ventas
            WHERE vehiculo_idvehiculo = '$idvehiculo'
            LIMIT 1
        ";

        $res = $con->query($query);

        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc();
        }

        return null;
    }

    /* ===========================================================
    REPORTE: VENTAS POR PERÍODO (AGRUPADAS POR MES/AÑO)
    -> Basado en las comisiones de la concesionaria
    =========================================================== */
    public function reporte_ventas_por_periodo($desde = null, $hasta = null) {
        $con = $this->getConexion();

        // Filtro base: solo ventas realizadas
        $filtro = " WHERE v.estado_venta = 'Realizada' ";

        // Opcional: excluir comisiones anuladas
        $filtro .= " AND (cv.estado_comision IS NULL OR cv.estado_comision <> 'anulada') ";

        if (!empty($desde)) {
            $desde = $con->real_escape_string($desde);
            $filtro .= " AND DATE(v.fecha_venta) >= '$desde'";
        }

        if (!empty($hasta)) {
            $hasta = $con->real_escape_string($hasta);
            $filtro .= " AND DATE(v.fecha_venta) <= '$hasta'";
        }

        $query = "
            SELECT 
                DATE_FORMAT(v.fecha_venta, '%Y-%m') AS periodo,
                DATE_FORMAT(v.fecha_venta, '%m/%Y') AS periodo_legible,
                COUNT(*) AS cantidad_ventas,

                -- 👇 TOTAL QUE GANA LA CONCESIONARIA EN EL PERÍODO
                SUM(cv.monto_concesionaria) AS total_vendido,

                -- 👇 TICKET PROMEDIO DE COMISIÓN POR VENTA
                CASE 
                    WHEN COUNT(*) > 0 
                    THEN SUM(cv.monto_concesionaria) / COUNT(*) 
                    ELSE 0 
                END AS ticket_promedio

            FROM ventas v
            INNER JOIN comisiones_ventas cv
                ON cv.ventas_idventas = v.idventas
            $filtro
            GROUP BY DATE_FORMAT(v.fecha_venta, '%Y-%m')
            ORDER BY DATE_FORMAT(v.fecha_venta, '%Y-%m') ASC
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res;
    }





    public function reporte_ventas_por_vendedor($desde, $hasta)
    {
        $con = $this->getConexion();

        // Sanitizar mínimamente
        $desde = $con->real_escape_string($desde);
        $hasta = $con->real_escape_string($hasta);

        $query = "
            SELECT 
                e.idempleados,
                CONCAT(p.apellido, ' ', p.nombre) AS vendedor,
                COUNT(v.idventas) AS cantidad_ventas,

                -- 👇 TOTAL COMISIONES DE LA CONCESIONARIA POR VENDEDOR
                SUM(cv.monto_concesionaria) AS total_vendido,

                -- 👇 TICKET PROMEDIO DE COMISIÓN POR VENTA
                CASE 
                    WHEN COUNT(v.idventas) > 0 
                    THEN SUM(cv.monto_concesionaria) / COUNT(v.idventas)
                    ELSE 0
                END AS ticket_promedio

            FROM ventas v
            INNER JOIN comisiones_ventas cv
                ON cv.ventas_idventas = v.idventas
            INNER JOIN empleados e 
                ON e.idempleados = v.empleados_idempleados
            INNER JOIN usuarios u
                ON u.idusuarios = e.Usuarios_idusuarios
            INNER JOIN personas p
                ON p.idpersonas = u.personas_idpersonas
            WHERE DATE(v.fecha_venta) BETWEEN '$desde' AND '$hasta'
            AND v.estado_venta = 'Realizada'
            -- opcional: excluir comisiones anuladas
            AND (cv.estado_comision IS NULL OR cv.estado_comision <> 'anulada')
            GROUP BY e.idempleados, p.apellido, p.nombre
            ORDER BY total_vendido DESC
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res;
    }


    
    public function reporte_ventas_anuladas_detalle($desde, $hasta)
    {
        $conexion = new Conexion();

        $query = "
            SELECT 
                v.idventas,
                v.fecha_venta,
                v.fecha_anulacion,

                -- Precio de venta original (por si querés mostrarlo)
                v.precio_venta,

                -- 👇 Comisión de la concesionaria asociada a la venta
                COALESCE(cv.monto_concesionaria, 0) AS comision_concesionaria,

                -- Datos del vendedor
                ev.idempleados,
                CONCAT(pv.apellido, ' ', pv.nombre) AS vendedor,

                -- Datos del cliente
                CONCAT(pc.apellido, ' ', pc.nombre) AS cliente,

                -- Datos del vehículo
                ve.patente,
                mo.nombre AS modelo,
                m.nombre  AS marca

            FROM ventas v

            -- LEFT JOIN por si hubiera alguna venta anulada sin comisión cargada
            LEFT JOIN comisiones_ventas cv 
                ON cv.ventas_idventas = v.idventas

            INNER JOIN empleados ev 
                ON ev.idempleados = v.empleados_idempleados
            INNER JOIN Usuarios uv
                ON uv.idUsuarios = ev.Usuarios_idUsuarios
            INNER JOIN personas pv
                ON pv.idpersonas = uv.personas_idpersonas

            INNER JOIN registro_clientes rc
                ON rc.idregistro_clientes = v.registro_clientes_idregistro_clientes
            INNER JOIN Usuarios uc
                ON uc.idUsuarios = rc.Usuarios_idusuarios
            INNER JOIN personas pc
                ON pc.idpersonas = uc.personas_idpersonas

            INNER JOIN vehiculos ve 
                ON ve.idvehiculos = v.vehiculo_idvehiculo
            INNER JOIN modelos mo
                ON mo.idmodelos = ve.modelos_idmodelos
            INNER JOIN marcas m
                ON m.idmarcas = mo.marcas_idmarcas

            WHERE v.estado_venta = 'Anulada'
            AND DATE(v.fecha_anulacion) BETWEEN '$desde' AND '$hasta'
            ORDER BY v.fecha_anulacion ASC
        ";

        return $conexion->consultar($query);
    }



    public function reporte_clientes_frecuentes($desde, $hasta)
    {
        $conexion = new Conexion();

        $query = "
            SELECT 
                rc.idregistro_clientes,
                CONCAT(p.apellido, ' ', p.nombre) AS cliente,
                COUNT(v.idventas) AS cantidad_ventas,
                SUM(v.precio_venta) AS total_vendido,
                AVG(v.precio_venta) AS ticket_promedio
            FROM ventas v
            INNER JOIN registro_clientes rc 
                ON rc.idregistro_clientes = v.registro_clientes_idregistro_clientes
            INNER JOIN usuarios u
                ON u.idusuarios = rc.Usuarios_idusuarios
            INNER JOIN personas p
                ON p.idpersonas = u.personas_idpersonas
            WHERE DATE(v.fecha_venta) BETWEEN '$desde' AND '$hasta'
            AND v.estado_venta = 'Realizada'
            GROUP BY rc.idregistro_clientes, p.apellido, p.nombre
            HAVING cantidad_ventas > 0
            ORDER BY cantidad_ventas DESC, total_vendido DESC
        ";

        return $conexion->consultar($query);
    }


    public function reporte_ventas_por_metodo_pago($desde, $hasta)
    {
        $conexion = new Conexion();

        // Filtro base: solo ventas realizadas
        $filtro = " WHERE v.estado_venta = 'Realizada' ";

        if (!empty($desde)) {
            $filtro .= " AND DATE(v.fecha_venta) >= '$desde'";
        }

        if (!empty($hasta)) {
            $filtro .= " AND DATE(v.fecha_venta) <= '$hasta'";
        }

        // Opcional: excluir comisiones anuladas
        $filtro .= " AND (cv.estado_comision IS NULL OR cv.estado_comision <> 'anulada') ";

        $query = "
            SELECT 
                tp.idtipo_pago,
                tp.descripcion AS metodo_pago,
                COUNT(v.idventas) AS cantidad_ventas,

                -- 👇 TOTAL DE COMISIONES DE LA CONCESIONARIA POR MÉTODO DE PAGO
                SUM(cv.monto_concesionaria) AS total_vendido,

                -- 👇 TICKET PROMEDIO DE COMISIÓN
                CASE 
                    WHEN COUNT(v.idventas) > 0 
                    THEN SUM(cv.monto_concesionaria) / COUNT(v.idventas)
                    ELSE 0
                END AS ticket_promedio

            FROM ventas v
            INNER JOIN comisiones_ventas cv
                ON cv.ventas_idventas = v.idventas
            INNER JOIN tipo_pago tp 
                ON tp.idtipo_pago = v.tipo_pago_idtipo_pago
            $filtro
            GROUP BY tp.idtipo_pago, tp.descripcion
            ORDER BY total_vendido DESC
        ";

        return $conexion->consultar($query);
    }



    public function existeVentaActivaPorVehiculo($idvehiculo)
    {
        $con = $this->getConexion();

        // Por las dudas casteamos a int para evitar inyección
        $idvehiculo = (int)$idvehiculo;

        $query = "
            SELECT COUNT(*) AS total
            FROM ventas
            WHERE vehiculo_idvehiculo = $idvehiculo
            AND estado_venta = 'Realizada'
        ";

        $res = $con->query($query);
        $total = 0;

        if ($res && $row = $res->fetch_assoc()) {
            $total = (int)$row['total'];
        }

        // Si NO usamos conexión externa, la cierro acá
        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $total > 0; // true si hay al menos una venta activa
    }


    public function traer_ventas_por_usuario($idusuario)
    {
        $con = $this->getConexion();

        // Por seguridad lo casteamos a int
        $idusuario = (int)$idusuario;

        $query = "
            SELECT 
                v.*,
                v.descripcion AS observacion,
                tp.descripcion AS nombre_pago,
                
                ve.patente,
                ve.anio,
                mo.nombre AS nombre_modelo,
                ma.nombre AS nombre_marca,

                -- Precio público actual (si querés mostrarlo)
                pv.precio AS precio_publico

            FROM ventas v
            INNER JOIN registro_clientes rc
                ON rc.idregistro_clientes = v.registro_clientes_idregistro_clientes
            INNER JOIN usuarios u
                ON u.idusuarios = rc.Usuarios_idusuarios

            INNER JOIN vehiculos ve
                ON ve.idvehiculos = v.vehiculo_idvehiculo
            INNER JOIN modelos mo
                ON mo.idmodelos = ve.modelos_idmodelos
            INNER JOIN marcas ma
                ON ma.idmarcas = mo.marcas_idmarcas

            LEFT JOIN precios_vehiculos pv
                ON pv.vehiculos_idvehiculos = ve.idvehiculos
            AND pv.activo_precio = 1

            LEFT JOIN tipo_pago tp
                ON tp.idtipo_pago = v.tipo_pago_idtipo_pago

            WHERE u.idusuarios = $idusuario
            AND v.estado_venta = 'Realizada'
            ORDER BY v.fecha_venta DESC
        ";

        $res = $con->query($query);

        // NO cierro conexión porque el caller va a recorrer el result
        // (igual que en traer_ventas())

        return $res;
    }


    public function actualizar_estado_credito($idventa)
    {
        $con = $this->getConexion();

        $estado_venta         = $con->real_escape_string($this->estado_venta);
        $estado_venta_credito = $con->real_escape_string($this->estado_venta_credito);

        $fecha_respuesta = $this->fecha_respuesta_credito
            ? "'" . $con->real_escape_string($this->fecha_respuesta_credito) . "'"
            : "NULL";

        $monto_aprobado = ($this->monto_aprobado_credito !== null && $this->monto_aprobado_credito !== '')
            ? floatval($this->monto_aprobado_credito)
            : "NULL";

        $observacion = $this->observacion_credito
            ? "'" . $con->real_escape_string($this->observacion_credito) . "'"
            : "observacion_credito"; // para no pisar con NULL si no querés actualizar

        // Si NO querés sobreescribir observacion_credito cuando viene null,
        // podés armar el SET condicionalmente. Acá hago una versión simple:
        $setObservacion = "";
        if ($this->observacion_credito !== null) {
            $setObservacion = ", observacion_credito = $observacion";
        }

        $query = "
            UPDATE ventas
            SET 
                estado_venta = '$estado_venta',
                estado_venta_credito = '$estado_venta_credito',
                fecha_respuesta_credito = $fecha_respuesta,
                monto_aprobado_credito = $monto_aprobado
                $setObservacion
            WHERE idventas = ".(int)$idventa."
        ";

        $ok = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $ok;
    }


        /* ===========================================================
       LISTADO / GESTIÓN DE CRÉDITOS BANCARIOS
       =========================================================== */

        public function contar_creditos($estadoCredito = null)
        {
            $con = $this->getConexion();

            $estadoCredito = $estadoCredito ? $con->real_escape_string($estadoCredito) : null;

            $whereEstado = "";
            if ($estadoCredito && $estadoCredito !== 'todos') {
                $whereEstado = " AND v.estado_venta_credito = '$estadoCredito' ";
            }

            $query = "
                SELECT COUNT(*) AS total
                FROM ventas v
                WHERE v.tipo_pago_idtipo_pago = 3
                $whereEstado
            ";

            $res = $con->query($query);
            $total = 0;

            if ($res && $row = $res->fetch_assoc()) {
                $total = (int)$row['total'];
            }

            if (!$this->conexion_externa) {
                $this->cerrarConexion($con);
            }

            return $total;
        }


        public function traer_creditos($estadoCredito = null, $inicio = 0, $cantidad = 20)
        {
            $con = $this->getConexion();

            $estadoCredito = $estadoCredito ? $con->real_escape_string($estadoCredito) : null;

            $whereEstado = "";
            if ($estadoCredito && $estadoCredito !== 'todos') {
                $whereEstado = " AND v.estado_venta_credito = '$estadoCredito' ";
            }

            $query = "
                SELECT 
                    v.*,
                    v.descripcion AS observacion,
                    tp.descripcion AS nombre_pago,
                    
                    ve.patente,
                    ve.anio,
                    mo.nombre AS nombre_modelo,
                    ma.nombre AS nombre_marca,

                    per.nombre,
                    per.apellido

                FROM ventas v
                INNER JOIN tipo_pago tp 
                    ON tp.idtipo_pago = v.tipo_pago_idtipo_pago

                INNER JOIN vehiculos ve
                    ON ve.idvehiculos = v.vehiculo_idvehiculo
                INNER JOIN modelos mo
                    ON mo.idmodelos = ve.modelos_idmodelos
                INNER JOIN marcas ma
                    ON ma.idmarcas = mo.marcas_idmarcas

                INNER JOIN registro_clientes rc
                    ON rc.idregistro_clientes = v.registro_clientes_idregistro_clientes
                INNER JOIN usuarios u
                    ON u.idusuarios = rc.Usuarios_idusuarios
                INNER JOIN personas per
                    ON per.idpersonas = u.personas_idpersonas

                WHERE v.tipo_pago_idtipo_pago = 3
                $whereEstado

                ORDER BY 
                    COALESCE(v.fecha_solicitud_credito, v.fecha_venta) DESC,
                    v.idventas DESC

                LIMIT $inicio, $cantidad
            ";

            $res = $con->query($query);

            if (!$this->conexion_externa) {
                $this->cerrarConexion($con);
            }

            return $res;
        }


        public function buscar_creditos($buscador, $estadoCredito = null)
        {
            $con = $this->getConexion();

            $buscador = $con->real_escape_string($buscador);
            $estadoCredito = $estadoCredito ? $con->real_escape_string($estadoCredito) : null;

            $whereEstado = "";
            if ($estadoCredito && $estadoCredito !== 'todos') {
                $whereEstado = " AND v.estado_venta_credito = '$estadoCredito' ";
            }

            $query = "
                SELECT 
                    v.*,
                    v.descripcion AS observacion,
                    tp.descripcion AS nombre_pago,
                    
                    ve.patente,
                    ve.anio,
                    mo.nombre AS nombre_modelo,
                    ma.nombre AS nombre_marca,

                    per.nombre,
                    per.apellido

                FROM ventas v
                INNER JOIN tipo_pago tp 
                    ON tp.idtipo_pago = v.tipo_pago_idtipo_pago

                INNER JOIN vehiculos ve
                    ON ve.idvehiculos = v.vehiculo_idvehiculo
                INNER JOIN modelos mo
                    ON mo.idmodelos = ve.modelos_idmodelos
                INNER JOIN marcas ma
                    ON ma.idmarcas = mo.marcas_idmarcas

                INNER JOIN registro_clientes rc
                    ON rc.idregistro_clientes = v.registro_clientes_idregistro_clientes
                INNER JOIN usuarios u
                    ON u.idusuarios = rc.Usuarios_idusuarios
                INNER JOIN personas per
                    ON per.idpersonas = u.personas_idpersonas

                WHERE v.tipo_pago_idtipo_pago = 3
                $whereEstado
                AND (
                    ve.patente       LIKE '%$buscador%'
                    OR ma.nombre     LIKE '%$buscador%'
                    OR mo.nombre     LIKE '%$buscador%'
                    OR per.nombre    LIKE '%$buscador%'
                    OR per.apellido  LIKE '%$buscador%'
                )

                ORDER BY 
                    COALESCE(v.fecha_solicitud_credito, v.fecha_venta) DESC,
                    v.idventas DESC
            ";

            $res = $con->query($query);

            if (!$this->conexion_externa) {
                $this->cerrarConexion($con);
            }

            return $res;
        }


    /**
     * Get the value of idventas
     */ 
    public function getIdventas()
    {
        return $this->idventas;
    }

    /**
     * Set the value of idventas
     *
     * @return  self
     */ 
    public function setIdventas($idventas)
    {
        $this->idventas = $idventas;

        return $this;
    }

    /**
     * Get the value of descripcion
     */ 
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     *
     * @return  self
     */ 
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get the value of fecha_venta
     */ 
    public function getFecha_venta()
    {
        return $this->fecha_venta;
    }

    /**
     * Set the value of fecha_venta
     *
     * @return  self
     */ 
    public function setFecha_venta($fecha_venta)
    {
        $this->fecha_venta = $fecha_venta;

        return $this;
    }

    /**
     * Get the value of tipo_pago_idtipo_pago
     */ 
    public function getTipo_pago_idtipo_pago()
    {
        return $this->tipo_pago_idtipo_pago;
    }

    /**
     * Set the value of tipo_pago_idtipo_pago
     *
     * @return  self
     */ 
    public function setTipo_pago_idtipo_pago($tipo_pago_idtipo_pago)
    {
        $this->tipo_pago_idtipo_pago = $tipo_pago_idtipo_pago;

        return $this;
    }

    /**
     * Get the value of vehiculo_idvehiculo
     */ 
    public function getVehiculo_idvehiculo()
    {
        return $this->vehiculo_idvehiculo;
    }

    /**
     * Set the value of vehiculo_idvehiculo
     *
     * @return  self
     */ 
    public function setVehiculo_idvehiculo($vehiculo_idvehiculo)
    {
        $this->vehiculo_idvehiculo = $vehiculo_idvehiculo;

        return $this;
    }



    /**
     * Get the value of registro_clientes_idregistro_clientes
     */ 
    public function getRegistro_clientes_idregistro_clientes()
    {
        return $this->registro_clientes_idregistro_clientes;
    }

    /**
     * Set the value of registro_clientes_idregistro_clientes
     *
     * @return  self
     */ 
    public function setRegistro_clientes_idregistro_clientes($registro_clientes_idregistro_clientes)
    {
        $this->registro_clientes_idregistro_clientes = $registro_clientes_idregistro_clientes;

        return $this;
    }

    /**
     * Get the value of empleados_idempleados
     */ 
    public function getEmpleados_idempleados()
    {
        return $this->empleados_idempleados;
    }

    /**
     * Set the value of empleados_idempleados
     *
     * @return  self
     */ 
    public function setEmpleados_idempleados($empleados_idempleados)
    {
        $this->empleados_idempleados = $empleados_idempleados;

        return $this;
    }

    /**
     * Get the value of titular_vehiculo_idtitular_vehiculo
     */ 
    public function getTitular_vehiculo_idtitular_vehiculo()
    {
        return $this->titular_vehiculo_idtitular_vehiculo;
    }

    /**
     * Set the value of titular_vehiculo_idtitular_vehiculo
     *
     * @return  self
     */ 
    public function setTitular_vehiculo_idtitular_vehiculo($titular_vehiculo_idtitular_vehiculo)
    {
        $this->titular_vehiculo_idtitular_vehiculo = $titular_vehiculo_idtitular_vehiculo;

        return $this;
    }

    /**
     * Get the value of precio_venta
     */ 
    public function getPrecio_venta()
    {
        return $this->precio_venta;
    }

    /**
     * Set the value of precio_venta
     *
     * @return  self
     */ 
    public function setPrecio_venta($precio_venta)
    {
        $this->precio_venta = $precio_venta;

        return $this;
    }

    // ================= ESTADO VENTA =================

public function getEstado_venta()
{
    return $this->estado_venta;
}

public function setEstado_venta($estado_venta)
{
    $this->estado_venta = $estado_venta;
    return $this;
}

public function getEstado_venta_credito()
{
    return $this->estado_venta_credito;
}

public function setEstado_venta_credito($estado_venta_credito)
{
    $this->estado_venta_credito = $estado_venta_credito;
    return $this;
}

    // ================= CAMPOS CRÉDITO =================

    public function getBanco_credito()
    {
        return $this->banco_credito;
    }

    public function setBanco_credito($banco_credito)
    {
        $this->banco_credito = $banco_credito;
        return $this;
    }

    public function getObservacion_credito()
    {
        return $this->observacion_credito;
    }

    public function setObservacion_credito($observacion_credito)
    {
        $this->observacion_credito = $observacion_credito;
        return $this;
    }

    public function getFecha_solicitud_credito()
    {
        return $this->fecha_solicitud_credito;
    }

    public function setFecha_solicitud_credito($fecha_solicitud_credito)
    {
        $this->fecha_solicitud_credito = $fecha_solicitud_credito;
        return $this;
    }

    public function getFecha_respuesta_credito()
    {
        return $this->fecha_respuesta_credito;
    }

    public function setFecha_respuesta_credito($fecha_respuesta_credito)
    {
        $this->fecha_respuesta_credito = $fecha_respuesta_credito;
        return $this;
    }

    public function getMonto_aprobado_credito()
    {
        return $this->monto_aprobado_credito;
    }

    public function setMonto_aprobado_credito($monto_aprobado_credito)
    {
        $this->monto_aprobado_credito = $monto_aprobado_credito;
        return $this;
    }

}
?>