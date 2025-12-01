<?php
use PHPMailer\PHPMailer\PHPMailer;
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
$nombre = limpiar($_POST['nombre'] ?? '');
$email = limpiar($_POST['email'] ?? '');
$telefono = limpiar($_POST['telefono'] ?? '');
$observaciones = limpiar($_POST['observaciones'] ?? '');

// ===============================================================
// VALIDACIONES
// ===============================================================

// Validación nombre
if (strlen($nombre) < 3 || !preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $nombre)) {
    header('location: ../../index.php?page=form_contacto&mensaje=Nombre inválido.&status=error');
    exit;
}

// Validación email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('location: ../../index.php?page=form_contacto&mensaje=Email inválido.&status=error');
    exit;
}

// Validación dominios permitidos (opcional pero recomendado)
$dominios_validos = ['gmail.com', 'gmail.com.ar', 'hotmail.com', 'hotmail.com.ar', 'outlook.com', 'outlook.com.ar', 'yahoo.com', 'yahoo.com.ar'];

$dominio_email = substr(strrchr($email, "@"), 1);

if (!in_array(strtolower($dominio_email), $dominios_validos)) {
    header('location: ../../index.php?page=form_contacto&mensaje=Dominio de email no permitido.&status=error');
    exit;
}

// Validación teléfono (mínimo 8 a 15 números)
if (!preg_match("/^[0-9\+\s-]{8,20}$/", $telefono)) {
    header('location: ../../index.php?page=form_contacto&mensaje=Teléfono inválido.&status=error');
    exit;
}

// Validación observaciones
if (strlen($observaciones) < 5) {
    header('location: ../../index.php?page=form_contacto&mensaje=Observaciones insuficientes.&status=error');
    exit;
}


// ===============================================================
// ENVÍO DE EMAIL CON PHPMailer
// ===============================================================

$mail = new PHPMailer(true);

try {
    // Configurar SMTP
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'famartinez2611994@gmail.com';
    $mail->Password = 'axsr vtnf hguy jwtq';  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    // Remitente y receptor
    $mail->setFrom('famartinez2611994@gmail.com', 'Fabio Martinez');
    $mail->addAddress('famartinez_13@hotmail.com', 'Martinez Fabio'); 

    // Mensaje
    $mail->isHTML(true);
    $mail->Subject = 'Nuevo contacto desde CarSales';
    $mail->Body = "
        <div style='
            background:#F5F5F5;
            padding:20px;
            font-family: Arial, sans-serif;
            color:#333;
        '>

            <!-- ENCABEZADO -->
            <div style='
                background:#333A56;
                padding:20px;
                text-align:center;
                border-radius:8px 8px 0 0;
            '>
                <h2 style='color:white; margin:0;'>Nuevo contacto desde CarSales</h2>
            </div>

            <!-- CUERPO DEL MENSAJE -->
            <div style='
                background:white;
                padding:25px;
                border-radius:0 0 8px 8px;
                box-shadow:0 4px 15px rgba(0,0,0,.15);
            '>
                <p style='font-size:16px;'>Has recibido una nueva consulta desde el formulario de contacto.</p>

                <table style='width:100%; border-collapse:collapse; margin-top:20px;'>
                    <tr>
                        <td style='padding:10px; font-weight:bold; width:30%; border-bottom:1px solid #ddd;'>Nombre</td>
                        <td style='padding:10px; border-bottom:1px solid #ddd;'>$nombre</td>
                    </tr>
                    <tr>
                        <td style='padding:10px; font-weight:bold; border-bottom:1px solid #ddd;'>Correo</td>
                        <td style='padding:10px; border-bottom:1px solid #ddd;'>$email</td>
                    </tr>
                    <tr>
                        <td style='padding:10px; font-weight:bold; border-bottom:1px solid #ddd;'>Teléfono</td>
                        <td style='padding:10px; border-bottom:1px solid #ddd;'>$telefono</td>
                    </tr>
                    <tr>
                        <td style='padding:10px; font-weight:bold; vertical-align:top;'>Observaciones</td>
                        <td style='padding:10px;'>$observaciones</td>
                    </tr>
                </table>

                <p style='text-align:center; margin-top:25px; font-size:14px; color:#555;'>
                    Este mensaje ha sido generado automáticamente por el sistema CarSales.
                </p>
            </div>

            <!-- FOOTER -->
            <div style='text-align:center; margin-top:15px; font-size:12px; color:#777;'>
                © " . date('Y') . " CarSales - Sistema de gestión de vehículos
            </div>

        </div>
    ";

    $mail->send();

    header('location: ../../index.php?page=form_contacto&mensaje=Formulario enviado con éxito!&status=success');
} catch (Exception $e) {
    header('location: ../../index.php?page=form_contacto&mensaje=Error al enviar el formulario.&status=error');
}
?>

