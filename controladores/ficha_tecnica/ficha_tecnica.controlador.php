<?php

ini_set('display_errors', 1);
require_once('../../modelos/ficha_tecnica.php');

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'guardar') {
        $ficha_tecnica_controlador = new Ficha_TecnicaControlador();
        $ficha_tecnica_controlador->guardar();
    }
}

class Ficha_TecnicaControlador {

    public function guardar() {

        // Validamos los campos obligatorios
        if (empty($_POST['descripcion_carroceria']) || empty($_POST['descripcion_neumatico']) || empty($_POST['vencimiento_rto']) || empty($_POST['vehiculos_idvehiculos'])) {
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Todos los datos obligatorios deben estar completos.&status=error');
            exit();
        }

        // Instanciamos el modelo Fichas_Tecnicas (corregir nombre según tu modelo)
        $ficha_tecnica = new Fichas_Tenicas();

        // Asignación de los valores de los campos al modelo
        $ficha_tecnica->setDescripcion_carroceria($_POST['descripcion_carroceria']);
        $ficha_tecnica->setDescripcion_neumatico($_POST['descripcion_neumatico']);
        $ficha_tecnica->setVencimiento_RTO($_POST['vencimiento_rto']);
        $ficha_tecnica->setVencimiento_bateria($_POST['vencimiento_bateria']);
        $ficha_tecnica->setVencimiento_service($_POST['vencimiento_service']);
        $ficha_tecnica->setVehiculos_idvehiculos($_POST['vehiculos_idvehiculos']);
        
        // Checkboxes - validamos si están seleccionados, si no lo están, se asigna valor 0
        $ficha_tecnica->setForm_08(isset($_POST['switch_08']) ? 1 : 0);
        $ficha_tecnica->setForm_12(isset($_POST['switch_12']) ? 1 : 0);
        $ficha_tecnica->setTitulo_vehiculo(isset($_POST['switch_titulo']) ? 1 : 0);
        $ficha_tecnica->setCedula_vehiculo(isset($_POST['switch_cedula']) ? 1 : 0);
        $ficha_tecnica->setSeguro(isset($_POST['switch_seguro']) ? 1 : 0);
        $ficha_tecnica->setMunicipalidad(isset($_POST['switch_municipalidad']) ? 1 : 0);
        $ficha_tecnica->setInforme_dominio(isset($_POST['switch_dominio']) ? 1 : 0);
        $ficha_tecnica->setForm_13i(isset($_POST['switch_multas']) ? 1 : 0);
        $ficha_tecnica->setPrenda(isset($_POST['switch_prenda']) ? 1 : 0);

        // Guardar la ficha técnica en la base de datos
        $ficha_tecnica->agregar_ficha_tecnica();

        // Redirigir al listado con mensaje de éxito
        header('location: ../../index.php?page=listado_ficha_tecnica&mensaje=Ficha Técnica registrada correctamente.&status=success');
        exit();
    }
}
