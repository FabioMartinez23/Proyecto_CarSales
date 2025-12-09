<?php

require_once('conexion.php');

class ComprarVehiculo {

    private $_con = null;
    private $conexion_externa = false;

    private $idcompras;
    private $descripcion;
    private $fecha_compra;
    private $tipo_pago_idtipo_pago;
    private $vehiculo_idvehiculo;
    private $titular_vehiculo_idtitular_vehiculo;
    private $empleados_idempleados;

    // ===========================================================
    // CONSTRUCTOR COMPATIBLE CON CONEXIÓN COMPARTIDA
    // ===========================================================
    public function __construct(
        $idcompras = '',
        $descripcion = '',
        $fecha_compra = '',
        $tipo_pago_idtipo_pago = '',
        $vehiculo_idvehiculo = '',
        $titular_vehiculo_idtitular_vehiculo = '',
        $empleados_idempleados = '',
        $conn = null
    ) {
        $this->idcompras = $idcompras;
        $this->descripcion = $descripcion;
        $this->fecha_compra = $fecha_compra;
        $this->tipo_pago_idtipo_pago = $tipo_pago_idtipo_pago;
        $this->vehiculo_idvehiculo = $vehiculo_idvehiculo;
        $this->titular_vehiculo_idtitular_vehiculo = $titular_vehiculo_idtitular_vehiculo;
        $this->empleados_idempleados = $empleados_idempleados;

        // Si pasa conexión externa → usarla
        if ($conn instanceof mysqli) {
            $this->_con = $conn;
            $this->conexion_externa = true;
        }
    }

    // ===========================================================
    // MANEJO DE CONEXIÓN
    // ===========================================================
    private function getConexion() {
        if ($this->conexion_externa && $this->_con instanceof mysqli) {
            return $this->_con;
        }
        // conexión normal
        $conexion = new Conexion();
        $conexion->conectar();
        return $conexion->_con;
    }

    private function cerrarConexion($con) {
        if (!$this->conexion_externa && $con instanceof mysqli) {
            $con->close();
        }
    }

    public function agregar_compra() {
        $con = $this->getConexion();

        $query = "
            INSERT INTO compras 
            (descripcion, fecha_compra, tipo_pago_idtipo_pago, vehiculo_idvehiculo, 
             titular_vehiculo_idtitular_vehiculo, empleados_idempleados)
            VALUES 
            ('$this->descripcion', CURDATE(), '$this->tipo_pago_idtipo_pago', 
             '$this->vehiculo_idvehiculo', '$this->titular_vehiculo_idtitular_vehiculo', 
             '$this->empleados_idempleados')
        ";

        $ok = $con->query($query);
        $id = $ok ? $con->insert_id : null;

        $this->cerrarConexion($con);
        return $id;
    }

    public function traer_compras() {
        $con = $this->getConexion();

        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, compras.descripcion as observacion, tipo_pago.descripcion as nombre_pago 
        FROM compras 
        INNER JOIN tipo_pago ON compras.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago 
        INNER JOIN vehiculos ON compras.vehiculo_idvehiculo = vehiculos.idvehiculos 
        INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos 
        INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas 
        INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos 
        INNER JOIN titular_vehiculo ON compras.titular_vehiculo_idtitular_vehiculo = titular_vehiculo.idtitulares 
        WHERE DATE(compras.fecha_compra) = DATE(precios_vehiculos.fecha_precio)";

        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }

    public function traer_compra_por_id($idcompras) {
        $con = $this->getConexion();

        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, compras.descripcion as observacion, tipo_pago.descripcion as nombre_pago, contactos.valor as valor_contacto, documentos.valor as valor_documento, domicilios.descripcion as nombre_domicilio, barrios.descripcion as nombre_barrio, localidades.descripcion as nombre_localidad, provincias.descripcion as nombre_provincia, colores.descripcion as nombre_descripcion, tipo_vehiculos.nombre as nombre_tipo 
        FROM compras 
        INNER JOIN empleados ON empleados.idempleados = compras.empleados_idempleados 
        INNER JOIN tipo_pago ON compras.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago 
        INNER JOIN vehiculos ON compras.vehiculo_idvehiculo = vehiculos.idvehiculos 
        INNER JOIN tipo_vehiculos ON tipo_vehiculos.idtipo_vehiculos = vehiculos.tipo_vehiculos_idtipo_vehiculos 
        INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos 
        INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas 
        INNER JOIN colores ON vehiculos.colores_idcolores = colores.idcolores 
        INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos 
        INNER JOIN titular_vehiculo ON compras.titular_vehiculo_idtitular_vehiculo = titular_vehiculo.idtitular_vehiculo 
        INNER JOIN personas ON titular_vehiculo.Personas_idpersonas = personas.idpersonas 
        INNER JOIN contactos ON contactos.Personas_idPersonas = personas.idpersonas 
        INNER JOIN documentos ON documentos.Personas_idPersonas = personas.idpersonas 
        INNER JOIN domicilios ON domicilios.Personas_idPersonas = personas.idpersonas 
        INNER JOIN barrios ON domicilios.barrios_idbarrios = barrios.idbarrios 
        INNER JOIN localidades ON barrios.localidades_idlocalidades = localidades.idlocalidades 
        INNER JOIN provincias ON localidades.provincias_idprovincias = provincias.idprovincias 
        WHERE idcompras = $idcompras";

        $res = $con->query($query);
        $fila = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;

        $this->cerrarConexion($con);
        return $fila;
    }

