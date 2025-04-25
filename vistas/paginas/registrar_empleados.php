<?php

ini_set('display_errors', 1);


$usuario1 = new Usuario();
if(isset($_GET['idusuarios'])){
    $usuario_editar = $usuario1->traer_usuario_por_id($_GET['idusuarios']);
}

$tipo_contacto = new Tipo_Contactos();
$result_tipo_contacto = $tipo_contacto->traer_tipo_contacto();

$tipo_domicilio = new Tipo_Domicilios();
$result_tipo_domicilio = $tipo_domicilio->traer_tipo_domicilio();

$pais = new Paises();
$result_pais = $pais->traer_pais();

$provincia = new Provincias();
$result_provincia = $provincia->traer_provincia();

$localidad = new Localidades();
$result_localidad = $localidad->traer_localidad();

$barrio = new Barrios();
$result_barrio = $barrio->traer_barrio();

$tipo_documento = new Tipo_Documentos();
$result_tipo_documento = $tipo_documento->traer_tipo_documento(); 

$tipo_sexo = new Tipo_Sexos();
$result_tipo_sexo = $tipo_sexo->traer_tipo_sexo();

?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Usuarios</a></li>
        <?php 
        if(isset($_GET['accion']) && $_GET['accion'] === 'registrar'){
        ?>
        <li class="breadcrumb-item"><a href="index.php?page=listado_empleados">Empleados</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registrar Empleado</li>
        <?php
        }else{
        ?>
        <li class="breadcrumb-item active" aria-current="page">Registrar Empleado</li>
        <?php
        }
        ?>
    </ol>
</nav>

