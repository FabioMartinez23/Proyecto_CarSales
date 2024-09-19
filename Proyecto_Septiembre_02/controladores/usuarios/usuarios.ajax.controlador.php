<?php

ini_set('display_errors', 1);
require_once('../../modelos/usuarios.php');
$form_data_usuario = array();

if(isset($_POST['action'])){
    if($_POST['action'] == 'ajax'){
        $usuario = new Usuario();
        $usuario->setUsername($_POST['username']);
        $usuario->validar_usuario();
        $resultado = $usuario->validar_usuario();
            if($resultado->num_rows > 0){
                //existe ese usuario
                $form_data_usuario['data'] = 'error';
            }else{
                //no existe ese usuario
                $form_data_usuario['data'] = 'success';
            }
            echo json_encode($form_data_usuario);
    } 
}




?>