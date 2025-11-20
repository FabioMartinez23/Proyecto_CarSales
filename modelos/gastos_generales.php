<?php
require_once("conexion.php");
require_once("caja.php");

date_default_timezone_set('America/Argentina/Buenos_Aires');


class GastoGeneral {

    private $_con;
    private $conexion_externa = false;

    private $idgastos_generales;
    private $descripcion;
    private $monto;
    private $fecha_gasto;
    private $Usuarios_idusuarios;
    private $tipo_gasto_idtipo_gasto;
    private $origen;
    private $vehiculos_idvehiculos;
    private $ventas_idventas;
    private $empleados_idempleados;
    private $referencia_movimiento;

    /* ============================================================
       CONSTRUCTOR — Compatible con conexión compartida
    ============================================================ */
    public function __construct(
        $idgastos_generales = '', $descripcion = '', $monto = '',
        $fecha_gasto = '', $Usuarios_idusuarios = '', $tipo_gasto_idtipo_gasto = '',
        $origen = 'general', $vehiculos_idvehiculos = null, $ventas_idventas = null,
        $empleados_idempleados = null, $referencia_movimiento = null, $conn = null
    ) {
        $this->idgastos_generales = $idgastos_generales;
        $this->descripcion = $descripcion;
        $this->monto = $monto;
        $this->fecha_gasto = $fecha_gasto;
        $this->Usuarios_idusuarios = $Usuarios_idusuarios;
        $this->tipo_gasto_idtipo_gasto = $tipo_gasto_idtipo_gasto;
        $this->origen = $origen;
        $this->vehiculos_idvehiculos = $vehiculos_idvehiculos;
        $this->ventas_idventas = $ventas_idventas;
        $this->empleados_idempleados = $empleados_idempleados;
        $this->referencia_movimiento = $referencia_movimiento;

        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    /* ============================================================
       CONTROL DE CONEXIÓN
    ============================================================ */
    private function getConexion() {
        if ($this->conexion_externa && $this->_con instanceof mysqli) {
            return $this->_con;
        }
        $conexion = new Conexion();
        $conexion->conectar();
        return $conexion->_con;
    }

    private function cerrarConexion($con) {
        if (!$this->conexion_externa && $con instanceof mysqli) {
            $con->close();
        }
    }

    /* ============================================================
       REGISTRAR GASTO + MOVIMIENTO EN CAJA
    ============================================================ */
    public function registrar_gasto_con_movimiento($data)
    {
        $con = $this->getConexion();
        $con->begin_transaction();

        try {

            // ======================================
            // Datos (sin escapes ni filtros)
            // ======================================
            $descripcion   = $data['descripcion'];
            $monto         = $data['monto'];
            $idusuario     = $data['Usuarios_idusuarios'];
            $idTipoGasto   = $data['tipo_gasto_idtipo_gasto'];
            $origen        = $data['origen'];

            // Manejo de NULLs sin comillas
            $idVehiculo = ($data['vehiculos_idvehiculos'] != "") ? $data['vehiculos_idvehiculos'] : "NULL";
            $idVenta    = ($data['ventas_idventas'] != "") ? $data['ventas_idventas'] : "NULL";
            $idEmpleado = ($data['empleados_idempleados'] != "") ? $data['empleados_idempleados'] : "NULL";

            $idCaja         = $data['caja_idcaja'];
            $tipo_movimiento = $data['tipo_movimiento_idtipo_movimiento'];
            $idTipoPago     = $data['tipo_pago_idtipo_pago'];

            $fecha = date("Y-m-d H:i:s");


            /* ============================================================
            1) INSERTAR GASTO
            ============================================================ */
            $sql_gasto = "
                INSERT INTO gastos_generales
                (descripcion, monto, fecha_gasto, Usuarios_idusuarios, tipo_gasto_idtipo_gasto,
                origen, vehiculos_idvehiculos, ventas_idventas, empleados_idempleados)
                VALUES
                ('$descripcion', $monto, '$fecha', $idusuario, $idTipoGasto,
                '$origen', $idVehiculo, $idVenta, $idEmpleado)
            ";

            if (!$con->query($sql_gasto)) {
                throw new Exception("Error al insertar gasto: " . $con->error);
            }

            $idGasto = $con->insert_id;


            /* ============================================================
            2) INSERTAR MOVIMIENTO DE CAJA
            ============================================================ */

            $descripcionMov = "Gasto: $descripcion";

            $sql_mov = "
                INSERT INTO caja_movimientos
                (tipo, monto, descripcion, referencia_tabla, referencia_id,
                fecha_movimiento, activo_movimiento, caja_idcaja,
                tipo_movimiento_idtipo_movimiento, tipo_pago_idtipo_pago, Usuarios_idusuarios)
                VALUES
                ('egreso', $monto, '$descripcionMov', 'gastos_generales', $idGasto,
                '$fecha', 1, $idCaja, $tipo_movimiento, $idTipoPago, $idusuario)
            ";

            if (!$con->query($sql_mov)) {
                throw new Exception("Error al insertar movimiento en caja: " . $con->error);
            }

            $idMovimiento = $con->insert_id;


            /* ============================================================
            3) ACTUALIZAR referencia_movimiento EN EL GASTO
            ============================================================ */

            $sql_ref = "
                UPDATE gastos_generales
                SET referencia_movimiento = $idMovimiento
                WHERE idgastos_generales = $idGasto
            ";

            if (!$con->query($sql_ref)) {
                throw new Exception("Error al guardar referencia mov.: " . $con->error);
            }


            /* ============================================================
            4) ACTUALIZAR SALDO DE CAJA
            ============================================================ */

            $sql_saldo = "
                UPDATE caja
                SET saldo_actual = saldo_actual - $monto
                WHERE idcaja = $idCaja
            ";

            if (!$con->query($sql_saldo)) {
                throw new Exception("Error al actualizar saldo de caja: " . $con->error);
            }


            /* ============================================================
            5) FIN DE TRANSACCIÓN
            ============================================================ */
            $con->commit();

            if (!$this->conexion_externa) $this->cerrarConexion($con);

            return [
                "status"  => "success",
                "mensaje" => "Gasto registrado correctamente.",
                "id_gasto" => $idGasto,
                "id_mov"   => $idMovimiento
            ];

        } catch (Exception $e) {

            $con->rollback();
            if (!$this->conexion_externa) $this->cerrarConexion($con);

            return [
                "status"  => "error",
                "mensaje" => $e->getMessage()
            ];
        }
    }


    /* ============================================================
       TRAER TIPOS DE GASTO
    ============================================================ */
    public function traer_tipos_gasto() {
        $con = $this->getConexion();

        $query = "SELECT * FROM tipo_gasto WHERE activo_gasto = 1 ORDER BY descripcion ASC";
        $res = $con->query($query);

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return $res;
    }

    /* ============================================================
    TRAER GASTOS con paginación opcional
    ============================================================ */
    public function traer_gastos($desde = '', $hasta = '', $limit = null, $offset = null) {
        $con = $this->getConexion();

        $filtro = "WHERE 1=1";

        if ($desde !== '') 
            $filtro .= " AND DATE(g.fecha_gasto) >= '$desde'";

        if ($hasta !== '') 
            $filtro .= " AND DATE(g.fecha_gasto) <= '$hasta'";

        // Construir LIMIT/OFFSET solo si vienen
        $limite = "";
        if ($limit !== null && $offset !== null) {
            $limite = " LIMIT $limit OFFSET $offset ";
        }

        $query = "
            SELECT 
                g.*, 
                tg.descripcion AS tipo_gasto, 
                u.username
            FROM gastos_generales g
            LEFT JOIN tipo_gasto tg ON tg.idtipo_gasto = g.tipo_gasto_idtipo_gasto
            LEFT JOIN usuarios u ON u.idusuarios = g.Usuarios_idusuarios
            $filtro
            ORDER BY g.fecha_gasto DESC
            $limite
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) 
            $this->cerrarConexion($con);

        return $res;
    }


