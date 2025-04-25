

<div class="row">

    <div class="col"></div>
    <div class="col login" >
    <h1 style="text-align: center;">Registrar Nuevo Usuario</h1>
    <p style="text-align: center;">Acceso rapido, tenga en cuenta que la CONTRASEÑA por defecto es el mismo que el Username</p>
        <form id="id_form" method="POST" action="controladores/login.controlador.php">
            <input type="hidden" name="action" value="registrarse">
            <div class="form-floating mb-3 mt-3">
                <input type="text" name="username" onfocusout="validate_username(event)" class="form-control" id="username" aria-describedby="emailHelp">
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating mb-3 mt-3">
                <input type="email" name="email" onfocusout="validate_email(event)" class="form-control" id="email" aria-describedby="emailHelp">
                <label for="floatingInput">Email</label>
            </div>
            <input type="hidden" name="perfiles_idperfiles" value="7">
            <div class="d-grid gap-2 col-6 mx-auto">
                <button type="submit" class="btn btn-primary">
                    Registrarse
                </button>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>

    <script src="assets/js/validaciones/usuarios.js"></script>
<script src="assets/js/validaciones/email.js"></script>