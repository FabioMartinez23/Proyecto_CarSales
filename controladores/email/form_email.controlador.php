<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

// Verificar método
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('location: ../../index.php?page=form_contacto&mensaje=Acceso no permitido.&status=error');
    exit;
}

// Sanitizar función
function limpiar($texto) {
    return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
}

// Capturar y limpiar datos
$nombre        = limpiar($_POST['nombre'] ?? '');
$email         = limpiar($_POST['email'] ?? '');
$telefono      = limpiar($_POST['telefono'] ?? '');
$observaciones = limpiar($_POST['observaciones'] ?? '');

// ===============================================================
// VALIDACIONES
// ===============================================================

// Nombre
if (strlen($nombre) < 3 || !preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $nombre)) {
    header('location: ../../index.php?page=form_contacto&mensaje=Nombre inválido.&status=error');
    exit;
}

// Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('location: ../../index.php?page=form_contacto&mensaje=Email inválido.&status=error');
    exit;
}

// Dominio válido
$dominios_validos = [
    'gmail.com','hotmail.com','outlook.com','yahoo.com',
    'gmail.com.ar','hotmail.com.ar','outlook.com.ar','yahoo.com.ar'
];
$dominio_email = substr(strrchr($email, "@"), 1);

if (!in_array(strtolower($dominio_email), $dominios_validos)) {
    header('location: ../../index.php?page=form_contacto&mensaje=Dominio de email no permitido.&status=error');
    exit;
}

// Teléfono
if (!preg_match("/^[0-9\+\s-]{8,20}$/", $telefono)) {
    header('location: ../../index.php?page=form_contacto&mensaje=Teléfono inválido.&status=error');
    exit;
}

// Observaciones
if (strlen($observaciones) < 5) {
    header('location: ../../index.php?page=form_contacto&mensaje=Observaciones insuficientes.&status=error');
    exit;
}

// ===============================================================
// ENVÍO DE EMAIL CON BREVO
// ===============================================================
$mail = new PHPMailer(true);

try {
    // SMTP Brevo (MISMA CONFIG QUE EN OLVIDÉ CONTRASEÑA)
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = '9d6f3f001@smtp-brevo.com';
    $mail->Password   = 'mqA4CSBVnGxgDZLO';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'base64';

    // 👇 Esto es lo que te faltaba y que SÍ está en el otro script
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];

    // Remitente (usamos el mismo que en recuperar contraseña, que ya sabés que funciona)
    $mail->setFrom('famartinez2611994@gmail.com', 'CarSales - Contacto');

    // Destino principal: vos
    $mail->addAddress('famartinez2611994@gmail.com', 'CarSales - Soporte');

    // Que al responder, vaya al cliente
    $mail->addReplyTo($email, $nombre);

    $mail->Subject = '📝 Nueva consulta recibida - CarSales';

    // ================================
    // CUERPO HTML PROFESIONAL
    // ================================
    $mail->isHTML(true);

    $mail->Body = "
    <div style='background:#f4f4f8;padding:20px;font-family:Arial,sans-serif;color:#333;'>

        <div style='max-width:650px;margin:0 auto;background:#ffffff;border-radius:10px;
                    box-shadow:0 4px 15px rgba(0,0,0,0.1);overflow:hidden;'>

            <!-- ENCABEZADO -->
            <div style='background:#333A56;padding:20px;text-align:center;'>
                <h2 style='color:#ffffff;margin:0;font-size:22px;'>
                    Nuevo mensaje desde el formulario de contacto
                </h2>
            </div>

            <!-- CUERPO -->
            <div style='padding:25px;'>
                <p style='font-size:15px;'>
                    Se ha recibido una nueva consulta desde el sitio web de <strong>CarSales</strong>.
                </p>

                <table style='width:100%;border-collapse:collapse;margin-top:20px;font-size:14px;'>
                    <tr>
                        <td style='padding:10px;font-weight:bold;background:#F1F1F1;width:30%;'>Nombre</td>
                        <td style='padding:10px;'>$nombre</td>
                    </tr>
                    <tr>
                        <td style='padding:10px;font-weight:bold;background:#F1F1F1;'>Correo</td>
                        <td style='padding:10px;'>$email</td>
                    </tr>
                    <tr>
                        <td style='padding:10px;font-weight:bold;background:#F1F1F1;'>Teléfono</td>
                        <td style='padding:10px;'>$telefono</td>
                    </tr>
                    <tr>
                        <td style='padding:10px;font-weight:bold;background:#F1F1F1;vertical-align:top;'>Mensaje</td>
                        <td style='padding:10px;'>$observaciones</td>
                    </tr>
                </table>

                <p style='margin-top:25px;font-size:13px;color:#555;text-align:center;'>
                    Este mensaje fue enviado automáticamente desde el formulario de contacto de CarSales.
                </p>
            </div>

            <!-- FOOTER -->
            <div style='background:#f0f0f5;padding:12px 20px;text-align:center;
                        font-size:12px;color:#777;'>
                © " . date('Y') . " CarSales · Sistema de gestión de vehículos
            </div>

        </div>
    </div>
    ";

    // Texto plano (por si el cliente de correo no lee HTML)
    $mail->AltBody =
        "Nueva consulta recibida desde CarSales\n\n" .
        "Nombre: $nombre\n" .
        "Correo: $email\n" .
        "Teléfono: $telefono\n" .
        "Mensaje:\n$observaciones\n";

    $mail->send();

    header('location: ../../index.php?page=form_contacto&mensaje=Formulario enviado con éxito.&status=success');
    exit;
} catch (Exception $e) {
    error_log('Error PHPMailer (form_contacto): ' . $mail->ErrorInfo);
    header('location: ../../index.php?page=form_contacto&mensaje=Error al enviar el formulario.&status=error');
    exit;
}

