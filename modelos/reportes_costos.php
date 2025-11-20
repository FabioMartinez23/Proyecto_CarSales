<?php
require_once('conexion.php');

class ReportesCostos
{
    private $con;

    public function __construct()
    {
        $db = new Conexion();
        $db->conectar();
        $this->con = $db->_con;
    }

    /* ============================================================
       A) COSTOS POR UN VEHÍCULO (DETALLE)
    ============================================================ */
    public function traer_costos_vehiculo($idvehiculo)
    {
        $idvehiculo = intval($idvehiculo);

        /* ============================
        1) PRECIO TOMADO + GASTOS
        ============================ */
        $q = "
            SELECT
                v.idvehiculos,
                v.patente,

                -- PRECIO TOMADO
                (
                    SELECT pv.precio 
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp 
                        ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'tomado'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS precio_tomado,

                -- GASTOS
                (
                    SELECT SUM(g.monto)
                    FROM gastos_generales g
                    WHERE g.origen = 'vehiculo'
                    AND g.vehiculos_idvehiculos = v.idvehiculos
                ) AS gastos

            FROM vehiculos v
            WHERE v.idvehiculos = $idvehiculo
            LIMIT 1
        ";

        $res = $this->con->query($q);
        if (!$res || $res->num_rows == 0) return null;

        $base = $res->fetch_assoc();

        $precio_tomado = floatval($base['precio_tomado'] ?? 0);
        $gastos        = floatval($base['gastos'] ?? 0);
        $costo_total   = $precio_tomado + $gastos;

        /* ============================
        2) DETALLE DE TODOS LOS GASTOS
        ============================ */
        $qG = "
            SELECT g.fecha_gasto, tg.descripcion AS tipo_gasto, g.monto, g.descripcion
            FROM gastos_generales g
            LEFT JOIN tipo_gasto tg ON tg.idtipo_gasto = g.tipo_gasto_idtipo_gasto
            WHERE g.origen = 'vehiculo'
            AND g.vehiculos_idvehiculos = $idvehiculo
            ORDER BY g.fecha_gasto ASC
        ";
        $detalle_gastos = $this->con->query($qG)->fetch_all(MYSQLI_ASSOC);

        /* ============================
        3) ¿ESTÁ VENDIDO?
        ============================ */
        $qVenta = "
            SELECT 
                v.idventas,
                v.precio_venta,
                c.monto_empleado,
                c.monto_concesionaria
            FROM ventas v
            LEFT JOIN comisiones_ventas c 
                ON c.ventas_idventas = v.idventas
            WHERE v.vehiculo_idvehiculo = $idvehiculo
            LIMIT 1
        ";
        $resVenta = $this->con->query($qVenta);

        $tieneVenta = ($resVenta && $resVenta->num_rows > 0);

        if ($tieneVenta) {

            $venta = $resVenta->fetch_assoc();

            $precio_venta = floatval($venta['precio_venta'] ?? 0);
            $ganancia_bruta = $precio_venta - $costo_total;

            return [
                'tiene_venta' => true,
                'detalle_venta' => [
                    'idventa' => $venta['idventas'],
                    'precio_venta' => $precio_venta,
                    'costo_total_real' => $costo_total,
                    'ganancia_bruta_real' => $ganancia_bruta,
                    'monto_empleado' => floatval($venta['monto_empleado'] ?? 0),
                    'ganancia_neta_conce' => floatval($venta['monto_concesionaria'] ?? 0),
                    'rentabilidad_real' =>
                        $precio_venta > 0 ? round(($ganancia_bruta / $precio_venta) * 100, 2) : 0
                ],

                // Para las tarjetas
                'precio_tomado' => $precio_tomado,
                'gastos_vehiculo' => $gastos,
                'precio_venta' => $precio_venta,
                'costo_total' => $costo_total,
                'rentabilidad' =>
                    $precio_venta > 0 ? round(($ganancia_bruta / $precio_venta) * 100, 2) : 0,

                'detalle_gastos' => $detalle_gastos,
            ];
        }

        /* ============================
        4) VEHÍCULO DISPONIBLE
        ============================ */

        // Traer precio público actual
        $qPublico = "
            SELECT pv.precio 
            FROM precios_vehiculos pv
            INNER JOIN tipo_precios tp 
                ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
            WHERE pv.vehiculos_idvehiculos = $idvehiculo
            AND tp.descripcion = 'publico'
            ORDER BY pv.fecha_precio DESC
            LIMIT 1
        ";

        $resPub = $this->con->query($qPublico);
        $precio_publico = ($resPub && $resPub->num_rows > 0)
            ? floatval($resPub->fetch_assoc()['precio'])
            : 0;

        $ganancia_proyectada = $precio_publico - $costo_total;

        return [
            'tiene_venta' => false,

            'precio_tomado' => $precio_tomado,
            'gastos_vehiculo' => $gastos,
            'precio_publico' => $precio_publico,
            'costo_total' => $costo_total,

            'ganancia_proyectada' => $ganancia_proyectada,
            'rentabilidad' =>
                $precio_publico > 0 ? round(($ganancia_proyectada / $precio_publico) * 100, 2) : 0,

            'detalle_gastos' => $detalle_gastos,
        ];
    }





    /* ============================================================
       B) COSTOS TOTALES (LISTADO COMPLETO)
    ============================================================ */
    public function traer_costos_totales($desde = '', $hasta = '', $estado = '', $vehiculo = '')
    {
        /* ============================================================
        1) TRAER TODOS LOS VEHÍCULOS SIN FILTRAR (luego filtramos)
        ============================================================ */
        $q = "
            SELECT 
                v.idvehiculos,
                v.patente,

                -- PRECIO TOMADO
                (
                    SELECT pv.precio 
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp 
                        ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'tomado'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS precio_tomado,

                -- GASTOS
                (
                    SELECT SUM(g.monto)
                    FROM gastos_generales g
                    WHERE g.origen = 'vehiculo'
                    AND g.vehiculos_idvehiculos = v.idvehiculos
                ) AS gastos,

                -- PRECIO PUBLICO
                (
                    SELECT pv.precio 
                    FROM precios_vehiculos pv
                    INNER JOIN tipo_precios tp 
                        ON pv.tipo_precios_idtipo_precios = tp.idtipo_precios
                    WHERE pv.vehiculos_idvehiculos = v.idvehiculos
                    AND tp.descripcion = 'publico'
                    ORDER BY pv.fecha_precio DESC
                    LIMIT 1
                ) AS precio_publico,

                -- PRECIO VENTA
                (
                    SELECT ve.precio_venta
                    FROM ventas ve
                    WHERE ve.vehiculo_idvehiculo = v.idvehiculos
                    LIMIT 1
                ) AS precio_venta,

                -- ESTADO
                (
                    SELECT ev.estado_vehiculo
                    FROM estado_vehiculo ev
                    WHERE ev.idestado_vehiculo = v.estado_vehiculo_idestado_vehiculo
                    LIMIT 1
                ) AS estado_vehiculo

            FROM vehiculos v
            WHERE v.activo_vehiculo = 1
            OR v.idvehiculos IN (SELECT vehiculo_idvehiculo FROM ventas)
            ORDER BY v.idvehiculos DESC
        ";

        $res = $this->con->query($q);
        $lista = [];

        /* ============================================================
        2) ARMADO DE RESULTADOS PARA CADA VEHÍCULO
        ============================================================ */
        while ($row = $res->fetch_assoc()) {

            $precio_tomado   = floatval($row['precio_tomado'] ?? 0);
            $gastos          = floatval($row['gastos'] ?? 0);
            $precio_publico  = floatval($row['precio_publico'] ?? 0);
            $precio_venta    = floatval($row['precio_venta'] ?? 0);

            $costo_final = $precio_tomado + $gastos;

            // === RENTABILIDAD ===
            if ($precio_venta > 0) {
                // Vendido (REAL)
                $margen = $precio_venta - $costo_final;
                $rentabilidad = $precio_venta > 0
                    ? round(($margen / $precio_venta) * 100, 2)
                    : 0;
            } else {
                // Disponible (PROYECTADA)
                $margen = $precio_publico - $costo_final;
                $rentabilidad = $precio_publico > 0
                    ? round(($margen / $precio_publico) * 100, 2)
                    : 0;
            }

            $row['gastos']        = $gastos;
            $row['costo_final']   = $costo_final;
            $row['margen']        = $margen;
            $row['rentabilidad']  = $rentabilidad;

            $lista[] = $row;
        }

        /* ============================================================
        3) APLICAR FILTROS (fecha, estado, patente) — EN PHP
        ============================================================ */
        $lista_filtrada = [];

        foreach ($lista as $v) {

            // ------ FILTRO ESTADO ------
            if ($estado !== '' && $v['estado_vehiculo'] !== $estado) {
                continue;
            }

            // ------ FILTRO PATENTE / ID ------
            if ($vehiculo !== '' &&
                stripos($v['patente'], $vehiculo) === false &&
                stripos(strval($v['idvehiculos']), $vehiculo) === false
            ) {
                continue;
            }

            // ------ FILTRO FECHAS (si existen gastos) ------
            if ($desde !== '' || $hasta !== '') {

                $qFecha = "
                    SELECT MIN(DATE(g.fecha_gasto)) AS primera_fecha
                    FROM gastos_generales g
                    WHERE g.origen='vehiculo'
                    AND g.vehiculos_idvehiculos = " . $v['idvehiculos'];

                $resF = $this->con->query($qFecha);
                $fecha_gasto = $resF->fetch_assoc()['primera_fecha'] ?? null;

                if ($desde !== '' && $fecha_gasto !== null && $fecha_gasto < $desde)
                    continue;

                if ($hasta !== '' && $fecha_gasto !== null && $fecha_gasto > $hasta)
                    continue;
            }

            /* Agregar vehículo que pasó los filtros */
            $lista_filtrada[] = $v;
        }

        return $lista_filtrada;
    }


    public function traer_kpis_costos($desde = '', $hasta = '', $estado = '', $vehiculo = '')
    {
        /* ============================
        FILTRO GLOBAL PARA GASTOS
        ============================ */
        $filtroFecha = "";
        if ($desde !== '') $filtroFecha .= " AND DATE(g.fecha_gasto) >= '$desde'";
        if ($hasta !== '') $filtroFecha .= " AND DATE(g.fecha_gasto) <= '$hasta'";

        // Total de gastos filtrados
        $qGastos = "
            SELECT SUM(g.monto) AS total
            FROM gastos_generales g
            WHERE g.origen = 'vehiculo' $filtroFecha
        ";
        $resG = $this->con->query($qGastos);
        $total_gastos = floatval($resG->fetch_assoc()['total'] ?? 0);

        /* ============================
        COSTOS POR VEHÍCULO (ahora filtrados)
        ============================ */
        $vehiculos = $this->traer_costos_totales($desde, $hasta, $estado, $vehiculo);
        $cant = count($vehiculos);

        $acumCosto = 0;
        $acumRent = 0;

        foreach ($vehiculos as $v) {
            $acumCosto += floatval($v['costo_final']);
            $acumRent  += floatval($v['rentabilidad']);
        }

        return [
            'total_gastos'          => $total_gastos,
            'cantidad_vehiculos'   => $cant,
            'costo_promedio'       => $cant > 0 ? round($acumCosto / $cant, 2) : 0,
            'rentabilidad_promedio'=> $cant > 0 ? round($acumRent / $cant, 1) : 0
        ];
    }

    public function traer_gastos_por_categoria($desde = '', $hasta = '')
    {
        $filtro = "";
        if ($desde !== '') $filtro .= " AND DATE(g.fecha_gasto) >= '$desde'";
        if ($hasta !== '') $filtro .= " AND DATE(g.fecha_gasto) <= '$hasta'";

        $q = "
            SELECT tg.descripcion AS categoria, SUM(g.monto) AS total
            FROM gastos_generales g
            INNER JOIN tipo_gasto tg ON g.tipo_gasto_idtipo_gasto = tg.idtipo_gasto
            WHERE g.origen = 'vehiculo' $filtro
            GROUP BY tg.descripcion
            ORDER BY total DESC
        ";

        $res = $this->con->query($q);

        $data = [];
        while ($r = $res->fetch_assoc()) {
            $data[] = $r;
        }
        return $data;
    }


    public function traer_costos_mensuales($desde = '', $hasta = '')
    {
        $filtro = "";
        if ($desde !== '') $filtro .= " AND DATE(g.fecha_gasto) >= '$desde'";
        if ($hasta !== '') $filtro .= " AND DATE(g.fecha_gasto) <= '$hasta'";

        $q = "
            SELECT 
                DATE_FORMAT(g.fecha_gasto, '%Y-%m') AS mes,
                SUM(g.monto) AS total
            FROM gastos_generales g
            WHERE g.origen = 'vehiculo' $filtro
            GROUP BY DATE_FORMAT(g.fecha_gasto, '%Y-%m')
            ORDER BY mes ASC
        ";

        $res = $this->con->query($q);

        $lista = [];
        while ($row = $res->fetch_assoc()) {
            $lista[] = $row;
        }
        return $lista;
    }



}

