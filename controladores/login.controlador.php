<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

use Pusher\Pusher;
require_once('../modelos/conexion.php');
require_once('../modelos/usuarios.php');
require_once('../modelos/perfiles.php');
require_once('../modelos/personas.php');
require_once('../modelos/vehiculos.php');

session_start();

if (isset($_POST['action'])) {
    $login_controlador = new LoginControlador();

    if ($_POST['action'] == 'login') {
        $login_controlador->ingresar();
    }

    if ($_POST['action'] == 'registrarse') {
        $login_controlador->registrarse();
    }
}

class LoginControlador
{
    /* ============================
       LOGIN
    ============================ */
    public function ingresar()
    {
        $usuario = new Usuario();
        $perfil  = new Perfil();

        $usuario->setUsername($_POST['username'] ?? '');
        $resultado = $usuario->validar_usuario();

        if ($resultado->num_rows <= 0) {
            header('location: ../index.php?page=login&mensaje=Usuario o Password no correcto.&status=error');
            return;
        }

        while ($row = $resultado->fetch_assoc()) {

            // Debe tener email verificado
            if ($row['verificado_email'] == 0) {
                header('location: ../index.php?page=login&mensaje=Debes verificar tu correo antes de ingresar.&status=warning');
                return;
            }

            // Validar password
            if (!password_verify($_POST['password'], $row['password'])) {
                header('location: ../index.php?page=login&mensaje=Usuario o Password no correcto.&status=error');
                return;
            }

            // Login OK → cargo sesión
            $_SESSION['username']            = $row['username'];
            $_SESSION['idusuarios']          = $row['idusuarios'];
            $_SESSION['Personas_idPersonas'] = $row['personas_idpersonas'];

            // Perfil
            $resultado_perfiles = $perfil->traer_perfil($row['perfiles_idperfiles']);
            while ($row_perfiles = $resultado_perfiles->fetch_assoc()) {
                $_SESSION['idperfiles']  = $row['perfiles_idperfiles'];
                $_SESSION['descripcion'] = $row_perfiles['descripcion'];

                // Vehículos con faltante
                $vehiculo = new Vehiculos();
                $_SESSION['vehiculos_faltantes'] = $vehiculo->contarVehiculosConFaltante();
            }

            // Usuario nuevo: password = username
            if (password_verify($row['username'], $row['password'])) {
                header('location: ../index.php?page=cambiar_password&mensaje=Usuario Nuevo - Cambiar la constraseña, Por favor.&status=warning');
                return;
            }

            // Perfil sin completar
            if ($row['verificado_perfil'] == 0) {
                header('location: ../index.php?page=form_mis_datos&mensaje=Debe completar sus datos antes de continuar.&status=warning');
                return;
            }

            // Todo OK
            header('location: ../index.php?page=bienvenida');
            exit();
        }
    }

