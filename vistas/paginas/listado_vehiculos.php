<?php

ini_set('display_errors', 1);

// Inicializar la clase Vehiculos
$vehiculos = new PrecioVehiculo();
$filas_por_pagina = 5;  // Número de filas que se muestran por página
$total_registros = 0;
$pagina_actual = isset($_GET['pagina_actual']) ? (int)$_GET['pagina_actual'] : 1;

$cantidad_vehiculo = new Vehiculos();

// Asegúrate de que la página actual nunca sea menor que 1
if ($pagina_actual < 1) {
    $pagina_actual = 1;
}

// Calcular el OFFSET
$inicio = ($pagina_actual - 1) * $filas_por_pagina;

// Si hay una búsqueda activa
if (isset($_GET['buscador']) && !empty($_GET['buscador'])) {
    $busqueda = $_GET['buscador'];
    $result_vehiculos = $cantidad_vehiculo->buscar_vehiculo($busqueda);
} else {
    // Si no hay búsqueda, manejar los filtros o traer todos los vehículos
    $filtros = [
        'marca' => $_GET['marca'] ?? null,
        'modelo' => $_GET['modelo'] ?? null,
        'color' => $_GET['color'] ?? null,
        'año' => $_GET['año'] ?? null,
        'tipo' => $_GET['tipo'] ?? null,
    ];

    if (array_filter($filtros)) {
        // Si hay filtros aplicados
        $result_vehiculos = $vehiculos->traer_los_vehiculos_con_precio($filtros, $inicio, $filas_por_pagina);
    } else {
        // Si no hay filtros, obtener el total de vehículos
        $result_vehiculos_total = $vehiculos->traer_cantidad_vehiculo();
        foreach ($result_vehiculos_total as $vehiculo_1) {
            $total_registros = $vehiculo_1['total'];
        }
        // Traer vehículos con paginación
        $result_vehiculos = $vehiculos->traer_los_vehiculos_con_precio($inicio, $filas_por_pagina);
    }
}

// Calcular el número total de páginas
$total_paginas = ceil($total_registros / $filas_por_pagina);



$color = new Colores();
$result_color = $color->traer_color();

$modelo = new Modelos_Vehiculos();
$result_modelo = $modelo->traer_modelos();

$tipo_vehiculo = new Tipo_Vehiculos();
$result_tipo_vehiculo = $tipo_vehiculo->traer_tipo_vehiculo();

$precio_vehiculo = new PrecioVehiculo();
$result_precio_vehiculo = $precio_vehiculo->traer_los_precios();



