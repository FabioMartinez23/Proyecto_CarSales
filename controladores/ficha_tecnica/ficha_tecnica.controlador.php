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
        if (empty($_POST['descripcion_carroceria']) || empty($_POST['descripcion_neumatico']) || empty($_POST['vencimiento_rto']) || empty($_POST['vehiculos_idvehiculos'])) {
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Todos los datos obligatorios deben estar completos.&status=error');
            exit();
        }

        $ficha_tecnica = new Fichas_Tenicas();

        // Seteamos valores
        $ficha_tecnica->setCarroceria_idcarroceria($_POST['descripcion_carroceria']);
        $ficha_tecnica->setNeumaticos_idneumaticos($_POST['descripcion_neumatico']);
        $ficha_tecnica->setCristales_idcristales($_POST['descripcion_cristales']);
        $ficha_tecnica->setVencimiento_RTO($_POST['vencimiento_rto']);
        $ficha_tecnica->setVencimiento_bateria($_POST['vencimiento_bateria']);
        $ficha_tecnica->setVencimiento_service($_POST['vencimiento_service']);
        $ficha_tecnica->setVehiculos_idvehiculos($_POST['vehiculos_idvehiculos']);

        $ficha_tecnica->setForm_08(isset($_POST['switch_08']) ? 1 : 0);
        $ficha_tecnica->setForm_12(isset($_POST['switch_12']) ? 1 : 0);
        $ficha_tecnica->setTitulo_vehiculo(isset($_POST['switch_titulo']) ? 1 : 0);
        $ficha_tecnica->setCedula_vehiculo(isset($_POST['switch_cedula']) ? 1 : 0);
        $ficha_tecnica->setSeguro(isset($_POST['switch_seguro']) ? 1 : 0);
        $ficha_tecnica->setMunicipalidad(isset($_POST['switch_municipalidad']) ? 1 : 0);
        $ficha_tecnica->setInforme_dominio(isset($_POST['switch_dominio']) ? 1 : 0);
        $ficha_tecnica->setForm_13i(isset($_POST['switch_multas']) ? 1 : 0);
        $ficha_tecnica->setPrenda(isset($_POST['switch_prenda']) ? 1 : 0);

        // 🔹 Verificamos si ya existe ficha técnica para ese vehículo
        $existe = $ficha_tecnica->buscar_por_vehiculo($_POST['vehiculos_idvehiculos']);

        if ($existe) {
            // Si ya existe → actualizar
            $ficha_tecnica->actualizar_ficha_tecnica();
            $mensaje = "Ficha Técnica actualizada correctamente.";
        } else {
            // Si no existe → insertar nueva
            $ficha_tecnica->agregar_ficha_tecnica();
            $mensaje = "Ficha Técnica registrada correctamente.";
        }
        if($_POST['listado'] == 'falta_documentacion'){
            header("location: ../../index.php?page=listado_falta_documentacion&mensaje=$mensaje&status=success");
            exit();
        } else {
            header("location: ../../index.php?page=listado_vehiculos&mensaje=$mensaje&status=success");
            exit();
        }
    }

}
