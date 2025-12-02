<?php
require_once('conexion.php');

class Caja {
    private $_con;
    private $conexion_externa = false; // 🔹 Nuevo: indica si la conexión fue inyectada

    private $idcaja;
    private $fecha_apertura;
    private $saldo_inicial;
    private $saldo_actual;
    private $estado;
    private $observaciones;
    private $Usuarios_idusuarios;

    // ===========================================================
    // CONSTRUCTOR COMPATIBLE CON CONEXIÓN COMPARTIDA
    // ===========================================================
    public function __construct(
        $idcaja = '', $fecha_apertura = '', $saldo_inicial = '',
        $saldo_actual = '', $estado = '', $observaciones = '',
        $Usuarios_idusuarios = '', $conn = null
    ) {
        $this->idcaja = $idcaja;
        $this->fecha_apertura = $fecha_apertura;
        $this->saldo_inicial = $saldo_inicial;
        $this->saldo_actual = $saldo_actual;
        $this->estado = $estado;
        $this->observaciones = $observaciones;
        $this->Usuarios_idusuarios = $Usuarios_idusuarios;

        // ✅ Si se pasa una conexión, la usamos (modo transacción)
        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    /* ===========================================================
       MÉTODOS PRINCIPALES DE CAJA
       =========================================================== */

    private function getConexion() {
        // ✅ Si ya hay conexión externa, la usa
        if ($this->conexion_externa && $this->_con instanceof mysqli) {
            return $this->_con;
        }
        // ✅ Caso contrario, crea una nueva (modo tradicional)
        $conexion = new Conexion();
        $conexion->conectar();
        return $conexion->_con;
    }

    private function cerrarConexion($conexion) {
        if (!$this->conexion_externa && $conexion instanceof mysqli) {
            $conexion->close();
        }
    }


    // Abrir una nueva caja
    public function abrir_caja($saldo_inicial, $Usuarios_idusuarios) {
        $con = $this->getConexion();
        $fecha_apertura = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO caja 
            (fecha_apertura, saldo_inicial, saldo_actual, estado, Usuarios_idusuarios)
            VALUES ('$fecha_apertura', '$saldo_inicial', '$saldo_inicial', 'abierta', '$Usuarios_idusuarios')
        ";
        $con->query($query);
        $id = $con->insert_id;

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $id;
    }

    // Cerrar caja y calcular saldo final según movimientos
    public function cerrar_caja($idcaja, $observaciones = '') {
        $con = $this->getConexion();

        $qSaldo = "
            SELECT 
                SUM(CASE WHEN tipo='ingreso' THEN monto ELSE 0 END) AS ingresos,
                SUM(CASE WHEN tipo='egreso' THEN monto ELSE 0 END) AS egresos
            FROM caja_movimientos
            WHERE caja_idcaja = '$idcaja' AND activo_movimiento = 1
        ";

        $fila = $con->query($qSaldo)->fetch_assoc();
        $saldo_final = floatval($fila['ingresos']) - floatval($fila['egresos']);

        $query = "
            UPDATE caja 
            SET saldo_actual='$saldo_final', estado='cerrada', observaciones='$observaciones'
            WHERE idcaja='$idcaja'
        ";
        $ok = $con->query($query);

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $ok;
    }

    // Traer todas las cajas (últimas primero)
    public function traer_cajas() {
        $con = $this->getConexion();

        $query = "
            SELECT c.*, u.username 
            FROM caja c
            LEFT JOIN usuarios u ON u.idusuarios = c.Usuarios_idusuarios
            ORDER BY c.idcaja DESC
        ";
        $res = $con->query($query);

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $res;
    }

    // Obtener la caja abierta actual
    public function traer_caja_abierta() {
        $con = $this->getConexion();
        $query = "
            SELECT * FROM caja 
            WHERE estado='abierta'
            ORDER BY fecha_apertura DESC
            LIMIT 1
        ";
        $res = $con->query($query);

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $res;
    }

    // Actualizar saldo manualmente (si se requiere)
    public function actualizar_saldo($idcaja, $nuevo_saldo) {
        $con = $this->getConexion();
        $ok = $con->query("UPDATE caja SET saldo_actual='$nuevo_saldo' WHERE idcaja='$idcaja'");

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $ok;
    }

    /* ===========================================================
       VERIFICAR / ABRIR CAJA MENSUAL AUTOMÁTICA
       =========================================================== */
    // ================================================
    // 🔹 Verificar / abrir caja mensual (ajustada)
    // ================================================
    public function verificar_o_abrir_caja_mensual($Usuarios_idusuarios) {
        $con = $this->getConexion();
        $mes_actual = date('Y-m');
        $hora_actual = date('H:i:s');

        $resultado = $con->query("
            SELECT * FROM caja
            WHERE DATE_FORMAT(fecha_apertura, '%Y-%m') = '$mes_actual'
            AND estado='abierta'
            LIMIT 1
        ");

        if ($resultado->num_rows > 0) {
            return $resultado;
        }

        if ($hora_actual >= '06:00:00') {
            $con->query("
                INSERT INTO caja (fecha_apertura, saldo_inicial, saldo_actual, estado, Usuarios_idusuarios)
                VALUES (NOW(), 0.00, 0.00, 'abierta', '$Usuarios_idusuarios')
            ");

            $idcaja = $con->insert_id;
            return $con->query("SELECT * FROM caja WHERE idcaja='$idcaja' LIMIT 1");
        }

        return $resultado;
    }

    /* ===========================================================
       MOVIMIENTOS DE CAJA
       =========================================================== */
    public function registrar_movimiento(
        $tipo, $monto, $descripcion, 
        $referencia_tabla, $referencia_id,
        $idcaja, $tipo_movimiento, $tipo_pago, $Usuarios_idusuarios
    ) {
        $con = $this->getConexion();
        $fecha = date('Y-m-d H:i:s');

        // CORRECCIÓN ★ → Evitar vacío o null en tipo_pago
        if ($tipo_pago === null || $tipo_pago === '' || $tipo_pago === false) {
            $tipo_pago = 4; // Reverso / Ajuste
        }

        // Bloquear fila de caja
        $con->query("SELECT idcaja FROM caja WHERE idcaja='$idcaja' FOR UPDATE");

        // Registrar movimiento
        $query = "
            INSERT INTO caja_movimientos 
            (tipo, monto, descripcion, referencia_tabla, referencia_id, fecha_movimiento, activo_movimiento,
            caja_idcaja, tipo_movimiento_idtipo_movimiento, tipo_pago_idtipo_pago, Usuarios_idusuarios)
            VALUES 
            ('$tipo', '$monto', '$descripcion', '$referencia_tabla', '$referencia_id',
            '$fecha', 1, '$idcaja', '$tipo_movimiento', '$tipo_pago', '$Usuarios_idusuarios')
        ";

        $ok = $con->query($query);

        if ($ok) {
            if ($tipo == 'ingreso') {
                $con->query("UPDATE caja SET saldo_actual = saldo_actual + $monto WHERE idcaja='$idcaja'");
            } else {
                $con->query("UPDATE caja SET saldo_actual = saldo_actual - $monto WHERE idcaja='$idcaja'");
            }
        }

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $ok;
    }


    // ================================================
    // 🔹 Traer movimientos (usa conexión compartida)
    // ================================================
    public function traer_movimientos($desde = '', $hasta = '', $tipo = '', $limit = null, $offset = null) {
        $con = $this->getConexion();

        $filtro = "WHERE 1=1";
        if ($desde != '') $filtro .= " AND DATE(cm.fecha_movimiento) >= '$desde'";
        if ($hasta != '') $filtro .= " AND DATE(cm.fecha_movimiento) <= '$hasta'";
        if ($tipo  != '') $filtro .= " AND cm.tipo = '$tipo'";

        // LIMIT/OFFSET opcional
        $limite = "";
        if ($limit !== null && $offset !== null) {
            $limite = " LIMIT $limit OFFSET $offset";
        }

        $query = "
            SELECT 
                cm.*, 
                tm.descripcion AS tipo_movimiento, 
                tp.descripcion AS tipo_pago, 
                u.username,
                c.idcaja,
                c.fecha_apertura,
                c.saldo_inicial,
                c.saldo_actual
            FROM caja_movimientos cm
            INNER JOIN tipo_movimiento tm 
                ON tm.idtipo_movimiento = cm.tipo_movimiento_idtipo_movimiento
            LEFT JOIN tipo_pago tp 
                ON tp.idtipo_pago = cm.tipo_pago_idtipo_pago
            LEFT JOIN usuarios u 
                ON u.idusuarios = cm.Usuarios_idusuarios
            LEFT JOIN caja c
                ON c.idcaja = cm.caja_idcaja
            $filtro
            ORDER BY cm.fecha_movimiento DESC
            $limite
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $res;
    }

    public function contar_movimientos($desde = '', $hasta = '', $tipo = '') {
        $con = $this->getConexion();

        $filtro = "WHERE 1=1";
        if ($desde != '') $filtro .= " AND DATE(fecha_movimiento) >= '$desde'";
        if ($hasta != '') $filtro .= " AND DATE(fecha_movimiento) <= '$hasta'";
        if ($tipo  != '') $filtro .= " AND tipo = '$tipo'";

        $query = "
            SELECT COUNT(*) AS total
            FROM caja_movimientos
            $filtro
        ";

        $res = $con->query($query);
        $total = 0;

        if ($res && $res->num_rows > 0) {
            $fila = $res->fetch_assoc();
            $total = $fila['total'] ?? 0;
        }

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $total;
    }

    public function obtener_id_tipo_movimiento($descripcion) {
        $con = $this->getConexion();

        $query = "
            SELECT idtipo_movimiento
            FROM tipo_movimiento
            WHERE descripcion = '$descripcion'
            LIMIT 1
        ";

        $res = $con->query($query);
        $id = ($res && $res->num_rows > 0) ? $res->fetch_assoc()['idtipo_movimiento'] : null;

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $id;
    }

    /* ===========================================================
       GASTOS GENERALES
       =========================================================== */
    // ================================================
    // 🔹 Registrar gasto (usa conexión compartida)
    // ================================================
    public function registrar_gasto($descripcion, $monto, $tipo_gasto, $Usuarios_idusuarios) {
        $con = $this->getConexion();
        $fecha = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO gastos_generales 
                (descripcion, monto, fecha_gasto, Usuarios_idusuarios, tipo_gasto_idtipo_gasto)
            VALUES 
                ('$descripcion', '$monto', '$fecha', '$Usuarios_idusuarios', '$tipo_gasto')
        ";
        $ok = $con->query($query);

        if ($ok) {
            // Descontar del saldo de la caja abierta (usa misma conexión)
            $rCaja = $this->traer_caja_abierta();
            if ($rCaja && $rCaja->num_rows > 0) {
                $fila = $rCaja->fetch_assoc();
                $idcaja = $fila['idcaja'];
                $con->query("UPDATE caja SET saldo_actual = saldo_actual - $monto WHERE idcaja = '$idcaja'");
            }
        }

        $this->cerrarConexion($con);
        return $ok;
    }

    public function traer_gastos($desde = '', $hasta = '') {
        $conexion = new Conexion();
        $filtro = "WHERE 1=1";
        if ($desde != '') $filtro .= " AND DATE(g.fecha_gasto) >= '$desde'";
        if ($hasta != '') $filtro .= " AND DATE(g.fecha_gasto) <= '$hasta'";
        
        $query = "
            SELECT g.*, tg.descripcion AS tipo_gasto, u.username
            FROM gastos_generales g
            LEFT JOIN tipo_gasto tg ON tg.idtipo_gasto = g.tipo_gasto_idtipo_gasto
            LEFT JOIN usuarios u ON u.idusuarios = g.Usuarios_idusuarios
            $filtro
            ORDER BY g.fecha_gasto DESC
        ";
        return $conexion->consultar($query);
    }

    /* ===========================================================
       BALANCES Y REPORTES
       =========================================================== */

    // Balance diario (para gráficos o reporte rápido)
    public function traer_balance_diario() {
        $conexion = new Conexion();
        $query = "
            SELECT 
                DATE(fecha_movimiento) AS fecha,
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS ingresos,
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) AS egresos,
                (SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) -
                 SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END)) AS balance_dia
            FROM caja_movimientos
            WHERE activo_movimiento = 1
            GROUP BY DATE(fecha_movimiento)
            ORDER BY fecha ASC
        ";
        return $conexion->consultar($query);
    }

