
<div class="login hacer_padding">
    <h1>Iniciar sesión</h1>
    <form id="id_form" method="POST" action="controladores/login.controlador.php">
        <input type="hidden" name="action" value="login">
        <div class="form-floating mb-3 mt-3">
            <input type="text" class="form-control" id="username" placeholder="Ingresar Username" name="username">
            <label for="floatingInput">Username</label>
            <p id="id_usuario_parrafo" style="color:red; display:none;">Usuario Requerido</p>
        </div>
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" placeholder="Ingresar Contraseña" name="password">
            <label for="floatingInput">Password</label>
            <p id="id_password_parrafo" style="color:red; display:none;">Password Requerido</p>
        </div>
        <div class="d-flex justify-content-center">
            <button onclick="validate()" class="btn me-4 btn-action" type="button">Ingresar</button>
            <a href="index.php?page=olvidar_contraseña">¿Olvidaste tu contraseña?</a>
            <!-- <button class="btn btn-primary" type="button" ><a style="color: white; text-decoration:none;" href="index.php?page=registrarse">Registrarse</a></button> -->
        </div>
    </form>
</div>


<script src="assets/js/validaciones/login.js"></script>