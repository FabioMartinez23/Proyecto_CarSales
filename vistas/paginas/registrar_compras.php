<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['idusuarios'])){
    $idusuarios = $_SESSION['idusuarios'];
}

// PARTE PERSONAS

$tipo_sexo = new Tipo_Sexos();
$result_tipo_sexo = $tipo_sexo->traer_tipo_sexo();

$tipo_documento = new Tipo_Documentos();
$result_tipo_documento = $tipo_documento->traer_tipo_documento();

$tipo_domicilio = new Tipo_Domicilios();
$result_tipo_domicilio = $tipo_domicilio->traer_tipo_domicilio();

$tipo_contacto = new Tipo_Contactos();
$result_tipo_contacto = $tipo_contacto->traer_tipo_contacto();

$pais = new Paises();
$result_pais = $pais->traer_pais();

$provincia = new Provincias();
$result_provincia = $provincia->traer_provincia();

$localidad = new Localidades();
$result_localidad = $localidad->traer_localidad();

$barrio = new Barrios();
$result_barrio = $barrio->traer_barrio();

// PARTE VEHICULOS

$color = new Colores();
$result_color = $color->traer_color();

$marca = new Marcas();
$result_marca = $marca->traer_marca();

$modelo = new Modelos_Vehiculos();
$result_modelo = $modelo->traer_modelo();

$tipo_vehiculo = new Tipo_Vehiculos();
$result_tipo_vehiculo = $tipo_vehiculo->traer_tipo_vehiculo();

$carroceria = new Carrocerias();
$result_carroceria =  $carroceria->traer_carroceria();

$cristal = new Cristales();
$result_cristal = $cristal->traer_cristal();

$neumatico = new Neumaticos();
$result_neumatico = $neumatico->traer_neumatico();

// PARTE OPERACION

$tipo_pago = new Tipo_Pagos();
$resulta_tipo_pago = $tipo_pago->traer_tipo_pago();

// Parte DOCUMENTACIONES

$tipo_doc = new Tipo_Documentacion();
$result_tipo_doc = $tipo_doc->mostrar_tipo_doc();


?>

<!-- Modal Ficha Técnica -->
<div class="modal fade" id="fichaTecnicaModal" tabindex="-1" aria-labelledby="fichaTecnicaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ficha Técnica del Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h2>Revisión Técnica</h2>
                <h6>Vencimientos</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="vencimientoBateria">Batería</label>
                        <input type="date" class="form-control" id="vencimientoBateria">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="vencimientoRTO">RTO - Revisión Técnica</label>
                        <input type="date" class="form-control" id="vencimientoRTO">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="vencimientoService">Service Automotor</label>
                        <input type="date" class="form-control" id="vencimientoService">
                    </div>
                </div>

                <h6>Estado Carrocería</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="descripcionCarroceria">Descripción de la Carrocería</label>
                        <select id="descripcionCarroceria" class="form-select">
                            <?php foreach($result_carroceria as $carroceria): ?>
                                <option value="<?php echo $carroceria['idcarroceria']; ?>"><?php echo $carroceria['descripcion_carroceria']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="descripcionNeumaticos">Descripción de Neumáticos</label>
                        <select id="descripcionNeumaticos" class="form-select">
                            <?php foreach($result_neumatico as $neumatico): ?>
                                <option value="<?php echo $neumatico['idneumaticos']; ?>"><?php echo $neumatico['descripcion_neumaticos']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="descripcionCristales">Descripción de Cristales</label>
                        <select id="descripcionCristales" class="form-select">
                            <?php foreach($result_cristal as $cristal): ?>
                                <option value="<?php echo $cristal['idcristales']; ?>"><?php echo $cristal['descripcion_cristales']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-action" id="guardarFicha">Guardar Ficha Técnica</button>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal Documentación -->