    // ⚠ Compatibilidad: si pasás $anio y $mes devuelve ese mes; si no, devuelve agrupado por mes
    public function traer_balance_mensual($anio = null, $mes = null) {
        $conexion = new Conexion();

        if ($anio !== null && $mes !== null) {
            $filtro = "$anio-$mes";
            $query = "
                SELECT 
                    SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS total_ingresos,
                    SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) AS total_egresos,
                    (SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) - 
                     SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END)) AS balance
                FROM caja_movimientos
                WHERE DATE_FORMAT(fecha_movimiento, '%Y-%m') = '$filtro'
                  AND activo_movimiento = 1
            ";
            return $conexion->consultar($query);
        } else {
            // Modo agrupado por mes (por si en otra parte lo usabas así)
            $query = "
                SELECT 
                    DATE_FORMAT(fecha_movimiento, '%Y-%m') AS periodo,
                    SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS total_ingresos,
                    SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) AS total_egresos,
                    (SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) -
                     SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END)) AS balance_mensual
                FROM caja_movimientos
                WHERE activo_movimiento = 1
                GROUP BY DATE_FORMAT(fecha_movimiento, '%Y-%m')
                ORDER BY periodo DESC
            ";
            return $conexion->consultar($query);
        }
    }

    // Historial de cajas cerradas
    public function traer_cajas_cerradas() {
        $conexion = new Conexion();
        $query = "
            SELECT idcaja, fecha_apertura, saldo_inicial, saldo_actual, estado, observaciones
            FROM caja
            WHERE estado = 'cerrada'
            ORDER BY fecha_apertura DESC
        ";
        return $conexion->consultar($query);
    }

    /* ===========================================================
    CIERRE DE CAJA MENSUAL (CON REGISTRO EN HISTORIAL)
    =========================================================== */
    public function cerrar_caja_mensual($anio = null, $mes = null, $observaciones = '') {
        $conexion = new Conexion();

        $anio = $anio ?? date('Y');
        $mes = $mes ?? date('m');
        $Usuarios_idusuarios = $_SESSION['idusuarios'] ?? 1;

        // Buscar caja abierta del mes actual
        $query_caja = "
            SELECT * FROM caja
            WHERE estado = 'abierta'
            AND DATE_FORMAT(fecha_apertura, '%Y-%m') = '$anio-$mes'
            ORDER BY fecha_apertura DESC
            LIMIT 1
        ";
        $resultado_caja = $conexion->consultar($query_caja);

        if ($resultado_caja->num_rows == 0) {
            return ['status' => 'error', 'mensaje' => 'No existe una caja abierta para este mes.'];
        }

        $caja = $resultado_caja->fetch_assoc();
        $idcaja = $caja['idcaja'];

        // Calcular totales del mes desde caja_movimientos
        $query_totales = "
            SELECT 
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS total_ingresos,
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) AS total_egresos
            FROM caja_movimientos
            WHERE caja_idcaja = '$idcaja'
            AND MONTH(fecha_movimiento) = '$mes'
            AND YEAR(fecha_movimiento) = '$anio'
            AND activo_movimiento = 1
        ";
        $totales = $conexion->consultar($query_totales)->fetch_assoc();

        $total_ingresos = $totales['total_ingresos'] ?? 0;
        $total_egresos  = $totales['total_egresos'] ?? 0;
        $balance_final  = $total_ingresos - $total_egresos;

        // Registrar cierre en cierres_caja
        $query_insert = "
            INSERT INTO cierres_caja 
            (fecha_cierre, saldo_final, observaciones, caja_idcaja, Usuarios_idusuarios, 
            total_ingresos, total_egresos, balance_final)
            VALUES (NOW(), '$balance_final', '$observaciones', '$idcaja', '$Usuarios_idusuarios',
                    '$total_ingresos', '$total_egresos', '$balance_final')
        ";
        $conexion->insertar($query_insert);

        // Actualizar estado de caja
        $query_update = "
            UPDATE caja 
            SET estado = 'cerrada', observaciones = '$observaciones', saldo_actual = '$balance_final'
            WHERE idcaja = '$idcaja'
        ";
        $conexion->actualizar($query_update);

        return [
            'status' => 'success',
            'mensaje' => 'Caja mensual cerrada correctamente.',
            'datos' => [
                'total_ingresos' => $total_ingresos,
                'total_egresos' => $total_egresos,
                'balance_final' => $balance_final,
                'idcaja' => $idcaja
            ]
        ];
    }

    /* ===========================================================
    TRAER HISTORIAL DE CIERRES DE CAJA
    =========================================================== */
    public function traer_historial_cierres() {
        $conexion = new Conexion();
        $query = "
            SELECT 
                cc.idcierres_caja,
                DATE_FORMAT(cc.fecha_cierre, '%d/%m/%Y %H:%i') AS fecha_cierre,
                cc.saldo_final,
                cc.total_ingresos,
                cc.total_egresos,
                cc.balance_final,
                cc.observaciones,
                u.username AS usuario,
                c.idcaja
            FROM cierres_caja cc
            LEFT JOIN usuarios u ON u.idusuarios = cc.Usuarios_idusuarios
            LEFT JOIN caja c ON c.idcaja = cc.caja_idcaja
            ORDER BY cc.fecha_cierre DESC
        ";
        return $conexion->consultar($query);
    }


    /* ===========================================================
    GRAFICO DIARIO DEL MES ACTUAL
    =========================================================== */
    public function grafico_diario() {
        $conexion = new Conexion();

        $query = "
            SELECT 
                DATE(fecha_movimiento) AS dia,
                SUM(CASE WHEN tipo='ingreso' THEN monto ELSE 0 END) AS total_ingresos,
                SUM(CASE WHEN tipo='egreso' THEN monto ELSE 0 END) AS total_egresos
            FROM caja_movimientos
            WHERE activo_movimiento = 1
            AND DATE_FORMAT(fecha_movimiento, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
            GROUP BY DATE(fecha_movimiento)
            ORDER BY dia ASC
        ";

        return $conexion->consultar($query);
    }

    /* ===========================================================
    GRAFICO ANUAL (12 MESES)
    =========================================================== */
    public function grafico_anual() {
        $conexion = new Conexion();

        $query = "
            SELECT 
                DATE_FORMAT(fecha_movimiento, '%m') AS mes_num,
                DATE_FORMAT(fecha_movimiento, '%b') AS mes_nombre,
                SUM(CASE WHEN tipo='ingreso' THEN monto ELSE 0 END) AS ingresos,
                SUM(CASE WHEN tipo='egreso' THEN monto ELSE 0 END) AS egresos,
                (SUM(CASE WHEN tipo='ingreso' THEN monto ELSE 0 END) -
                SUM(CASE WHEN tipo='egreso' THEN monto ELSE 0 END)) AS balance
            FROM caja_movimientos
            WHERE activo_movimiento = 1
            AND YEAR(fecha_movimiento) = YEAR(NOW())
            GROUP BY mes_num
            ORDER BY mes_num ASC
        ";

        return $conexion->consultar($query);
    }

    /* ===========================================================
    BALANCE MENSUAL (para gráfico mensual)
    =========================================================== */
    public function traer_balance_mensual_grafico($anio = null, $mes = null) {
        $conexion = new Conexion();

        if ($anio !== null && $mes !== null) {
            $filtro = "$anio-$mes";

            $query = "
                SELECT 
                    DATE(fecha_movimiento) AS periodo,
                    SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS total_ingresos,
                    SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) AS total_egresos,
                    (SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) -
                    SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END)) AS balance_mensual
                FROM caja_movimientos
                WHERE activo_movimiento = 1
                AND DATE_FORMAT(fecha_movimiento, '%Y-%m') = '$filtro'
                GROUP BY DATE(fecha_movimiento)
                ORDER BY periodo ASC
            ";

            return $conexion->consultar($query);
        }

        // si no pasan año/mes → agrupado por mes (lo dejé como lo tenías)
        $query = "
            SELECT 
                DATE_FORMAT(fecha_movimiento, '%Y-%m') AS periodo,
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS total_ingresos,
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) AS total_egresos,
                (SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) -
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END)) AS balance_mensual
            FROM caja_movimientos
            WHERE activo_movimiento = 1
            GROUP BY DATE_FORMAT(fecha_movimiento, '%Y-%m')
            ORDER BY periodo DESC
        ";

        return $conexion->consultar($query);
    }


    public function traer_movimiento_por_id($id) {
        $con = $this->getConexion();

        $query = "
            SELECT 
                cm.*, 
                tm.descripcion AS tipo_movimiento,
                tp.descripcion AS tipo_pago,
                u.username,
                c.fecha_apertura,
                c.saldo_inicial,
                c.saldo_actual
            FROM caja_movimientos cm
            LEFT JOIN tipo_movimiento tm 
                ON tm.idtipo_movimiento = cm.tipo_movimiento_idtipo_movimiento
            LEFT JOIN tipo_pago tp 
                ON tp.idtipo_pago = cm.tipo_pago_idtipo_pago
            LEFT JOIN usuarios u 
                ON u.idusuarios = cm.Usuarios_idusuarios
            LEFT JOIN caja c
                ON c.idcaja = cm.caja_idcaja
            WHERE cm.idcaja_movimientos = '$id'
            LIMIT 1
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) $this->cerrarConexion($con);

        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc(); // devolvemos directamente el array
        }

        return null;
    }


    public function reporte_movimientos($desde, $hasta, $tipo_mov)
    {
        $con = $this->getConexion();

        // Sanitizar fechas mínimamente
        $desde = $con->real_escape_string($desde);
        $hasta = $con->real_escape_string($hasta);

        // Base del query: usamos SIEMPRE c.tipo como tipo_movimiento
        $query = "
            SELECT
                c.idcaja_movimientos,
                c.fecha_movimiento,
                c.descripcion,
                c.monto,
                c.tipo AS tipo_movimiento
            FROM caja_movimientos c
            WHERE DATE(c.fecha_movimiento) BETWEEN '$desde' AND '$hasta'
        ";

        // Filtro por tipo_mov (ingresos / egresos / todos)
        if ($tipo_mov === 'ingresos') {
            $query .= " AND c.tipo = 'ingreso'";
        } elseif ($tipo_mov === 'egresos') {
            $query .= " AND c.tipo = 'egreso'";
        }

        // Ordenar por fecha ascendente
        $query .= " ORDER BY c.fecha_movimiento ASC";

        // Ejecutar
        $res = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res;
    }



    /**
     * Get the value of idcaja
     */ 
    public function getIdcaja()
    {
        return $this->idcaja;
    }

    /**
     * Set the value of idcaja
     *
     * @return  self
     */ 
    public function setIdcaja($idcaja)
    {
        $this->idcaja = $idcaja;

        return $this;
    }

    /**
     * Get the value of fecha_apertura
     */ 
    public function getFecha_apertura()
    {
        return $this->fecha_apertura;
    }

    /**
     * Set the value of fecha_apertura
     *
     * @return  self
     */ 
    public function setFecha_apertura($fecha_apertura)
    {
        $this->fecha_apertura = $fecha_apertura;

        return $this;
    }

    /**
     * Get the value of saldo_inicial
     */ 
    public function getSaldo_inicial()
    {
        return $this->saldo_inicial;
    }

    /**
     * Set the value of saldo_inicial
     *
     * @return  self
     */ 
    public function setSaldo_inicial($saldo_inicial)
    {
        $this->saldo_inicial = $saldo_inicial;

        return $this;
    }

    /**
     * Get the value of saldo_actual
     */ 
    public function getSaldo_actual()
    {
        return $this->saldo_actual;
    }

    /**
     * Set the value of saldo_actual
     *
     * @return  self
     */ 
    public function setSaldo_actual($saldo_actual)
    {
        $this->saldo_actual = $saldo_actual;

        return $this;
    }

    /**
     * Get the value of estado
     */ 
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set the value of estado
     *
     * @return  self
     */ 
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get the value of observaciones
     */ 
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set the value of obervaciones
     *
     * @return  self
     */ 
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get the value of Usuarios_idusuarios
     */ 
    public function getUsuarios_idusuarios()
    {
        return $this->Usuarios_idusuarios;
    }

    /**
     * Set the value of Usuarios_idusuarios
     *
     * @return  self
     */ 
    public function setUsuarios_idusuarios($Usuarios_idusuarios)
    {
        $this->Usuarios_idusuarios = $Usuarios_idusuarios;

        return $this;
    }
}