?>

    <!-- Modal de Agregar/Actualizar Precio -->
    <div class="modal fade" id="ActualizarPrecioModal" tabindex="-1" aria-labelledby="ActualizarPrecioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ActualizarPrecioModalLabel">Actualizar Precio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3 id="marcaModeloVehiculo"></h3> <!-- Elemento que estás intentando actualizar -->
                    <!-- Formulario para agregar o actualizar precio -->
                    <form id="vehiculo-form" method="POST" action="controladores/vehiculos/precio_vehiculo.controlador.php">
                        <input type="hidden" name="action" id="action" value="agregar">
                        <input type="hidden" name="idvehiculos" id="idvehiculos">
                        
                        <div class="row mb-3" id="precioActualContainer" style="display: none;">
                            <div class="col-md-6">
                                <label for="precio_actual" class="form-label">Precio Actual:</label>
                                <input oninput="formatearNumero(this)" type="text" id="precio_actual" name="precio_actual" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="precio_nuevo" class="form-label">Nuevo Precio:</label>
                                <input oninput="formatearNumero(this)" type="text" id="precio_nuevo_agregar" name="precio_nuevo" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Guardar Precio</button>
                        </div>
                    </form>
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

                    <form id="nuevo-vehiculo-form" method="POST" action="controladores/ficha_tecnica/ficha_tecnica.controlador.php">
                        <input type="hidden" name="action" value="guardar">
                        <input type="hidden" name="vehiculos_idvehiculos" id="vehiculos_idvehiculos"> <!-- Campo oculto para el id del vehículo -->

                        <!-- Revisión Técnica -->
                        <h2 class="mb-3">Revisión Técnica</h2>

                        <!-- Agrupamos fechas en dos columnas -->
                        <h6>Vencimientos</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vencimientoBateria" class="form-label">Batería</label>
                                <input type="date" class="form-control" id="vencimientoBateria" name="vencimiento_bateria">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vencimientoRTO" class="form-label">RTO - Revisión Técnica</label>
                                <input type="date" class="form-control" id="vencimientoRTO" name="vencimiento_rto">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vencimientoService" class="form-label">Service Automotor</label>
                                <input type="date" class="form-control" id="vencimientoService" name="vencimiento_service">
                            </div>
                        </div>

                        <!-- Agrupamos los checkboxes en dos columnas -->
                        <h6>Estado de Documentación</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switch08" name="switch_08">
                                    <label class="form-check-label" for="switch08">Formulario 08</label>
                                    <!-- Campo para cargar la imagen -->
                                    <div id="imageInputContainer" class="hidden mt-3">
                                        <label for="imageUpload" class="form-label">Elige una imagen:</label>
                                        <input type="file" id="imageUpload" accept="image/*" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switch12" name="switch_12">
                                    <label class="form-check-label" for="switch12">Formulario 12</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchTitulo" name="switch_titulo">
                                    <label class="form-check-label" for="switchTitulo">Título Automotor</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchCedula" name="switch_cedula">
                                    <label class="form-check-label" for="switchCedula">Cédula del Vehículo</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchSeguro" name="switch_seguro">
                                    <label class="form-check-label" for="switchSeguro">Seguro Automotor</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchMunicipalidad" name="switch_municipalidad">
                                    <label class="form-check-label" for="switchMunicipalidad">Municipalidad - Deudas</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchDominio" name="switch_dominio">
                                    <label class="form-check-label" for="switchDominio">Informe de Dominio</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchMultas" name="switch_multas">
                                    <label class="form-check-label" for="switchMultas">Formulario 13i - Infracciones/Multas</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchPrenda" name="switch_prenda">
                                    <label class="form-check-label" for="switchPrenda">Prenda</label>
                                </div>
                            </div>
                        </div>

                        <!-- Agrupamos los select en dos columnas -->
                        <h6>Estado Carrocería</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="descripcionCarroceria" class="form-label">Descripción de la Carrocería:</label>
                                <select id="descripcionCarroceria" name="descripcion_carroceria" class="form-select">
                                    <option value="Excelente">Excelente</option>
                                    <option value="Buena">Buena</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Mala">Mala</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="descripcionNeumaticos" class="form-label">Descripción de los Neumáticos:</label>
                                <select id="descripcionNeumaticos" name="descripcion_neumatico" class="form-select">
                                    <option value="Nuevos">Nuevos</option>
                                    <option value="Buen Estado">Buen Estado</option>
                                    <option value="Desgaste Medio">Desgaste Medio</option>
                                    <option value="Desgastados">Desgastados</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="descripcionCristales" class="form-label">Descripción de Cristales:</label>
                                <select id="descripcionCristales" name="descripcion_cristales" class="form-select">
                                    <option value="Sin Rayas">Sin Rayas</option>
                                    <option value="Con Rayas">Con Rayas</option>
                                    <option value="Rotura">Rotura</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botón de Enviar -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Guardar Datos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Imágenes y Documentos del Vehículo -->
    <div class="modal fade" id="DocumentosModal" tabindex="-1" aria-labelledby="DocumentosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DocumentosModalLabel">Documentación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3 id="marcaModeloVehiculoDoc"></h3> <!-- Aquí se mostrará la marca y modelo -->

                    <form id="documentos-form" method="POST" action="controladores/documentos/documentos.controlador.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="guardar">
                        <input type="hidden" name="vehiculos_idvehiculos" id="doc_idvehiculos"> <!-- Campo oculto para el id del vehículo -->

                        <!-- Sección de Imágenes del Vehículo -->
                        <div class="mb-4">
                            <h5>Imágenes del Vehículo</h5>
                            <input type="file" class="form-control" name="imagen_vehiculo[]" accept=".jpg, .jpeg, .png" multiple>
                            <small class="form-text text-muted">Subir imágenes en formato JPG o PNG.</small>
                        </div>
                        <div id="imagenesSubidas" class="mb-4">
                            <!-- Aquí se mostrarán las imágenes subidas -->
                        </div>

                        <!-- Sección de Documentación -->
                        <div class="mb-4">
                            <h5>Documentación</h5>
                            <input type="file" class="form-control" name="documentos_vehiculo[]" accept=".pdf" multiple>
                            <small class="form-text text-muted">Subir documentos en formato PDF.</small>
                        </div>
                        <div id="documentosSubidos" class="mb-4">
                            <!-- Aquí se mostrarán los documentos subidos -->
                        </div>

                        <!-- Botón de Enviar -->
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal de Ficha Técnica - Ver Mas -->
    <div class="modal fade" id="modalVerMas<?=$vehiculo_['idvehiculos']; ?>" tabindex="-1" aria-labelledby="fichaVerMasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fichaVerMasLabel">Ficha Técnica del Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3 id="marcaModeloVehiculoDoc"></h3> <!-- Aquí se mostrará la marca y modelo -->

                    <form id="nuevo-vehiculo-form" method="POST" action="controladores/ficha_tecnica/ficha_tecnica.controlador.php">
                        <input type="hidden" name="action" value="guardar">
                        <input type="hidden" name="vehiculos_idvehiculos" id="vehiculos_idvehiculos"> <!-- Campo oculto para el id del vehículo -->

                        <!-- Revisión Técnica -->
                        <h2 class="mb-3">Revisión Técnica</h2>

                        <!-- Agrupamos fechas en dos columnas -->
                        <h6>Vencimientos</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vencimientoBateria" class="form-label">Batería</label>
                                <input type="date" class="form-control" id="vencimientoBateria" name="vencimiento_bateria">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vencimientoRTO" class="form-label">RTO - Revisión Técnica</label>
                                <input type="date" class="form-control" id="vencimientoRTO" name="vencimiento_rto">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vencimientoService" class="form-label">Service Automotor</label>
                                <input type="date" class="form-control" id="vencimientoService" name="vencimiento_service">
                            </div>
                        </div>

                        <!-- Agrupamos los checkboxes en dos columnas -->
                        <h6>Estado de Documentación</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switch08" name="switch_08">
                                    <label class="form-check-label" for="switch08">Formulario 08</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switch12" name="switch_12">
                                    <label class="form-check-label" for="switch12">Formulario 12</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchTitulo" name="switch_titulo">
                                    <label class="form-check-label" for="switchTitulo">Título Automotor</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchCedula" name="switch_cedula">
                                    <label class="form-check-label" for="switchCedula">Cédula del Vehículo</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchSeguro" name="switch_seguro">
                                    <label class="form-check-label" for="switchSeguro">Seguro Automotor</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchMunicipalidad" name="switch_municipalidad">
                                    <label class="form-check-label" for="switchMunicipalidad">Municipalidad - Deudas</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchDominio" name="switch_dominio">
                                    <label class="form-check-label" for="switchDominio">Informe de Dominio</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchMultas" name="switch_multas">
                                    <label class="form-check-label" for="switchMultas">Formulario 13i - Infracciones/Multas</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="switchPrenda" name="switch_prenda">
                                    <label class="form-check-label" for="switchPrenda">Prenda</label>
                                </div>
                            </div>
                        </div>

                        <!-- Agrupamos los select en dos columnas -->
                        <h6>Estado Carrocería</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="descripcionCarroceria" class="form-label">Descripción de la Carrocería:</label>
                                <select id="descripcionCarroceria" name="descripcion_carroceria" class="form-select">
                                    <option value="Excelente">Excelente</option>
                                    <option value="Buena">Buena</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Mala">Mala</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="descripcionNeumaticos" class="form-label">Descripción de los Neumáticos:</label>
                                <select id="descripcionNeumaticos" name="descripcion_neumatico" class="form-select">
                                    <option value="Nuevos">Nuevos</option>
                                    <option value="Buen Estado">Buen Estado</option>
                                    <option value="Desgaste Medio">Desgaste Medio</option>
                                    <option value="Desgastados">Desgastados</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="descripcionCristales" class="form-label">Descripción de Cristales:</label>
                                <select id="descripcionCristales" name="descripcion_cristales" class="form-select">
                                    <option value="Sin Rayas">Sin Rayas</option>
                                    <option value="Con Rayas">Con Rayas</option>
                                    <option value="Rotura">Rotura</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botón de Enviar -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Guardar Datos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Vehículos</a></li>
        <li class="breadcrumb-item"><a href="#">Gestión de Vehículos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vehículos Disponibles</li>
    </ol>