<div class="modal fade" id="documentacionModal" tabindex="-1" aria-labelledby="documentacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Documentación del Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php foreach($result_tipo_doc as $tipo_doc): ?>
                    <div class="form-check form-switch mb-2">
                        <input 
                            class="form-check-input doc-switch" 
                            type="checkbox" 
                            value="<?php echo $tipo_doc['idtipo_documentacion']; ?>" 
                            data-descripcion="<?php echo htmlspecialchars($tipo_doc['descripcion']); ?>"
                            id="doc_<?php echo $tipo_doc['idtipo_documentacion']; ?>">
                        <label class="form-check-label" for="doc_<?php echo $tipo_doc['idtipo_documentacion']; ?>">
                            <?php echo $tipo_doc['descripcion']; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-action" id="guardarDocumentacion">Guardar Documentación</button>
            </div>
        </div>
    </div>
</div>



<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Gestión de Compras</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=listado_compras">Compras</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registrar Compra</li>
    </ol>
</nav>





<!-- Modal Cliente -->
<div class="modal fade" id="buscarClienteModal" tabindex="-1" aria-labelledby="buscarClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buscarClienteModalLabel">Buscar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="controladores/ventas/ventas.controlador.php" id="buscarClienteForm">
                    <input type="hidden" name="action" value="consultar_usuario">
                    <div class="mb-3">
                        <label for="dni" class="form-label">DNI</label>
                        <input maxlength="8" type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese el DNI del cliente">
                    </div>
                    <div class="mb-3">
                        <label for="sexo" class="form-label">Sexo</label>
                        <select name="tipo_sexo_idtipo_sexo" id="id_tipo_sexo" class="form-select">
                            <option value="">Seleccione un Sexo</option>
                        <?php foreach($result_tipo_sexo as $tipo_sexo): ?>
                            <option value="<?php echo $tipo_sexo['idtipo_sexo']; ?>"><?php echo $tipo_sexo['descripcion']; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button id="buscar_cliente_btn" type="button" class="btn btn-primary">Buscar</button> 
            </div>
        </div>
    </div>
</div>


