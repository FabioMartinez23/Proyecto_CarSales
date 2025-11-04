<?php
use PHPMailer\PHPMailer\PHPMailer;
require '../vendor/autoload.php';
use Pusher\Pusher;
require_once('../modelos/conexion.php');
require_once('../modelos/usuarios.php');
require_once('../modelos/perfiles.php');
require_once('../modelos/personas.php');
require_once('../modelos/vehiculos.php');

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
                if ($row['verificado_email'] == 0) {
                    header('location: ../index.php?page=login&mensaje=Debes verificar tu correo antes de ingresar.&status=warning');
                    return;
                }
                if(password_verify($_POST['password'], $row['password'])){
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['idusuarios'] = $row['idusuarios'];
                    $_SESSION['Personas_idPersonas'] = $row['personas_idpersonas'];

                    // obtiene el perfil inicio
                    $resultado_perfiles = $perfil->traer_perfil($row['perfiles_idperfiles']);
                    while($row_perfiles = $resultado_perfiles->fetch_assoc()){
                        $_SESSION['idperfiles'] = $row['perfiles_idperfiles'];
                        $_SESSION['descripcion'] = $row_perfiles['descripcion'];
                        $vehiculo = new Vehiculos();
                        $_SESSION['vehiculos_faltantes'] = $vehiculo->contarVehiculosConFaltante();
                    }
                    // obtiene el perfil fin

                    if(password_verify($row['username'], $row['password'])){
                        // es un usuario nuevo
                        return header('location: ../index.php?page=cambiar_password&mensaje=Usuario Nuevo - Cambiar la constraseña, Porfavor.&status=warning');
                    }
                    // ✅ Nueva validación: verificar perfil completo
                    if ($row['verificado_perfil'] == 0) {
                        return header('location: ../index.php?page=form_mis_datos&mensaje=Debe completar sus datos antes de continuar.&status=warning');
                    }

                    // Si todo OK → bienvenida
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

    public function registrarse() {
        if (
            empty($_POST['username']) || empty($_POST['email']) || 
            empty($_POST['perfiles_idperfiles']) || empty($_POST['nombre']) || 
            empty($_POST['apellido']) || empty($_POST['fecha_nacimiento']) || 
            empty($_POST['tipo_sexo_idtipo_sexo'])
        ) {
            header('location: ../index.php?page=registrarse&mensaje=Todos los datos son obligatorios.&status=warning');
            return;
        }

        // Validar edad mínima
        $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y;

        if ($edad < 18) {
            header('location: ../index.php?page=registrarse&mensaje=Debes ser mayor de 18 años para registrarte.&status=warning');
            return;
        }

        // Crear persona
        $persona = new Persona();
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->agregar_persona();
        $personas_idpersonas = $persona->getIdpersonas();

        if (!$personas_idpersonas) {
            header('location: ../index.php?page=registrarse&mensaje=Error al cargar Persona.&status=error');
            return;
        }

        // Crear usuario
        $usuarios = new Usuario();
        $usuarios->setUsername($_POST['username']);
        $usuarios->setEmail($_POST['email']);
        $usuarios->setPassword($_POST['username']); // Cambiar luego a algo más seguro
        $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
        $usuarios->setPersonas_idpersonas($personas_idpersonas);
        $usuarios->guardar();

        // Obtener ID del usuario recién creado
        $id_usuario = $usuarios->obtener_id_por_username($_POST['username']); // Método que deberías tener en Usuario.php

        // -------------------- Pusher --------------------
        $options = [
            'cluster' => 'us2',
            'useTLS' => true
        ];

        $pusher = new Pusher(
            'c28c5dc6a68db65e2590',   // key
            '90c5eb55b40bc7f10daa',   // secret
            '2054403',                 // app_id
            $options
        );

        $data = [
            'tipo' => 'cliente', // Más genérico y consistente
            'mensaje' => 'Se registró un nuevo cliente: ' . $_POST['username'],
            'detalle' => [
                'idusuario' => $id_usuario,
                'nombre'    => $_POST['nombre'],
                'apellido'  => $_POST['apellido'],
                'perfil'    => $_POST['perfiles_idperfiles']
            ],
            'fecha' => date('Y-m-d H:i:s')
        ];

        $pusher->trigger('notificaciones', 'nuevo-evento', $data);
        // --------------------------------------------------

        if (!$id_usuario) {
            header('location: ../index.php?page=registrarse&mensaje=Error al registrar usuario.&status=error');
            return;
        }

        // Generar token y guardarlo
        require_once('../modelos/conexion.php');
        $conexion = new Conexion();
        $token = bin2hex(random_bytes(32));
        $fechaExpiracion = date('Y-m-d H:i:s', strtotime('+1 day'));

        $conexion->consultar("INSERT INTO tokens_recuperacion (token,fecha_expiracion, Usuarios_idusuarios)
                            VALUES ('$token','$fechaExpiracion', $id_usuario)");

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'famartinez2611994@gmail.com';
            $mail->Password = 'axsr vtnf hguy jwtq'; // Usar app password en producción
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom('famartinez2611994@gmail.com', 'CarSales - Verificación');
            $mail->addAddress($_POST['email']);

            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu correo electrónico';
            $mail->Body = "
                <h3>¡Bienvenido/a a CarSales!</h3>
                <p>Para activar tu cuenta, por favor hacé clic en el siguiente enlace:</p>
                <a href='http://localhost/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/index.php?page=verificar_email&token=$token'>
                    Verificar Email
                </a>
                <p>Este enlace expirará en 24 horas.</p>
            ";

            $mail->send();

            header('location: ../index.php?page=login&mensaje=Usuario registrado correctamente. Revisa tu email para verificar tu cuenta.&status=success');
        } catch (Exception $e) {
            header('location: ../index.php?page=login&mensaje=Error al enviar correo de verificación.&status=error');
        }
    }
}

?>