<?php
require_once('../modelos/usuarios.php');
require_once('../modelos/perfiles.php');

session_start();

if(isset($_POST['action'])){
    if($_POST['action'] == 'login'){
        $login_controlador = new LoginControlador();
        $login_controlador->ingresar();
    }
    if($_POST['action'] == 'registrarse'){
        $login_controlador = new LoginControlador();
        $login_controlador->registrarse();
    }
}

class LoginControlador {

    public function ingresar(){
        $usuario = new Usuario();
        $perfil = new Perfil();
        $usuario->setUsername($_POST['username']);
        $resultado = $usuario->validar_usuario();
        if($resultado->num_rows > 0){
            while($row = $resultado->fetch_assoc()){
                if(password_verify($_POST['password'], $row['password'])){
                    $_SESSION['username'] = $row['username']; 
                    $_SESSION['idusuarios'] = $row['idusuarios'];

                    // obtiene el perfil inicio
                    $resultado_perfiles = $perfil->traer_perfil($row['perfiles_idperfiles']);
                    while($row_perfiles = $resultado_perfiles->fetch_assoc()){
                        $_SESSION['idperfiles'] = $row['perfiles_idperfiles'];
                        $_SESSION['descripcion'] = $row_perfiles['descripcion'];
                    }
                    // obtiene el perfil fin

                    if(password_verify($row['username'], $row['password'])){
                        // es un usuario nuevo
                        return header('location: ../index.php?page=cambiar_password&mensaje=Usuario Nuevo - Cambiar la constraseña, Porfavor.&status=danger');
                    }
                    
                    header('location: ../index.php?page=bienvenida');
                    exit();
                }else{
                    header('location: ../index.php?page=login&mensaje=Usuario o Password no correcto.&status=danger');
                }
            }
        }else{
            header('location: ../index.php?page=login&mensaje=Usuario o Password no correcto.&status=danger');
        }
    }

    public function registrarse(){

        if(empty($_POST['username'] || empty($_POST['email'] || empty($_POST['perfiles_idperfiles'])))){
            header('location: ../../index.php&mensaje=Todos los datos son obligarios.&status=danger');
        }
        $usuarios = new Usuario();
        $usuarios->setUsername($_POST['username']);
        $usuarios->setEmail($_POST['email']);
        $usuarios->setPassword($_POST['username']);
        $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
        $usuarios->guardar();
        header('location: ./../index.php&mensaje=Usuario creado correctamente.&status=success');
    }
}

?>