    public function buscar_compra($buscador) {
        $con = $this->getConexion();

        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, compras.descripcion as observacion, tipo_pago.descripcion as nombre_pago 
        FROM compras 
        INNER JOIN tipo_pago ON compras.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago 
        INNER JOIN vehiculos ON compras.vehiculo_idvehiculo = vehiculos.idvehiculos 
        INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos 
        INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas 
        INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos 
        INNER JOIN titular_vehiculo ON compras.titular_vehiculo_idtitular_vehiculo = titular_vehiculo.idtitular_vehiculo 
        INNER JOIN personas ON titular_vehiculo.Personas_idpersonas = personas.idpersonas 
        WHERE estado_compra = 'Realizada' AND (patente LIKE '%$buscador%' OR marcas.nombre LIKE '%$buscador%' 
           OR modelos.nombre LIKE '%$buscador%' OR anio LIKE '%$buscador%' 
           OR personas.nombre LIKE '%$buscador%' OR personas.apellido LIKE '%$buscador%')
        AND DATE(compras.fecha_compra) = DATE(precios_vehiculos.fecha_precio)";

        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }

    public function traer_cantidad_compras() {
        $con = $this->getConexion();
        $query = "SELECT count(*) as total FROM compras";
        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }

    public function traer_compras_paginacion($inicio, $cantidad) {
        $con = $this->getConexion();

        $query = "SELECT *, marcas.nombre as nombre_marca, modelos.nombre as nombre_modelo, compras.descripcion as observacion, tipo_pago.descripcion as nombre_pago 
        FROM compras 
        INNER JOIN tipo_pago ON compras.tipo_pago_idtipo_pago = tipo_pago.idtipo_pago 
        INNER JOIN vehiculos ON compras.vehiculo_idvehiculo = vehiculos.idvehiculos 
        INNER JOIN modelos ON vehiculos.modelos_idmodelos = modelos.idmodelos 
        INNER JOIN marcas ON modelos.marcas_idmarcas = marcas.idmarcas 
        INNER JOIN precios_vehiculos ON precios_vehiculos.vehiculos_idvehiculos = vehiculos.idvehiculos 
        INNER JOIN titular_vehiculo ON compras.titular_vehiculo_idtitular_vehiculo = titular_vehiculo.idtitular_vehiculo 
        INNER JOIN personas ON titular_vehiculo.Personas_idpersonas = personas.idpersonas 
        WHERE estado_compra = 'Realizada' AND DATE(compras.fecha_compra) = DATE(precios_vehiculos.fecha_precio) ORDER BY compras.idcompras DESC
        LIMIT $inicio,$cantidad";

        $res = $con->query($query);
        $this->cerrarConexion($con);
        return $res;
    }


