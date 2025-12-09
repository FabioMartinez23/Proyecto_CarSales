<?php
require_once('conexion.php');

class Vehiculos_Taller
{
    private $_con;

    public function __construct()
    {
        $conexion = new Conexion();
        $conexion->conectar();
        $this->_con = $conexion->_con;
    }

    /**
     * Registra un ingreso básico al taller para un vehículo.
     * Podés ampliar después con km_ingreso, motivo, observaciones.
     */
    public function registrar_ingreso($vehiculos_idvehiculos, $usuario_ingreso = null, $km_ingreso = null, $motivo = null, $observaciones = null)
    {
        $vehiculos_idvehiculos = (int)$vehiculos_idvehiculos;
        $usuario_ingreso       = $usuario_ingreso !== null ? (int)$usuario_ingreso : 'NULL';
        $km_ingreso            = $km_ingreso !== null ? (int)$km_ingreso : 'NULL';

        // Escapamos texto para evitar problemas
        $motivo        = $motivo !== null ? "'" . $this->_con->real_escape_string($motivo) . "'" : "NULL";
        $observaciones = $observaciones !== null ? "'" . $this->_con->real_escape_string($observaciones) . "'" : "NULL";

        $sql = "
            INSERT INTO vehiculos_taller
                (fecha_ingreso, km_ingreso, motivo, observaciones, usuario_ingreso, estado_taller, vehiculos_idvehiculos)
            VALUES
                (NOW(), $km_ingreso, $motivo, $observaciones, $usuario_ingreso, 'en_proceso', $vehiculos_idvehiculos)
        ";

        return $this->_con->query($sql);
    }

    /**
     * Trae todos los vehículos que están actualmente en taller (estado_taller = 'en_proceso')
     * con datos básicos del vehículo.
     */
    public function traer_vehiculos_en_taller()
    {
        $sql = "
            SELECT 
                vt.*,
                v.patente,
                v.anio,
                m.nombre  AS nombre_marca,
                mo.nombre AS nombre_modelo,
                c.descripcion AS nombre_color
            FROM vehiculos_taller vt
            INNER JOIN vehiculos v 
                ON vt.vehiculos_idvehiculos = v.idvehiculos
            INNER JOIN modelos mo 
                ON v.modelos_idmodelos = mo.idmodelos
            INNER JOIN marcas m 
                ON mo.marcas_idmarcas = m.idmarcas
            INNER JOIN colores c
                ON v.colores_idcolores = c.idcolores
            WHERE vt.estado_taller = 'en_proceso'
            ORDER BY vt.fecha_ingreso DESC
        ";

        return $this->_con->query($sql);
    }

    /**
     * Traer un registro puntual de taller por ID
     */
    public function traer_por_id($idvehiculos_taller)
    {
        $id = (int)$idvehiculos_taller;
        $sql = "
            SELECT 
                vt.*,
                v.idvehiculos,
                v.patente,
                v.anio,
                m.nombre  AS nombre_marca,
                mo.nombre AS nombre_modelo,
                c.descripcion AS nombre_color
            FROM vehiculos_taller vt
            INNER JOIN vehiculos v 
                ON vt.vehiculos_idvehiculos = v.idvehiculos
            INNER JOIN modelos mo 
                ON v.modelos_idmodelos = mo.idmodelos
            INNER JOIN marcas m 
                ON mo.marcas_idmarcas = m.idmarcas
            INNER JOIN colores c
                ON v.colores_idcolores = c.idcolores
            WHERE vt.idvehiculos_taller = $id
            LIMIT 1
        ";

        $res = $this->_con->query($sql);
        return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    }

