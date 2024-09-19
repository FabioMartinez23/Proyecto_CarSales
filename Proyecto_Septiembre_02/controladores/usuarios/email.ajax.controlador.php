<?php

ini_set('display_errors', 1);
require_once('../../modelos/usuarios.php');
$form_data_email = array();

if(isset($_POST['action'])){
    if($_POST['action'] == 'ajax'){
        $usuario = new Usuario();
        $usuario->setEmail($_POST['email']);
        $usuario->validar_email();
        $resultado = $usuario->validar_email();
            if($resultado->num_rows > 0){
                //existe ese usuario
                $form_data_email['data'] = 'error';
            }else{
                //no existe ese usuario
                $form_data_email['data'] = 'success';
            }
            echo json_encode($form_data_email);
    } 
}

?>