    /* ============================================================
    CONTAR TOTAL DE GASTOS (para paginación)
    ============================================================ */
    public function contar_gastos($desde = '', $hasta = '') {
        $con = $this->getConexion();

        $filtro = "WHERE 1=1";

        if ($desde !== '') 
            $filtro .= " AND DATE(fecha_gasto) >= '$desde'";

        if ($hasta !== '') 
            $filtro .= " AND DATE(fecha_gasto) <= '$hasta'";

        $query = "
            SELECT COUNT(*) AS total
            FROM gastos_generales
            $filtro
        ";

        $res = $con->query($query)->fetch_assoc();

        if (!$this->conexion_externa) 
            $this->cerrarConexion($con);

        return $res['total'] ?? 0;
    }


    /* ============================================================
       TRAER GASTO POR ID
    ============================================================ */
    public function traer_gasto_por_id($id) {
        $con = $this->getConexion();

        $query = "SELECT * FROM gastos_generales WHERE idgastos_generales = '$id' LIMIT 1";
        $res = $con->query($query);

        if (!$this->conexion_externa) $this->cerrarConexion($con);
        return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    }

    public function traer_gasto_por_id_detalle($id) {
        $con = $this->getConexion();

        $query = "
            SELECT 
                g.*, 
                tg.descripcion AS tipo_gasto,
                u.username,
                
                v.patente,
                mo.nombre AS modelo,
                ma.nombre AS marca,

                ven.idventas AS venta_numero,
                vehV.patente AS venta_patente,
                moV.nombre AS venta_modelo,
                maV.nombre AS venta_marca,

                e.legajo,
                e.idempleados,
                td.descripcion AS puesto
                
            FROM gastos_generales g

            LEFT JOIN tipo_gasto tg ON tg.idtipo_gasto = g.tipo_gasto_idtipo_gasto
            LEFT JOIN usuarios u ON u.idusuarios = g.Usuarios_idusuarios

            -- Vehículo
            LEFT JOIN vehiculos v ON v.idvehiculos = g.vehiculos_idvehiculos
            LEFT JOIN modelos mo ON mo.idmodelos = v.modelos_idmodelos
            LEFT JOIN marcas ma ON ma.idmarcas = mo.marcas_idmarcas

            -- Venta + vehículo de la venta
            LEFT JOIN ventas ven ON ven.idventas = g.ventas_idventas
            LEFT JOIN vehiculos vehV ON vehV.idvehiculos = ven.vehiculo_idvehiculo
            LEFT JOIN modelos moV ON moV.idmodelos = vehV.modelos_idmodelos
            LEFT JOIN marcas maV ON maV.idmarcas = moV.marcas_idmarcas

            -- Empleado
            LEFT JOIN empleados e ON e.idempleados = g.empleados_idempleados
            LEFT JOIN tipo_de_puestos td ON td.idtipo_de_puestos = e.tipo_de_puestos_idtipo_de_puestos
            
            WHERE g.idgastos_generales = '$id'
            LIMIT 1
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) $this->cerrarConexion($con);

