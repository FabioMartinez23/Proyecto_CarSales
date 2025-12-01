<?php
ini_set('display_errors', 1);
require_once('../../modelos/ficha_tecnica.php');

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'guardar') {
        $ficha_tecnica_controlador = new Ficha_TecnicaControlador();
        $ficha_tecnica_controlador->guardar();
    }
        if ($_POST['action'] == 'actualizar') {
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
