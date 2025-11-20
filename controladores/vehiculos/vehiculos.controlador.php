<?php
ini_set('display_errors', 1);
require_once('../../modelos/vehiculos.php');

class VehiculosControlador
{
    /* ============================================================
       SANITIZAR INPUT  
    ============================================================ */
    private function limpiar($valor)
    {
        return trim(htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'));
    }

    /* ============================================================
       VALIDAR CAMPOS OBLIGATORIOS
    ============================================================ */
    private function validar_campos($campos)
    {
        foreach ($campos as $campo => $valor) {
            if (empty($valor)) {
                return "El campo <b>$campo</b> es obligatorio.";
            }
        }
        return null;
    }

    /* ============================================================
       GUARDAR NUEVO VEHÍCULO
    ============================================================ */
    public function guardar()
    {
        $datos = [
            "patente"   => $this->limpiar($_POST['patente'] ?? ''),
            "chasis"    => $this->limpiar($_POST['chasis'] ?? ''),
            "motor"     => $this->limpiar($_POST['motor'] ?? ''),
            "anio"      => $this->limpiar($_POST['año'] ?? ''),
            "kilometraje" => $this->limpiar($_POST['kilometraje'] ?? ''),
            "modelo"    => $_POST['modelos_idmodelos'] ?? '',
            "color"     => $_POST['colores_idcolores'] ?? '',
            "tipo"      => $_POST['tipo_vehiculos_idtipo_vehiculos'] ?? ''
        ];

        if ($error = $this->validar_campos($datos)) {
            header("location: ../../index.php?page=registrar_vehiculos&mensaje=$error&status=error");
            exit;
        }

        $vehiculo = new Vehiculos();
        $vehiculo->setPatente($datos['patente']);
        $vehiculo->setChasis($datos['chasis']);
        $vehiculo->setMotor($datos['motor']);
        $vehiculo->setAño($datos['anio']);
        $vehiculo->setKilometraje($datos['kilometraje']);
        $vehiculo->setModelos_idmodelos($datos['modelo']);
        $vehiculo->setColores_idcolores($datos['color']);
        $vehiculo->setTipo_vehiculos_idtipo_vehiculos($datos['tipo']);
        $vehiculo->agregar_vehiculo();

        header("location: ../../index.php?page=listado_vehiculos&mensaje=Vehículo registrado correctamente.&status=success");
        exit;
    }

    /* ============================================================
       MODIFICAR VEHÍCULO EXISTENTE
    ============================================================ */
    public function modificar()
    {
        $idvehiculos = $_POST['idvehiculos'] ?? null;
        if (!$idvehiculos) {
            header("location: ../../index.php?page=listado_vehiculos&mensaje=ID de vehículo inválido.&status=error");
            exit;
        }

        $datos = [
            "patente" => $this->limpiar($_POST['patente'] ?? ''),
            "chasis" => $this->limpiar($_POST['chasis'] ?? ''),
            "motor" => $this->limpiar($_POST['motor'] ?? ''),
            "anio" => $this->limpiar($_POST['año'] ?? ''),
            "kilometraje" => $this->limpiar($_POST['kilometraje'] ?? ''),
            "modelo" => $_POST['modelos_idmodelos'] ?? '',
            "color" => $_POST['colores_idcolores'] ?? '',
            "tipo" => $_POST['tipo_vehiculos_idtipo_vehiculos'] ?? ''
        ];

        if ($error = $this->validar_campos($datos)) {
            header("location: ../../index.php?page=registrar_vehiculos&idvehiculos=$idvehiculos&mensaje=$error&status=error");
            exit;
        }

        $vehiculo = new Vehiculos();
        $vehiculo->setIdvehiculos($idvehiculos);
        $vehiculo->setPatente($datos['patente']);
        $vehiculo->setChasis($datos['chasis']);
        $vehiculo->setMotor($datos['motor']);
        $vehiculo->setAño($datos['anio']);
        $vehiculo->setKilometraje($datos['kilometraje']);
        $vehiculo->setModelos_idmodelos($datos['modelo']);
        $vehiculo->setColores_idcolores($datos['color']);
        $vehiculo->setTipo_vehiculos_idtipo_vehiculos($datos['tipo']);
        $vehiculo->actualizar_vehiculo();

        header("location: ../../index.php?page=listado_vehiculos&mensaje=Vehículo modificado correctamente.&status=success");
        exit;
    }

    /* ============================================================
       ELIMINAR VEHÍCULO
    ============================================================ */
    public function eliminar()
    {
        $id = $_POST['idvehiculos'] ?? null;

        if (!$id) {
            header("location: ../../index.php?page=listado_vehiculos&mensaje=ID inválido.&status=error");
            exit;
        }

        $vehiculo = new Vehiculos();
        $vehiculo->setIdvehiculos($id);
        $vehiculo->eliminar_vehiculo();

        header("location: ../../index.php?page=listado_vehiculos&mensaje=Vehículo eliminado correctamente.&status=success");
        exit;
    }

    /* ============================================================
       BUSCAR POR PATENTE (JSON)
    ============================================================ */
    public function buscar_por_patente()
    {
        ob_clean();

        $patente = $this->limpiar($_POST['patente'] ?? '');

        $vehiculo = new Vehiculos();
        $resultado = $vehiculo->traer_vehiculos_por_patente_json($patente);

        if ($resultado) {
            $res = [
                'status'   => 'success',
                'vehiculo' => $resultado
            ];
        } else {
            $res = [
                'status'   => 'error',
                'mensaje'  => 'Vehículo no encontrado.'
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* ============================================================
   RUTEO DEL CONTROLADOR
============================================================ */
if (isset($_POST['action'])) {

    $vehiculo_controlador = new VehiculosControlador();
    $action = $_POST['action'];

    switch ($action) {

        case 'guardar':
            $vehiculo_controlador->guardar();
            break;

        case 'modificar':
        case 'actualizar':   // compatibilidad
            $vehiculo_controlador->modificar();
            break;

        case 'eliminar':
            $vehiculo_controlador->eliminar();
            break;

        case 'buscar_por_patente':
            $vehiculo_controlador->buscar_por_patente();
            break;

        default:
            header("location: ../../index.php?page=listado_vehiculos&mensaje=Acción no reconocida.&status=error");
            exit;
    }
}