    /* ============================
       REGISTRO
    ============================ */
    public function registrarse()
    {
        // Validar campos obligatorios
        if (
            empty($_POST['username']) ||
            empty($_POST['email']) ||
            empty($_POST['perfiles_idperfiles']) ||
            empty($_POST['nombre']) ||
            empty($_POST['apellido']) ||
            empty($_POST['fecha_nacimiento']) ||
            empty($_POST['tipo_sexo_idtipo_sexo'])
        ) {
            header('location: ../index.php?page=registrarse&mensaje=Todos los datos son obligatorios.&status=warning');
            return;
        }

        // Validar mayoría de edad
        $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
        $hoy              = new DateTime();
        $edad             = $hoy->diff($fecha_nacimiento)->y;

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
        $usuarios->setPassword($_POST['username']); // por defecto = username
        $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
        $usuarios->setPersonas_idpersonas($personas_idpersonas);
        $usuarios->guardar();

        // ID del usuario recién creado
        $id_usuario = $usuarios->obtener_id_por_username($_POST['username']);
        if (!$id_usuario) {
            header('location: ../index.php?page=registrarse&mensaje=Error al registrar usuario.&status=error');
            return;
        }

        // -------------------- Pusher --------------------
        $options = [
            'cluster' => 'us2',
            'useTLS'  => true
        ];

        $pusher = new Pusher(
            'c28c5dc6a68db65e2590', // key
            '90c5eb55b40bc7f10daa', // secret
            '2054403',              // app_id
            $options
        );

        $data = [
            'tipo'    => 'cliente',
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
        // ------------------------------------------------

        // Enviar correo de verificación
        $okMail = $this->enviarCorreoVerificacion(
            $id_usuario,
            $_POST['email'],
            $_POST['nombre'] . ' ' . $_POST['apellido']
        );

        if ($okMail) {
            header('location: ../index.php?page=login&mensaje=Usuario registrado correctamente. Revisa tu email para verificar tu cuenta.&status=success');
        } else {
            // Usuario creado pero sin mail (no lo rompo)
            header('location: ../index.php?page=login&mensaje=Usuario registrado, pero hubo un problema al enviar el correo de verificación.&status=warning');
        }
    }

    /* ============================
       ENVÍO CORREO VERIFICACIÓN
    ============================ */
    private function enviarCorreoVerificacion($id_usuario, $emailDestino, $nombreDestino)
    {
        // Generar y guardar token
        $conexion = new Conexion();

        $token          = bin2hex(random_bytes(32));
        $fechaExpiracion = date('Y-m-d H:i:s', strtotime('+1 day'));

        $sql = "INSERT INTO tokens_recuperacion (token, fecha_expiracion, Usuarios_idusuarios)
                VALUES ('$token', '$fechaExpiracion', $id_usuario)";
        $conexion->consultar($sql); // en tu clase esto ejecuta el query

        // URL base del proyecto (ajustá si cambia)
        $baseUrl          = 'http://localhost/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02';
        $linkVerificacion = $baseUrl . "/index.php?page=verificar_email&token=$token";

        $mail = new PHPMailer(true);

        try {
            // Config SMTP Brevo
            $mail->isSMTP();
            $mail->Host       = 'smtp-relay.brevo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '9d6f3f001@smtp-brevo.com';  // TU usuario SMTP Brevo
            $mail->Password   = 'mqA4CSBVnGxgDZLO';          // TU contraseña SMTP Brevo
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            // Remitente y destinatario
            $mail->setFrom('famartinez2611994@gmail.com', 'CarSales - Verificación');
            $mail->addAddress($emailDestino, $nombreDestino);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Confirmá tu cuenta en CarSales';

            $mail->Body = "
            <div style='background:#f4f4f8;padding:20px;font-family:Arial,sans-serif;color:#333;'>
                <div style='max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;
                            box-shadow:0 4px 15px rgba(0,0,0,0.08);overflow:hidden;'>
                    
                    <!-- ENCABEZADO -->
                    <div style='background:#333A56;padding:20px;text-align:center;'>
                        <h2 style='color:#ffffff;margin:0;'>Bienvenido/a a CarSales</h2>
                    </div>

                    <!-- CUERPO -->
                    <div style='padding:25px;'>
                        <p style='font-size:15px;margin-top:0;'>
                            Hola <strong>" . htmlspecialchars($nombreDestino, ENT_QUOTES, 'UTF-8') . "</strong>,
                        </p>

                        <p style='font-size:14px;line-height:1.6;'>
                            Gracias por registrarte en <strong>CarSales</strong>. Para activar tu cuenta y comenzar a usar todas las funcionalidades del sistema, es necesario que verifiques tu correo electrónico.
                        </p>

                        <div style='text-align:center;margin:30px 0;'>
                            <a href='" . $linkVerificacion . "' 
                               style='background:#52658F;color:#ffffff;text-decoration:none;
                                      padding:12px 25px;border-radius:30px;font-size:15px;
                                      display:inline-block;'>
                                Verificar mi correo
                            </a>
                        </div>

                        <p style='font-size:13px;color:#555;line-height:1.6;'>
                            Si el botón no funciona, copiá y pegá el siguiente enlace en tu navegador:
                        </p>
                        <p style='font-size:12px;color:#777;word-wrap:break-word;'>
                            " . $linkVerificacion . "
                        </p>

                        <p style='font-size:12px;color:#999;margin-top:20px;'>
                            Este enlace es válido por <strong>24 horas</strong>. Luego de ese tiempo deberás solicitar una nueva verificación.
                        </p>
                    </div>

                    <!-- FOOTER -->
                    <div style='background:#f0f0f5;padding:10px 20px;text-align:center;font-size:11px;color:#777;'>
                        © " . date('Y') . " CarSales · Sistema de gestión de vehículos<br>
                        Este correo fue enviado automáticamente, por favor no respondas a este mensaje.
                    </div>
                </div>
            </div>
            ";

            $mail->AltBody = "Hola $nombreDestino,\n\n"
                . "Gracias por registrarte en CarSales.\n\n"
                . "Para activar tu cuenta, copiá y pegá este enlace en tu navegador:\n"
                . "$linkVerificacion\n\n"
                . "Este enlace es válido por 24 horas.\n\n"
                . "CarSales - Sistema de gestión de vehículos.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log para debug
            error_log('Error PHPMailer verificación: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
