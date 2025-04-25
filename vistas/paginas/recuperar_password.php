<?php
ini_set('display_errors', 1);

require_once 'modelos/conexion.php';

$mostrarFormulario = false;
$mensajeError = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $conexion = new Conexion();

    // Verificar si el token existe y no ha expirado
    $query = "SELECT Usuarios_idusuarios FROM tokens_recuperacion WHERE token = '$token' AND fecha_expiracion > NOW()";
    $resultado = $conexion->consultar($query);

    if ($resultado->num_rows > 0) {
        // El token es válido y no ha expirado
        $mostrarFormulario = true;
    } else {
        // Token inválido o expirado
        $mensajeError = "El enlace de recuperación ha expirado o es inválido.";
    }
} else {
    $mensajeError = "El enlace de recuperación ha expirado o es inválido.";
}
?>

<div class="row hacer_padding">
    <div class="col login">
        <?php if ($mostrarFormulario): ?>
            <h1>Recuperar Contraseña</h1>
            <form id="resetPasswordForm" method="POST" action="controladores/usuarios/usuarios.controlador.php">
                <input type="hidden" name="action" value="recuperar_password">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-floating mb-3 mt-3">
                    <input type="password" class="form-control" id="new_password" placeholder="Ingrese su nueva contraseña" name="new_password">
                    <label for="new_password" class="form-label">Nuevo Password:</label>
                    <div class="invalid-feedback" id="passwordError"></div>
                </div>

                <div class="form-floating mb-3 mt-3">
                    <input type="password" class="form-control" id="new_password_confirm" placeholder="Confirme su nueva contraseña" name="new_password_confirm">
                    <label for="new_password_confirm" class="form-label">Confirmar Nuevo Password:</label>
                    <div class="invalid-feedback" id="confirmPasswordError"></div>
                </div>

                <div class="d-grid gap-2 col-6 mx-auto">
                    <button class="btn btn-action" type="submit">Cambiar Password</button>
                </div>
            </form>
        <?php else: ?>
            <h1><?php echo $mensajeError; ?></h1>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('resetPasswordForm').addEventListener('submit', function(event) {
        // Limpiar mensajes de error previos
        document.getElementById('passwordError').innerText = '';
        document.getElementById('confirmPasswordError').innerText = '';
        document.getElementById('new_password').classList.remove('is-invalid');
        document.getElementById('new_password_confirm').classList.remove('is-invalid');

        // Obtener valores de los campos
        const newPassword = document.getElementById('new_password').value.trim();
        const confirmPassword = document.getElementById('new_password_confirm').value.trim();
        let valid = true;

        // Validar nuevo password
        if (!newPassword) {
            document.getElementById('passwordError').innerText = 'El campo nuevo password no puede estar vacío.';
            document.getElementById('new_password').classList.add('is-invalid');
            valid = false;
        }

        // Validar confirmación de password
        if (!confirmPassword) {
            document.getElementById('confirmPasswordError').innerText = 'El campo de confirmación de password no puede estar vacío.';
            document.getElementById('new_password_confirm').classList.add('is-invalid');
            valid = false;
        }

        // Validar que ambos passwords coincidan
        if (newPassword && confirmPassword && newPassword !== confirmPassword) {
            document.getElementById('confirmPasswordError').innerText = 'Las contraseñas no coinciden.';
            document.getElementById('new_password_confirm').classList.add('is-invalid');
            valid = false;
        }

        // Si no es válido, prevenir el envío del formulario
        if (!valid) {
            event.preventDefault(); // Prevenir el envío del formulario
        }
    });
</script>
