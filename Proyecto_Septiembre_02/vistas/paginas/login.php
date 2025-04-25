


<div class="row">

    <div class="col"></div>
    <div class="col login" >
    <h1 style="text-align: center;">Iniciar sesion</h1>
        <form id="id_form" method="POST" action="controladores/login.controlador.php">
            <input type="hidden" name="action" value="login">
            <div class="form-floating mb-3 mt-3">
                <input type="text" class="form-control" id="username" placeholder="Nombre de Usuario" name="username">
                <label for="floatingInput">Username</label>
                <p id="id_usuario_parrafo" style="color:red; display:none;">Usuario Requerido</p>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" placeholder="Enter password" name="password">
                <label for="floatingInput">Password</label>
                <p id="id_password_parrafo" style="color:red; display:none;">Password Requerido</p>
            </div>
            <div class="d-grid gap-2 col-6 mx-auto">
                <button onclick="validate()" class="btn btn-primary" type="button">Ingresar</button>
                <button class="btn btn-primary" type="button" ><a style="color: white; text-decoration:none;" href="index.php?page=registrarse">Registrarse</a></button>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>


<script src="assets/js/validaciones/login.js"></script>