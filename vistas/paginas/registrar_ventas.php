<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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


?>




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
                <button id="buscar_cliente_btn" type="button" class="btn btn-action">Buscar</button> 
            </div>
        </div>
    </div>
</div>

<!-- Modal Auto -->
<div class="modal fade" id="buscarAutoModal" tabindex="-1" aria-labelledby="buscarAutoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buscarAutoModalLabel">Buscar Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="controladores/ventas/ventas.controlador.php" id="buscarAutoForm">
                    <input type="hidden" name="action" value="consultar_auto">
                    <div class="mb-3">
                        <label for="dni" class="form-label">Patente</label>
                        <input maxlength="8" type="text" class="form-control" id="patente" name="patente" placeholder="Ingrese la Patente del Vehiculo">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button id="buscar_auto_btn" type="button" class="btn btn-action">Buscar</button> 
            </div>
        </div>
    </div>
</div>



    <!-- Modal de Ficha Técnica -->
    <div class="modal fade" id="fichaTecnicaModal" tabindex="-1" aria-labelledby="fichaTecnicaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fichaTecnicaModalLabel">Ficha Técnica del Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3 id="marcaModeloVehiculo"></h3> <!-- Aquí se mostrará la marca y modelo -->

                        <!-- Agrupamos fechas en dos columnas -->
                        <h6>Vencimientos</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vencimientoBateria" class="form-label">Batería</label>
                                <input type="date" class="form-control" id="vencimientoBateria" name="vencimiento_bateria" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vencimientoRTO" class="form-label">RTO - Revisión Técnica</label>
                                <input type="date" class="form-control" id="vencimientoRTO" name="vencimiento_rto" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vencimientoService" class="form-label">Service Automotor</label>
                                <input type="date" class="form-control" id="vencimientoService" name="vencimiento_service" readonly>
                            </div>
                        </div>

                        <!-- Agrupamos los checkboxes en dos columnas -->
                        <h6>Estado de Documentación</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switch08" name="switch_08" disabled>
                                    <label class="form-check-label" for="switch08">Formulario 08</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switch12" name="switch_12" disabled>
                                    <label class="form-check-label" for="switch12">Formulario 12</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchTitulo" name="switch_titulo" disabled>
                                    <label class="form-check-label" for="switchTitulo">Título Automotor</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchCedula" name="switch_cedula" disabled>
                                    <label class="form-check-label" for="switchCedula">Cédula del Vehículo</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchSeguro" name="switch_seguro" disabled>
                                    <label class="form-check-label" for="switchSeguro">Seguro Automotor</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchMunicipalidad" name="switch_municipalidad" disabled>
                                    <label class="form-check-label" for="switchMunicipalidad">Municipalidad - Deudas</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchDominio" name="switch_dominio" disabled>
                                    <label class="form-check-label" for="switchDominio">Informe de Dominio</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchMultas" name="switch_multas" disabled>
                                    <label class="form-check-label" for="switchMultas">Formulario 13i - Infracciones/Multas</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchPrenda" name="switch_prenda" disabled>
                                    <label class="form-check-label" for="switchPrenda">Prenda</label>
                                </div>
                            </div>
                        </div>

                        <!-- Agrupamos los select en dos columnas -->
                        <h6>Estado Carrocería</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="descripcionCarroceria" class="form-label">Descripción de la Carrocería:</label>
                                <select id="descripcionCarroceria" name="descripcion_carroceria" class="form-select" disabled>
                                    <?php foreach($result_carroceria as $carroceria): ?>
                                            <option value="<?php echo $carroceria['idcarroceria']; ?>"><?php echo $carroceria['descripcion_carroceria']; ?></option>
                                        <?php endforeach;?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="descripcionNeumaticos" class="form-label">Descripción de los Neumáticos:</label>
                                <select id="descripcionNeumaticos" name="descripcion_neumatico" class="form-select" disabled>
                                    <?php foreach($result_neumatico as $neumatico): ?>
                                            <option value="<?php echo $neumatico['idneumaticos']; ?>"><?php echo $neumatico['descripcion_neumaticos']; ?></option>
                                        <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="descripcionCristales" class="form-label">Descripción de Cristales:</label>
                                <select id="descripcionCristales" name="descripcion_cristales" class="form-select" disabled>
                                    <?php foreach($result_cristal as $cristal): ?>
                                            <option value="<?php echo $cristal['idcristales']; ?>"><?php echo $cristal['descripcion_cristales']; ?></option>
                                        <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                    <!-- Botón de Cerrar -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar Ficha</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Gestión de Ventas</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=listado_ventas">Ventas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registrar Venta</li>
    </ol>
</nav>



