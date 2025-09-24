function validateLogin() {
    const nombre_usuario = document.getElementById('username');
    const password = document.getElementById('password');
    const form = document.getElementById('id_form');

    let valido = true;

    // Resetear clases previas
    nombre_usuario.classList.remove('is-invalid');
    password.classList.remove('is-invalid');

    // Validar usuario
    if (nombre_usuario.value.trim() === "") {
        mostrarError(nombre_usuario);
        valido = false;
    }

    // Validar password
    if (password.value.trim() === "") {
        mostrarError(password);
        valido = false;
    }

    // Si todo está correcto, enviar formulario
    if (valido) {
        form.submit();
    }
}

/**
 * Marca un input como inválido y lo quita en 3 segundos
 */
function mostrarError(input) {
    input.classList.add('is-invalid');
    setTimeout(() => {
        input.classList.remove('is-invalid');
    }, 2000); // 3 segundos
}
