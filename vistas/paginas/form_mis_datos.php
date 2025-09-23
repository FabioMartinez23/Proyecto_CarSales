<?php
ini_set('display_errors', 0);
session_start();

require_once 'session_check.php';

if (isset($_SESSION['idusuarios']) && isset($_SESSION['Personas_idPersonas'])){
    $idusuarios = $_SESSION['idusuarios'];
    $personas_idpersonas = $_SESSION['Personas_idPersonas'];
} else {
    header('Location: vistas/paginas/errores/404.php'); // Redirigir a una página de error
    exit();
}

$usuario = new Usuario();
$result_usuarios = $usuario->traer_usuarios_y_personas($idusuarios);

$contacto = new Contacto();
$resultado_contacto = $contacto->consultar_contacto($personas_idpersonas);

$domicilio = new Domicilios();
$resultado_domicilio = $domicilio->consultar_domicilio($personas_idpersonas);

$documento = new Documento();
$result_documento = $documento->traer_documento_por_id($personas_idpersonas);

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

?>



<div class="container d-flex justify-content-center align-items-center hacer_padding">
    <div class="col-md-8 col-lg-12">
    <h1 class="text-center mb-4">Mis Datos</h1>
            <form class="row g-3" method="POST" action="controladores/usuarios/mis.datos.controlador.php" >
            <input type="hidden" name="idusuarios" value="<?= $idusuarios ?>"/>
            <input type="hidden" name="personas_idpersonas" value="<?= $personas_idpersonas ?>"/>

        <div class="col-md-2">
            <label for="inputEmail4" class="form-label-list">Nombre:</label>
            <input type="text" class="form-control no-editable form-control-no-edit" id="id_nombre" <?php foreach($result_usuarios as $usuarios){ echo "value=".$usuarios['nombre'];} ?> readonly>
        </div>

        <div class="col-md-2">
            <label for="inputPassword4" class="form-label-list">Apellido:</label>
            <input type="text" class="form-control form-control-no-edit" id="id_apellido" <?php foreach($result_usuarios as $usuarios){ echo "value=".$usuarios['apellido'];} ?> readonly>
        </div>

        <div class="col-md-2">
            <label for="inputPassword4" class="form-label-list">Fecha de Nacimiento:</label>
            <input type="date" class="form-control form-control-no-edit" id="id_fecha_nacimiento" <?php foreach($result_usuarios as $usuarios){ echo "value=".$usuarios['fecha_nacimiento'];} ?> readonly>
        </div>

        <!-- Tipo de Documento -->
        <div class="col-md-2">
            <label for="tipo_documento" class="form-label-list">Tipo de Documento:</label>
            <select class="form-select editable form-control-no-edit" id="tipo_documento" name="idTipo_documento"
                <?php foreach($result_documento as $documentos){ 
                    if (!empty($documentos['idTipo_documento'])) { 
                        echo 'disabled';
                    }
                } ?>>

                <option value="">Seleccionar Tipo de Documento</option>

                <?php foreach($result_tipo_documento as $tipo_documentos){ ?>
                    <option value="<?php echo $tipo_documentos['idTipo_documento']; ?>"
                        <?php
                        foreach($result_documento as $documentos) {
                            if ($documentos['idTipo_documento'] == $tipo_documentos['idTipo_documento']) {
                                echo 'selected';
                            }
                        }
                        ?>>
                        <?php echo $tipo_documentos['descripcion']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-2">
            <label for="inputPassword4" class="form-label-list">Documento:</label>
            <input name="documento" type="text" class="form-control editable form-control-no-edit" id="id_documento"             
            <?php foreach($result_documento as $documentos){ 
                if (!empty($documentos['valor'])) { 
                    echo "value='".$documentos['valor']."' readonly"; 
                }
            } ?>  >
        </div>

        <div class="col-md-2">
            <label for="inputEmail4" class="form-label-list">Username:</label>
            <input type="text" class="form-control form-control-no-edit" id="id_username"             
            <?php foreach($result_usuarios as $usuarios){ 
                if (!empty($usuarios['username'])) { 
                    echo "value='".$usuarios['username']."' readonly"; 
                }
            } ?>  >
        </div>

        <div class="col-md-4">
            <label for="inputPassword4" class="form-label-list">Email:</label>
            <input type="email" class="form-control form-control-no-edit" id="id_email" <?php foreach($result_usuarios as $usuarios){ echo "value=".$usuarios['email'];} ?> readonly>
        </div>

        <!-- Tipo de Contacto -->
        <div class="col-md-2">
            <label for="tipo_contacto" class="form-label-list">Tipo de Contacto:</label>
            <select class="form-select editable form-control-no-edit" id="tipo_contacto" name="idtipo_contacto"
                <?php foreach($resultado_contacto as $contactos){ 
                    if (!empty($contactos['idtipo_contacto'])) { 
                        echo 'disabled';
                    }
                } ?>>

                <option value="">Seleccionar Tipo de Contacto</option>

                <?php foreach($result_tipo_contacto as $tipo_contactos){ ?>
                    <option value="<?php echo $tipo_contactos['idtipo_contacto']; ?>"
                        <?php
                        foreach($resultado_contacto as $contactos) {
                            if ($contactos['idtipo_contacto'] == $tipo_contactos['idtipo_contacto']) {
                                echo 'selected';
                            }
                        }
                        ?>>
                        <?php echo $tipo_contactos['descripcion']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-3">
            <label for="inputPassword4" class="form-label-list">Contacto:</label>

            <input name="contacto" type="text" class="form-control editable form-control-no-edit" id="id_contacto"             
            <?php foreach($resultado_contacto as $contactos){ 
                if (!empty($contactos['valor'])) { 
                    echo "value='".$contactos['valor']."' readonly"; 
                }
            } ?>  >
        </div>

        <!-- Tipo de Domicilio -->
        <div class="col-md-2">
            <label for="tipo_contacto" class="form-label-list">Tipo de Domicilio:</label>
            <select class="form-select editable form-control-no-edit" id="idtipo_domicilio" name="idtipo_domicilio"
                <?php foreach($resultado_domicilio as $domicilios){ 
                    if (!empty($domicilios['idtipo_domicilio'])) { 
                        echo 'disabled';
                    }
                } ?>>
                <option value="">Seleccionar Tipo de Domicilio</option>

                <?php foreach($result_tipo_domicilio as $tipo_domicilios){ ?>
                    <option value="<?php echo $tipo_domicilios['idtipo_domicilio']; ?>"
                        <?php
                        foreach($resultado_domicilio as $domicilios) {
                            if ($domicilios['idtipo_domicilio'] == $tipo_domicilios['idtipo_domicilio']) {
                                echo 'selected';
                            }
                        }
                        ?>>
                        <?php echo $tipo_domicilios['descripcion']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-4">
            <label for="inputAddress" class="form-label-list">Domicilio:</label>
            <input name="descripcion" maxlength="20" type="text" class="form-control editable form-control-no-edit" id="id_domicilio" placeholder="Agregar Domicilio Aquí"
            
            <?php foreach($resultado_domicilio as $domicilios){ 
                if (!empty($domicilios['nombre_domicilio'])) { 
                    echo "value='".$domicilios['nombre_domicilio']."' readonly"; 
                }
            } ?>  >
        </div>

        <!-- Barrio -->
        <div class="col-md-2">
            <label for="tipo_contacto" class="form-label-list">Barrio:</label>
            <select class="form-select editable form-control-no-edit" id="idbarrios" name="barrios_idbarrios"
                <?php foreach($resultado_domicilio as $domicilios){ 
                    if (!empty($domicilios['idtipo_domicilio'])) { 
                        echo 'disabled';
                    }
                } ?>>
                <option value="">Seleccionar un Barrio</option>

                <?php foreach($result_barrio as $barrios){ ?>
                    <option value="<?php echo $barrios['idbarrios']; ?>"
                        <?php
                        foreach($resultado_domicilio as $domicilios) {
                            if ($domicilios['idbarrios'] == $barrios['idbarrios']) {
                                echo 'selected';
                            }
                        }
                        ?>>
                        <?php echo $barrios['descripcion']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Localidad -->
        <div class="col-md-2">
            <label for="tipo_contacto" class="form-label-list">Localidad:</label>
            <select onchange="validar_localidad(this.value)" class="form-select editable form-control-no-edit" id="idlocalidades" name="localidades_idlocalidades"
                <?php foreach($resultado_domicilio as $domicilios){ 
                    if (!empty($domicilios['idtipo_domicilio'])) { 
                        echo 'disabled';
                    }
                } ?>>
                <option value="">Seleccionar una Localidad</option>

                <?php foreach($result_localidad as $localidades){ ?>
                    <option value="<?php echo $localidades['idlocalidades']; ?>"
                        <?php
                        foreach($resultado_domicilio as $domicilios) {
                            if ($domicilios['idlocalidades'] == $localidades['idlocalidades']) {
                                echo 'selected';
                            }
                        }
                        ?>>
                        <?php echo $localidades['descripcion']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Provincia -->
        <div class="col-md-2">
            <label for="tipo_contacto" class="form-label-list">Provincia:</label>
            <select onchange="validar_provincia(this.value)" class="form-select editable form-control-no-edit" id="idprovincias" name="provincias_idprovincias"
                <?php foreach($resultado_domicilio as $domicilios){ 
                    if (!empty($domicilios['idtipo_domicilio'])) { 
                        echo 'disabled';
                    }
                } ?>>
                <option value="">Seleccionar una Provincia</option>

                <?php foreach($result_provincia as $provincias){ ?>
                    <option value="<?php echo $provincias['idprovincias']; ?>"
                        <?php
                        foreach($resultado_domicilio as $domicilios) {
                            if ($domicilios['idprovincias'] == $provincias['idprovincias']) {
                                echo 'selected';
                            }
                        }
                        ?>>
                        <?php echo $provincias['descripcion']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Pais -->
        <div class="col-md-2">
            <label for="tipo_contacto" class="form-label-list">Pais:</label>
            <select onchange="validar_pais(this.value)" class="form-select editable form-control-no-edit" id="idpaises" name="paises_idpaises"
                <?php foreach($resultado_domicilio as $domicilios){ 
                    if (!empty($domicilios['idtipo_domicilio'])) { 
                        echo 'disabled';
                    }
                } ?>>
                <option value="">Seleccionar un Pais</option>

                <?php foreach($result_pais as $paises){ ?>
                    <option value="<?php echo $paises['idpaises']; ?>"
                        <?php
                        foreach($resultado_domicilio as $domicilios) {
                            if ($domicilios['idpaises'] == $paises['idpaises']) {
                                echo 'selected';
                            }
                        }
                        ?>>
                        <?php echo $paises['descripcion']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <?php if(isset($contactos['idtipo_contacto']) && isset($documentos['idTipo_documento']) && isset($domicilios['idtipo_domicilio'])){ ?>
                <input type="hidden" name="action" value="actualizar"/>
                <input type="hidden" name="iddocumentos" value="<?= $documentos['iddocumentos'] ?>"/>
                <input type="hidden" name="idcontactos" value="<?= $contactos['idcontactos'] ?>"/>
                <input type="hidden" name="iddomicilios" value="<?= $domicilios['iddomicilios'] ?>"/>
                <?php }else{?>
                    <input type="hidden" name="action" value="guardar"/>
        <?php }?>

        
        <div class="col-12">
                <!-- Botón para habilitar la edición -->
                <button class="btn btn-action" type="button" id="editarBtn">Editar Datos</button>
    
                <!-- Botón de guardar que se muestra solo cuando se habilita la edición -->
                <button class="btn btn-success" type="button" id="guardarBtn" style="display:none;">Guardar Datos</button>

            <a type="button" class="btn btn-primary btn-action" href="index.php?page=cambiar_password">Cambiar Contraseña</a>
        </div>
    </form>
    </div>

</div>

<script src="assets/js/validaciones/mis_datos/validar_pais.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_provincia.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_localidad.ajax.js"></script>


<script>
// Script para habilitar edición
document.getElementById("editarBtn").addEventListener("click", function() {
    let elementos = document.querySelectorAll(".editable");
    elementos.forEach(elemento => {
        // Eliminar la clase 'form-control-no-edit' para hacer el campo editable
        elemento.classList.remove("form-control-no-edit");
        elemento.removeAttribute("readonly");
        elemento.removeAttribute("disabled");
    });

    // Mostrar botón de guardar
    document.getElementById("guardarBtn").style.display = "inline-block";
    this.style.display = "none"; // Ocultar botón de editar
});

// Script para guardar edicion
document.getElementById("guardarBtn").addEventListener("click", function() {
    let elementos = document.querySelectorAll(".editable");
    elementos.forEach(elemento => {
        // Volver a agregar la clase form-control-no-edit al guardar
        elemento.classList.add("form-control-no-edit");
        elemento.addAttribute("readonly");
        elemento.addAttribute("disabled");
    });

    // Esconder botón de guardar
    document.getElementById("guardarBtn").style.display = "none";
});

document.getElementById('guardarBtn').onclick = function() {
    this.disabled = true; // Deshabilita el botón
    this.form.submit(); // Envía el formulario
};
</script>

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



