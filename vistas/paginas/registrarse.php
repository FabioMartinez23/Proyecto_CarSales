<?php

$tipo_sexo = new Tipo_Sexos();
$result_tipo_sexo = $tipo_sexo->traer_tipo_sexo();

?>


<div class="container d-flex justify-content-center align-items-center hacer_padding">
    <div class="col-md-8 col-lg-6">
        <h1 class="text-center mb-4">Registrarse</h1>
        <div class="alert alert-warning text-center" role="alert">
        <i class="fa-solid fa-circle-exclamation"></i>
            Aviso! Tenga en cuenta que la CONTRASEÑA por defecto es la misma que su USERNAME
        </div>
        <form id="id_form" method="POST" action="controladores/login.controlador.php" onsubmit="return validarEmail()">
            <input type="hidden" name="action" value="registrarse">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input type="text" name="nombre" class="form-control" id="id_nombre" aria-describedby="emailHelp" placeholder="Escribir Nombre">
                        <label for="floatingInput">Nombre</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input type="text" name="apellido" class="form-control" id="id_apellido" aria-describedby="emailHelp" placeholder="Escribir Apellido">
                        <label for="floatingInput">Apellido</label>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input type="date" name="fecha_nacimiento" class="form-control" id="id_fecha_nacimiento" aria-describedby="emailHelp" onchange="verificarEdad()">
                        <label for="floatingInput">Fecha de Nacimiento</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select name="tipo_sexo_idtipo_sexo" id="id_tipo_sexo" class="form-select">
                            <option value="">Seleccione un Sexo</option>
                            <?php
                            foreach($result_tipo_sexo as $tipo_sexo){
                                ?>
                            <option value="<?php echo $tipo_sexo['idtipo_sexo']?>"><?php echo $tipo_sexo['descripcion']?></option>
                            <?php
                            }
                            ?>
                        </select>
                        
                        <label for="floatingInput">Sexo</label>
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input type="text" name="username" onfocusout="validate_username(event)" class="form-control" id="username" placeholder="Escribir Username">
                        <label for="username" class="form-label">Nombre de Usuario</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input type="email" onfocusout="validate_email(event)" class="form-control" id="idemail" name="email" placeholder="ejemplo@correo.com">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="invalid-feedback">Por favor, ingrese un correo válido.</div>
                    </div>
                </div>
            </div>


            <input type="hidden" name="perfiles_idperfiles" value="3">
            <div class="d-flex justify-content-center mt-4">
                <!-- <a type="button" class="btn btn-primary me-2" href="index.php?page=login">Login</a> -->
                <button type="submit" class="btn btn-action">
                    Registrarse
                </button>
            </div>

        </form>
    </div>
</div>

<script src="assets/js/validaciones/verificar_edad.js"></script>
<script src="assets/js/validaciones/usuarios.js"></script>
<script src="assets/js/validaciones/email.js"></script>


<script>
function validarEmail() {
    const emailInput = document.getElementById('idemail');
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