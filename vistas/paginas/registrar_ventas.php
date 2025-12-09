<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['idusuarios'])){
    $idusuarios = $_SESSION['idusuarios'];
}

// ======= Traer idempleados =================
$empleado = new Empleado();
$idempleado = $empleado->traerEmpleadoPorUsuario($idusuarios);

if (!$idempleado) {
    echo "No se encontró el empleado para este usuario.";
    exit;
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
        <input type="hidden" name="idempleado" value="<?php echo $idempleado; ?>">
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
                            <input type="hidden" name="titular_vehiculo" id="titular_vehiculo">
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
                            <div class="col-12 mb-3 text-center" id="parte_pago_contenedor" style="display: none;">
                                <div class="input-group mx-auto" style="max-width: 350px;">
                                    <input type="text" id="buscar_patente" name="buscar_patente" class="form-control" placeholder="Buscar patente...">
                                    <button type="button" id="btn_buscar_patente" class="btn btn-success">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                    <button type="button" id="btn_limpiar_busqueda" class="btn btn-outline-danger" title="Limpiar búsqueda">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <input type="hidden" id="idvehiculo_parte_pago" name="idvehiculo_parte_pago">
                                    <input type="hidden" id="idcompras" name="idcompras">
                                </div>
                            </div>

                            <!-- Tabla oculta por defecto -->
                            <div id="resultado_busqueda" style="display: none;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID Compra</th>
                                            <th>Fecha</th>
                                            <th>Patente</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Año</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla_resultado"></tbody>
                                </table>
                            </div>

                            <!-- Tipo de pago -->
                            <div class="col-md-4 mb-3">
                                <label for="tipoPago" class="form-label">Tipo de Pago:</label>
                                <select class="form-select" id="tipoPago" name="tipo_pago">
                                    <?php foreach($resulta_tipo_pago as $tipo_pagos): ?>
                                        <option value="<?php echo $tipo_pagos['idtipo_pago']; ?>">
                                            <?php echo $tipo_pagos['descripcion']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Monto -->
                            <div class="col-md-4 mb-3">
                                <input type="hidden" name="precio_tomado" id="id_precio_tomado">
                                <input type="hidden" name="precio_publico_real" id="precio_publico_real">
                                <label for="id_precio_publico" class="form-label">Monto de la operación:</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control text-end" id="id_precio_publico" name="precio_publico" readonly>
                                </div>
                                <small class="form-text text-muted">
                                    Monto total del vehículo, antes de gastos o comisiones.
                                </small>
                            </div>

                            <!-- Observaciones generales -->
                            <div class="col-md-4 mb-3">
                                <label for="observaciones" class="form-label">Observaciones:</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                            </div>

                            <!-- 🔹 Bloque específico para TRANSFERENCIA -->
                            <div class="col-12" id="bloque_transferencia" style="display:none;">
                                <div class="bloque-pago-secundario bloque-transferencia">
                                    <h5>
                                        <i class="fa-solid fa-receipt"></i>
                                        Datos de la Transferencia
                                    </h5>
                                    <div class="row g-3 mt-1">
                                        <div class="col-md-4">
                                            <label for="banco_transferencia" class="form-label">Banco / Entidad:</label>
                                            <input type="text" class="form-control" id="banco_transferencia" name="banco_transferencia">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="nro_comprobante_transferencia" class="form-label">N° de comprobante:</label>
                                            <input type="text" class="form-control" id="nro_comprobante_transferencia" name="nro_comprobante_transferencia">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="fecha_transferencia" class="form-label">Fecha de transferencia:</label>
                                            <input type="date" class="form-control" id="fecha_transferencia" name="fecha_transferencia">
                                        </div>
                                    </div>
                                    <small class="form-text text-muted d-block mt-2">
                                        Verifique que el comprobante corresponda al monto total o al anticipo acordado.
                                    </small>
                                </div>
                            </div>

                            <!-- 🔹 Bloque específico para CRÉDITO BANCARIO -->
                            <div class="col-12" id="bloque_credito" style="display:none;">
                                <div class="bloque-pago-secundario bloque-credito">
                                    <h5>
                                        <i class="fa-solid fa-building-columns"></i>
                                        Datos del Crédito Bancario
                                    </h5>

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="banco_credito" class="form-label">Banco / Entidad:</label>
                                            <input type="text" class="form-control" id="banco_credito" name="banco_credito" placeholder="Ej: Banco Nación">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="monto_estimado_credito" class="form-label">Monto estimado a financiar:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input 
                                                    type="number" 
                                                    step="0.01" 
                                                    min="0" 
                                                    class="form-control text-end" 
                                                    id="monto_estimado_credito" 
                                                    name="monto_estimado_credito"
                                                >
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="observacion_credito" class="form-label">Referencia / N° de gestión (opcional):</label>
                                            <input type="text" class="form-control" id="observacion_credito" name="referencia_credito" placeholder="Ref. del banco / legajo">
                                        </div>

                                        <div class="col-12">
                                            <label for="nota_credito" class="form-label">Notas internas sobre el crédito:</label>
                                            <textarea 
                                                class="form-control" 
                                                id="nota_credito" 
                                                name="nota_credito" 
                                                rows="2" 
                                                placeholder="Ej: Se envió documentación al banco, a la espera de aprobación.">
                                            </textarea>
                                        </div>
                                    </div>

                                    <small class="form-text text-muted d-block mt-2">
                                        Esta venta quedará registrada como <strong>Pendiente por crédito</strong> hasta cargar la respuesta del banco.
                                    </small>
                                </div>
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
        </div>    
    </form>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const formVenta = document.getElementById("ventaForm");
    const loader = document.getElementById("loader-overlay");

    formVenta.addEventListener("submit", function () {
        loader.style.display = "flex";  // Mostrar loader
    });
});
</script>

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
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputPatente = document.getElementById('buscar_patente');
        const btnBuscar = document.getElementById('btn_buscar_patente');
        const btnLimpiar = document.getElementById('btn_limpiar_busqueda');
        const tablaResultado = document.getElementById('tabla_resultado');
        const contenedorResultado = document.getElementById('resultado_busqueda');
        const radiosPartePago = document.querySelectorAll('input[name="parte_de_pago"]');

        // 1️⃣ Mostrar u ocultar buscador según radio
        radiosPartePago.forEach(radio => {
            radio.addEventListener('change', function() {
                const contenedor = document.getElementById('parte_pago_contenedor');
                if (this.value === 'si') {
                    contenedor.style.display = 'block';
                } else {
                    contenedor.style.display = 'none';
                    contenedorResultado.style.display = 'none';
                    limpiarCampos();
                }
            });
        });

        // 2️⃣ Evitar que Enter envíe el formulario completo
        document.querySelector("form").addEventListener("keypress", function(e) {
            if (e.key === "Enter" && e.target.id !== "buscar_patente") {
                e.preventDefault();
            }
        });

        // 3️⃣ Buscar con Enter o Click
        btnBuscar.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            buscarVehiculo();
        });

        inputPatente.addEventListener('keypress', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                e.stopPropagation();
                buscarVehiculo();
            }
        });

        // 4️⃣ Botón limpiar búsqueda
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            limpiarCampos();
        });

        // 5️⃣ Función buscar vehículo por AJAX
        function buscarVehiculo() {
        const patentePartePago = inputPatente.value.trim().toUpperCase();
        const patentePrincipal = (document.getElementById('id_patente')?.value || '').trim().toUpperCase(); // 👈 la del modal principal

        // 1️⃣ Validar campo vacío
        if (patentePartePago.length === 0) {
            limpiarCampos();
            return;
        }

        // 2️⃣ Comparar con la patente del vehículo principal
        if (patentePrincipal && patentePartePago === patentePrincipal) {
            Swal.fire({
                icon: 'warning',
                title: 'Patente duplicada',
                text: 'Esta patente ya pertenece al vehículo principal de la venta. No puede utilizarse como parte de pago.',
                confirmButtonColor: '#52658F',
                confirmButtonText: 'Entendido'
            });
            limpiarCampos();
            return; // 🚫 Detiene la búsqueda AJAX
        }

        // 3️⃣ Ejecutar búsqueda si es diferente
        fetch('controladores/ventas/buscar_vehiculo_venta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'patente=' + encodeURIComponent(patentePartePago)
        })
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                const v = data[0];
                document.getElementById('idvehiculo_parte_pago').value = v.idvehiculos;
                document.getElementById('idcompras').value = v.idcompras;

                tablaResultado.innerHTML = `
                    <tr>
                        <td>${v.idcompras}</td>
                        <td>${v.fecha_compra}</td>
                        <td>${v.patente}</td>
                        <td>${v.nombre_marca}</td>
                        <td>${v.nombre_modelo}</td>
                        <td>${v.anio}</td>
                        <td>$${new Intl.NumberFormat('es-AR').format(v.precio)}</td>
                    </tr>
                `;
                contenedorResultado.style.display = 'block';
            } else {
                tablaResultado.innerHTML = `<tr><td colspan="7" class="text-muted">No se encontraron resultados</td></tr>`;
                contenedorResultado.style.display = 'block';
            }
        })
        .catch(err => console.error("Error AJAX:", err));
    }

        // 6️⃣ Función limpiar campos y ocultar tabla
        function limpiarCampos() {
            inputPatente.value = '';
            document.getElementById('idvehiculo_parte_pago').value = '';
            document.getElementById('idcompras').value = '';
            tablaResultado.innerHTML = '';
            contenedorResultado.style.display = 'none';
            inputPatente.focus();
        }
    });
