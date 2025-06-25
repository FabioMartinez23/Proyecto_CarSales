
<div class="row hacer_padding">
    <div class="col"></div>
    <div class="col login" >
        <h1>Cambiar Password</h1>
        <form id="id_form" method="POST" action="controladores/usuarios/usuarios.controlador.php">
            <input type="hidden" name="action" value="cambiar_password">
            <input type="hidden" name="idusuarios" value="<?php echo $_SESSION['idusuarios']; ?>">
            <div class="mb-3">
                <label for="pwd" class="form-label">Contraseña actual:</label>
                <input type="password" class="form-control" id="password_actual" placeholder="Ingresar Contraseña Actual" name="password_actual">
            </div>

            <div class="mb-3">
                <label for="pwd" class="form-label">Nueva Contraseña:</label>
                <input type="password" class="form-control" id="new_password" placeholder="Ingrese Nueva Contraseña" name="new_password">
            </div>

            <div class="mb-3">
                <label for="pwd" class="form-label">Confirmar Nueva Contraseña:</label>
                <input type="password" class="form-control" id="new_password_confirm" placeholder="Confirmar Nueva Contraseña" name="new_password_confirm">
            </div>

            <div class="d-grid gap-2 col-6 mx-auto">
                <button class="btn btn-primary" type="submit">Cambiar Contraseña</button>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>