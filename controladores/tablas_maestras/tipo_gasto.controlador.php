<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/tipo_gasto.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    $tipo_gasto = new Tipo_Gasto();

    switch ($action) {

        case 'guardar':
            $tipo_gasto->setDescripcion($_POST['descripcion'] ?? '');
            $tipo_gasto->setAplica_en($_POST['aplica_en'] ?? 'manual');
            $tipo_gasto->setModo_calculo($_POST['modo_calculo'] ?? 'monto_fijo');
            $tipo_gasto->setValor_calculo($_POST['valor_calculo'] ?? 0);

            $tipo_gasto->agregar_tipo_gasto();
            header('Location: ../../vistas/tablas_maestras/form_tipo_gasto.php?msg=creado');
            exit;
        
        case 'modificar':
            $tipo_gasto->setIdtipo_gasto($_POST['idtipo_gasto'] ?? 0);
            $tipo_gasto->setDescripcion($_POST['descripcion'] ?? '');
            $tipo_gasto->setAplica_en($_POST['aplica_en'] ?? 'manual');
            $tipo_gasto->setModo_calculo($_POST['modo_calculo'] ?? 'monto_fijo');
            $tipo_gasto->setValor_calculo($_POST['valor_calculo'] ?? 0);

            $tipo_gasto->actualizar_tipo_gasto();
            header('Location: ../../vistas/tablas_maestras/form_tipo_gasto.php?msg=modificado');
            exit;

        case 'eliminar':
            $tipo_gasto->setIdtipo_gasto($_POST['idtipo_gasto'] ?? 0);
            $tipo_gasto->eliminar_tipo_gasto();
            header('Location: ../../vistas/tablas_maestras/form_tipo_gasto.php?msg=eliminado');
            exit;

        default:
            header('Location: ../../vistas/tablas_maestras/form_tipo_gasto.php');
            exit;
    }
} else {
    header('Location: ../../vistas/tablas_maestras/form_tipo_gasto.php');
    exit;
}
