<?php

ini_set('display_errors', 1);
require_once('../../modelos/usuarios.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->guardar();
    }
    if ($_POST['action'] == 'eliminar'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->eliminar();
    }
    if ($_POST['action'] == 'resetear'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->resetear();
    }
    if ($_POST['action'] == 'cambiar_password'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->cambiar_password();
    }
}


class UsuarioControlador {

    public function guardar(){

        if(empty($_POST['username'] || empty($_POST['email'] || empty($_POST['perfiles_idperfiles'])))){
            header('location: ../../index.php?page=listado_usuarios&mensaje=Todos los datos obligarios.&status=danger');
        }
        $usuarios = new Usuario();
        $usuarios->setUsername($_POST['username']);
        $usuarios->setEmail($_POST['email']);
        $usuarios->setPassword($_POST['username']);
        $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
        $usuarios->guardar();
        header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario registrado correctamente.&status=success');
    }

    public function eliminar(){
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario->eliminar();
        header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario eliminado correctamente.&status=success');
    }

    public function cambiar_password() {
        if (!isset($_POST['password_actual'], $_POST['new_password'], $_POST['new_password_confirm'])) {
            header('location: ../../index.php?page=cambiar_password&mensaje=Todos los datos son obligatorios&status=danger');
            return;
        }
    
        if (empty($_POST['password_actual']) || empty($_POST['new_password']) || empty($_POST['new_password_confirm'])) {
            header('location: ../../index.php?page=cambiar_password&mensaje=Todos los datos son obligatorios&status=danger');
            return;
        }
    

        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_actual = $usuario->validar_usuario_por_id(); 
    

        if (!$usuario_actual) {
            header('location: ../../index.php?page=cambiar_password&mensaje=Usuario no encontrado&status=danger');
            return;
        }
    
        if (!password_verify($_POST['password_actual'], $usuario_actual['password']) && $_POST['password_actual'] != $usuario->getUsername()) {
            header('location: ../../index.php?page=cambiar_password&mensaje=La contraseña actual no coincide&status=danger');
            return;
        }
    
        if ($_POST['new_password'] != $_POST['new_password_confirm']) {
            header('location: ../../index.php?page=cambiar_password&mensaje=Las contraseñas nuevas no coinciden&status=danger');
            return;
        }
    
        $usuario->setPassword($_POST['new_password']);
        $usuario->cambiar_password();
    
        session_start();
        session_unset();
        session_destroy();
    
        header('location: ../../index.php?mensaje=Contraseña cambiada correctamente.&status=success');
    }
    
    public function resetear(){
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_info = $usuario->traer_usuario_id($_POST['idusuarios']);
        
        if ($usuario_info) {
            $nuevo_password = $usuario_info['username'];
            $usuario->setPassword($nuevo_password);
            $usuario->cambiar_password();
            // Redirigir con mensaje de éxito
            header('location: ../../index.php?page=listado_usuarios&mensaje=Contraseña reseteada correctamente.&status=success');
        } else {
            // Si no se encontró el usuario, redirigir con un mensaje de error
            header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario no encontrado.&status=danger');
        }
    }
    
}


?>