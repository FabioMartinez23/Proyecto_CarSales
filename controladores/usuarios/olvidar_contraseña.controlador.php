<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
require_once('../../modelos/usuarios.php');

// ============================
// Validación básica de acceso
// ============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('location: ../../index.php?page=olvidar_contraseña&mensaje=Acceso no permitido.&status=error');
    exit();
}

$email = trim($_POST['email'] ?? '');

// Validar email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('location: ../../index.php?page=olvidar_contraseña&mensaje=Ingrese un email válido, por favor.&status=error');
    exit();
}

// ============================
// Buscar usuario por email
// ============================
$usuario = new Usuario();
$resultado = $usuario->validar_email_para_contraseña($email);

if (!$resultado) {
    header('location: ../../index.php?page=olvidar_contraseña&mensaje=Email no encontrado en el sistema.&status=error');
    exit();
}

$idUsuario = $resultado['idusuarios'];

// ============================
// Generar token y guardar
// ============================
$token           = bin2hex(random_bytes(32)); // token más largo
$fechaExpiracion = date('Y-m-d H:i:s', strtotime('+1 hour')); // 1 hora

$guardarToken = $usuario->guardar_token($token, $fechaExpiracion, $idUsuario);

if (!$guardarToken) {
    header('location: ../../index.php?page=olvidar_contraseña&mensaje=Error al generar token de recuperación.&status=error');
    exit();
}

// ============================
// Preparar y enviar email
// ============================
$baseUrl = 'http://localhost/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02';
$linkRecuperar = $baseUrl . "/index.php?page=recuperar_password&token=$token";

$mail = new PHPMailer(true);

try {
    // Config SMTP (Brevo)
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = '9d6f3f001@smtp-brevo.com';  // Usuario Brevo
    $mail->Password   = 'mqA4CSBVnGxgDZLO';          // Contraseña SMTP Brevo
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';        // 🔴 IMPORTANTE para ñ y acentos
    $mail->Encoding   = 'base64';

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];

    // Remitente y destinatario
    $mail->setFrom('famartinez2611994@gmail.com', 'CarSales - Soporte');
    $mail->addAddress($email);

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Recuperación de contraseña - CarSales';

    $mail->Body = "
    <div style='background:#f4f4f8;padding:20px;font-family:Arial,sans-serif;color:#333;'>
        <div style='max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;
                    box-shadow:0 4px 15px rgba(0,0,0,0.08);overflow:hidden;'>
            
            <!-- ENCABEZADO -->
            <div style='background:#333A56;padding:20px;text-align:center;'>
                <h2 style='color:#ffffff;margin:0;'>Recuperación de contraseña</h2>
            </div>

            <!-- CUERPO -->
            <div style='padding:25px;'>
                <p style='font-size:15px;margin-top:0;'>
                    Hemos recibido una solicitud para restablecer la contraseña asociada a este correo electrónico.
                </p>

                <p style='font-size:14px;line-height:1.6;'>
                    Para crear una nueva contraseña, hacé clic en el siguiente botón:
                </p>

                <div style='text-align:center;margin:25px 0;'>
                    <a href='" . $linkRecuperar . "'
                       style='background:#B33030;color:#ffffff;text-decoration:none;
                              padding:12px 25px;border-radius:30px;font-size:15px;
                              display:inline-block;'>
                        Recuperar contraseña
                    </a>
                </div>

                <p style='font-size:13px;color:#555;line-height:1.6;'>
                    Si el botón no funciona, copiá y pegá el siguiente enlace en tu navegador:
                </p>
                <p style='font-size:12px;color:#777;word-wrap:break-word;'>
                    " . $linkRecuperar . "
                </p>

                <p style='font-size:12px;color:#999;margin-top:15px;'>
                    Este enlace tendrá validez por <strong>1 hora</strong>. 
                    Si no solicitaste esta recuperación, podés ignorar este correo. 
                    Tu contraseña actual seguirá siendo válida.
                </p>
            </div>

            <!-- FOOTER -->
            <div style='background:#f0f0f5;padding:10px 20px;text-align:center;
                        font-size:11px;color:#777;'>
                © " . date('Y') . " CarSales · Sistema de gestión de vehículos<br>
                Este mensaje fue generado automáticamente, por favor no respondas a este correo.
            </div>
        </div>
    </div>
    ";

    $mail->AltBody =
        "Recuperación de contraseña - CarSales\n\n" .
        "Hemos recibido una solicitud para restablecer tu contraseña.\n\n" .
        "Para crear una nueva contraseña, abrí este enlace en tu navegador:\n" .
        $linkRecuperar . "\n\n" .
        "Este enlace es válido por 1 hora. Si no solicitaste el cambio, podés ignorar este mensaje.\n\n" .
        "CarSales - Sistema de gestión de vehículos.";

    $mail->send();

    header('location: ../../index.php?page=olvidar_contraseña&mensaje=Correo de recuperación enviado. Revisá tu bandeja de entrada.&status=success');
    exit();
} catch (Exception $e) {
    // Log interno para debug, pero mensaje amigable para el usuario
    error_log('Error PHPMailer (recuperación): ' . $mail->ErrorInfo);
    header('location: ../../index.php?page=olvidar_contraseña&mensaje=Error al enviar el correo de recuperación.&status=error');
    exit();
}
