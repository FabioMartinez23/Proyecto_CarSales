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
    $result_vehiculos = $cantidad_vehiculo->buscar_vehiculo($busqueda, 1);
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
        $result_vehiculos = $vehiculos->traer_los_vehiculos_con_precio_filtrado($filtros, $inicio, $filas_por_pagina, 1);
    } else {
        // Si no hay filtros, obtener el total de vehículos
        $result_vehiculos_total = $vehiculos->traer_cantidad_vehiculo();
        foreach ($result_vehiculos_total as $vehiculo_1) {
            $total_registros = $vehiculo_1['total'];
        }
        // Traer vehículos con paginación
        $result_vehiculos = $vehiculos->traer_los_vehiculos_con_precio($inicio, $filas_por_pagina, 1);
    }
}

// Calcular el número total de páginas
$total_paginas = ceil($total_registros / $filas_por_pagina);



$color = new Colores();
$result_color = $color->traer_color();

$marca = new Marcas();
$result_marca = $marca->traer_marca();

$modelo = new Modelos_Vehiculos();
$result_modelo = $modelo->traer_modelos_filtro();

$tipo_vehiculo = new Tipo_Vehiculos();
$result_tipo_vehiculo = $tipo_vehiculo->traer_tipo_vehiculo();

$años = new Vehiculos();
$result_años = $años->traer_año_vehiculo();

$precio_vehiculo = new PrecioVehiculo();
$result_precio_vehiculo = $precio_vehiculo->traer_los_precios();

$carroceria = new Carrocerias();
$result_carroceria =  $carroceria->traer_carroceria();

$cristal = new Cristales();
$result_cristal = $cristal->traer_cristal();

$neumatico = new Neumaticos();
$result_neumatico = $neumatico->traer_neumatico();

$intereses = new Intereses();
$result_intereses = $intereses->traer_interes();

