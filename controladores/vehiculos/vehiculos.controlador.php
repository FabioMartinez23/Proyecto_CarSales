<?php
ini_set('display_errors', 1);
session_start(); // 👈 MUY IMPORTANTE
require_once('../../modelos/vehiculos.php');
require_once('../../modelos/auditoria_edicion.php'); // 🔹 importante

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
            "patente"      => $this->limpiar($_POST['patente'] ?? ''),
            "chasis"       => $this->limpiar($_POST['chasis'] ?? ''),
            "motor"        => $this->limpiar($_POST['motor'] ?? ''),
            "anio"         => $this->limpiar($_POST['año'] ?? ''),
            "kilometraje"  => $this->limpiar($_POST['kilometraje'] ?? ''),
            "modelo"       => $_POST['modelos_idmodelos'] ?? '',
            "color"        => $_POST['colores_idcolores'] ?? '',
            "tipo"         => $_POST['tipo_vehiculos_idtipo_vehiculos'] ?? ''
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

        // 👉 devuelve el ID insertado
        $id_insertado = $vehiculo->agregar_vehiculo();

        if ($id_insertado) {
            $datos_nuevos = [
                'patente'                     => $datos['patente'],
                'chasis'                      => $datos['chasis'],
                'motor'                       => $datos['motor'],
                'anio'                        => $datos['anio'],
                'kilometraje'                 => $datos['kilometraje'],
                'modelos_idmodelos'           => $datos['modelo'],
                'colores_idcolores'           => $datos['color'],
                'tipo_vehiculos_idtipo_vehiculos' => $datos['tipo'],
            ];

            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'vehiculos',           // tabla
                $id_insertado,         // id_registro
                'Vehículos',           // módulo lógico
                'INSERT',              // acción
                null,                  // datos_anteriores
                $datos_nuevos,         // datos_nuevos
                'Alta de vehículo',    // descripción
                $_SESSION['idusuarios'] ?? null  // 👈 idusuario
            );
        }

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

        // 1) Datos anteriores (DB)
        $vehiculo_model = new Vehiculos();
        $vehiculo_anterior = $vehiculo_model->traer_por_id($idvehiculos);

        if (!$vehiculo_anterior) {
            header("location: ../../index.php?page=listado_vehiculos&mensaje=Vehículo no encontrado.&status=error");
            exit;
        }

        // 2) Datos nuevos (POST)
        $datos = [
            "patente"      => $this->limpiar($_POST['patente'] ?? ''),
            "chasis"       => $this->limpiar($_POST['chasis'] ?? ''),
            "motor"        => $this->limpiar($_POST['motor'] ?? ''),
            "anio"         => $this->limpiar($_POST['año'] ?? ''),
            "kilometraje"  => $this->limpiar($_POST['kilometraje'] ?? ''),
            "modelo"       => $_POST['modelos_idmodelos'] ?? '',
            "color"        => $_POST['colores_idcolores'] ?? '',
            "tipo"         => $_POST['tipo_vehiculos_idtipo_vehiculos'] ?? ''
        ];

        if ($error = $this->validar_campos($datos)) {
            header("location: ../../index.php?page=registrar_vehiculos&idvehiculos=$idvehiculos&mensaje=$error&status=error");
            exit;
        }

        // 3) Ejecutar UPDATE
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

        $ok = $vehiculo->actualizar_vehiculo(); // tu método ya devuelve true/false

        // 4) Registrar auditoría si el UPDATE fue OK
        if ($ok) {

            // 🔹 Datos anteriores "planchados" (lo que había en la DB)
            $datos_anteriores = [
                'vehiculo' => [
                    'idvehiculos' => (int)$vehiculo_anterior['idvehiculos'],
                    'patente'     => $vehiculo_anterior['patente'] ?? null,
                    'anio'        => $vehiculo_anterior['anio'] ?? null,
                    'kilometraje' => $vehiculo_anterior['kilometraje'] ?? null,
                    'chasis'      => $vehiculo_anterior['chasis'] ?? null,
                    'motor'       => $vehiculo_anterior['motor'] ?? null,
                ],
                'marca' => [
                    'id'     => $vehiculo_anterior['idmarcas'] ?? null,
                    'nombre' => $vehiculo_anterior['nombre_marca'] ?? null,
                ],
                'modelo' => [
                    'id'     => $vehiculo_anterior['modelos_idmodelos'] ?? $vehiculo_anterior['idmodelos'] ?? null,
                    'nombre' => $vehiculo_anterior['nombre_modelo'] ?? null,
                ],
                'color' => [
                    'id'     => $vehiculo_anterior['colores_idcolores'] ?? $vehiculo_anterior['idcolores'] ?? null,
                    'nombre' => $vehiculo_anterior['nombre_color'] ?? null,
                ],
                'tipo_vehiculo' => [
                    'id'     => $vehiculo_anterior['tipo_vehiculos_idtipo_vehiculos'] ?? $vehiculo_anterior['idtipo_vehiculos'] ?? null,
                    'nombre' => $vehiculo_anterior['nombre_tipo_vehiculo'] ?? $vehiculo_anterior['nombre_tipo'] ?? null,
                ],
            ];

            // 🔹 Datos nuevos "planchados" (lo que se guardó desde el formulario)
            $datos_nuevos = [
                'vehiculo' => [
                    'idvehiculos' => (int)$idvehiculos,
                    'patente'     => $datos['patente'],
                    'anio'        => $datos['anio'],
                    'kilometraje' => $datos['kilometraje'],
                    'chasis'      => $datos['chasis'],
                    'motor'       => $datos['motor'],
                ],
                // Como en el POST solo vienen los IDs, dejamos los nombres de marca/modelo/color
                // con los mismos que tenía antes (si cambiaste de modelo/color, igual se verá
                // el nombre anterior vs ID nuevo, pero ya es más legible que solo IDs sueltos).
                'marca' => [
                    'id'     => $vehiculo_anterior['idmarcas'] ?? null,
                    'nombre' => $vehiculo_anterior['nombre_marca'] ?? null,
                ],
                'modelo' => [
                    'id'     => $datos['modelo'],
                    'nombre' => $vehiculo_anterior['nombre_modelo'] ?? null,
                ],
                'color' => [
                    'id'     => $datos['color'],
                    'nombre' => $vehiculo_anterior['nombre_color'] ?? null,
                ],
                'tipo_vehiculo' => [
                    'id'     => $datos['tipo'],
                    'nombre' => $vehiculo_anterior['nombre_tipo_vehiculo'] ?? $vehiculo_anterior['nombre_tipo'] ?? null,
                ],
            ];

            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'vehiculos',
                $idvehiculos,
                'Vehículos',
                'UPDATE',
                $datos_anteriores,
                $datos_nuevos,
                'Modificación de datos del vehículo',
                $_SESSION['idusuarios'] ?? null
            );

        }

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

        // 1) Datos antes de "eliminar"
        $vehiculo_model = new Vehiculos();
        $vehiculo_anterior = $vehiculo_model->traer_por_id($id);

        $vehiculo = new Vehiculos();
        $vehiculo->setIdvehiculos($id);
        $ok = $vehiculo->eliminar_vehiculo(); // UPDATE activo_vehiculo = 0

        if ($ok && $vehiculo_anterior) {
            $datos_anteriores = [
                'patente'                     => $vehiculo_anterior['patente'] ?? null,
                'chasis'                      => $vehiculo_anterior['chasis'] ?? null,
                'motor'                       => $vehiculo_anterior['motor'] ?? null,
                'anio'                        => $vehiculo_anterior['anio'] ?? null,
                'kilometraje'                 => $vehiculo_anterior['kilometraje'] ?? null,
                'modelos_idmodelos'           => $vehiculo_anterior['modelos_idmodelos'] ?? null,
                'colores_idcolores'           => $vehiculo_anterior['colores_idcolores'] ?? null,
                'tipo_vehiculos_idtipo_vehiculos' => $vehiculo_anterior['tipo_vehiculos_idtipo_vehiculos'] ?? null,
                'activo_vehiculo'             => $vehiculo_anterior['activo_vehiculo'] ?? null,
            ];

            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'vehiculos',
                $id,
                'Vehículos',
                'DELETE',                      // borrado lógico
                $datos_anteriores,
                null,
                'Baja lógica de vehículo (activo_vehiculo = 0)',
                $_SESSION['idusuarios'] ?? null  // 👈 idusuario
            );
        }

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