<!-- Ajuste del diseño del formulario -->
<div class="d-flex justify-content-center align-items-center hacer_padding">
    <div class="col-md-8 col-lg-6">
        <h1 class="text-center mb-4">Registrar Empleado</h1>
        <form method="POST" action="controladores/usuarios/usuarios.controlador.php">
            <?php if(isset($_GET['idusuarios'])){ ?>
                <input type="hidden" name="action" value="actualizar_empleado"/>
                <input type="hidden" name="idusuarios" value="<?= $_GET['idusuarios'] ?>"/>
                <input type="hidden" name="idpersonas" <?php if(isset($_GET['idusuarios'])){ foreach($usuario_editar as $usuario1){ echo "value=".$usuario1['personas_idpersonas'];}} ?> >
            <?php } else { ?>
                <input type="hidden" name="action" value="guardar_empleado"/>
            <?php } ?>

            <div id="paso1" class="paso">
                <div class="row">
                    <h3 style="text-decoration: underline;">Datos Personales</h3>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idusuarios'])){ foreach($usuario_editar as $usuario1){ echo "value=".$usuario1['nombre'];}} ?> type="text" name="nombre" class="form-control" id="id_nombre" placeholder="nombre">
                            <label for="floatingInput">Nombre</label>
                            <div class="invalid-feedback">Por favor ingrese nombre.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idusuarios'])){ foreach($usuario_editar as $usuario1){ echo "value=".$usuario1['apellido'];}} ?> type="text" name="apellido" class="form-control" id="id_apellido" placeholder="apellido">
                            <label for="floatingInput">Apellido</label>
                            <div class="invalid-feedback">Por favor ingrese apellido.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idusuarios'])){ foreach($usuario_editar as $usuario1){ echo "value=".$usuario1['fecha_nacimiento'];}} ?> type="date" name="fecha_nacimiento" class="form-control" id="id_fecha_nacimiento" onchange="verificarEdad()">
                            <label for="floatingInput">Fecha de Nacimiento</label>
                            <div class="invalid-feedback">Por favor ingrese fecha de nacimiento valida.</div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <select name="tipo_sexo_idtipo_sexo" id="id_tipo_sexo" class="form-select">
                                <option value="">Seleccione un Sexo</option>
                                <?php foreach($result_tipo_sexo as $tipo_sexo){ ?>
                                    <option value="<?php echo $tipo_sexo['idtipo_sexo']; ?>"
                                    <?php
                                        if (isset($usuario_editar)) {
                                            foreach($usuario_editar as $usuario1) {
                                                if ($usuario1['tipo_sexo_idtipo_sexo'] == $tipo_sexo['idtipo_sexo']) {
                                                    echo 'selected';
                                                }
                                            }
                                        }
                                        ?>>
                                        <?php echo $tipo_sexo['descripcion']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            <label for="floatingInput">Sexo</label>
                            <div class="invalid-feedback">Por favor ingrese un sexo.</div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idusuarios'])){ foreach($usuario_editar as $usuario1){ echo "value=".$usuario1['valor_documento'];}} ?> type="text" name="documento" class="form-control" id="id_documento" placeholder="documento">
                            <label for="floatingInput">Documento</label>
                            <div class="invalid-feedback">Por favor ingrese valor en documento.</div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <select name="tipo_documento_idtipo_documento" id="id_tipo_documento" class="form-select">
                                <option value="">Seleccione un Tipo de Documento</option>
                                <?php foreach($result_tipo_documento as $tipo_documento){ ?>
                                    <option value="<?php echo $tipo_documento['idTipo_documento']; ?>"
                                    <?php
                                        if (isset($usuario_editar)) {
                                            foreach($usuario_editar as $usuario1) {
                                                if ($usuario1['Tipo_documento_idTipo_documento'] == $tipo_documento['idTipo_documento']) {
                                                    echo 'selected';
                                                }
                                            }
                                        }
                                        ?>>
                                        <?php echo $tipo_documento['descripcion']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            <label for="floatingInput">Tipo de Documento</label>
                            <div class="invalid-feedback">Por favor ingrese un tipo de documento.</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <button type="button" class="btn btn-action" id="next1" onclick="siguiente1()">Siguiente</button>
                </div>
            </div>
            


            <div id="paso2" class="paso">
                <div class="row">
                    <h3 style="text-decoration: underline;">Datos Domicilio</h3>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idusuarios'])){ foreach($usuario_editar as $usuario1){ echo "value='".$usuario1['nombre_domicilio']."'";}} ?> type="text" name="domicilio" class="form-control" id="id_domicilio" placeholder="domicilio">
                            <label for="floatingInput">Domicilio</label>
                            <div class="invalid-feedback">Por favor ingrese un domicilio.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select name="tipo_domicilio_idtipo_domicilio" id="id_tipo_domicilio" class="form-select">
                            <option value="">Seleccione un Tipo de Domicilio</option>
                            <?php foreach($result_tipo_domicilio as $tipo_domicilio){ ?>
                                <option value="<?php echo $tipo_domicilio['idtipo_domicilio']; ?>"
                                <?php
                                        if (isset($usuario_editar)) {
                                            foreach($usuario_editar as $usuario1) {
                                                if ($usuario1['tipo_domicilio_idtipo_domicilio'] == $tipo_domicilio['idtipo_domicilio']) {
                                                    echo 'selected';
                                                }
                                            }
                                        }
                                        ?>>
                                        <?php echo $tipo_domicilio['descripcion']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            <label for="floatingInput">Tipo de Domicilio</label>
                            <div class="invalid-feedback">Por favor ingrese un tipo de domicilio.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select name="barrios_idbarrios" id="idbarrios" class="form-select">
                            <option value="">Seleccione un Barrio</option>
                            <?php foreach($result_barrio as $barrio){ ?>
                                <option value="<?php echo $barrio['idbarrios']; ?>"
                                <?php
                                        if (isset($usuario_editar)) {
                                            foreach($usuario_editar as $usuario1) {
                                                if ($usuario1['barrios_idbarrios'] == $barrio['idbarrios']) {
                                                    echo 'selected';
                                                }
                                            }
                                        }
                                        ?>>
                                        <?php echo $barrio['descripcion']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            <label for="floatingInput">Barrio</label>
                            <div class="invalid-feedback">Por favor ingrese un barrio.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select onchange="validar_localidad(this.value)" name="localidades_idlocalidades" id="idlocalidades" class="form-select">
                            <option value="">Seleccione una Localidad</option>
                            <?php foreach($result_localidad as $localidad){ ?>
                                <option value="<?php echo $localidad['idlocalidades']; ?>"
                                <?php
                                        if (isset($usuario_editar)) {
                                            foreach($usuario_editar as $usuario1) {
                                                if ($usuario1['localidades_idlocalidades'] == $localidad['idlocalidades']) {
                                                    echo 'selected';
                                                }
                                            }
                                        }
                                        ?>>
                                        <?php echo $localidad['descripcion']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            <label for="floatingInput">Localidad</label>
                            <div class="invalid-feedback">Por favor ingrese una localidad.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select onchange="validar_provincia(this.value)" name="provinicias_idprovincias" id="idprovincias" class="form-select">
                            <option value="">Seleccione una Provincia</option>
                            <?php foreach($result_provincia as $provincia){ ?>
                                <option value="<?php echo $provincia['idprovincias']; ?>"
                                <?php
                                        if (isset($usuario_editar)) {
                                            foreach($usuario_editar as $usuario1) {
                                                if ($usuario1['provincias_idprovincias'] == $provincia['idprovincias']) {
                                                    echo 'selected';
                                                }
                                            }
                                        }
                                        ?>>
                                        <?php echo $provincia['descripcion']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            <label for="floatingInput">Provincia</label>
                            <div class="invalid-feedback">Por favor ingrese una provincia.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select onchange="validar_pais(this.value)" name="paises_idpaises" id="id_pais" class="form-select">
                            <option value="">Seleccione un Pais</option>
                            <?php foreach($result_pais as $pais){ ?>
                                <option value="<?php echo $pais['idpaises']; ?>"
                                <?php
                                        if (isset($usuario_editar)) {
                                            foreach($usuario_editar as $usuario1) {
                                                if ($usuario1['paises_idpaises'] == $pais['idpaises']) {
                                                    echo 'selected';
                                                }
                                            }
                                        }
                                        ?>>
                                        <?php echo $pais['descripcion']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            <label for="floatingInput">Pais</label>
                            <div class="invalid-feedback">Por favor ingrese un pais.</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <button type="button" class="btn btn-secondary me-4" id="back1" onclick="atras1()">Atrás</button>
                    <button type="button" class="btn btn-action" id="next2" onclick="siguiente2()">Siguiente</button>
                </div>
            </div>
            

            <div id="paso3" class="paso">
                <div class="row">
                    <h3 style="text-decoration: underline;">Datos Contacto</h3>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idusuarios'])){ foreach($usuario_editar as $usuario1){ echo "value=".$usuario1['valor_contacto'];}} ?> type="text" name="contacto" class="form-control" id="id_contacto" placeholder="contacto">
                            <label for="floatingInput">Contacto</label>
                            <div class="invalid-feedback">Por favor ingrese un valor en contacto.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <select name="tipo_contacto_idtipo_contacto" id="id_tipo_contacto" class="form-select">
                                <option value="">Seleccione un Tipo de Contacto</option>
                                <?php foreach($result_tipo_contacto as $tipo_contacto){ ?>
                                    <option value="<?php echo $tipo_contacto['idtipo_contacto']; ?>"
                                    <?php
                                        if (isset($usuario_editar)) {
                                            foreach($usuario_editar as $usuario1) {
                                                if ($usuario1['tipo_contactos_idtipo_contactos'] == $tipo_contacto['idtipo_contacto']) {
                                                    echo 'selected';
                                                }
                                            }
                                        }
                                        ?>>
                                        <?php echo $tipo_contacto['descripcion']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            <label for="floatingInput">Tipo de Contacto</label>
                            <div class="invalid-feedback">Por favor ingrese un tipo de contacto.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <h3 style="text-decoration: underline;">Datos Usuario</h3>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idusuarios'])){ foreach($usuario_editar as $usuario1){ echo "value=".$usuario1['username'];}} ?> type="text" name="username" class="form-control" id="id_username" onfocusout="validate_username(event)" placeholder="username">
                            <label for="floatingInput">Username</label>
                            <div class="invalid-feedback">Por favor ingrese un username.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idusuarios'])){ foreach($usuario_editar as $usuario1){ echo "value=".$usuario1['email'];}} ?> type="email" name="email" class="form-control" id="id_email" onfocusout="validate_email(event)" placeholder="email">
                            <label for="floatingInput">Email</label>
                            <div class="invalid-feedback">Por favor ingrese un correo electrónico válido (debe tener un @ y terminar en .com o .com.ar).</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <button type="button" class="btn btn-secondary me-4" id="back2" onclick="atras2()">Atrás</button>
                    <button type="button" class="btn btn-action" id="next3" onclick="siguiente3()">Siguiente</button>
                </div>
            </div>

            <div id="confirmacion" class="paso">
                <h5>Confirmación</h5>
                <p>Revise sus datos antes de enviar:</p>
                <ul>
                    <li><strong>Nombre:</strong> <span id="confirmNombre">Marcelo</span></li>
                    <li><strong>Apellido:</strong> <span id="confirmApellido">Zunino</span></li>
                    <li><strong>Fecha de Nacimiento:</strong> <span id="confirmFechaNacimiento"></span></li>
                    <li><strong>Sexo:</strong> <span id="confirmTipoSexo"></span></li>

                    <li><strong>Documento:</strong> <span id="confirmDocumento"></span></li>
                    <li><strong>Tipo de Documento:</strong> <span id="confirmTipoDocumento"></span></li>
                    <li><strong>Domicilio:</strong> <span id="confirmDomicilio"></span></li>
                    <li><strong>Tipo de Domicilio:</strong> <span id="confirmTipoDomicilio"></span></li>

                    <li><strong>Barrio:</strong> <span id="confirmBarrio"></span></li>
                    <li><strong>Localidad:</strong> <span id="confirmLocalidad"></span></li>
                    <li><strong>Provincia:</strong> <span id="confirmProvincia"></span></li>
                    <li><strong>Pais:</strong> <span id="confirmPais"></span></li>

                    <li><strong>Contacto:</strong> <span id="confirmContacto"></span></li>
                    <li><strong>Tipo de Contacto:</strong> <span id="confirmTipoContacto"></span></li>
                    <li><strong>Username:</strong> <span id="confirmUsername"></span></li>
                    <li><strong>Email:</strong> <span id="confirmEmail"></span></li>
                </ul>

                <input type="hidden" name="perfiles_idperfiles" value="2">

                <div class="d-flex justify-content-center mt-4">
                    <!-- <a type="button" class="btn btn-dark me-4" href="index.php?page=listado_clientes">Volver</a> -->
                    <button type="button" class="btn btn-secondary me-4" id="back3" onclick="atras3()">Atrás</button>
                    <button type="submit" class="btn btn-action" onclick="return validarFormulario()">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="assets/js/imask.js"></script>

<script src="assets/js/validaciones/verificar_edad.js"></script>
<script src="assets/js/validaciones/usuarios.js"></script>
<script src="assets/js/validaciones/email.js"></script>

<script src="assets/js/validaciones/mis_datos/validar_pais.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_provincia.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_localidad.ajax.js"></script>

<script>
    let contacto = document.getElementById('id_contacto');
    let maskContacto = {
    mask: '+{54}(000)000-00-00'
    };
    let mask_contacto = IMask(contacto, maskContacto);
</script>

<script>
    let dni  = document.getElementById('id_documento');
    let maskDni = {
    mask: '00.000.000'
    };
    let mask_dni = IMask(dni, maskDni);
</script>


<script>
    // Botones de pasos
    const next1 = document.getElementById('next1');
    const back1 = document.getElementById('back1');
    const next2 = document.getElementById('next2');
    const back2 = document.getElementById('back2');
    const next3 = document.getElementById('next3');
    const back3 = document.getElementById('back3');

    // Pasos (divs)
    let paso1 = document.getElementById('paso1');
    let paso2 = document.getElementById('paso2');
    let paso3 = document.getElementById('paso3');
    let paso4 = document.getElementById('confirmacion');

    // Al principio, solo el paso 1 es visible
    paso1.style.display = 'block';
    paso2.style.display = 'none';
    paso3.style.display = 'none';
    paso4.style.display = 'none';


    // Función para calcular si la persona tiene al menos 18 años
    function validarFechaNacimiento() {
        let fechaNacimiento = document.getElementById('id_fecha_nacimiento').value; // Asegúrate de que el input tenga el id 'fechaNacimiento'
        let fechaActual = new Date();
        let fechaNacimientoObj = new Date(fechaNacimiento);
        
        // Restamos 18 años a la fecha actual
        fechaActual.setFullYear(fechaActual.getFullYear() - 18);
        
        // Verificamos si la fecha de nacimiento es menor o igual a la fecha de 18 años atrás
        if (fechaNacimientoObj > fechaActual) {
            console.log('La persona es menor de 18 años');
            return false; // Si la persona es menor de 18 años
        }
        return true; // Si la persona tiene 18 años o más
    }

    // Función de validación para el paso 1
    function validarPaso1() {
        let inputs = paso1.querySelectorAll('input, select');
        let valid = true;

        // Verificar si todos los campos tienen un valor
        inputs.forEach(input => {
            if (input.value.trim() === '') {
                valid = false;
                input.classList.add('is-invalid'); // Agregar clase is-invalid
                input.classList.remove('is-valid'); // Asegurar que no tenga la clase is-valid
            } else {
                input.classList.remove('is-invalid'); // Eliminar clase is-invalid
                input.classList.add('is-valid'); // Agregar clase is-valid
            }
        });

        // Verificar que la fecha de nacimiento sea válida (mayor de 18 años)
        if (!validarFechaNacimiento()) {
            valid = false;
            let inputFecha = document.getElementById('fechaNacimiento');
            inputFecha.classList.add('is-invalid');
            inputFecha.classList.remove('is-valid');
        }

        console.log(valid); // Verificar si la validación es correcta
        return valid;
    }

    // Función de validación para el paso 2
    function validarPaso2() {
        let inputs = paso2.querySelectorAll('input, select');
        let valid = true;

        // Verificar si todos los campos tienen un valor
        inputs.forEach(input => {
            if (input.value.trim() === '') {
                valid = false;
                input.classList.add('is-invalid'); // Agregar clase is-invalid
                input.classList.remove('is-valid'); // Asegurar que no tenga la clase is-valid
            } else {
                input.classList.remove('is-invalid'); // Eliminar clase is-invalid
                input.classList.add('is-valid'); // Agregar clase is-valid
            }
        });

        console.log(valid); // Verificar si la validación es correcta
        return valid;
    }

    // Función de validación para el paso 3
    function validarPaso3() {
        let inputs = paso3.querySelectorAll('input, select');
        let valid = true;

        // Expresión regular para validar el formato de email
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|com\.ar)$/;

        // Verificar si todos los campos tienen un valor
        inputs.forEach(input => {
            if (input.type === 'email') {
                // Validar el email con la expresión regular
                if (!emailRegex.test(input.value.trim())) {
                    valid = false;
                    input.classList.add('is-invalid'); // Agregar clase is-invalid
                    input.classList.remove('is-valid'); // Asegurar que no tenga la clase is-valid
                } else {
                    input.classList.remove('is-invalid'); // Eliminar clase is-invalid
                    input.classList.add('is-valid'); // Agregar clase is-valid
                }
            } else {
                // Validación general para otros campos
                if (input.value.trim() === '') {
                    valid = false;
                    input.classList.add('is-invalid'); // Agregar clase is-invalid
                    input.classList.remove('is-valid'); // Asegurar que no tenga la clase is-valid
                } else {
                    input.classList.remove('is-invalid'); // Eliminar clase is-invalid
                    input.classList.add('is-valid'); // Agregar clase is-valid
                }
            }
        });

        console.log(valid); // Verificar si la validación es correcta
        return valid;
    }


    // Función para ir al paso 2 si la validación es correcta
    function siguiente1() {
        if (validarPaso1()) {

            paso1.style.display = 'none';
            paso2.style.display = 'block';
            console.log('Validación exitosa, pasando al paso 2');
        } else {
            console.log('Validación fallida');
            return false; // No pasa al siguiente paso si la validación falla
        }
    }

    // Función para ir al paso 3
    function siguiente2() {
        if (validarPaso2()) {

            paso2.style.display = 'none';
            paso3.style.display = 'block';
            console.log('Validación exitosa, pasando al paso 3');
        } else {
            console.log('Validación fallida');
            return false; // No pasa al siguiente paso si la validación falla
        }
    }

    // Elementos de confirmación
    const confirmNombre = document.getElementById('confirmNombre');
    const confirmApellido = document.getElementById('confirmApellido');
    const confirmFechaNacimiento = document.getElementById('confirmFechaNacimiento');
    const confirmTipoSexo = document.getElementById('confirmTipoSexo');
    const confirmDocumento = document.getElementById('confirmDocumento');
    const confirmTipoDocumento = document.getElementById('confirmTipoDocumento');


    const confirmDomicilio = document.getElementById('confirmDomicilio');
    const confirmTipoDocimilio = document.getElementById('confirmTipoDomicilio');
    const confirmBarrio = document.getElementById('confirmBarrio');
    const confirmLocalidad = document.getElementById('confirmLocalidad');
    const confirmProvincia = document.getElementById('confirmProvincia');
    const confirmPais = document.getElementById('confirmPais');

    const confirmContacto = document.getElementById('confirmContacto');
    const confirmTipoContacto = document.getElementById('confirmTipoContacto');
    const confirmUsername = document.getElementById('confirmUsername');
    const confirmEmail = document.getElementById('confirmEmail');

    // Función para ir al paso 4 después de validar el paso 3 y pasar los valores a los confirm
    function siguiente3() {
        if (validarPaso3()) {

            console.log("Asignando valores a los elementos de confirmación");
            // Paso 1: Asignar los valores del paso 3 a los elementos de confirmación en el paso 4
            confirmNombre.textContent = document.getElementById('id_nombre').value;

            confirmApellido.textContent = document.getElementById('id_apellido').value;

            // Fecha de nacimiento (formato d-m-y)
            let fechaNacimiento = document.getElementById('id_fecha_nacimiento').value;
            let fechaNacimientoObj = new Date(fechaNacimiento);
            let fechaNacimientoFormateada = `${fechaNacimientoObj.getDate()}-${fechaNacimientoObj.getMonth() + 1}-${fechaNacimientoObj.getFullYear()}`;
            confirmFechaNacimiento.textContent = fechaNacimientoFormateada;

            // Tipo de sexo (mostrar texto en lugar de ID)
            let tipoSexo = document.getElementById('id_tipo_sexo');
            confirmTipoSexo.textContent = tipoSexo.options[tipoSexo.selectedIndex].text;

            confirmDocumento.textContent = document.getElementById('id_documento').value;

            let tipoDocumento = document.getElementById('id_tipo_documento')
            confirmTipoDocumento.textContent = tipoDocumento.options[tipoDocumento.selectedIndex].text;


            confirmDomicilio.textContent = document.getElementById('id_domicilio').value;

            let tipoDomicilio = document.getElementById('id_tipo_domicilio');
            confirmTipoDocimilio.textContent = tipoDomicilio.options[tipoDomicilio.selectedIndex].text;

            let barrios = document.getElementById('idbarrios');
            confirmBarrio.textContent = barrios.options[barrios.selectedIndex].text;

            let localidades = document.getElementById('idlocalidades')
            confirmLocalidad.textContent = localidades.options[localidades.selectedIndex].text;

            let provincias = document.getElementById('idprovincias');
            confirmProvincia.textContent = provincias.options[provincias.selectedIndex].text;

            let pais = document.getElementById('id_pais');
            confirmPais.textContent = pais.options[pais.selectedIndex].text;

            confirmContacto.textContent = document.getElementById('id_contacto').value;

            let tipoContacto = document.getElementById('id_tipo_contacto');
            confirmTipoContacto.textContent = tipoContacto.options[tipoContacto.selectedIndex].text;

            confirmUsername.textContent = document.getElementById('id_username').value;

            confirmEmail.textContent = document.getElementById('id_email').value;

            
            // Paso 2: Pasar al paso 4
            paso3.style.display = 'none';
            paso4.style.display = 'block';
            console.log('Validación exitosa, pasando al paso 4');
        } else {
            console.log('Validación fallida');
            return false; // No pasa al siguiente paso si la validación falla
        }
    }

    // Función para volver al paso 1
    function atras1() {
        paso2.style.display = 'none';
        paso1.style.display = 'block';
    }

    // Función para volver al paso 2
    function atras2() {
        paso3.style.display = 'none';
        paso2.style.display = 'block';
    }

    // Función para volver al paso 3
    function atras3() {
    paso4.style.display = 'none';
    paso3.style.display = 'block';
    }

    // Asignación de los eventos
    next1.addEventListener('click', siguiente1);
    next2.addEventListener('click', siguiente2);
    next3.addEventListener('click', siguiente3);
    back1.addEventListener('click', atras1);
    back2.addEventListener('click', atras2);
    back3.addEventListener('click', atras3);

</script>