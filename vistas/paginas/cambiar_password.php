<div class="row hacer_padding">
    <div class="col"></div>
    <div class="col login">
        <h1>Cambiar Password</h1>
        <form id="id_form" method="POST" action="controladores/usuarios/usuarios.controlador.php">
            <input type="hidden" name="action" value="cambiar_password">
            <input type="hidden" name="idusuarios" value="<?php echo $_SESSION['idusuarios']; ?>">

            <!-- Contraseña actual -->
            <div class="form-floating mb-3 position-relative">
                <input type="password" class="form-control" id="password_actual" placeholder="Ingresar Contraseña Actual" name="password_actual">
                <label for="password_actual" class="form-label">Contraseña actual:</label>
                
                <!-- Icono ojo -->
                <span class="position-absolute top-50 end-0 translate-middle-y me-2 toggle-password" style="cursor:pointer;">
                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.75 21.75 0 0 1 5.06-5.94"/>
                        <path d="M1 1l22 22"/>
                        <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"/>
                    </svg>
                </span>
            </div>

            <!-- Nueva contraseña -->
            <div class="form-floating mb-3 position-relative">
                <input type="password" class="form-control" id="new_password" placeholder="Ingrese Nueva Contraseña" name="new_password">
                <label for="new_password" class="form-label">Nueva Contraseña:</label>
                
                <!-- Icono ojo -->
                <span class="position-absolute top-50 end-0 translate-middle-y me-2 toggle-password" style="cursor:pointer;">
                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.75 21.75 0 0 1 5.06-5.94"/>
                        <path d="M1 1l22 22"/>
                        <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"/>
                    </svg>
                </span>
            </div>

            <!-- Confirmar nueva contraseña -->
            <div class="form-floating mb-3 position-relative">
                <input type="password" class="form-control" id="new_password_confirm" placeholder="Confirmar Nueva Contraseña" name="new_password_confirm">
                <label for="new_password_confirm" class="form-label">Confirmar Contraseña:</label>
                
                <!-- Icono ojo -->
                <span class="position-absolute top-50 end-0 translate-middle-y me-2 toggle-password" style="cursor:pointer;">
                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.75 21.75 0 0 1 5.06-5.94"/>
                        <path d="M1 1l22 22"/>
                        <path d="M9.88 9.88A3 3 0 0 0 14.12 14.12"/>
                    </svg>
                </span>
            </div>

            <div class="d-grid gap-2 col-6 mx-auto">
                <button class="btn btn-action" type="submit">Cambiar Contraseña</button>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

<!-- Script -->
<script>
document.addEventListener('click', function(e){
    const icon = e.target.closest('.toggle-password');
    if (!icon) return;

    // Buscar el input dentro del mismo div padre
    const input = icon.parentElement.querySelector('input');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    const eye = icon.querySelector('.icon-eye');
    const eyeOff = icon.querySelector('.icon-eye-off');
    eye.style.display = isPassword ? 'none' : '';
    eyeOff.style.display = isPassword ? '' : 'none';
});
</script>
