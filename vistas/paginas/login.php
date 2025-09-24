
<div class="login hacer_padding">
    <h1>Iniciar sesión</h1>
    <form id="id_form" method="POST" action="controladores/login.controlador.php">
        <input type="hidden" name="action" value="login">

        <!-- Usuario -->
        <div class="form-floating mb-3 mt-3">
            <input type="text" class="form-control" id="username" placeholder="Ingresar Nombre de Usuario" name="username">
            <label for="username">Nombre de Usuario</label>
            <div id="id_usuario_parrafo" class="invalid-feedback">
                Nombre de Usuario Requerido
            </div>
        </div>

        <!-- Contraseña con botón ojo -->
        <div class="form-floating mb-3 position-relative">
            <input type="password" class="form-control" id="password" placeholder="Ingresar Contraseña" name="password">
            <label for="password">Contraseña</label>
            <div id="id_password_parrafo" class="invalid-feedback">
                Contraseña Requerida
            </div>

            <!-- Icono clickeable -->
            <span class="position-absolute top-50 end-0 translate-middle-y me-2 toggle-password" style="cursor:pointer;">
                <!-- Ojo abierto -->
                <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <!-- Ojo cerrado -->
                <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.75 21.75 0 0 1 5.06-5.94"/>
                    <path d="M1 1l22 22"/>
                    <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"/>
                </svg>
            </span>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-center">
            <button onclick="validateLogin()" class="btn me-4 btn-action" type="button">Ingresar</button>
            <a href="index.php?page=olvidar_contraseña">¿Olvidaste tu contraseña?</a>
        </div>
    </form>
</div>

<!-- Script para mostrar/ocultar -->
<script>
document.addEventListener('click', function(e){
    const icon = e.target.closest('.toggle-password');
    if (!icon) return;

    const input = document.querySelector('#password');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    const eye = icon.querySelector('.icon-eye');
    const eyeOff = icon.querySelector('.icon-eye-off');
    eye.style.display = isPassword ? 'none' : '';
    eyeOff.style.display = isPassword ? '' : 'none';
});
</script>


<script src="assets/js/validaciones/login.js"></script>