</nav>


    <div class="col hacer_padding">
        <h1 class="text-center mb-4">Vehículos Disponibles</h1>

        <!-- Contenedor para centrar el botón y el buscador -->
        <div class="d-flex justify-content-center align-items-center mb-4">
            <!-- Botón de Registrar Nuevo Vehículo -->
            <a type="button" class="btn me-3  btn-action" href="index.php?page=registrar_vehiculos&accion=registrar">Registrar Nuevo Vehículo</a>

            <!-- Buscador -->
            <div class="d-flex">
                <input name="buscador" id="idbuscador" class="form-control form-control-sm me-2" type="search" placeholder="Buscar por Patente - Marca - Modelo" aria-label="Buscar" style="width: 250px;">
                <button class="btn btn-success btn-sm" type="submit" onclick="buscador()">Buscar</button>
            </div>
        </div>
        <div class="d-flex mb-2">
            <form method="GET" action="controladores/vehiculos/exportar_excel.php" class="d-inline">
                <input type="hidden" name="buscador" value="<?= $_GET['buscador'] ?? '' ?>">
                <button type="submit" class="btn btn-success btn-sm">
                    Exportar a Excel
                </button>
            </form>
        </div>
        <!-- Tabla centrada -->
        <div class="table-responsive">
            <table class="table table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>Patente</th>
                        <th>Año</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Tipo</th>
                        <th>Precios</th>
                        <th>Actualizar Precio</th>
                        <th>Ficha Técnica</th>
                        <th>Modificar</th>
                        <th>Agregar</th>
                        <th>Eliminar</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result_vehiculos as $vehiculo_) { ?>
                        <tr>
                            <td><?= $vehiculo_['patente']; ?></td>
                            <td><?= $vehiculo_['anio']; ?></td>
                            <td><?= $vehiculo_['nombre_marca']; ?></td>
                            <td><?= $vehiculo_['nombre_modelo']; ?></td>
                            <td><?= $vehiculo_['nombre_tipo']; ?></td>
                            <td>
                                <?php if(isset($vehiculo_['precio'])): ?>
                                    <?= $vehiculo_['precio']; ?>
                                <?php else: ?>
                                    <a title="Agregar Precio" href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ActualizarPrecioModal" 
                                    data-marca="<?= $vehiculo_['nombre_marca']; ?>" 
                                    data-modelo="<?= $vehiculo_['nombre_modelo']; ?>" 
                                    data-año="<?= $vehiculo_['anio']; ?>"
                                    data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                    <i class="fa-solid fa-sack-dollar"></i>
                                    </a>
                                <?php endif; ?>
                            </td>

                            <td>
                            <?php if (isset($vehiculo_['precio'])): ?>
                                <a title="Actualizar Precio" href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ActualizarPrecioModal" 
                                    data-marca="<?= $vehiculo_['nombre_marca']; ?>" 
                                    data-modelo="<?= $vehiculo_['nombre_modelo']; ?>" 
                                    data-año="<?= $vehiculo_['anio']; ?>"
                                    data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>" 
                                    data-precio="<?= $vehiculo_['precio']; ?>">
                                    <i class="fa-solid fa-arrow-rotate-right"></i>
                                    </a>
                            <?php endif; ?>
                            </td>
                            <td>
                                <!-- Botón para abrir el modal, con los datos de marca y modelo -->
                                <a title="Agregar Ficha" href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fichaTecnicaModal" data-marca="<?= $vehiculo_['nombre_marca']; ?>" data-modelo="<?= $vehiculo_['nombre_modelo']; ?>" data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                    <i class="fa-solid fa-gears"></i>
                                </a>
                            </td>
                            <td>
                                <a href="index.php?page=registrar_vehiculos&idvehiculos=<?= $vehiculo_['idvehiculos']; ?>" class="btn btn-success" title="Modificar Auto">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                            <td>
                                <a title="Agregar img/doc" href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#DocumentosModal" data-marca="<?= $vehiculo_['nombre_marca']; ?>" data-modelo="<?= $vehiculo_['nombre_modelo']; ?>" data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                    <i class="fa-solid fa-file-circle-plus"></i>
                            </a>
                            </td>
                            <td>
                                <form id="formulario-eliminar-<?= $vehiculo_['idvehiculos']; ?>" method="POST" action="controladores/vehiculos/vehiculos.controlador.php">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="idvehiculos" value="<?= $vehiculo_['idvehiculos'] ?>">
                                    <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $vehiculo_['idvehiculos']; ?>')" class="btn btn-danger" type="submit" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning" title="Ver Más" data-bs-toggle="modal" data-bs-target="#modalVerMas<?= $vehiculo_['idvehiculos']; ?>">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación centrada -->
        <nav aria-label="..." class="d-flex justify-content-center">
            <ul class="pagination">
                <!-- Botón "Anterior" -->
                <li class="page-item <?php if ($pagina_actual <= 1) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $pagina_actual - 1 ?>" aria-disabled="true">Previo</a>
                </li>

                <!-- Botones de número de página -->
                <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                    <li class="page-item <?php if ($pagina_actual == $i) { echo 'active'; } ?>">
                        <a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php } ?>

                <!-- Botón "Siguiente" -->
                <li class="page-item <?php if ($pagina_actual >= $total_paginas) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $pagina_actual + 1 ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    var fichaTecnicaModal = document.getElementById('fichaTecnicaModal');

    fichaTecnicaModal.addEventListener('show.bs.modal', function (event) {
    // Botón que disparó el modal
    var button = event.relatedTarget;
    
    // Extraer la información de los atributos data-*
    var marca = button.getAttribute('data-marca');
    var modelo = button.getAttribute('data-modelo');
    var idvehiculo = button.getAttribute('data-idvehiculo');

    // Actualizar el título con la marca y el modelo
    var marcaModeloText = marca + ' - ' + modelo;
    var marcaModeloVehiculo = document.getElementById('marcaModeloVehiculo');
    marcaModeloVehiculo.textContent = marcaModeloText;

    // Actualizar el campo hidden con el id del vehículo
    var inputIdVehiculo = document.getElementById('vehiculos_idvehiculos');
    inputIdVehiculo.value = idvehiculo;
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var documentosModal = document.getElementById('DocumentosModal');

    documentosModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var marca = button.getAttribute('data-marca');
        var modelo = button.getAttribute('data-modelo');
        var idvehiculo = button.getAttribute('data-idvehiculo');
        var marcaModeloText = marca + ' - ' + modelo;

        document.getElementById('marcaModeloVehiculo').textContent = marcaModeloText;
        document.getElementById('doc_idvehiculos').value = idvehiculo;

        $.ajax({
            url: 'controladores/documentos/obtener_documentos.php',
            method: 'POST',
            data: { vehiculos_idvehiculos: idvehiculo },
            dataType: 'json',
            success: function(response) {
                $('#imagenesSubidas').html(response.imagenes);
                $('#documentosSubidos').html(response.documentos);

                // Añadir eventos para ampliar imagen al hacer clic
                $('#imagenesSubidas img').on('click', function() {
                    var src = $(this).attr('src');
                    showImageModal(src);
                });

                // Añadir eventos para eliminar imagen
                $('.delete-image').on('click', function() {
                    var imageId = $(this).data('image-id');
                    deleteFile(imageId, 'image');
                });

                // Añadir eventos para eliminar documento
                $('.delete-document').on('click', function() {
                    var docId = $(this).data('doc-id');
                    deleteFile(docId, 'document');
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error en la petición AJAX: " + textStatus, errorThrown);
            }
        });
    });

    // Función para mostrar la imagen en un modal de vista ampliada
    function showImageModal(src) {
        var modalHtml = `
            <div class="modal fade" id="imageViewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${src}" class="img-fluid" alt="Imagen del Vehículo">
                        </div>
                    </div>
                </div>
            </div>`;
        $('body').append(modalHtml);
        $('#imageViewModal').modal('show');

        // Eliminar el modal del DOM al cerrarlo
        $('#imageViewModal').on('hidden.bs.modal', function () {
            $(this).remove();
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var fichaTecnicaModal = document.getElementById('ActualizarPrecioModal');

    fichaTecnicaModal.addEventListener('show.bs.modal', function (event) {
        // Botón que disparó el modal
        var button = event.relatedTarget;

        // Extraer la información de los atributos data-*
        var marca = button.getAttribute('data-marca');
        var modelo = button.getAttribute('data-modelo');
        var año = button.getAttribute('data-año');
        var idvehiculo = button.getAttribute('data-idvehiculo');
        var precio = button.getAttribute('data-precio');

        // Actualizar el título con la marca y el modelo
        var marcaModeloText = marca + ' - ' + modelo + ' - Año: ' + año;
        var marcaModeloVehiculo = document.getElementById('marcaModeloVehiculo');
        marcaModeloVehiculo.textContent = marcaModeloText;

        // Actualizar el campo hidden con el id del vehículo
        var inputIdVehiculo = document.getElementById('idvehiculos');
        inputIdVehiculo.value = idvehiculo;

        // Obtener elementos del formulario
        var precioActualContainer = document.getElementById('precioActualContainer');
        var precioActualInput = document.getElementById('precio_actual');
        var precioNuevoInput = document.getElementById('precio_nuevo_agregar');
        var actionInput = document.getElementById('action');

        if (precio && precio !== '') {
            // Caso de actualización de precio
            actionInput.value = 'actualizar';
            precioActualContainer.style.display = 'block'; // Mostrar el precio actual
            precioActualInput.value = precio; // Mostrar el precio actual en el campo de solo lectura
            precioNuevoInput.value = ''; // Limpiar el campo de nuevo precio
        } else {
            // Caso de agregar precio
            actionInput.value = 'agregar';
            precioActualContainer.style.display = 'none'; // Ocultar el campo de precio actual
            precioActualInput.value = ''; // Limpiar el campo de precio actual
            precioNuevoInput.value = ''; // Limpiar el campo de nuevo precio
        }
    });
});


</script>

<script>
    function eliminarDocumento(id) {
        if (confirm("¿Está seguro de que desea eliminar este documento?")) {
            $.ajax({
                url: 'controladores/documentos/eliminar_documentos.php',
                type: 'POST',
                data: { idDocumentacion: id },
                success: function(response) {
                    alert('Documento eliminado');
                    location.reload();
                },
                error: function() {
                    alert('Error al eliminar el documento.');
                }
            });
        }
    }
</script>


<script>
    function buscador(){
        let buscador = document.getElementById('idbuscador').value;
        console.log(buscador);
        location.href='index.php?page=listado_vehiculos&buscador='+buscador;
    }

</script>

<script>
    const switchCheckbox = document.getElementById("switch08");
    const imageInputContainer = document.getElementById("imageInputContainer");

    switchCheckbox.addEventListener("change", function() {
        if (switchCheckbox.checked) {
            imageInputContainer.classList.remove("hidden");
        } else {
            imageInputContainer.classList.add("hidden");
        }
    });
</script>

<script>
        function formatearNumero(input) {
            // Remueve cualquier carácter que no sea número
            let valor = input.value.replace(/\D/g, '');
            
            // Formatea el número con separadores de miles
            valor = new Intl.NumberFormat('es-ES').format(valor);
            
            // Actualiza el valor del input
            input.value = valor;
        }
</script>