    public function finalizar_taller($idvehiculos_taller, $km_salida = null, $trabajo_realizado = null, $usuario_salida = null)
    {
        $conexion = new Conexion();

        $id = (int)$idvehiculos_taller;

        // km_salida puede venir vacío
        if ($km_salida === '' || $km_salida === null) {
            $km_salida_sql = "NULL";
        } else {
            $km_salida_sql = (int)$km_salida;
        }

        // usuario_salida (idusuarios) puede venir null
        if ($usuario_salida === '' || $usuario_salida === null) {
            $usuario_salida_sql = "NULL";
        } else {
            $usuario_salida_sql = (int)$usuario_salida;
        }

        // trabajo_realizado: texto opcional
        if ($trabajo_realizado === '' || $trabajo_realizado === null) {
            $trabajo_sql = "NULL";
        } else {
            // escapamos mínimamente para evitar romper el SQL
            $trabajo_sql = "'" . addslashes($trabajo_realizado) . "'";
        }

        $query = "
            UPDATE vehiculos_taller
            SET 
                estado_taller = 'finalizado',
                fecha_salida = NOW(),
                km_salida = $km_salida_sql,
                trabajo_realizado = $trabajo_sql,
                usuario_salida = $usuario_salida_sql
            WHERE idvehiculos_taller = $id
            AND estado_taller = 'en_proceso'
        ";

        return $conexion->actualizar($query);
    }

    public function reporte_trabajos_taller($filtros = [])
    {
        // 1) Abrir conexión
        $conexion = new Conexion();
        $conexion->conectar();
        $con = $conexion->_con; // mysqli

        $where = " WHERE 1=1 ";

        // -----------------------------
        // Filtro: fecha desde
        // -----------------------------
        if (!empty($filtros['fecha_desde'])) {
            $fd = $con->real_escape_string($filtros['fecha_desde']);
            $where .= " AND DATE(vt.fecha_ingreso) >= '$fd'";
        }

        // -----------------------------
        // Filtro: fecha hasta
        // -----------------------------
        if (!empty($filtros['fecha_hasta'])) {
            $fh = $con->real_escape_string($filtros['fecha_hasta']);
            $where .= " AND DATE(vt.fecha_ingreso) <= '$fh'";
        }

        // -----------------------------
        // Filtro: estado_taller (en_proceso / finalizado / todos)
        // -----------------------------
        if (!empty($filtros['estado_taller']) && $filtros['estado_taller'] !== 'todos') {
            $est = $con->real_escape_string($filtros['estado_taller']);
            $where .= " AND vt.estado_taller = '$est'";
        }

        // -----------------------------
        // Filtro: patente
        // -----------------------------
        if (!empty($filtros['patente'])) {
            $pat = $con->real_escape_string($filtros['patente']);
            $where .= " AND v.patente LIKE '%$pat%'";
        }

        // -----------------------------
        // Consulta principal
        // -----------------------------
        $sql = "
            SELECT 
                vt.idvehiculos_taller,
                vt.fecha_ingreso,
                vt.km_ingreso,
                vt.motivo,
                vt.observaciones,
                vt.estado_taller,
                vt.fecha_salida,
                vt.km_salida,
                vt.trabajo_realizado,
                vt.vehiculos_idvehiculos,

                v.patente,
                v.anio,
                m.nombre  AS nombre_marca,
                mo.nombre AS nombre_modelo,
                c.descripcion AS nombre_color,

                -- 🔹 Total de gastos del taller vigentes (NO anulados)
                COALESCE((
                    SELECT SUM(gt.monto)
                    FROM gastos_taller gt
                    WHERE gt.vehiculos_taller_idvehiculos_taller = vt.idvehiculos_taller
                    AND gt.anulado = 0
                ), 0) AS total_gastos_taller

            FROM vehiculos_taller vt
            INNER JOIN vehiculos v
                ON vt.vehiculos_idvehiculos = v.idvehiculos
            INNER JOIN modelos mo
                ON v.modelos_idmodelos = mo.idmodelos
            INNER JOIN marcas m
                ON mo.marcas_idmarcas = m.idmarcas
            INNER JOIN colores c
                ON v.colores_idcolores = c.idcolores

            $where

            ORDER BY vt.fecha_ingreso DESC, vt.idvehiculos_taller DESC
        ";

        $resultado = $con->query($sql);
        return $resultado;
    }

}
