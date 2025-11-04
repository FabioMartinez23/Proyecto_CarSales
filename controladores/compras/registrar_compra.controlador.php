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
require_once('../../modelos/documentaciones.php');
require_once('../../modelos/tablas_maestras/estado_vehiculo.php');
require_once('../../modelos/tablas_maestras/tipo_documentacion.php');
require_once('../../modelos/tablas_maestras/tipo_precio.php');

if (isset($_POST['action']) && $_POST['action'] == 'registrar_compra') {
    $controlador = new RegistrarCompraControlador();
    $controlador->registrar_compra();
}

class RegistrarCompraControlador {

    public function registrar_compra() {

        // 🔍 DEPURACIÓN (puede comentarse una vez verificado)
        // var_dump($_POST);
        // exit();

        // -------------------------------------------------------------
        // 1️⃣ VALIDAR EDAD
        // -------------------------------------------------------------
        $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y;

        if ($edad < 18) {
            header('location: ../../index.php?page=registrar_compras&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
            return;
        }

        // -------------------------------------------------------------
        // 2️⃣ PERSONA Y DATOS RELACIONADOS
        // -------------------------------------------------------------
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

            // Contacto
            $contacto = new Contacto();
            $contacto->setPersonas_idPersonas($personas_idpersonas);
            $contacto->setValor($_POST['contacto']);
            $contacto->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
            $contacto->agregar_contato();

            // Documento
            $documento = new Documento();
            $documento->setPersonas_idPersonas($personas_idpersonas);
            $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);
            $documento->setValor($_POST['documento']);
            $documento->agregar_documento();

            // Domicilio
            $domicilio = new Domicilios();
            $domicilio->setPersonas_idPersonas($personas_idpersonas);
            $domicilio->setDescripcion($_POST['domicilio']);
            $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
            $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
            $domicilio->agregar_domicilio();

        } else {
            $personas_idpersonas = $_POST['id_personas'];
        }

        // -------------------------------------------------------------
        // 3️⃣ VEHÍCULO
        // -------------------------------------------------------------
        $campos_obligatorios = ['patente', 'chasis', 'motor', 'año', 'kilometraje', 'modelos_idmodelos', 'colores_idcolores', 'tipo_vehiculos_idtipo_vehiculos'];

        foreach ($campos_obligatorios as $campo) {
            if (empty($_POST[$campo])) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Todos los datos del vehículo son obligatorios.&status=error');
                return;
            }
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

        // Titular del vehículo
        $titular = new Titular_Vehiculo();
        $titular->setVehiculos_idvehiculos($vehiculos_idvehiculos);
        $titular->setPersonas_idpersonas($personas_idpersonas);

        if (!$titular->agregar_titular()) {
            header('location: ../../index.php?page=registrar_compras&mensaje=Error al agregar titular al vehículo.&status=error');
            return;
        }

        $titular_vehiculo = $titular->getIdtitular_vehiculo();

        // -------------------------------------------------------------
        // 4️⃣ FICHA TÉCNICA
        // -------------------------------------------------------------
        if (empty($_POST['descripcion_carroceria']) || empty($_POST['descripcion_neumatico']) || empty($_POST['vencimiento_rto'])) {
            header('location: ../../index.php?page=registrar_compras&mensaje=Todos los datos de la ficha técnica son obligatorios.&status=error');
            exit();
        }

        $ficha_tecnica = new Fichas_Tenicas();
        $ficha_tecnica->setVehiculos_idvehiculos($vehiculos_idvehiculos);
        $ficha_tecnica->setCarroceria_idcarroceria($_POST['descripcion_carroceria']);
        $ficha_tecnica->setNeumaticos_idneumaticos($_POST['descripcion_neumatico']);
        $ficha_tecnica->setCristales_idcristales($_POST['descripcion_cristales']);
        $ficha_tecnica->setVencimiento_RTO($_POST['vencimiento_rto']);
        $ficha_tecnica->setVencimiento_bateria($_POST['vencimiento_bateria']);
        $ficha_tecnica->setVencimiento_service($_POST['vencimiento_service']);

        if (!$ficha_tecnica->agregar_ficha_tecnica()) {
            header('location: ../../index.php?page=registrar_compras&mensaje=Error al agregar ficha técnica al vehículo.&status=error');
            return;
        }

        // -------------------------------------------------------------
        // 5️⃣ DOCUMENTACIÓN
        // -------------------------------------------------------------
        $documentacion = new Documentacion();
        $ids = $_POST['documentacion_ids'] ?? '';  // "1,3,5"
        $id_array = array_filter(explode(',', $ids));

        if (empty($id_array)) {
            header('location: ../../index.php?page=registrar_compras&mensaje=Debe seleccionar al menos un documento.&status=error');
            return;
        }