    /* ===========================================================
   🔎 TRAER COMPRA POR ID
   Devuelve datos básicos de la consignación
    =========================================================== */
    public function traer_compra_por_id_devolver($idcompra) {
        $con = $this->getConexion();

        $query = "
            SELECT *
            FROM compras
            WHERE idcompras = '$idcompra'
            LIMIT 1
        ";

        $res = $con->query($query);
        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc();
        }
        return null;
    }

    /* ===========================================================
    ❌ ANULAR ESTADO DE COMPRA
    Solo cambia el estado, NO borra registro
    =========================================================== */
    public function anular_compra_estado($idcompra) {
        $con = $this->getConexion();

        $fecha = date('Y-m-d H:i:s');

        $query = "
            UPDATE compras
            SET estado_compra = 'Anulada',
                fecha_anulacion = '$fecha'
            WHERE idcompras = '$idcompra'
        ";

        return $con->query($query);
    }

    /* ===========================================================
    🔎 OPCIONAL: TRAER COMPRA DETALLADA
    Para usar en el formulario de anulación
    =========================================================== */
    public function traer_compra_detallada($idcompra) {

        $con = $this->getConexion();

        $query = "
            SELECT 
                c.*,
                v.patente,
                v.motor,
                v.chasis,
                v.anio,
                m.nombre AS marca,
                mo.nombre AS modelo,
                tv.nombre AS tipo_vehiculo,
                p.nombre AS nombre_persona,
                p.apellido AS apellido_persona,
                d.valor AS dni
            FROM compras c
            INNER JOIN vehiculos v ON v.idvehiculos = c.vehiculo_idvehiculo
            INNER JOIN modelos mo ON mo.idmodelos = v.modelos_idmodelos
            INNER JOIN marcas m ON m.idmarcas = mo.marcas_idmarcas
            INNER JOIN tipo_vehiculos tv ON tv.idtipo_vehiculos = v.tipo_vehiculos_idtipo_vehiculos
            INNER JOIN titular_vehiculo t ON t.idtitular_vehiculo = c.titular_vehiculo_idtitular_vehiculo
            INNER JOIN personas p ON p.idpersonas = t.Personas_idpersonas
            INNER JOIN documentos d ON d.Personas_idPersonas = p.idpersonas AND d.Tipo_documento_idTipo_documento = 1
            WHERE c.idcompras = '$idcompra'
            LIMIT 1
        ";

        $res = $con->query($query);
        return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;
    }

    public function reporte_ingresos_por_periodo($desde = null, $hasta = null)
    {
        $con = $this->getConexion();

        // Filtro por fecha
        $filtro = " WHERE 1=1 ";

        if (!empty($desde)) {
            $desde = $con->real_escape_string($desde);
            $filtro .= " AND DATE(c.fecha_compra) >= '$desde'";
        }

        if (!empty($hasta)) {
            $hasta = $con->real_escape_string($hasta);
            $filtro .= " AND DATE(c.fecha_compra) <= '$hasta'";
        }

        $query = "
            SELECT 
                DATE_FORMAT(c.fecha_compra, '%Y-%m') AS periodo,
                DATE_FORMAT(c.fecha_compra, '%m/%Y') AS periodo_legible,
                COUNT(*) AS cantidad_ingresos,
                SUM(pv.precio) AS total_tomado,
                AVG(pv.precio) AS valor_promedio
            FROM compras c
            INNER JOIN vehiculos v 
                ON v.idvehiculos = c.vehiculo_idvehiculo
            INNER JOIN precios_vehiculos pv 
                ON pv.vehiculos_idvehiculos = v.idvehiculos
            AND pv.activo_precio = 1
            INNER JOIN tipo_precios tp
                ON tp.idtipo_precios = pv.tipo_precios_idtipo_precios
            AND tp.descripcion = 'tomado'
            $filtro
            AND c.estado_compra = 'Realizada'
            GROUP BY DATE_FORMAT(c.fecha_compra, '%Y-%m')
            ORDER BY DATE_FORMAT(c.fecha_compra, '%Y-%m') ASC
        ";

        $res = $con->query($query);

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res;
    }


    public function traer_compra_para_acuse($idcompra)
    {
        $con = $this->getConexion();
        $idcompra = (int)$idcompra;

        $query = "
            SELECT 
                c.idcompras,
                c.descripcion              AS observacion,
                c.fecha_compra,
                c.estado_compra,

                -- Vehículo
                v.idvehiculos,
                v.patente,
                v.chasis,
                v.motor,
                v.kilometraje,
                v.anio,
                col.descripcion            AS nombre_descripcion,
                tv.nombre                  AS nombre_tipo,
                mo.nombre                  AS nombre_modelo,
                ma.nombre                  AS nombre_marca,

                -- Precio tomado
                pv.precio,
                tp.descripcion             AS nombre_pago,

                -- Titular / Persona
                per.nombre                 AS nombre,
                per.apellido               AS apellido,

                -- DNI (documentos, solo tipo 1 activo)
                doc.valor                  AS valor_documento,

                -- Contacto (teléfono, suponemos tipo_contacto = 1)
                cont.valor                 AS valor_contacto,

                -- Domicilio
                dom.descripcion            AS nombre_domicilio,
                b.descripcion              AS nombre_barrio,
                loc.descripcion            AS nombre_localidad,
                prov.descripcion           AS nombre_provincia,

                -- Ficha técnica
                ft.vencimiento_bateria,
                ft.vencimiento_service,
                ft.vencimiento_RTO,
                carr.descripcion_carroceria,
                cris.descripcion_cristales,
                neu.descripcion_neumaticos

            FROM compras c
            INNER JOIN vehiculos v 
                ON v.idvehiculos = c.vehiculo_idvehiculo
            INNER JOIN modelos mo 
                ON mo.idmodelos = v.modelos_idmodelos
            INNER JOIN marcas ma 
                ON ma.idmarcas = mo.marcas_idmarcas
            INNER JOIN colores col
                ON col.idcolores = v.colores_idcolores
            INNER JOIN tipo_vehiculos tv
                ON tv.idtipo_vehiculos = v.tipo_vehiculos_idtipo_vehiculos

            -- Precio tomado activo
            INNER JOIN precios_vehiculos pv
                ON pv.vehiculos_idvehiculos = v.idvehiculos
            AND pv.activo_precio = 1
            INNER JOIN tipo_precios tpr
                ON tpr.idtipo_precios = pv.tipo_precios_idtipo_precios
            AND tpr.descripcion = 'tomado'

            -- Forma de pago
            INNER JOIN tipo_pago tp
                ON tp.idtipo_pago = c.tipo_pago_idtipo_pago

            -- Titular y persona
            INNER JOIN titular_vehiculo tvh
                ON tvh.idtitular_vehiculo = c.titular_vehiculo_idtitular_vehiculo
            INNER JOIN personas per
                ON per.idpersonas = tvh.Personas_idpersonas

            -- Documento (DNI) - ajustá el id de tipo_documento si hace falta
            LEFT JOIN documentos doc
                ON doc.Personas_idPersonas = per.idpersonas
            AND doc.activo_documento = 1
            AND doc.Tipo_documento_idTipo_documento = 1

            -- Contacto (teléfono) - ajustá el id de tipo_contacto si hace falta
            LEFT JOIN contactos cont
                ON cont.Personas_idPersonas = per.idpersonas
            AND cont.tipo_contactos_idtipo_contactos = 1

            -- Domicilio
            LEFT JOIN domicilios dom
                ON dom.Personas_idPersonas = per.idpersonas
            LEFT JOIN barrios b
                ON b.idbarrios = dom.barrios_idbarrios
            LEFT JOIN localidades loc
                ON loc.idlocalidades = b.localidades_idlocalidades
            LEFT JOIN provincias prov
                ON prov.idprovincias = loc.provincias_idprovincias

            -- Ficha técnica
            LEFT JOIN ficha_tecnica ft
                ON ft.vehiculos_idvehiculos = v.idvehiculos
            LEFT JOIN carroceria carr
                ON carr.idcarroceria = ft.carroceria_idcarroceria
            LEFT JOIN cristales cris
                ON cris.idcristales = ft.cristales_idcristales
            LEFT JOIN neumaticos neu
                ON neu.idneumaticos = ft.neumaticos_idneumaticos

            WHERE c.idcompras = $idcompra
            LIMIT 1
        ";

        $res = $con->query($query);
        $fila = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;

        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $fila;
    }


    public function traer_documentaciones_por_vehiculo($idvehiculo)
    {
        $con = $this->getConexion();
        $idvehiculo = (int)$idvehiculo;

        $query = "
            SELECT 
                d.idDocumentaciones,
                td.descripcion       AS tipo_documento,
                d.estado_doc,
                d.digitalizado,
                d.fecha_registro
            FROM documentaciones d
            INNER JOIN tipo_documentacion td
                ON td.idtipo_documentacion = d.tipo_documentacion_idtipo_documentacion
            WHERE d.vehiculos_idvehiculos = $idvehiculo
            ORDER BY td.descripcion ASC
        ";

        $res = $con->query($query);

        // NO cierro conexión aquí si la clase está en transacción,
        // pero como normalmente no lo está, cerramos si corresponde:
        if (!$this->conexion_externa) {
            $this->cerrarConexion($con);
        }

        return $res;
    }



    /**
     * Get the value of idcompras
     */ 
    public function getIdcompras()
    {
        return $this->idcompras;
    }

    /**
     * Set the value of idcompras
     *
     * @return  self
     */ 
    public function setIdcompras($idcompras)
    {
        $this->idcompras = $idcompras;

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
     * Get the value of fecha_compra
     */ 
    public function getFecha_compra()
    {
        return $this->fecha_compra;
    }

    /**
     * Set the value of fecha_compra
     *
     * @return  self
     */ 
    public function setFecha_compra($fecha_compra)
    {
        $this->fecha_compra = $fecha_compra;

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
}



?>