</script>

<script>
document.getElementById('formPatente').addEventListener('submit', function(e) {
    e.preventDefault(); // evita la recarga de la página

    const formData = new FormData(this);

    fetch(this.action, {
        method: this.method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: data.error,
                confirmButtonText: 'OK',
                confirmButtonColor: '#52658F'
            }).then(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('buscarVehiculoModal'));
                if (modal) modal.hide();
            });
        } else {
            // acá podés continuar tu flujo normal si el vehículo existe
            console.log("Vehículo encontrado:", data);
            // por ejemplo, rellenar inputs de otro formulario o mostrar los datos
        }
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un problema al consultar el vehículo.'
        });
    });
});
</script>


<!-- 🔹 Validación del formulario principal -->
<script>
document.getElementById('ventaForm').addEventListener('submit', function(e) {
    const cliente = document.getElementById('id_usuario').value;
    const vehiculo = document.getElementById('idvehiculos_1').value;
    const titular = document.getElementById('titular_vehiculo').value;

    if (!cliente || !vehiculo || !titular) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Datos incompletos',
            text: 'Debe seleccionar cliente, vehículo y asegurarse de que el titular esté vinculado.',
            confirmButtonColor: '#52658F'
        });
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectTipoPago      = document.getElementById('tipoPago');
    const bloqueTransferencia = document.getElementById('bloque_transferencia');
    const bloqueCredito       = document.getElementById('bloque_credito');

    function actualizarBloquesPago() {
        const valor = selectTipoPago.value;

        // Ocultar todo por defecto
        bloqueTransferencia.style.display = 'none';
        bloqueCredito.style.display       = 'none';

        // 2 = Transferencia, 3 = Crédito Bancario (según tu tabla tipo_pago)
        if (valor === '2') {
            bloqueTransferencia.style.display = 'block';
        } else if (valor === '3') {
            bloqueCredito.style.display = 'block';
        }
    }

    // Al cambiar el select
    selectTipoPago.addEventListener('change', actualizarBloquesPago);

    // Al cargar la página, por si viene con un valor preseleccionado
    actualizarBloquesPago();
});
</script>


<script src="assets/js/json/traer_datos_cliente.js"></script>
<script src="assets/js/json/traer_datos_vehiculo.js"></script>

<script src="assets/js/validaciones/mis_datos/validar_pais.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_provincia.ajax.js"></script>
<script src="assets/js/validaciones/mis_datos/validar_localidad.ajax.js"></script>

<script src="assets/js/validaciones/validar_marca.ajax.js"></script>