        return $res;
    }


    /* ============================================================
    GENERAR GASTOS AUTOMÁTICOS
    ============================================================ */
    public function generar_gastos_automaticos($contexto, $monto_base, $idReferencia, $idUsuario, $conn = null)
    {
        $con = $conn ?? $this->getConexion();

        // Traer tipos de gasto que apliquen al contexto
        $query = "
            SELECT *
            FROM tipo_gasto
            WHERE aplica_en = '$contexto'
            AND activo_gasto = 1
        ";
        $res = $con->query($query);

        if (!$res || $res->num_rows == 0) {
            return []; // no hay gastos automáticos
        }

        $resultados = [];
        $caja = new Caja(null, null, null, null, null, null, null, $con);

        // Necesitamos caja abierta
        $caja_abierta = $caja->traer_caja_abierta();
        if (!$caja_abierta || $caja_abierta->num_rows == 0) {
            return ['error' => 'No hay caja abierta.'];
        }

        $idcaja = $caja_abierta->fetch_assoc()['idcaja'];

        while ($tg = $res->fetch_assoc()) {

            // Calcular monto dependiendo del modo
            if ($tg['modo_calculo'] == 'monto_fijo') {
                $monto = floatval($tg['valor_calculo']);
            }
            elseif ($tg['modo_calculo'] == 'porcentaje_sobre_venta') {
                $monto = ($monto_base * floatval($tg['valor_calculo'])) / 100;
            }
            elseif ($tg['modo_calculo'] == 'porcentaje_sobre_comision') {
                $monto = ($monto_base * floatval($tg['valor_calculo'])) / 100;
            }
            else { 
                continue; 
            }

            // Construir descripción automática
            $descripcion = "[Automático] " . $tg['descripcion'];

            // Crear array para reusar la función de registrar gasto completo
            $data = [
                'descripcion' => $descripcion,
                'monto' => $monto,
                'Usuarios_idusuarios' => $idUsuario,
                'tipo_gasto_idtipo_gasto' => $tg['idtipo_gasto'],

                'origen' => $contexto,

                'vehiculos_idvehiculos'   => $contexto == 'vehiculo' ? $idReferencia : null,
                'ventas_idventas'         => $contexto == 'venta'    ? $idReferencia : null,
                'empleados_idempleados'   => $contexto == 'empleado' ? $idReferencia : null,

                'caja_idcaja' => $idcaja,
                'tipo_movimiento_idtipo_movimiento' => $caja->obtener_id_tipo_movimiento('Gasto General'),
                'tipo_pago_idtipo_pago' => 1 // efectivo por defecto, se puede modificar luego
            ];

            // Registrar gasto y movimiento
            $resultado = $this->registrar_gasto_con_movimiento($data, $con);

            $resultados[] = $resultado;
        }

        if (!$conn) $this->cerrarConexion($con);

        return $resultados;
    }


    /* ============================================================
       GETTERS & SETTERS
    ============================================================ */

    public function getId() { return $this->idgastos_generales; }
    public function setId($v) { $this->idgastos_generales = $v; }

    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($v) { $this->descripcion = $v; }

    public function getMonto() { return $this->monto; }
    public function setMonto($v) { $this->monto = $v; }

    public function getFechaGasto() { return $this->fecha_gasto; }
    public function setFechaGasto($v) { $this->fecha_gasto = $v; }

    public function getOrigen() { return $this->origen; }
    public function setOrigen($v) { $this->origen = $v; }

    public function getUsuario() { return $this->Usuarios_idusuarios; }
    public function setUsuario($v) { $this->Usuarios_idusuarios = $v; }
}
