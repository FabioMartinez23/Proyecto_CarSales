<?php
use PHPMailer\PHPMailer\PHPMailer;
require '../../vendor/autoload.php';
require_once('../../modelos/usuarios.php');

// Verificar que el campo de email no esté vacío
if (empty($_POST['email'])) {
    header('location: ../../index.php?page=olvidar_contraseña&mensaje=Ingrese un Email Válido Por Favor&status=error');
    exit();
}

$usuario = new Usuario();
$email = $_POST['email'];

// Verificar si el email existe y obtener el idusuarios
$resultado = $usuario->validar_email_para_contraseña($email);
if ($resultado) {
    $idUsuario = $resultado['idusuarios'];  // Obtener idusuarios
    
    // Generar el token y fecha de expiración
    $token = bin2hex(random_bytes(16));  // Token aleatorio
    $fechaExpiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));  // Expira en 1 hora

    // Insertar token en la tabla tokens_recuperacion
    $guardarToken = $usuario->guardar_token($token, $fechaExpiracion, $idUsuario);
    if ($guardarToken) {
        // Preparar el email para el usuario
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username   = 'famartinez2611994@gmail.com';                     //SMTP username
            $mail->Password   = 'axsr vtnf hguy jwtq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            // Opciones SSL para desactivar verificación (no recomendado en producción)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Remitente y destinatario
            $mail->setFrom('famartinez2611994@gmail.com', 'Soporte');
            $mail->addAddress($email);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de contraseña';
            $mail->Body = "
                <h1>Recupera tu contraseña</h1>
                <p>Para restablecer tu contraseña, haz clic en el siguiente enlace:</p>
                <p><a href='localhost/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/index.php?page=recuperar_password&token=$token'>Recuperar Contraseña</a></p>
                <p>Este enlace expirará en 1 hora.</p>
            ";

            $mail->send();
            header('location: ../../index.php?page=olvidar_contraseña&mensaje=Correo de recuperación enviado&status=success');
        } catch (Exception $e) {
            header('location: ../../index.php?page=olvidar_contraseña&mensaje=Error al enviar el correo&status=error');
        }
    } else {
        header('location: ../../index.php?page=olvidar_contraseña&mensaje=Error al generar token&status=error');
    }
} else {
    header('location: ../../index.php?page=olvidar_contraseña&mensaje=Email no encontrado&status=error');
}
?>
