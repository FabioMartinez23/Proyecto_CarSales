<?php

ini_set('display_errors', 1);
require_once('../../modelos/vehiculos.php');
$form_data_patente = array();

if(isset($_POST['action'])){
    if($_POST['action'] == 'ajax'){
        $patente = new Vehiculos();
        $patente->setPatente($_POST['patente']);
        $resultado = $patente->validar_patente();
            if($resultado->num_rows > 0){

                $form_data_patente['data'] = 'error';
            }else{
                
                $form_data_patente['data'] = 'success';
            }
            echo json_encode($form_data_patente);
    } 
}

?>