        // 🔒 Validar documentos obligatorios
        $obligatorios = [2, 6]; // Ejemplo: 2=Formulario 08, 6=Cédula Vehicular
        $faltantes = array_diff($obligatorios, $id_array);

        if (!empty($faltantes)) {
            header('location: ../../index.php?page=registrar_compras&mensaje=Faltan documentos obligatorios (Formulario 08 y Cédula Vehicular).&status=error');
            return;
        }

        // Insertamos cada documento físico
        $documentacion->setVehiculos_idvehiculos($vehiculos_idvehiculos);
        $documentacion->setEstado_doc(1);

        foreach ($id_array as $id) {
            $documentacion->setTipo_documentacion_idtipo_documentacion($id);
            if (!$documentacion->agregar_doc_fisica()) {
                header('location: ../../index.php?page=registrar_compras&mensaje=Error al guardar la documentación.&status=error');
                return;
            }
        }

        // -------------------------------------------------------------
        // 🔹 ACTUALIZAR ESTADO DEL VEHÍCULO
        // -------------------------------------------------------------
        $estadoVehiculo = new Estado_Vehiculo();
        $vehiculo = new Vehiculos();
        $tipo_documentacion = new Tipo_Documentacion();

        // ✅ Total de tipos posibles (documentos requeridos)
        $documentos_requeridos = $tipo_documentacion->traer_todos_los_tipos();
        $total_tipos = 0;

        if (!empty($documentos_requeridos) && is_array($documentos_requeridos)) {
            $total_tipos = count($documentos_requeridos);
        }

        // ✅ Cantidad de documentos físicos registrados
        $documentos_fisicos = count($id_array);

        // ✅ Cantidad de documentos no digitalizados
        $no_digitalizados = $documentacion->contar_no_digitalizados($vehiculos_idvehiculos);

        // 🔍 Determinar estado con prioridad lógica
        if ($documentos_fisicos < $total_tipos) {
            // 🔴 Faltan documentos físicos
            $id_estado = $estadoVehiculo->obtenerIdPorEstado('falta_documento');
        } elseif ($documentos_fisicos === $total_tipos && $no_digitalizados > 0) {
            // 🟠 Todos los físicos están, pero falta digitalizar
            $id_estado = $estadoVehiculo->obtenerIdPorEstado('falta_digitalizacion');
        } else {
            // 🟢 Todo completo
            $id_estado = $estadoVehiculo->obtenerIdPorEstado('disponible');
        }

        // ✅ Actualizar el estado del vehículo
        if ($id_estado) {
            $vehiculo->actualizar_estado($vehiculos_idvehiculos, $id_estado);
        }


        // -------------------------------------------------------------
        // 6️⃣ PRECIO
        // -------------------------------------------------------------
        $tipo_precio = new Tipo_Precios();
        $resultado = $tipo_precio->mostrar_tipo_precio(); // Devuelve un mysqli_result

        $precio_tipo_array = [];
        while ($row = $resultado->fetch_assoc()) {
            $precio_tipo_array[$row['descripcion']] = $row['idtipo_precios'];
        }

        // Verificar que exista el tipo de precio 'tomado'
        if (!isset($precio_tipo_array['tomado'])) {
            header('location: ../../index.php?page=registrar_compras&mensaje=No se encontró el tipo de precio "tomado".&status=error');
            return;
        }

        $precio = new PrecioVehiculo();
        $precio->setPrecio($_POST['precio']);
        $precio->setVehiculos_idvehiculos($vehiculos_idvehiculos);
        $precio->setTipo_precios_idtipo_precios($precio_tipo_array['tomado']);

        if (!$precio->actualizar_precio()) {
            header('location: ../../index.php?page=registrar_compras&mensaje=Error al agregar precio al vehículo.&status=error');
            return;
        }

        // -------------------------------------------------------------
        // 7️⃣ REGISTRAR COMPRA
        // -------------------------------------------------------------
        $registrar_compra = new ComprarVehiculo();
        $registrar_compra->setDescripcion($_POST['observaciones']);
        $registrar_compra->setTipo_pago_idtipo_pago($_POST['tipo_pago']);
        $registrar_compra->setVehiculo_idvehiculo($vehiculos_idvehiculos);
        $registrar_compra->setTitular_vehiculo_idtitular_vehiculo($titular_vehiculo);
        $registrar_compra->setEmpleados_idempleados($_POST['idempleado']);

        if (!$registrar_compra->agregar_compra()) {
            header('location: ../../index.php?page=registrar_compras&mensaje=Error al registrar el vehiculo.&status=error');
            return;
        }

        // -------------------------------------------------------------
        // ✅ ÉXITO
        // -------------------------------------------------------------
        header('location: ../../index.php?page=listado_compras&mensaje=Vehiculo registrado correctamente.&status=success');
    }
}