<div class="hacer_padding">
    <!-- Contenedor principal -->
    <div class="text-center mb-4">
        <h1>Registrar Venta</h1>
    </div>
    <form id="ventaForm" action="controladores/ventas/registrar_venta.controlador.php" method="POST">
        <input type="hidden" id="venta" name="action" value="registrar_venta">
        <div class="accordion" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Datos del Cliente
                </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                            <input type="hidden" id="id_personas" name="id_persona">
                            <input type="hidden" id="id_usuarios" name="id_usuario">
                            <input type="hidden" id="id_contactos" name="id_contacto">
                            <input type="hidden" id="id_domicilios" name="id_domicilio">
                            <input type="hidden" id="id_documentos" name="id_documento">

                        <div class="text-start mt-3">
                            <!-- Botón para abrir el modal de buscar cliente -->
                            <button style="margin-bottom: 15px;" id="buscar_cliente_btn" type="button" class="btn btn-action" data-bs-toggle="modal" data-bs-target="#buscarClienteModal">Buscar Cliente</button>
                        </div>
                        <div class="col-md-8 col-lg-12">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label for="id_nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="id_nombre" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="id_apellido" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="id_fecha_nacimiento" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                                    <select class="form-select editable" id="id_tipo_documento" name="idTipo_documento" disabled>
                                        <option value="">Seleccionar Tipo de Documento</option>
                                        <?php foreach($result_tipo_documento as $tipo_documentos): ?>
                                            <option value="<?php echo $tipo_documentos['idTipo_documento']; ?>"><?php echo $tipo_documentos['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_documento" class="form-label">Documento</label>
                                    <input maxlength="8" name="documento" type="text" class="form-control editable" id="id_documento" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="id_username" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="id_email" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="tipo_contacto" class="form-label">Tipo de Contacto</label>
                                    <select class="form-select" id="id_tipo_contacto" name="idtipo_contacto" disabled>
                                        <option value="">Seleccionar Tipo de Contacto</option>
                                        <?php foreach($result_tipo_contacto as $tipo_contactos): ?>
                                            <option value="<?php echo $tipo_contactos['idtipo_contacto']; ?>"><?php echo $tipo_contactos['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_contacto" class="form-label">Contacto</label>
                                    <input name="contacto" type="text" class="form-control editable" id="id_contacto" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="idtipo_domicilio" class="form-label">Tipo de Domicilio</label>
                                    <select class="form-select" id="id_tipo_domicilio" name="idtipo_domicilio" disabled>
                                        <option value="">Seleccionar Tipo de Domicilio</option>
                                        <?php foreach($result_tipo_domicilio as $tipo_domicilios): ?>
                                            <option value="<?php echo $tipo_domicilios['idtipo_domicilio']; ?>"><?php echo $tipo_domicilios['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_domicilio" class="form-label">Domicilio</label>
                                    <input name="descripcion" maxlength="20" type="text" class="form-control editable" id="id_domicilio" placeholder="Agregar Domicilio Aquí" readonly>
                                </div>

                                        <!-- Barrio -->
                                <div class="col-md-2">
                                    <label for="tipo_contacto" class="form-label ">Barrio</label>
                                    <select class="form-select" id="idbarrios" name="barrios_idbarrios" disabled>
                                        <?php foreach($result_barrio as $barrios): ?>
                                            <option value="<?php echo $barrios['idbarrios']; ?>"><?php echo $barrios['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Localidad -->
                                <div class="col-md-2">
                                    <label for="tipo_contacto" class="form-label ">Localidad</label>
                                    <select class="form-select" id="idlocalidades" name="localidades_idlocalidades" disabled>
                                    <?php foreach($result_localidad as $localidades): ?>
                                            <option value="<?php echo $localidades['idlocalidades']; ?>"><?php echo $localidades['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Provincia -->
                                <div class="col-md-2">
                                    <label for="tipo_contacto" class="form-label ">Provincia</label>
                                    <select class="form-select" id="idprovincias" name="provincias_idprovincias" disabled>
                                    <?php foreach($result_provincia as $provincias): ?>
                                            <option value="<?php echo $provincias['idprovincias']; ?>"><?php echo $provincias['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Pais -->
                                <div class="col-md-2">
                                    <label for="tipo_contacto" class="form-label ">Pais</label>
                                    <select class="form-select" id="idpaises" name="paises_idpaises" disabled>
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
                    Datos del Auto
                </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="text-start mt-3">
                            <!-- Botón para abrir el modal de buscar auto -->
                            <button style="margin-bottom: 15px;" id="buscar_auto_btn" type="button" class="btn btn-action" data-bs-toggle="modal" data-bs-target="#buscarAutoModal">Buscar Auto</button>
                        </div>
                        <div class="col-md-8 col-lg-12">
                            <input type="hidden" name="vehiculos_idvehiculos" id="idvehiculos_1">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="id_patente" class="form-label">Patente</label>
                                    <input type="text" name="patente" class="form-control" id="id_patente" maxlength="7" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_chasis" class="form-label">Chasis</label>
                                    <input type="text" name="chasis" class="form-control" id="id_chasis" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_motor" class="form-label">Motor</label>
                                    <input type="text" name="motor" class="form-control" id="id_motor" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_anio" class="form-label">Año</label>
                                    <input type="text" name="año" class="form-control" id="id_anio" min="1960" max="2024" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="id_kilometraje" class="form-label">Kilometraje</label>
                                    <input type="text" name="kilometraje" class="form-control" id="id_kilometraje" readonly>
                                </div>

                                <div class="col-md-4">
                                    <label for="idcolores" class="form-label">Color</label>
                                    <select name="colores_idcolores" id="id_colores" class="form-select" disabled>
                                        <option value="">Seleccione un Color</option>
                                        <?php foreach($result_color as $colores): ?>
                                            <option value="<?php echo $colores['idcolores']; ?>"><?php echo $colores['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="idmarcas" class="form-label">Marca</label>
                                    <select name="idmarcas" id="id_marcas" class="form-select" disabled>
                                        <option value="">Seleccione una Marca</option>
                                        <?php foreach($result_marca as $marcas): ?>
                                            <option value="<?php echo $marcas['idmarcas']; ?>"><?php echo $marcas['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="idmodelos" class="form-label">Modelo</label>
                                    <select name="modelos_idmodelos" id="idmodelos" class="form-select" disabled>
                                        <option value="">Seleccione un Modelo</option>
                                        <?php foreach($result_modelo as $modelos): ?>
                                            <option value="<?php echo $modelos['idmodelos']; ?>"><?php echo $modelos['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="idtipo_vehiculos" class="form-label">Tipo</label>
                                    <select name="tipo_vehiculos_idtipo_vehiculos" id="id_tipo_vehiculos" class="form-select" disabled>
                                        <option value="">Seleccione un Tipo</option>
                                        <?php foreach($result_tipo_vehiculo as $tipo_vehiculos): ?>
                                            <option value="<?php echo $tipo_vehiculos['idtipo_vehiculos']; ?>"><?php echo $tipo_vehiculos['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Botón "Ver Ficha Técnica" debajo de los datos del auto y por encima de los datos de la venta -->
                        <div class="text-start mt-3">
                            <button id="verFichaTecnicaBtn" type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#fichaTecnicaModal">Ver Ficha Técnica</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Datos de la Venta
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <!-- Pregunta con radios -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label d-block">¿Tiene un vehículo como parte de pago?</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="parte_de_pago" id="parte_pago_si" value="si">
                                    <label class="form-check-label" for="parte_pago_si">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="parte_de_pago" id="parte_pago_no" value="no" checked>
                                    <label class="form-check-label" for="parte_pago_no">No</label>
                                </div>
                            </div>

                            <!-- Input + botón ocultos -->
                            <div class="col-md-12 mb-3" id="parte_pago_contenedor" style="display: none;">
                                <label for="vehiculo_busqueda">Buscar vehículo:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="vehiculo_busqueda" name="vehiculo_busqueda" placeholder="Ingrese datos del vehículo">
                                    <button class="btn btn-primary" type="button" id="buscarVehiculo">Buscar</button>
                                </div>
                            </div>

                            <!-- Tipo de pago -->
                            <div class="col-md-6 mb-3">
                                <label for="tipoPago" class="form-label">Tipo de Pago:</label>
                                <select class="form-select" id="tipoPago" name="tipo_pago">
                                    <?php foreach($resulta_tipo_pago as $tipo_pagos): ?>
                                        <option value="<?php echo $tipo_pagos['idtipo_pago']; ?>"><?php echo $tipo_pagos['descripcion']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Monto -->
                            <div class="col-md-6 mb-3">
                                <label for="monto" class="form-label">Monto:</label>
                                <input type="text" class="form-control" id="id_precio" name="id_precio" readonly>
                            </div>

                            <!-- Observaciones -->
                            <div class="col-12 mb-3">
                                <label for="observaciones" class="form-label">Observaciones:</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones finales -->
            <div class="d-flex justify-content-center mt-4">
                <a type="button" class="btn btn-secondary me-2" href="index.php?page=listado_ventas">Volver a la Lista de Ventas</a>
                <button type="submit" class="btn btn-action">Registrar Venta</button>
            </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#buscar_cliente_btn').click(function(e) {
        e.preventDefault(); // Evitar el envío del formulario
        $('#buscarClienteModal').modal('show'); // Mostrar el modal
    });

    $('#buscar_auto_btn').click(function(e) {
        e.preventDefault(); // Evitar el envío del formulario
        $('#buscarAutoModal').modal('show'); // Mostrar el modal
    });
        // Eliminar el preventDefault para el botón de registrar venta
        $('#ventaForm').submit(function(e) {
        // Aquí puedes añadir validaciones si lo deseas
        // e.preventDefault(); // No uses esto aquí si deseas que el formulario se envíe
    });
});

document.getElementById('parte_pago_si').addEventListener('change', function () {
    document.getElementById('parte_pago_contenedor').style.display = 'block';
});

document.getElementById('parte_pago_no').addEventListener('change', function () {
    document.getElementById('parte_pago_contenedor').style.display = 'none';
});

</script>

<script src="assets/js/json/traer_datos_cliente.js"></script>
<script src="assets/js/json/traer_datos_vehiculo.js"></script>

<script src="assets/js/validaciones/mis_datos/validar_pais.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_provincia.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_localidad.ajax.js"></script>

<script src="assets/js/validaciones/validar_marca.ajax.js"></script>
