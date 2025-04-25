<?php
// Importar PHPMailer y cargar el autoloader de Composer
use PHPMailer\PHPMailer\PHPMailer;
require '../../vendor/autoload.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $observaciones = $_POST['observaciones'];

    // Crear instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'famartinez2611994@gmail.com';
        $mail->Password = 'axsr vtnf hguy jwtq';  // Contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Opciones SSL
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Remitente y destinatarios
        $mail->setFrom('famartinez2611994@gmail.com', 'Formulario de Contacto');
        $mail->addAddress('famartinez2611994@gmail.com'); 

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Datos para nuevo cliente';
        $mail->Body    = "
            <h1>Detalles del contacto</h1>
            <p><strong>Nombre y Apellido:</strong> $nombre</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Teléfono:</strong> $telefono</p>
            <p><strong>Observaciones:</strong> $observaciones</p>
        ";
        $mail->AltBody = "Nombre y Apellido: $nombre\nEmail: $email\nTeléfono: $telefono\nObservaciones: $observaciones";

        // Enviar el correo
        $mail->send();
        header('location: ../../index.php?page=form_contacto&mensaje=Formulario enviado con Exito!&status=success');
    } catch (Exception $e) {
        header('location: ../../index.php?page=form_contacto&mensaje=Error al enviar el formulario.&status=error');
        //echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
    }
}
?>
