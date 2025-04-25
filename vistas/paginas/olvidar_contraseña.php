<div class="d-flex justify-content-center align-items-center hacer_padding">
    <div class="card-2 p-4">
        <h1 class="card-title text-center mb-3">Cambiar Contraseña</h1>
        <p class="text-center">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
        <form action="controladores/usuarios/olvidar_contraseña.controlador.php" method="POST" onsubmit="return validarEmail()">
            <div class="form-floating mb-3 mt-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@correo.com">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="invalid-feedback">Por favor, ingrese un correo válido.</div>
            </div>
            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-action">
                    Enviar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function validarEmail() {
    const emailInput = document.getElementById('email');
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|com\.ar)$/;

    // Verificar si el correo cumple el patrón
    if (!emailPattern.test(emailInput.value)) {
        emailInput.classList.add('is-invalid');  // Añadir borde rojo
        return false;
    } else {
        emailInput.classList.remove('is-invalid');  // Remover borde rojo si es válido
        return true;
    }
}
</script>

