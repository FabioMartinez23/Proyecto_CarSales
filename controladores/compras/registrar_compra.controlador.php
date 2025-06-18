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
require_once('../../modelos/comprar_vehiculos.php');
require_once('../../modelos/titular_vehiculo.php');

//header('Content-Type: application/json'); // Establecer el tipo de contenido a JSON

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'registrar_compra') {
        $venta_controlador = new RegistrarCompraControlador();
        $venta_controlador->registrar_compra();
    }
}


    class RegistrarCompraControlador {
        public function registrar_compra() {
            // Validar que el usuario tenga al menos 18 años
            $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nacimiento)->y;

            if ($edad < 18) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
                return;
            }

            // Si no hay id_personas, registrar nueva persona
            if (empty($_POST['id_personas'])) {
                $persona = new Persona();
                $persona->setNombre($_POST['nombre']);
                $persona->setApellido($_POST['apellido']);
                $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
                $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);

                if (!$persona->agregar_persona()) {
                    header('location: ../../index.php?page=registrar_compras&mensaje=Error al cargar la persona.&status=error');
                    return;
                }

                $personas_idpersonas = $persona->getIdpersonas();

                $contactos = new Contacto();
                $contactos->setPersonas_idPersonas($personas_idpersonas);
                $contactos->setValor($_POST['contacto']);
                $contactos->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
                $contactos->agregar_contato();

                $documento = new Documento();
                $documento->setPersonas_idPersonas($personas_idpersonas);
                $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);
                $documento->setValor($_POST['documento']);
                $documento->agregar_documento();

                $domicilio = new Domicilios();
                $domicilio->setPersonas_idPersonas($personas_idpersonas);
                $domicilio->setDescripcion($_POST['domicilio']);
                $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
                $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
                $domicilio->agregar_domicilio();
            } else {
                // Usar ID de persona existente
                $personas_idpersonas = $_POST['id_personas'];
            }
        
            // Validar datos del vehículo
            if (empty($_POST['patente']) || empty($_POST['chasis']) || empty($_POST['motor']) || empty($_POST['año']) || 
                empty($_POST['kilometraje']) || empty($_POST['modelos_idmodelos']) || empty($_POST['colores_idcolores']) || 
                empty($_POST['tipo_vehiculos_idtipo_vehiculos'])) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Todos los datos son obligatorios.&status=error');
                return;
            }
        
            $vehiculo = new Vehiculos();
            $vehiculo->setPatente($_POST['patente']);
            $vehiculo->setChasis($_POST['chasis']);
            $vehiculo->setMotor($_POST['motor']);
            $vehiculo->setAño($_POST['año']);
            $vehiculo->setKilometraje($_POST['kilometraje']);
            $vehiculo->setModelos_idmodelos($_POST['modelos_idmodelos']);
            $vehiculo->setColores_idcolores($_POST['colores_idcolores']);
            $vehiculo->setTipo_vehiculos_idtipo_vehiculos($_POST['tipo_vehiculos_idtipo_vehiculos']);
            
            if (!$vehiculo->agregar_vehiculo()) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Error al registrar el vehículo.&status=error');
                return;
            }
        
            $vehiculos_idvehiculos = $vehiculo->getIdvehiculos();
            $titular = new Titular_Vehiculo();
            $titular->setVehiculos_idvehiculos($vehiculos_idvehiculos);
            $titular->setPersonas_idpersonas($personas_idpersonas);
            
            if (!$titular->agregar_titular()) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Error al agregar titular al vehículo.&status=error');
                return;
            }

            $titular_vehiculo = $titular->getIdtitular_vehiculo();

            // Validamos los campos obligatorios
            if (empty($_POST['descripcion_carroceria']) || empty($_POST['descripcion_neumatico']) || empty($_POST['vencimiento_rto'])) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Todos los datos de la ficha tecnica son obligatorios.&status=error');
                exit();
            }

            // Instanciamos el modelo Fichas_Tecnicas
            $ficha_tecnica = new Fichas_Tenicas();
            // Asignamos el idvehiculo
            $ficha_tecnica->setVehiculos_idvehiculos($vehiculos_idvehiculos);

            // Asignación de los valores de los campos al modelo
            $ficha_tecnica->setCarroceria_idcarroceria($_POST['descripcion_carroceria']);
            $ficha_tecnica->setNeumaticos_idneumaticos($_POST['descripcion_neumatico']);
            $ficha_tecnica->setCristales_idcristales($_POST['descripcion_cristales']);
            $ficha_tecnica->setVencimiento_RTO($_POST['vencimiento_rto']);
            $ficha_tecnica->setVencimiento_bateria($_POST['vencimiento_bateria']);
            $ficha_tecnica->setVencimiento_service($_POST['vencimiento_service']);

            $ficha_tecnica->setForm_08(isset($_POST['switch_08']) && $_POST['switch_08'] == '1' ? 1 : 0);
            $ficha_tecnica->setForm_12(isset($_POST['switch_12']) && $_POST['switch_12'] == '1' ? 1 : 0);
            $ficha_tecnica->setTitulo_vehiculo(isset($_POST['switch_titulo']) && $_POST['switch_titulo'] == '1' ? 1 : 0);
            $ficha_tecnica->setCedula_vehiculo(isset($_POST['switch_cedula']) && $_POST['switch_cedula'] == '1' ? 1 : 0);
            $ficha_tecnica->setSeguro(isset($_POST['switch_seguro']) && $_POST['switch_seguro'] == '1' ? 1 : 0);
            $ficha_tecnica->setMunicipalidad(isset($_POST['switch_municipalidad']) && $_POST['switch_municipalidad'] == '1' ? 1 : 0);
            $ficha_tecnica->setInforme_dominio(isset($_POST['switch_dominio']) && $_POST['switch_dominio'] == '1' ? 1 : 0);
            $ficha_tecnica->setForm_13i(isset($_POST['switch_multas']) && $_POST['switch_multas'] == '1' ? 1 : 0);
            $ficha_tecnica->setPrenda(isset($_POST['switch_prenda']) && $_POST['switch_prenda'] == '1' ? 1 : 0);
            

            if (!$ficha_tecnica->agregar_ficha_tecnica()) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Error al agregar ficha técnica al vehículo.&status=error');
                return;
            }

            $precio = new PrecioVehiculo();
            $precio->setPrecio($_POST['precio']);
            $precio->setVehiculos_idvehiculos($vehiculos_idvehiculos);

            if (!$precio->actualizar_precio()) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Error al agregar precio al vehículo.&status=error');
                return;
            }
        
            // Registrar la compra
            $registrar_compra = new ComprarVehiculo();
            $registrar_compra->setDescripcion($_POST['observaciones']);
            $registrar_compra->setTipo_pago_idtipo_pago($_POST['tipo_pago']);
            $registrar_compra->setVehiculo_idvehiculo($vehiculos_idvehiculos);
            $registrar_compra->setTitular_vehiculo_idtitular_vehiculo($titular_vehiculo);
        
            if (!$registrar_compra->agregar_compra()) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Error al registrar la compra.&status=error');
                return;
            }
        
            // Si llegamos aquí, la compra fue exitosa
            header('location: ../../index.php?page=listado_compras&mensaje=Compra registrada correctamente.&status=success');
        }
        
    }


