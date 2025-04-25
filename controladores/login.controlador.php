<?php
require_once('../modelos/usuarios.php');
require_once('../modelos/perfiles.php');
require_once('../modelos/personas.php');

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
                    $_SESSION['Personas_idPersonas'] = $row['personas_idpersonas'];

                    // obtiene el perfil inicio
                    $resultado_perfiles = $perfil->traer_perfil($row['perfiles_idperfiles']);
                    while($row_perfiles = $resultado_perfiles->fetch_assoc()){
                        $_SESSION['idperfiles'] = $row['perfiles_idperfiles'];
                        $_SESSION['descripcion'] = $row_perfiles['descripcion'];

                    }
                    // obtiene el perfil fin

                    if(password_verify($row['username'], $row['password'])){
                        // es un usuario nuevo
                        return header('location: ../index.php?page=cambiar_password&mensaje=Usuario Nuevo - Cambiar la constraseña, Porfavor.&status=warning');
                    }
                    
                    header('location: ../index.php?page=bienvenida');
                    exit();
                }else{
                    header('location: ../index.php?page=login&mensaje=Usuario o Password no correcto.&status=error');
                }
            }
        }else{
            header('location: ../index.php?page=login&mensaje=Usuario o Password no correcto.&status=error');
        }
    }

    public function registrarse(){

        if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['perfiles_idperfiles']) || empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['fecha_nacimiento']) || empty($_POST['tipo_sexo_idtipo_sexo'])){
            header('location: ../index.php?page=registrarse&mensaje=Todos los datos son obligatorios.&status=warning');
            return;
        }

            // Validar que el usuario tenga al menos 18 años
            $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nacimiento)->y; // Calcula la edad en años
    
            if ($edad < 18) {
                header('location: ../index.php?page=registrarse&mensaje=Debes ser mayor de 18 años para registrarte.&status=warning');
                return;
            }
            
        $persona = new Persona();
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->agregar_persona();
        $personas_idpersonas = $persona->getIdpersonas();

        if (!$personas_idpersonas){
            header('location: ../index.php?page=registrarse&mensaje=Error al cargar Persona.&status=error');
            return;
        }else{
            $usuarios = new Usuario();
            $usuarios->setUsername($_POST['username']);
            $usuarios->setEmail($_POST['email']);
            $usuarios->setPassword($_POST['username']);
            $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
            $usuarios->setPersonas_idpersonas($personas_idpersonas);
            $usuarios->guardar();
            header('location: ../index.php?page=login&mensaje=Usuario registrado correctamente.&status=success');
        }
    }
}

?>