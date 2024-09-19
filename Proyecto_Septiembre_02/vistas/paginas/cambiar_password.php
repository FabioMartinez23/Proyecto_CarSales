
<div class="row">
    <div class="col"></div>
    <div class="col login" >
        <form id="id_form" method="POST" action="controladores/usuarios/usuarios.controlador.php">
            <input type="hidden" name="action" value="cambiar_password">
            <input type="hidden" name="idusuarios" value="<?php echo $_SESSION['idusuarios']; ?>">
            <div class="mb-3">
                <label for="pwd" class="form-label">Password actual:</label>
                <input type="password" class="form-control" id="password_actual" placeholder="Enter password actual" name="password_actual">
            </div>

            <div class="mb-3">
                <label for="pwd" class="form-label">Nuevo Password:</label>
                <input type="password" class="form-control" id="new_password" placeholder="Enter New password" name="new_password">
            </div>

            <div class="mb-3">
                <label for="pwd" class="form-label">Confirmar Nuevo Password:</label>
                <input type="password" class="form-control" id="new_password_confirm" placeholder="Confirm New password" name="new_password_confirm">
            </div>

            <div class="d-grid gap-2 col-6 mx-auto">
                <button class="btn btn-primary" type="submit">Cambiar Password</button>
            </div>
        </form>
    </div>
    <div class="col"></div>
</div>