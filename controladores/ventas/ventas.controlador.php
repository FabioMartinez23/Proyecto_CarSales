<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../modelos/vehiculos.php');
require_once('../../modelos/precios_vehiculos.php');
require_once('../../modelos/ficha_tecnica.php');
require_once('../../modelos/personas.php');
require_once('../../modelos/domicilio.php');
require_once('../../modelos/documentos.php');
require_once('../../modelos/usuarios.php');
require_once('../../modelos/contactos.php');

header('Content-Type: application/json'); // Establecer el tipo de contenido a JSON

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'consultar_usuario') {
        $operacion_controlador = new OperacionesControlador();
        $operacion_controlador->consultar_usuario();
    }
    if ($_POST['action'] == 'consultar_auto') {
        $operacion_controlador = new OperacionesControlador();
        $operacion_controlador->consultar_auto();
    }
    if ($_POST['action'] == 'consultar_ficha_tecnica') {
        $operacion_controlador = new OperacionesControlador();
        $operacion_controlador->consultar_ficha_tecnica();
    }
}


class OperacionesControlador {

    public function consultar_usuario() {
        $dni = $_POST['dni'] ?? ''; // Usar null coalescing para evitar notices
        $tipo_sexo = $_POST['tipo_sexo_idtipo_sexo'] ?? '';

        // Validar entradas
        if (empty($dni)) {
            echo json_encode(['error' => 'Debe ingresar un DNI.']);
            exit;
        }

        if (empty($tipo_sexo)) {
            echo json_encode(['error' => 'Debe ingresar un Tipo de Sexo.']);
            exit;
        }

        // Buscar persona por DNI y tipo_sexo
        $persona = new Persona();
        $resultado_buscar = $persona->traer_personas_por_dni_json($dni, $tipo_sexo);

        // Verificar si hay resultados
        if ($resultado_buscar) {
            $personas_idpersonas = $resultado_buscar['idpersonas'] ?? null; // Asegurarse de que existe la clave 'idpersonas'

            // Traer otros datos asociados solo si personas_idpersonas no es nulo
            if ($personas_idpersonas !== null) {
                $usuario = new Usuario();
                $result_usuarios = $usuario->traer_usuario_por_idpersona_json($personas_idpersonas);

                $contacto = new Contacto();
                $resultado_contacto = $contacto->consultar_contacto_json($personas_idpersonas);

                $domicilio = new Domicilios();
                $resultado_domicilio = $domicilio->consultar_domicilio_json($personas_idpersonas);

                $documento = new Documento();
                $result_documento = $documento->traer_documento_por_id_json($personas_idpersonas);

                // Preparar la respuesta en JSON
                $response = [
                    'persona' => $resultado_buscar,  // Ya es un array asociativo
                    'usuario' => $result_usuarios ?? null,  // Verificar si hay resultados
                    'contacto' => $resultado_contacto ?? null,  // Verificar si hay resultados
                    'domicilio' => $resultado_domicilio ?? null,  // Verificar si hay resultados
                    'documento' => $result_documento ?? null  // Verificar si hay resultados
                ];
                // Loguear el contenido del response
                error_log("Respuesta JSON: " . json_encode($response));

                echo json_encode($response);
            } else {
                echo json_encode(['error' => 'ID de persona no encontrado.']);
            }
        } else {
            ob_clean();
            echo json_encode(['error' => 'No se encontraron Clientes con ese DNI y Tipo de Sexo.']);
            exit;
        }
    }


        public function consultar_auto() {
            $patente = $_POST['patente'] ?? ''; // Evita notices si no se envía la patente

            if (empty($patente)) {
                echo json_encode(['error' => 'Debe ingresar una Patente.']);
                exit;
            }

            $vehiculo = new Vehiculos();
            $resultado_vehiculo = $vehiculo->traer_vehiculo_por_patente_ventas($patente);

            if ($resultado_vehiculo !== null) {
                $idvehiculos = $resultado_vehiculo['idvehiculos'] ?? null;

                $precio = new PrecioVehiculo();
                $resultado_precio = $precio->traer_los_vehiculos_con_precio_json($idvehiculos);

                $ficha_tecnica = new Fichas_Tenicas();
                $resultado_ficha = $ficha_tecnica->traer_fichas_tecnica_id($idvehiculos);

                $response = [
                    'vehiculo' => $resultado_vehiculo ?? null,
                    'precio' => $resultado_precio ?? null,
                    'ficha_tecnica' => $resultado_ficha ?? null  
                ];

                echo json_encode($response);
            } else {
                echo json_encode(['error' => 'El vehículo no está disponible o no tiene precio público.']);
            }
        }


        public function consultar_ficha_tecnica() {
            $id_vehiculo = $_POST['id_vehiculo'] ?? ''; // Asegúrate de que estás usando el nombre correcto
        
            // Verificar si el ID del vehículo está definido
            if (empty($id_vehiculo) || $id_vehiculo === 'undefined') {
                echo json_encode(['error' => 'ID de vehículo no válido.']);
                exit;
            }
        
            // Continuar con la lógica de tu consulta aquí...
            $ficha_tecnica = new Fichas_Tenicas();
            $resultado_ficha = $ficha_tecnica->traer_fichas_tecnica_id($id_vehiculo);
        
            // Verificar si se obtuvieron datos de la ficha técnica
            if ($resultado_ficha !== null) {
                // Preparar la respuesta en JSON
                $response = [
                    'ficha_tecnica' => $resultado_ficha ?? null  
                ];
                // Loguear el contenido del response
                error_log("Respuesta JSON: " . json_encode($response));
        
                echo json_encode($response);
            } else {
                ob_clean();
                echo json_encode(['error' => 'No se encontró la ficha técnica para este vehículo.']);
            }
        }        
    }



?>