?>

    <!-- ============================================================= -->
    <!-- MODAL DE AGREGAR / ACTUALIZAR PRECIO VEHÍCULO -->
    <!-- ============================================================= -->
    <div class="modal fade" id="ActualizarPrecioModal" tabindex="-1" aria-labelledby="ActualizarPrecioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-modal text-white">
                <h5 class="modal-title" id="ActualizarPrecioModalLabel">
                <i class="fa-solid fa-sack-dollar me-2"></i> Gestión de Precio del Vehículo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <h4 id="marcaModeloVehiculo" class="text-center mb-4 fw-semibold text-dark"></h4>

                <form id="vehiculo-form" method="POST" action="controladores/vehiculos/precio_vehiculo.controlador.php">
                <input type="hidden" name="action" id="action" value="agregar">
                <input type="hidden" name="idvehiculos" id="idvehiculos">

                <!-- =================================================== -->
                <!-- FILA COMPACTA DE PRECIOS ACTUALES -->
                <!-- =================================================== -->
                <div class="row mb-4 justify-content-center" id="preciosActualesFila" style="display: none;">
                    <div class="col-md-4 text-center">
                    <label class="form-label fw-bold text-secondary">Precio Tomado</label>
                    <p id="precio_actual_texto" class="form-control-plaintext fs-5 fw-semibold text-dark mb-0"></p>
                    <input type="hidden" id="precio_actual" name="precio_actual">
                    </div>
                    <div class="col-md-4 text-center">
                    <label class="form-label fw-bold text-secondary">Precio al Público</label>
                    <p id="precio_publico_texto" class="form-control-plaintext fs-5 fw-semibold text-dark mb-0"></p>
                    <input type="hidden" id="precio_publico" name="precio_publico">
                    </div>
                    <div class="col-md-4 text-center">
                    <label class="form-label fw-bold text-secondary">Interés Aplicado</label>
                    <p id="interes_aplicado_texto" class="form-control-plaintext fs-5 fw-semibold text-dark mb-0"></p>
                    </div>
                </div>

                <!-- NUEVO PRECIO TOMADO -->
                <div class="row mb-3" id="nuevoPrecioTomadoContainer">
                    <div class="col-md-6">
                    <label for="precio_nuevo_agregar" class="form-label fw-bold text-secondary">Nuevo Precio Tomado:</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input oninput="formatearNumero(this)" type="text" id="precio_nuevo_agregar" name="precio_nuevo" class="form-control" placeholder="0.00">
                    </div>
                    </div>
                </div>

                <!-- INTERÉS Y NUEVO PRECIO PÚBLICO -->
                <div class="row mb-3" id="precioPublicoContainer">
                    <div class="col-md-6">
                    <label for="interes" class="form-label fw-bold text-secondary">Interés (%):</label>
                    <select id="interes" name="interes_id" class="form-select">
                        <option value="">Seleccionar interés</option>
                        <?php while ($row = $result_intereses->fetch_assoc()): ?>
                        <option value="<?= $row['idintereses']; ?>" data-porcentaje="<?= $row['porcentaje']; ?>">
                        <?= $row['descripcion']; ?> (<?= $row['porcentaje']; ?>%)
                        </option>
                        <?php endwhile; ?>
                    </select>
                    </div>
                    <div class="col-md-6">
                    <label for="precio_calculado" class="form-label fw-bold text-secondary">Nuevo Precio al Público:</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" id="precio_calculado" name="precio_calculado" class="form-control bg-light" readonly placeholder="Calculado automáticamente">
                    </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-action">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Precio
                    </button>
                </div>
                </form>
            </div>
            </div>
        </div>
    </div>


    <!-- ========================================================= -->
    <!-- MODAL FICHA TÉCNICA (Vehículo Disponible)                 -->
    <!-- ========================================================= -->
    <div class="modal fade" id="fichaTecnicaModal" tabindex="-1" aria-labelledby="fichaTecnicaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
        <div class="modal-header bg-modal text-white">
            <h5 class="modal-title">
            <i class="fa-solid fa-gears me-2"></i> Ficha Técnica del Vehículo
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
            <h4 id="marcaModeloVehiculo" class="text-center mb-4 fw-semibold text-secondary"></h4>

            <form id="ficha-tecnica-form" method="POST" action="controladores/ficha_tecnica/ficha_tecnica.controlador.php">
            <input type="hidden" name="action" value="guardar">
            <input type="hidden" name="vehiculos_idvehiculos" id="vehiculos_idvehiculos">

            <!-- ==================== REVISIÓN TÉCNICA ==================== -->
            <h5 class="border-bottom pb-2 mb-3">Revisión Técnica</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                <label for="vencimientoBateria" class="form-label">Batería</label>
                <input type="date" class="form-control" id="vencimientoBateria" name="vencimiento_bateria">
                </div>
                <div class="col-md-4 mb-3">
                <label for="vencimientoRTO" class="form-label">RTO</label>
                <input type="date" class="form-control" id="vencimientoRTO" name="vencimiento_rto">
                </div>
                <div class="col-md-4 mb-3">
                <label for="vencimientoService" class="form-label">Service</label>
                <input type="date" class="form-control" id="vencimientoService" name="vencimiento_service">
                </div>
            </div>

            <!-- ==================== ESTADO DEL VEHÍCULO ==================== -->
            <h5 class="border-bottom pb-2 mb-3">Estado del Vehículo</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                <label for="descripcionCarroceria" class="form-label">Carrocería</label>
                <select id="descripcionCarroceria" name="descripcion_carroceria" class="form-select">
                    <?php foreach($result_carroceria as $carroceria): ?>
                    <option value="<?= $carroceria['idcarroceria']; ?>">
                        <?= htmlspecialchars($carroceria['descripcion_carroceria']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                </div>

                <div class="col-md-4 mb-3">
                <label for="descripcionNeumaticos" class="form-label">Neumáticos</label>
                <select id="descripcionNeumaticos" name="descripcion_neumatico" class="form-select">
                    <?php foreach($result_neumatico as $neumatico): ?>
                    <option value="<?= $neumatico['idneumaticos']; ?>">
                        <?= htmlspecialchars($neumatico['descripcion_neumaticos']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                </div>

                <div class="col-md-4 mb-3">
                <label for="descripcionCristales" class="form-label">Cristales</label>
                <select id="descripcionCristales" name="descripcion_cristales" class="form-select">
                    <?php foreach($result_cristal as $cristal): ?>
                    <option value="<?= $cristal['idcristales']; ?>">
                        <?= htmlspecialchars($cristal['descripcion_cristales']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>

            <!-- ==================== DOCUMENTACIÓN ==================== -->
            <h5 class="border-bottom pb-2 mb-3">Documentación del Vehículo</h5>
            <div id="contenedorDocumentacion" class="p-3 border rounded bg-light text-center">
                <p class="text-muted">Cargando documentación...</p>
            </div>

            <!-- ==================== BOTÓN GUARDAR ==================== -->
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-action px-4">
                <i class="fa-solid fa-pen-to-square me-1"></i> Actualizar Ficha Técnica
                </button>
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
                <div class="modal-header bg-modal text-white">
                    <h5 class="modal-title" id="DocumentosModalLabel"><i class="fa-solid fa-file-invoice me-2"></i>Documentación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <button type="submit" class="btn btn-action">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Breadcrumb -->
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
                <input name="buscador" id="idbuscador" class="form-control form-control-sm me-2" type="search" placeholder="Buscar por Patente - Marca - Modelo" aria-label="Buscar" style="width: 250px;" onkeydown="if(event.key === 'Enter'){ buscador(); }">
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
            <section class="d-flex w-100">
                <aside class="w-100">
                    <!-- Botón para mostrar/ocultar filtros -->
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-info mb-3" type="button" id="toggleFiltros" onclick="toggleFiltros()">Mostrar Filtros&nbsp;&nbsp;<i class="fa-solid fa-chevron-down"></i></button>
                    </div>

                    <!-- Filtros ocultables -->
                    <div id="filtros" style="display: none;" class="border p-3 mb-4 rounded-3 bg-light">
                        <form method="GET" action="index.php" class="d-flex flex-wrap gap-3 align-items-end">
                            <input type="hidden" name="page" value="listado_vehiculos">
                            <div class="form-floating mb-3 mt-3">
                                <select name="marca" class="form-select form-select-sm" id="marca">
                                    <option value="">Seleccionar Marca</option>
                                    <?php foreach ($result_marca as $marca) { ?>
                                        <option value="<?= $marca['nombre'] ?>"><?= $marca['nombre'] ?></option>
                                        <?php } ?>
                                    </select>
                                <label for="marca" class="form-label">Marca</label>
                            </div>

                            <div class="form-floating mb-3 mt-3">
                                <select name="modelo" class="form-select form-select-sm" id="modelos">
                                    <option value="">Seleccionar Modelo</option>
                                    <?php foreach ($result_modelo as $modelo) { ?>
                                        <option value="<?= $modelo['nombre'] ?>"><?= $modelo['nombre'] ?></option>
                                        <?php } ?>
                                    </select>
                                <label for="modelo" class="form-label">Modelo</label>
                            </div>

                            <div class="form-floating mb-3 mt-3">
                                <select name="color" class="form-select form-select-sm" id="color">
                                    <option value="">Seleccionar Color</option>
                                    <?php foreach ($result_color as $color) { ?>
                                        <option value="<?= $color['descripcion'] ?>"><?= $color['descripcion'] ?></option>
                                        <?php } ?>
                                    </select>
                                <label for="color" class="form-label">Color</label>
                            </div>

                            <div class="form-floating mb-3 mt-3">
                                <select name="anio" class="form-select form-select-sm" id="anio">
                                    <option value="">Seleccionar Año</option>
                                    <?php foreach ($result_años as $año) { ?>
                                        <option value="<?= $año['anio'] ?>"><?= $año['anio'] ?></option>
                                        <?php } ?>
                                    </select>
                                <label for="anio" class="form-label">Año</label>
                            </div>

                            <div class="form-floating mb-3 mt-3">
                                <select name="tipo" class="form-select form-select-sm" id="tipo">
                                    <option value="">Seleccionar Tipo</option>
                                    <?php foreach ($result_tipo_vehiculo as $tipo) { ?>
                                        <option value="<?= $tipo['nombre'] ?>"><?= $tipo['nombre'] ?></option>
                                        <?php } ?>
                                    </select>
                                <label for="tipo" class="form-label">Tipo de Vehículo</label>
                            </div>

                            <button class="btn btn-primary btn-sm" type="submit">Aplicar Filtros</button>
                        </form>
                    </div>
                    
                </aside>
            </section>
        </div>
        <!-- Tabla centrada -->
        <?php
        // Convertir el resultado a array contable
        $result_vehiculos_array = [];
        if ($result_vehiculos) {
            while ($row = $result_vehiculos->fetch_assoc()) {
                $result_vehiculos_array[] = $row;
            }
        }
        ?>
        <div class="table-responsive">
            <table class="table table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>Patente</th>
                        <th>Año</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Tipo</th>
                        <?php if(isset($_SESSION['descripcion']) && $_SESSION['descripcion'] == 'Administrador'): ?>
                            <th>Precio Tomado</th>
                            <th>Precio Público</th>
                            <th>Actualizar Precio</th>
                            <th>Ficha Técnica</th>
                            <th>Modificar</th>
                            <th>Agregar</th>
                            <th>Eliminar</th>
                        <?php else: ?>
                            <th>Precio Público</th>
                            <th>Ficha Técnica</th>
                            <th>Modificar</th>
                            <th>Agregar</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                <?php if (!empty($result_vehiculos_array)): ?>
                    <?php foreach ($result_vehiculos_array as $vehiculo_): ?>
                        <tr>
                            <td><?= htmlspecialchars($vehiculo_['patente']); ?></td>
                            <td><?= htmlspecialchars($vehiculo_['anio']); ?></td>
                            <td><?= htmlspecialchars($vehiculo_['nombre_marca']); ?></td>
                            <td><?= htmlspecialchars($vehiculo_['nombre_modelo']); ?></td>
                            <td><?= htmlspecialchars($vehiculo_['nombre_tipo']); ?></td>

                            <!-- Solo ADMIN: Precio Tomado -->
                            <?php if(isset($_SESSION['descripcion']) && $_SESSION['descripcion'] == 'Administrador'): ?>
                                <td>
                                    <?php if (!empty($vehiculo_['precio_tomado'])): ?>
                                        <span class="fw-bold text-success">
                                            $<?= number_format($vehiculo_['precio_tomado'], 0, ',', '.'); ?>
                                        </span>
                                    <?php else: ?>
                                        <a title="Agregar Precio Tomado" href="#" class="btn btn-outline-success btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#ActualizarPrecioModal" 
                                            data-marca="<?= htmlspecialchars($vehiculo_['nombre_marca']); ?>" 
                                            data-modelo="<?= htmlspecialchars($vehiculo_['nombre_modelo']); ?>" 
                                            data-año="<?= htmlspecialchars($vehiculo_['anio']); ?>"
                                            data-idvehiculo="<?= htmlspecialchars($vehiculo_['idvehiculos']); ?>"
                                            data-tipo="tomado"
                                            data-precio="<?= htmlspecialchars($vehiculo_['precio_tomado'] ?? ''); ?>">
                                            <i class="fa-solid fa-sack-dollar"></i> Ingresar precio tomado
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>

                            <!-- Precio Público -->
                            <td>
                                <?php if (!empty($vehiculo_['precio_publico'])): ?>
                                    <span class="fw-bold text-primary">
                                        $<?= number_format($vehiculo_['precio_publico'], 0, ',', '.'); ?>
                                    </span>
                                <?php else: ?>
                                    <?php if(isset($_SESSION['descripcion']) && $_SESSION['descripcion'] == 'Administrador'): ?>
                                        <a title="Agregar Precio Público" href="#" class="btn btn-outline-primary btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#ActualizarPrecioModal" 
                                            data-marca="<?= htmlspecialchars($vehiculo_['nombre_marca']); ?>" 
                                            data-modelo="<?= htmlspecialchars($vehiculo_['nombre_modelo']); ?>" 
                                            data-anio="<?= htmlspecialchars($vehiculo_['anio']); ?>"
                                            data-idvehiculo="<?= htmlspecialchars($vehiculo_['idvehiculos']); ?>"
                                            data-tipo="publico"
                                            data-precio="<?= htmlspecialchars($vehiculo_['precio_tomado'] ?? ''); ?>">
                                            <i class="fa-solid fa-tags"></i> Ingresar precio al público
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">
                                            Sin Precio Público – Comunicarse con Administración
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <!-- Solo ADMIN: Botón actualizar precios -->
                            <?php if(isset($_SESSION['descripcion']) && $_SESSION['descripcion'] == 'Administrador'): ?>
                                <td>
                                    <a title="Actualizar Precios" href="#" class="btn btn-warning btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#ActualizarPrecioModal"
                                        data-idvehiculo="<?= htmlspecialchars($vehiculo_['idvehiculos']); ?>"
                                        data-marca="<?= htmlspecialchars($vehiculo_['nombre_marca']); ?>"
                                        data-modelo="<?= htmlspecialchars($vehiculo_['nombre_modelo']); ?>"
                                        data-anio="<?= htmlspecialchars($vehiculo_['anio']); ?>"
                                        data-tipo="actualizar"
                                        data-precio="<?= htmlspecialchars($vehiculo_['precio_tomado']); ?>"
                                        data-precio-publico="<?= htmlspecialchars($vehiculo_['precio_publico']); ?>"
                                        data-interes="<?= htmlspecialchars($vehiculo_['porcentaje_interes_publico']); ?>">
                                        <i class="fa-solid fa-arrow-rotate-right"></i>
                                    </a>
                                </td>
                            <?php endif; ?>

                            <!-- Ficha técnica -->
                            <td>
                                <a title="Ver / Editar Ficha Técnica" href="#" class="btn btn-primary btn-sm" 
                                    data-bs-toggle="modal" data-bs-target="#fichaTecnicaModal" 
                                    data-marca="<?= htmlspecialchars($vehiculo_['nombre_marca']); ?>" 
                                    data-modelo="<?= htmlspecialchars($vehiculo_['nombre_modelo']); ?>" 
                                    data-idvehiculo="<?= htmlspecialchars($vehiculo_['idvehiculos']); ?>" 
                                    data-estado="<?= htmlspecialchars($vehiculo_['nombre_estado']); ?>">
                                    <i class="fa-solid fa-gears"></i>
                                </a>
                            </td>

                            <!-- Modificar vehículo -->
                            <td>
                                <a href="index.php?page=registrar_vehiculos&idvehiculos=<?= htmlspecialchars($vehiculo_['idvehiculos']); ?>" 
                                class="btn btn-success btn-sm" title="Modificar Auto">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>

                           <!-- Agregar Documentación -->
                            <td>
                                <a title="Agregar img/doc" href="#" class="btn btn-info btn-sm" 
                                    data-bs-toggle="modal" data-bs-target="#DocumentosModal"
                                    data-marca="<?= htmlspecialchars($vehiculo_['nombre_marca']); ?>" 
                                    data-modelo="<?= htmlspecialchars($vehiculo_['nombre_modelo']); ?>" 
                                    data-idvehiculo="<?= htmlspecialchars($vehiculo_['idvehiculos']); ?>">
                                    <i class="fa-solid fa-file-circle-plus"></i>
                                </a>
                            </td>

                            <!-- Eliminar (solo para Administrador) -->
                            <?php if(isset($_SESSION['descripcion']) && $_SESSION['descripcion'] == 'Administrador'): ?>
                            <td>
                                <form id="formulario-eliminar-<?= htmlspecialchars($vehiculo_['idvehiculos']); ?>" 
                                    method="POST" action="controladores/vehiculos/vehiculos.controlador.php">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="idvehiculos" value="<?= htmlspecialchars($vehiculo_['idvehiculos']); ?>">
                                    <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= htmlspecialchars($vehiculo_['idvehiculos']); ?>')" 
                                            class="btn btn-danger btn-sm" type="submit" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center text-muted">
                            No se encontraron vehículos para los filtros aplicados.
                        </td>
                    </tr>
                <?php endif; ?>
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
        // Mostrar y ocultar filtros
        function toggleFiltros() {
            const filtros = document.getElementById('filtros');
            const toggleBtn = document.getElementById('toggleFiltros');
            
            if (filtros.style.display === 'none') {
                filtros.style.display = 'block';
                toggleBtn.textContent = 'Ocultar Filtros';
            } else {
                filtros.style.display = 'none';
                toggleBtn.textContent = 'Mostrar Filtros';
            }
        }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fichaTecnicaModal = document.getElementById('fichaTecnicaModal');

        fichaTecnicaModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const marca = button.getAttribute('data-marca');
            const modelo = button.getAttribute('data-modelo');
            const idvehiculo = button.getAttribute('data-idvehiculo');
            const estadoVehiculo = button.getAttribute('data-estado'); // viene de data-estado="disponible"

            document.getElementById('marcaModeloVehiculo').textContent = `${marca} - ${modelo}`;
            document.getElementById('vehiculos_idvehiculos').value = idvehiculo;

            // ===============================
            // 1️⃣ Traer ficha técnica existente
            // ===============================
            const formData = new FormData();
            formData.append("action", "consultar_ficha_tecnica");
            formData.append("id_vehiculo", idvehiculo);

            fetch('controladores/ventas/ventas.controlador.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (!data.error && data.ficha_tecnica) {
                    const f = data.ficha_tecnica;
                    document.querySelector("#ficha-tecnica-form input[name='action']").value = "actualizar";
                    document.getElementById("vencimientoBateria").value = f.vencimiento_bateria?.split(" ")[0] || '';
                    document.getElementById("vencimientoRTO").value = f.vencimiento_RTO?.split(" ")[0] || '';
                    document.getElementById("vencimientoService").value = f.vencimiento_service?.split(" ")[0] || '';
                    document.getElementById("descripcionCarroceria").value = f.carroceria_idcarroceria || '';
                    document.getElementById("descripcionNeumaticos").value = f.neumaticos_idneumaticos || '';
                    document.getElementById("descripcionCristales").value = f.cristales_idcristales || '';
                } else {
                    document.querySelectorAll("#fichaTecnicaModal input, #fichaTecnicaModal select").forEach(el => {
                        if (el.type === "checkbox") el.checked = false;
                        else el.value = "";
                    });
                }
            });

            // ===============================
            // 2️⃣ Traer documentación (solo vista)
            // ===============================
            fetch(`controladores/documentos/cargar_tipo_doc.php?idvehiculo=${idvehiculo}`)
            .then(res => res.text())
            .then(html => {
                const contenedor = document.getElementById('contenedorDocumentacion');
                contenedor.innerHTML = html;

                // 👇 Si el vehículo está disponible → reemplazar los switches por íconos
                if (estadoVehiculo?.toLowerCase() === 'disponible') {
                    contenedor.querySelectorAll('.form-check').forEach(div => {
                        const input = div.querySelector('input[type="checkbox"]');
                        const label = div.querySelector('label');
                        if (input && label) {
                            const icono = input.checked
                                ? '<i class="fa-solid fa-check text-success ms-2"></i>'
                                : '<i class="fa-solid fa-xmark text-danger ms-2"></i>';
                            input.remove(); // quita el switch
                            label.insertAdjacentHTML('beforeend', icono);
                        }
                    });
                }
            })
            .catch(err => console.error(err));
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
            data: { vehiculos_idvehiculos: idvehiculo, tipo: 'todos' },
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

<!-- ============================================================= -->
<!-- SCRIPT DE CONTROL DEL MODAL PRECIOS -->
<!-- ============================================================= -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('ActualizarPrecioModal');

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const tipo = button.getAttribute('data-tipo') || 'actualizar';

        const marca = button.getAttribute('data-marca');
        const modelo = button.getAttribute('data-modelo');
        const anio = button.getAttribute('data-anio');
        const idvehiculo = button.getAttribute('data-idvehiculo');
        const precioTomado = Number(button.getAttribute('data-precio')) || 0;
        const precioPublico = Number(button.getAttribute('data-precio-publico')) || 0;
        const interesPorcentaje = button.getAttribute('data-interes') || '';

        // Elementos
        const titulo = document.getElementById('marcaModeloVehiculo');
        const idvehiculosInput = document.getElementById('idvehiculos');
        const actionInput = document.getElementById('action');
        const preciosActualesFila = document.getElementById('preciosActualesFila');
        const nuevoPrecioTomadoContainer = document.getElementById('nuevoPrecioTomadoContainer');
        const precioPublicoContainer = document.getElementById('precioPublicoContainer');
        const precioActualTexto = document.getElementById('precio_actual_texto');
        const precioPublicoTexto = document.getElementById('precio_publico_texto');
        const interesAplicadoTexto = document.getElementById('interes_aplicado_texto');
        const precioActualInput = document.getElementById('precio_actual');
        const precioNuevoInput = document.getElementById('precio_nuevo_agregar');
        const interesSelect = document.getElementById('interes');
        const precioCalculadoInput = document.getElementById('precio_calculado');

        // Reset campos
        interesSelect.value = '';
        precioCalculadoInput.value = '';
        precioNuevoInput.value = '';
        precioNuevoInput.readOnly = false;

        titulo.textContent = `${marca} - ${modelo} - Año: ${anio}`;
        idvehiculosInput.value = idvehiculo;

        const formato = new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2 });

        // ----- Configuración según tipo -----
        if (tipo === 'publico') {
        actionInput.value = 'publico';
        preciosActualesFila.style.display = 'flex';
        nuevoPrecioTomadoContainer.style.display = 'none';
        precioPublicoContainer.style.display = 'flex';
        precioActualTexto.textContent = precioTomado ? '$ ' + formato.format(precioTomado) : 'Sin precio tomado';
        precioPublicoTexto.textContent = precioPublico ? '$ ' + formato.format(precioPublico) : 'Sin precio público';
        interesAplicadoTexto.textContent = interesPorcentaje ? interesPorcentaje + ' %' : '-';
        precioActualInput.value = precioTomado;
        precioNuevoInput.value = precioTomado;
        precioNuevoInput.readOnly = true;

        } else if (tipo === 'actualizar') {
        actionInput.value = 'actualizar';
        preciosActualesFila.style.display = 'flex';
        nuevoPrecioTomadoContainer.style.display = 'flex';
        precioPublicoContainer.style.display = 'flex';

        precioActualTexto.textContent = precioTomado ? '$ ' + formato.format(precioTomado) : 'Sin precio tomado';
        precioPublicoTexto.textContent = precioPublico ? '$ ' + formato.format(precioPublico) : 'Sin precio público';
        interesAplicadoTexto.textContent = interesPorcentaje ? interesPorcentaje + ' %' : '-';

        precioActualInput.value = precioTomado;
        precioNuevoInput.value = formato.format(precioTomado);

        } else if (tipo === 'tomado') {
        actionInput.value = 'tomado';
        preciosActualesFila.style.display = 'none';
        nuevoPrecioTomadoContainer.style.display = 'flex';
        precioPublicoContainer.style.display = 'none';
        precioActualTexto.textContent = precioTomado ? '$ ' + formato.format(precioTomado) : 'Sin precio tomado';
        precioActualInput.value = precioTomado;

        } else {
        actionInput.value = 'agregar';
        preciosActualesFila.style.display = 'none';
        nuevoPrecioTomadoContainer.style.display = 'flex';
        precioPublicoContainer.style.display = 'none';
        }
    });

    // ======================================================
    // CÁLCULO AUTOMÁTICO DEL PRECIO AL PÚBLICO
    // ======================================================
    const interesSelect = document.getElementById('interes');
    const precioNuevoInput = document.getElementById('precio_nuevo_agregar');
    const precioCalculadoInput = document.getElementById('precio_calculado');

    interesSelect.addEventListener('change', calcularPrecioPublico);
    precioNuevoInput.addEventListener('input', calcularPrecioPublico);

    function calcularPrecioPublico() {
        const interes = parseFloat(interesSelect.options[interesSelect.selectedIndex]?.dataset.porcentaje || 0);
        const base = parseFloat(precioNuevoInput.value.replace(/\./g, '').replace(',', '.')) || 0;
        if (interes > 0 && base > 0) {
        const publico = base * (1 + interes / 100);
        precioCalculadoInput.value = new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2 }).format(publico);
        } else {
        precioCalculadoInput.value = '';
        }
    }
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
        location.href='index.php?page=listado_vehiculos&buscador='+ encodeURIComponent(buscador);
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