<div class="hacer_padding">
    <!-- Contenedor principal -->
    <div class="text-center mb-4">
        <h1>Registrar Ingreso Nuevo</h1>
    </div>
    <form id="CompraForm" action="controladores/compras/registrar_compra.controlador.php" method="POST">
        <input type="hidden" id="compra" name="action" value="registrar_compra">
        <input type="hidden" name="idempleado" value="<?php echo $idusuarios?>">

        <!-- Inputs ocultos para almacenar datos del modal -->
        <input type="hidden" id="id_personas" name="id_personas">
        <input type="hidden" id="vencimiento_bateria" name="vencimiento_bateria">
        <input type="hidden" id="vencimiento_rto" name="vencimiento_rto">
        <input type="hidden" id="vencimiento_service" name="vencimiento_service">

        <!-- Inputs ocultos para la ficha técnica -->

        <input type="hidden" id="descripcion_carroceria" name="descripcion_carroceria">
        <input type="hidden" id="descripcion_neumatico" name="descripcion_neumatico">
        <input type="hidden" id="descripcion_cristales" name="descripcion_cristales">

        <!-- Input oculto que va a contener los IDs de los tipos de documentación -->
        <input type="hidden" id="documentacion_ids" name="documentacion_ids">

        <!-- Input oculto solo para mostrar las descripciones (informativo) -->
        <input type="hidden" id="documentacion_descripciones" name="documentacion_descripciones">


        <div class="accordion" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Datos del Cliente
                </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="text-start mt-3">
                            <!-- Botón para abrir el modal de buscar cliente -->
                            <button style="margin-bottom: 15px;" id="buscar_cliente_btn" type="button" class="btn btn-action" data-bs-toggle="modal" data-bs-target="#buscarClienteModal">Buscar Titular</button>
                        </div>
                        <div class="col-md-8 col-lg-12">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label for="id_nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="id_nombre" name="nombre">
                                </div>

                                <div class="col-md-4">
                                    <label for="id_apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="id_apellido" name="apellido">
                                </div>

                                <div class="col-md-4">
                                    <label for="id_fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="id_fecha_nacimiento" name="fecha_nacimiento">
                                </div>

                                <div class="col-md-4">
                                    <label for="tipo_sexo" class="form-label">Tipo de Sexo</label>
                                    <select class="form-select editable" id="id_tipo_sexo_1" name="tipo_sexo_idtipo_sexo">
                                        <option value="">Seleccionar Tipo de Sexo</option>
                                        <?php foreach($result_tipo_sexo as $tipo_sexo): ?>
                                            <option value="<?php echo $tipo_sexo['idtipo_sexo']; ?>"><?php echo $tipo_sexo['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                                    <select class="form-select editable" id="id_tipo_documento" name="tipo_documento_idtipo_documento">
                                        <option value="">Seleccionar Tipo de Documento</option>
                                        <?php foreach($result_tipo_documento as $tipo_documentos): ?>
                                            <option value="<?php echo $tipo_documentos['idTipo_documento']; ?>"><?php echo $tipo_documentos['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_documento" class="form-label">Documento</label>
                                    <input maxlength="8" name="documento" type="text" class="form-control editable" id="id_documento">
                                </div>

                                <!-- <div class="col-md-4">
                                    <label for="id_username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="id_username">
                                </div>

                                <div class="col-md-4">
                                    <label for="id_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="id_email">
                                </div> -->

                                <div class="col-md-4">
                                    <label for="tipo_contacto" class="form-label">Tipo de Contacto</label>
                                    <select class="form-select" id="id_tipo_contacto" name="tipo_contacto_idtipo_contacto">
                                        <option value="">Seleccionar Tipo de Contacto</option>
                                        <?php foreach($result_tipo_contacto as $tipo_contactos): ?>
                                            <option value="<?php echo $tipo_contactos['idtipo_contacto']; ?>"><?php echo $tipo_contactos['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_contacto" class="form-label">Contacto</label>
                                    <input maxlength="10" name="contacto" type="text" class="form-control editable" id="id_contacto">
                                </div>

                                <div class="col-md-4">
                                    <label for="idtipo_domicilio" class="form-label">Tipo de Domicilio</label>
                                    <select class="form-select" id="id_tipo_domicilio" name="tipo_domicilio_idtipo_domicilio">
                                        <option value="">Seleccionar Tipo de Domicilio</option>
                                        <?php foreach($result_tipo_domicilio as $tipo_domicilios): ?>
                                            <option value="<?php echo $tipo_domicilios['idtipo_domicilio']; ?>"><?php echo $tipo_domicilios['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_domicilio" class="form-label">Domicilio</label>
                                    <input name="domicilio" maxlength="35" type="text" class="form-control editable" id="id_domicilio" placeholder="Agregar Domicilio Aquí">
                                </div>

                                        <!-- Barrio -->
                                <div class="col-md-2">
                                    <label for="tipo_contacto" class="form-label ">Barrio</label>
                                    <select class="form-select" id="idbarrios" name="barrios_idbarrios">
                                        <?php foreach($result_barrio as $barrio): ?>
                                            <option value="<?php echo $barrio['idbarrios']; ?>"><?php echo $barrio['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Localidad -->
                                <div class="col-md-2">
                                    <label for="tipo_contacto" class="form-label ">Localidad</label>
                                    <select onchange="validar_localidad(this.value)" class="form-select" id="idlocalidades" name="localidades_idlocalidades">
                                    <?php foreach($result_localidad as $localidad): ?>
                                            <option value="<?php echo $localidad['idlocalidades']; ?>"><?php echo $localidad['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Provincia -->
                                <div class="col-md-2">
                                    <label for="tipo_contacto" class="form-label ">Provincia</label>
                                    <select onchange="validar_provincia(this.value)" class="form-select" id="idprovincias" name="provincias_idprovincias">
                                        <?php foreach($result_provincia as $provincia): ?>
                                            <option value="<?php echo $provincia['idprovincias']; ?>"><?php echo $provincia['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Pais -->
                                <div class="col-md-2">
                                    <label for="tipo_contacto" class="form-label ">Pais</label>
                                    <select onchange="validar_pais(this.value)" class="form-select" id="idpaises" name="paises_idpaises">
                                    <?php foreach($result_pais as $paises): ?>
                                            <option value="<?php echo $paises['idpaises']; ?>"><?php echo $paises['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Datos del Vehículo
                </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="col-md-8 col-lg-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="id_patente" class="form-label">Patente</label>
                                    <input type="text" name="patente" class="form-control" id="id_patente" maxlength="7" onfocusout="validate_patente(event)">
                                </div>

                                <div class="col-md-4">
                                    <label for="id_chasis" class="form-label">Chasis</label>
                                    <input type="text" name="chasis" class="form-control" id="id_chasis">
                                </div>

                                <div class="col-md-4">
                                    <label for="id_motor" class="form-label">Motor</label>
                                    <input type="text" name="motor" class="form-control" id="id_motor">
                                </div>

                                <div class="col-md-4">
                                    <label for="id_año" class="form-label">Año</label>
                                    <input type="number" name="año" class="form-control" id="id_año" min="1960" max="2024">
                                </div>

                                <div class="col-md-4">
                                    <label for="id_kilometraje" class="form-label">Kilometraje</label>
                                    <input oninput="formatearKilometraje(this)" maxlength="10" type="text" name="kilometraje" class="form-control" id="id_kilometraje">
                                </div>

                                <div class="col-md-4">
                                    <label for="idcolores" class="form-label">Color</label>
                                    <select name="colores_idcolores" id="id_colores" class="form-select">
                                        <option value="">Seleccione un Color</option>
                                        <?php foreach($result_color as $colores): ?>
                                            <option value="<?php echo $colores['idcolores']; ?>"><?php echo $colores['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="idmarcas" class="form-label">Marca</label>
                                    <select onchange="validar_marca(this.value)"  name="idmarcas" id="id_marcas" class="form-select">
                                        <option value="">Seleccione una Marca</option>
                                        <?php foreach($result_marca as $marcas): ?>
                                            <option value="<?php echo $marcas['idmarcas']; ?>"><?php echo $marcas['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="idmodelos" class="form-label">Modelo</label>
                                    <select name="modelos_idmodelos" id="idmodelos" class="form-select">
                                        <option value="">Seleccione un Modelo</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="idtipo_vehiculos" class="form-label">Tipo</label>
                                    <select name="tipo_vehiculos_idtipo_vehiculos" id="id_tipo_vehiculos" class="form-select">
                                        <option value="">Seleccione un Tipo</option>
                                        <?php foreach($result_tipo_vehiculo as $tipo_vehiculos): ?>
                                            <option value="<?php echo $tipo_vehiculos['idtipo_vehiculos']; ?>"><?php echo $tipo_vehiculos['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button id="verFichaTecnicaBtn" type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#fichaTecnicaModal">Ver Ficha Técnica</button>
                            <button id="verDocumentacionBtn" type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#documentacionModal">Ver Documentación del Vehículo</button>
                        </div>
                        <!-- Aquí aparecerá el aviso si falta documentación -->
                        <div id="avisoDocumentacion" class="mt-2" style="color: red; font-weight: bold;"></div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Datos de la Compra
                </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="tipoPago" class="form-label">Tipo de Pago:</label>
                                <select class="form-select" id="tipoPago" name="tipo_pago">
                                    <?php foreach($resulta_tipo_pago as $tipo_pagos): ?>
                                            <option value="<?php echo $tipo_pagos['idtipo_pago']; ?>"><?php echo $tipo_pagos['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="monto" class="form-label">Monto:</label>
                                <input oninput="formatearNumero(this)" type="text" class="form-control" id="id_precio" name="precio" placeholder="0">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="observaciones" class="form-label">Observaciones:</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
                <a type="button" class="btn btn-secondary me-2" href="index.php?page=listado_compras">Volver a la Lista de Compras</a>
                <button type="submit" class="btn btn-action">
                    Registrar Compra
                </button>
        </div>
        <div id="avisoEnvioDocumentacion" class="mt-2" style="color: red; font-weight: bold;"></div>
    </form>
</div>

<script src="assets/js/json/traer_datos_cliente_compra.js"></script>

<script>
document.getElementById("guardarFicha").addEventListener("click", function() {
    // Copiar valores de los inputs del modal al formulario principal
    document.getElementById("vencimiento_bateria").value = document.getElementById("vencimientoBateria").value;
    document.getElementById("vencimiento_rto").value = document.getElementById("vencimientoRTO").value;
    document.getElementById("vencimiento_service").value = document.getElementById("vencimientoService").value;

    document.getElementById("descripcion_carroceria").value = document.getElementById("descripcionCarroceria").value;
    document.getElementById("descripcion_neumatico").value = document.getElementById("descripcionNeumaticos").value;
    document.getElementById("descripcion_cristales").value = document.getElementById("descripcionCristales").value;

    // Cierra el modal automáticamente
    const modal = bootstrap.Modal.getInstance(document.getElementById('fichaTecnicaModal'));
    modal.hide();
});
</script>

<script>
document.getElementById("guardarDocumentacion").addEventListener("click", function() {
    const switches = document.querySelectorAll('.doc-switch');
    const seleccionadosId = [];
    const seleccionadosDesc = [];

    switches.forEach(sw => {
        if (sw.checked) {
            seleccionadosId.push(sw.value);
            seleccionadosDesc.push(sw.getAttribute('data-descripcion').toLowerCase());
        }
    });

    document.getElementById("documentacion_ids").value = seleccionadosId.join(',');
    document.getElementById("documentacion_descripciones").value = seleccionadosDesc.join(', ');

    // Detectar si faltan los documentos críticos
    const aviso = document.getElementById('avisoDocumentacion');
    let faltan = [];

    if (!seleccionadosDesc.includes('formulario 08')) faltan.push('Formulario 08');
    if (!seleccionadosDesc.includes('cedula vehicular')) faltan.push('Cédula Vehicular');

    if (faltan.length > 0) {
        aviso.textContent = `⚠️ Documentación importante faltante: ${faltan.join(', ')}`;
    } else {
        aviso.textContent = ''; // limpiar el aviso si todo está ok
    }

    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('documentacionModal'));
    modal.hide();
});

// 🔒 Validación antes de enviar el formulario
document.getElementById("CompraForm").addEventListener("submit", function(event) {
    const descs = document.getElementById("documentacion_descripciones").value.toLowerCase();
    const aviso = document.getElementById('avisoEnvioDocumentacion');

    let faltan = [];
    if (!descs.includes('formulario 08')) faltan.push('Formulario 08');
    if (!descs.includes('cedula vehicular')) faltan.push('Cédula Vehicular');

    if (faltan.length > 0) {
        event.preventDefault(); // 🚫 Evita el envío
        aviso.textContent = `⚠️ No se puede guardar. Faltan: ${faltan.join(', ')}`;
        aviso.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>

<script>
        function formatearKilometraje(input) {
            // Remueve cualquier carácter que no sea número
            let valor = input.value.replace(/\D/g, '');
            
            // Formatea el número con separadores de miles
            valor = new Intl.NumberFormat('es-ES').format(valor);
            
            // Agrega " km" al final
            input.value = valor + ' km';
        }

        function formatearNumero(input) {
            // Remueve cualquier carácter que no sea número
            let valor = input.value.replace(/\D/g, '');
            
            // Formatea el número con separadores de miles
            valor = new Intl.NumberFormat('es-ES').format(valor);
            
            // Actualiza el valor del input
            input.value = valor;
        }
</script>

<script src="assets/js/validaciones/mis_datos/validar_pais.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_provincia.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_localidad.ajax.js"></script>
<script src="assets/js/validaciones/patente.js"></script>

<script src="assets/js/validaciones/validar_marca.ajax.js"></script>