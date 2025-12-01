<?php

ini_set('display_errors', 1);

// Inicializar la clase Vehiculos
$vehiculos = new PrecioVehiculo();
$filas_por_pagina = 5;  // Número de filas que se muestran por página
$total_registros = 0;
$pagina_actual = isset($_GET['pagina_actual']) ? (int)$_GET['pagina_actual'] : 1;

$id_highlight = isset($_GET['id_highlight']) ? intval($_GET['id_highlight']) : null;

$cantidad_vehiculo = new Vehiculos();

// Asegúrate de que la página actual nunca sea menor que 1
if ($pagina_actual < 1) {
    $pagina_actual = 1;
}

// Calcular el OFFSET
$inicio = ($pagina_actual - 1) * $filas_por_pagina;

$estado_actual = isset($_GET['estado']) ? $_GET['estado'] : 'falta_documento'; // 'falta_documento' por defecto

// Si hay una búsqueda activa
if (isset($_GET['buscador']) && !empty($_GET['buscador'])) {
    $busqueda = $_GET['buscador'];
    $result_vehiculos = $cantidad_vehiculo->buscar_vehiculo($busqueda, $estado_actual);
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
        $result_vehiculos = $vehiculos->traer_los_vehiculos_con_precio_filtrado($filtros, $inicio, $filas_por_pagina, $estado_actual);
    } else {
        // Si no hay filtros, obtener el total de vehículos
        $result_vehiculos_total = $vehiculos->traer_cantidad_vehiculo();
        foreach ($result_vehiculos_total as $vehiculo_1) {
            $total_registros = $vehiculo_1['total'];
        }
        // Traer vehículos con paginación
        $result_vehiculos = $vehiculos->traer_los_vehiculos_con_precio($inicio, $filas_por_pagina, $estado_actual);
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

$carroceria = new Carrocerias();
$result_carroceria =  $carroceria->traer_carroceria();

$cristal = new Cristales();
$result_cristal = $cristal->traer_cristal();

$neumatico = new Neumaticos();
$result_neumatico = $neumatico->traer_neumatico();


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
                <h3 id="marcaModeloVehiculo"></h3>

                <form id="ficha-tecnica-form" method="POST" action="controladores/ficha_tecnica/ficha_tecnica.controlador.php">
                    <input type="hidden" name="action" value="guardar">
                    <input type="hidden" name="listado" value="falta_documentacion">
                    <input type="hidden" name="vehiculos_idvehiculos" id="vehiculos_idvehiculos">

                    <!-- Revisión Técnica -->
                    <h2>Revisión Técnica</h2>
                    <h6>Vencimientos</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vencimientoBateria">Batería</label>
                            <input type="date" class="form-control" id="vencimientoBateria" name="vencimiento_bateria">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vencimientoRTO">RTO - Revisión Técnica</label>
                            <input type="date" class="form-control" id="vencimientoRTO" name="vencimiento_rto">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vencimientoService">Service Automotor</label>
                            <input type="date" class="form-control" id="vencimientoService" name="vencimiento_service">
                        </div>
                    </div>

                    <!-- Estado Carrocería -->
                    <h6>Estado Carrocería</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="descripcionCarroceria">Descripción de la Carrocería</label>
                            <select id="descripcionCarroceria" name="descripcion_carroceria" class="form-select">
                                <?php foreach($result_carroceria as $carroceria): ?>
                                    <option value="<?php echo $carroceria['idcarroceria']; ?>"><?php echo $carroceria['descripcion_carroceria']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="descripcionNeumaticos">Descripción de Neumáticos</label>
                            <select id="descripcionNeumaticos" name="descripcion_neumatico" class="form-select">
                                <?php foreach($result_neumatico as $neumatico): ?>
                                    <option value="<?php echo $neumatico['idneumaticos']; ?>"><?php echo $neumatico['descripcion_neumaticos']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="descripcionCristales">Descripción de Cristales</label>
                            <select id="descripcionCristales" name="descripcion_cristales" class="form-select">
                                <?php foreach($result_cristal as $cristal): ?>
                                    <option value="<?php echo $cristal['idcristales']; ?>"><?php echo $cristal['descripcion_cristales']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-action">Guardar Ficha Técnica</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL DOCUMENTACIÓN FÍSICA VEHÍCULO -->
<!-- ============================================================= -->
<div class="modal fade" id="documentacionModal" tabindex="-1" aria-labelledby="documentacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-modal text-white">
                <h5 class="modal-title">
                    <i class="fa-solid fa-folder-open me-2"></i> Documentación Física Entregada
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <h4 id="marcaModeloDocVehiculo" class="text-center mb-4 fw-semibold text-dark"></h4>
                <input type="hidden" id="vehiculo_id_doc">

                <!-- Contenedor dinámico -->
                <div id="contenedorDocumentacion">
                    <p class="text-muted text-center mb-0">Cargando documentación...</p>
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>


<!-- =============================== -->
<!-- Modal de Imágenes del Vehículo -->
<!-- =============================== -->
<div class="modal fade" id="ImagenesModal" tabindex="-1" aria-labelledby="ImagenesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ImagenesModalLabel">Imágenes del Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <h3 id="marcaModeloVehiculoImg"></h3>
                <form id="imagenes-form" method="POST" action="controladores/documentos/documentos.controlador.php" enctype="multipart/form-data">
                    <input type="hidden" name="vehiculos_idvehiculos" id="img_idvehiculos">
                    <input type="hidden" name="action" value="guardar_imagenes">
                    <input type="hidden" name="estado_actual" value="<?= $estado_actual ?>">
                    <div class="mb-3">
                        <label class="form-label">Subir Imágenes (JPG, PNG)</label>
                        <input type="file" class="form-control" name="imagen_vehiculo[]" accept=".jpg,.jpeg,.png" multiple>
                    </div>
                    <div id="imagenesSubidas" class="row g-3">
                        <!-- Aquí se mostrarán las imágenes subidas -->
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-action">Guardar Imágenes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- =============================== -->
<!-- Modal de Documentos del Vehículo -->
<!-- =============================== -->
<div class="modal fade" id="DocumentosModal" tabindex="-1" aria-labelledby="DocumentosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DocumentosModalLabel">Documentación del Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <h3 id="marcaModeloVehiculoDoc"></h3>

                <form id="documentos-form" method="POST" action="controladores/documentos/documentos.controlador.php" enctype="multipart/form-data">
                    <input type="hidden" name="vehiculos_idvehiculos" id="doc_idvehiculos">
                    <input type="hidden" name="action" value="guardar_documentos">
                    <input type="hidden" name="estado_actual" value="<?= $estado_actual ?>">

                    <!-- Select para Tipo de Documento -->
                    <h5 class="mt-4">Subir Documentos Faltantes</h5>
                    <div class="row" id="tiposFaltantesContainer">
                        <!-- Aquí AJAX insertará las TARJETAS -->
                    </div>

                    <div id="documentosSubidos" class="mb-4">
                        <!-- Aquí se mostrarán los documentos subidos -->
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-action">Guardar Documentos</button>
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
        <li class="breadcrumb-item"><a href="index.php?page=gestion_stock">Gestión de Stock</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vehículos Faltantes de Documentación</li>
    </ol>
</nav>


    <div class="col hacer_padding">
        <h1 class="text-center mb-4">Vehículos Faltantes de Documentación</h1>

        <!-- Contenedor para centrar el botón y el buscador -->
        <div class="d-flex justify-content-center align-items-center mb-4">
            <!-- Botón de Registrar Nuevo Vehículo -->
            <a type="button" class="btn me-3  btn-action" href="index.php?page=listado_vehiculos">Vehiculos Disponibles</a>

            <!-- Buscador -->
            <div class="d-flex">
                <input name="buscador" id="idbuscador" class="form-control form-control-sm me-2" type="search" placeholder="Buscar por Patente - Marca - Modelo" aria-label="Buscar..." style="width: 250px;" onkeydown="if(event.key === 'Enter'){ buscador(); }">
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
                            <input type="hidden" name="page" value="listado_falta_documentacion">
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
        echo "<script>console.log('ID High:', " . json_encode($id_highlight) . ");</script>";
        // Convertir el resultado a array contable
        $result_vehiculos_array = [];
        if ($result_vehiculos) {
            while ($row = $result_vehiculos->fetch_assoc()) {
                $result_vehiculos_array[] = $row;
            }
        }

        $estado_vehiculo = new Estado_Vehiculo();
        $result_estado = $estado_vehiculo->traer_estado_vehiculo();
        foreach ($result_estado as $estado_) {
            $nombre_estado_actual = $estado_['estado_vehiculo'];
        }
        ?>

        <!-- Tabla centrada -->
        <div class="table-responsive">
            
            <!-- Pestañas de Estado -->
            <ul class="nav nav-tabs custom-nav-tabs mb-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($estado_actual == 'falta_documento') ? 'active' : '' ?>"
                    href="?page=listado_falta_documentacion&estado=falta_documento">
                    Falta Documentación
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($estado_actual == 'falta_digitalizacion') ? 'active' : '' ?>"
                    href="?page=listado_falta_documentacion&estado=falta_digitalizacion">
                    Falta Digitalización
                    </a>
                </li>
            </ul>
            <?php
            echo "<script>console.log('ID Highlight: $id_highlight');</script>";
            ?>

            <table class="table table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>Patente</th>
                        <th>Año</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Tipo</th>
                        <th>Ficha Técnica</th>
                        <th>Imágenes</th>
                        <th>Documentación (Digital)</th>
                        <th>Entrega Física (Estado)</th>
                        <th>Ver Gastos</th>
                        <th>Costos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($result_vehiculos_array)): ?>
                        <?php foreach ($result_vehiculos_array as $vehiculo_): ?>
                            <tr data-idvehiculo="<?= $vehiculo_['idvehiculos'] ?>"
                                class="<?= ($vehiculo_['idvehiculos'] == $id_highlight) ? 'fila-highlight' : '' ?>">
                                <td><?= htmlspecialchars($vehiculo_['patente']); ?></td>
                                <td><?= htmlspecialchars($vehiculo_['anio']); ?></td>
                                <td><?= htmlspecialchars($vehiculo_['nombre_marca']); ?></td>
                                <td><?= htmlspecialchars($vehiculo_['nombre_modelo']); ?></td>
                                <td><?= htmlspecialchars($vehiculo_['nombre_tipo']); ?></td>
                                
                                <!-- Botón para abrir modal Ficha Técnica -->
                                <td>
                                    <a title="Agregar Ficha" href="#" class="btn btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#fichaTecnicaModal"
                                    data-marca="<?= htmlspecialchars($vehiculo_['nombre_marca']); ?>"
                                    data-modelo="<?= htmlspecialchars($vehiculo_['nombre_modelo']); ?>"
                                    data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                    <i class="fa-solid fa-gears"></i>
                                    </a>
                                </td>

                                <!-- Botón para agregar imágenes -->
                                <td>
                                    <a title="Agregar Imágenes" href="#" class="btn btn-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ImagenesModal"
                                    data-marca="<?= htmlspecialchars($vehiculo_['nombre_marca']); ?>"
                                    data-modelo="<?= htmlspecialchars($vehiculo_['nombre_modelo']); ?>"
                                    data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                    <i class="fa-solid fa-image"></i>
                                    </a>
                                </td>

                                <!-- Botón para cargar documentación digital -->
                                <td>
                                    <a title="Cargar Documentos Digitales" href="#" class="btn btn-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#DocumentosModal"
                                    data-marca="<?= htmlspecialchars($vehiculo_['nombre_marca']); ?>"
                                    data-modelo="<?= htmlspecialchars($vehiculo_['nombre_modelo']); ?>"
                                    data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                    <i class="fa-solid fa-upload"></i>
                                    </a>
                                </td>

                                <!-- Botón para cambiar estado de entrega -->
                                <td>
                                    <a title="Registrar Entregas" href="#" class="btn btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#documentacionModal"
                                    data-marca="<?= htmlspecialchars($vehiculo_['nombre_marca']); ?>"
                                    data-modelo="<?= htmlspecialchars($vehiculo_['nombre_modelo']); ?>"
                                    data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    </a>
                                </td>

                                <td>
                                    <a href="index.php?page=listado_gastos&e=lfc&origen=vehiculo&idvehiculo=<?= $vehiculo_['idvehiculos'] ?>"
                                    class="btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-wallet"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="index.php?page=costo_vehiculo&e=lfc&idvehiculo=<?= $vehiculo_['idvehiculos'] ?>" 
                                    class="btn btn-sm btn-outline-primary"
                                    title="Ver Costos Totales del Vehículo">
                                        <i class="fa-solid fa-coins"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
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
                    <a class="page-link" 
                        <?php 
                            if (isset($_GET['estado']) && $_GET['estado'] == 2) {
                                echo 'href="index.php?page=listado_falta_documentacion&estado=2&pagina_actual=' . ($pagina_actual - 1) . '"';
                            } else if (isset($_GET['estado']) && $_GET['estado'] == 3) {
                                echo 'href="index.php?page=listado_falta_documentacion&estado=3&pagina_actual=' . ($pagina_actual - 1) . '"';
                            } else {
                                echo 'href="index.php?page=listado_falta_documentacion&pagina_actual=' . ($pagina_actual - 1) . '"';
                            }
                        ?> 
                        aria-disabled="true">Previo
                    </a>
                </li>

                <!-- Botones de número de página -->
                <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                    <li class="page-item <?php if ($pagina_actual == $i) { echo 'active'; } ?>">
                        <a class="page-link" 
                            <?php 
                                if (isset($_GET['estado']) && $_GET['estado'] == 'falta_documento') {
                                    echo 'href="index.php?page=listado_falta_documentacion&estado=falta_documento&pagina_actual=' . $i . '"';
                                } else if (isset($_GET['estado']) && $_GET['estado'] == 'falta_digitalizacion') {
                                    echo 'href="index.php?page=listado_falta_documentacion&estado=falta_digitalizacion&pagina_actual=' . $i . '"';
                                } else {
                                    echo 'href="index.php?page=listado_falta_documentacion&pagina_actual=' . $i . '"';
                                }
                            ?>
                        ><?= $i ?></a>
                    </li>
                <?php } ?>

                <!-- Botón "Siguiente" -->
                <li class="page-item <?php if ($pagina_actual >= $total_paginas) { echo 'disabled'; } ?>">
                    <a class="page-link"
                        <?php 
                            if (isset($_GET['estado']) && $_GET['estado'] == 'falta_documento') { 
                                echo 'href="index.php?page=listado_falta_documentacion&estado=falta_documento&pagina_actual=' . ($pagina_actual + 1) . '"';
                            } else if (isset($_GET['estado']) && $_GET['estado'] == 'falta_digitalizacion') {
                                echo 'href="index.php?page=listado_falta_documentacion&estado=falta_digitalizacion&pagina_actual=' . ($pagina_actual + 1) . '"';
                            } else {
                                echo 'href="index.php?page=listado_falta_documentacion&pagina_actual=' . ($pagina_actual + 1) . '"';
                            }
                        ?>
                    >Siguiente</a>
                </li>
            </ul>
        </nav>

    </div>

        <!-- MODAL: Ampliar Imagen -->
        <div class="modal fade" id="modalAmpliarImagen" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-transparent border-0 shadow-none">
                    <div class="modal-body p-0 text-center">

                        <img id="imagenAmpliada"
                            src=""
                            class="img-fluid rounded shadow-lg"
                            style="animation: zoomIn 0.25s ease-out; cursor: zoom-out;"
                            onclick="cerrarAmpliada()">

                    </div>
                </div>
            </div>
        </div>

        <style>
        @keyframes zoomIn {
            0% { transform: scale(0.4); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        </style>


<script>
document.addEventListener("DOMContentLoaded", () => {

    const id = <?= json_encode($_GET['id_highlight'] ?? null) ?>;

    if (id) {
        const fila = document.querySelector(`tr[data-idvehiculo='${id}']`);

        if (fila) {
            fila.classList.add("fila-highlight");

            // Scroll suave
            fila.scrollIntoView({
                behavior: "smooth",
                block: "center"
            });
        }
    }
});
</script>


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

    // ---------- Modal Ficha Técnica ----------
    var fichaModal = document.getElementById('fichaTecnicaModal');
    fichaModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var marca = button.getAttribute('data-marca');
        var modelo = button.getAttribute('data-modelo');
        var idvehiculo = button.getAttribute('data-idvehiculo');

        document.getElementById('marcaModeloVehiculo').textContent = marca + ' - ' + modelo;
        document.getElementById('vehiculos_idvehiculos').value = idvehiculo;

        // Traer ficha técnica por AJAX
        let formData = new FormData();
        formData.append("action", "consultar_ficha_tecnica");
        formData.append("id_vehiculo", idvehiculo);

        fetch('controladores/ventas/ventas.controlador.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.ficha_tecnica) {
                    let f = data.ficha_tecnica;
                    document.getElementById("vencimientoBateria").value = f.vencimiento_bateria?.split(" ")[0] || '';
                    document.getElementById("vencimientoRTO").value = f.vencimiento_RTO?.split(" ")[0] || '';
                    document.getElementById("vencimientoService").value = f.vencimiento_service?.split(" ")[0] || '';

                    document.getElementById("descripcionCarroceria").value = f.carroceria_idcarroceria || '';
                    document.getElementById("descripcionNeumaticos").value = f.neumaticos_idneumaticos || '';
                    document.getElementById("descripcionCristales").value = f.cristales_idcristales || '';
                }
            });
    });
});
</script>

<!-- ============================================================= -->
<!-- SCRIPT DEL MODAL DOCUMENTACIÓN FÍSICA -->
<!-- ============================================================= -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const docModal = document.getElementById('documentacionModal');

    // -------------------------------------------------------------
    // 📦 Al abrir el modal
    // -------------------------------------------------------------
    docModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const marca = button.getAttribute('data-marca');
        const modelo = button.getAttribute('data-modelo');
        const idvehiculo = button.getAttribute('data-idvehiculo');

        document.getElementById('marcaModeloDocVehiculo').textContent = `${marca} - ${modelo}`;
        document.getElementById('vehiculo_id_doc').value = idvehiculo;

        cargarDocumentacion(idvehiculo);
    });

    // -------------------------------------------------------------
    // 🔄 Función para cargar documentación actual
    // -------------------------------------------------------------
    function cargarDocumentacion(idvehiculo) {
        fetch(`controladores/documentos/cargar_tipo_doc_fisico.php?idvehiculo=${idvehiculo}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('contenedorDocumentacion').innerHTML = html;
                activarSwitches(idvehiculo);
            })
            .catch(() => {
                document.getElementById('contenedorDocumentacion').innerHTML = 
                    `<p class="text-danger text-center">Error al cargar documentación.</p>`;
            });
    }

    // -------------------------------------------------------------
    // 🧩 Activar eventos de los switches
    // -------------------------------------------------------------
    function activarSwitches(idvehiculo) {
        document.querySelectorAll('#contenedorDocumentacion input[type="checkbox"]').forEach(switchEl => {
            switchEl.addEventListener('change', function() {
                const tipoDocId = this.dataset.idtipo;
                const estado = this.checked ? 1 : 0;

                const formData = new FormData();
                formData.append('vehiculo_id', idvehiculo);
                formData.append('tipo_doc_id', tipoDocId);
                formData.append('estado_doc', estado);

                fetch('controladores/documentos/agregar_doc_fisica.php', {
                    method: 'POST',
                    body: formData
                })
                .then(async (r) => {
                    const text = await r.text();
                    try {
                        const resp = JSON.parse(text.trim());
                        console.log("Respuesta del servidor:", resp);

                        if (resp.status === 'success') {
                            Swal.fire({
                                toast: true,
                                icon: 'success',
                                title: (estado ? 'Documento marcado como entregado' : 'Documento marcado como faltante'),
                                text: resp.accion === 'insertado' ? 'Registro creado correctamente.' : 'Estado actualizado.',
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // 🔹 Si completó todos los documentos físicos
                            if (resp.estado_actualizado === true) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Vehículo completo',
                                    text: 'El vehículo pasó al estado: Falta digitalización.',
                                    timer: 1800,
                                    showConfirmButton: false
                                });

                                const modal = bootstrap.Modal.getInstance(docModal);
                                if (modal) {
                                    // Cuando cierre el modal → recarga la página
                                    docModal.addEventListener('hidden.bs.modal', function onClose() {
                                        location.reload();
                                        docModal.removeEventListener('hidden.bs.modal', onClose);
                                    });
                                }
                            }

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resp.mensaje || 'No se pudo guardar el cambio.'
                            });
                        }

                    } catch (e) {
                        console.error("Respuesta no válida:", text);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Aviso',
                            text: 'El cambio se guardó, pero la respuesta del servidor no fue reconocida correctamente.'
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error del servidor',
                        text: 'No se pudo contactar con el servidor.'
                    });
                });
            });
        });
    }
});

document.addEventListener("change", function(e){
    if (e.target.classList.contains("switch-doc")) {
        let container = e.target.closest(".doc-item");
        let status = container.querySelector(".doc-status");

        if (e.target.checked) {
            status.textContent = "Completado";
            status.classList.remove("pending");
            status.classList.add("ok");
        } else {
            status.textContent = "Faltante";
            status.classList.remove("ok");
            status.classList.add("pending");
        }
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // =======================
    // MODAL DE IMÁGENES
    // =======================
    var imagenesModal = document.getElementById('ImagenesModal');
    imagenesModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var marca = button.getAttribute('data-marca');
        var modelo = button.getAttribute('data-modelo');
        var idvehiculo = button.getAttribute('data-idvehiculo');

        document.getElementById('marcaModeloVehiculoImg').textContent = marca + ' - ' + modelo;
        document.getElementById('img_idvehiculos').value = idvehiculo;

        // ✅ Cargar imágenes existentes por AJAX
        $.ajax({
            url: 'controladores/documentos/obtener_documentos.php',
            method: 'POST',
            data: { vehiculos_idvehiculos: idvehiculo, tipo: 'imagen' },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                $('#imagenesSubidas').html(response.imagenes);
            },
            error: function() {
                $('#imagenesSubidas').html("<p>Error al cargar imágenes.</p>");
            }
        });
    });

    // =======================
    // MODAL DE DOCUMENTOS
    // =======================
    var documentosModal = document.getElementById('DocumentosModal');
    documentosModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var marca = button.getAttribute('data-marca');
        var modelo = button.getAttribute('data-modelo');
        var idvehiculo = button.getAttribute('data-idvehiculo');
        console.log(idvehiculo);

        document.getElementById('marcaModeloVehiculoDoc').textContent = marca + ' - ' + modelo;
        document.getElementById('doc_idvehiculos').value = idvehiculo;

        // ✅ Llamada AJAX unificada (documentos + tipos faltantes)
        $.ajax({
            url: 'controladores/documentos/obtener_documentos.php',
            method: 'POST',
            data: { vehiculos_idvehiculos: idvehiculo, tipo: 'documentos' },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                // Mostrar documentos existentes
                $('#documentosSubidos').html(response.documentos);

                // Mostrar opciones del select con los tipos faltantes
                $('#tiposFaltantesContainer').html(response.tipos);
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                console.log(status, error);
                $('#documentosSubidos').html("<p>Error al cargar documentos.</p>");
                $('#tipo_documentacion_idtipo_documentacion').html("<option value=''>Error al cargar tipos</option>");
            }
        });
    });
});


// ==========================================================
// Ampliar imagen
// ==========================================================
function ampliarImagen(url) {
    const img = document.getElementById("imagenAmpliada");

    if (!img) {
        console.error("❌ Error: No se encontró #imagenAmpliada");
        return;
    }

    img.src = url;

    const modal = new bootstrap.Modal(document.getElementById("modalAmpliarImagen"));
    modal.show();
}

function cerrarAmpliada() {
    const modal = bootstrap.Modal.getInstance(document.getElementById("modalAmpliarImagen"));
    if (modal) modal.hide();
}
</script>


<script>
function eliminarDocumento(id, elemento) {

    Swal.fire({
        title: '¿Eliminar documento?',
        text: 'Esta acción eliminará el archivo permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {

        if (result.isConfirmed) {

            // 🔄 Animación de cargando
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "controladores/documentos/eliminar_documentos.php",
                type: "POST",
                data: { idDocumentacion: id },
                dataType: "json",

                success: function(res) {
                    Swal.close();

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'El documento fue eliminado correctamente.',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        // 🔄 Eliminar visualmente la tarjeta del archivo sin recargar modal
                        let card = $(elemento).closest('[data-id]');
                        card.fadeOut(300, function () { $(this).remove(); });

                        // Opcional: volver a cargar los tipos faltantes para subir
                        recargarTiposFaltantes();

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'No se pudo eliminar el archivo.'
                        });
                    }
                },

                error: function(xhr) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en el servidor',
                        text: 'Ocurrió un problema al eliminar el documento.'
                    });
                }
            });
        }
    });
}


// ==========================================================
// Recargar tipos faltantes (sin recargar modal completo)
// ==========================================================
function recargarTiposFaltantes() {
    let idvehiculo = $("#doc_idvehiculos").val();

    $.ajax({
        url: 'controladores/documentos/obtener_documentos.php',
        method: 'POST',
        data: { vehiculos_idvehiculos: idvehiculo, tipo: 'documentos' },
        dataType: 'json',
        success: function(response) {
            $('#documentosSubidos').html(response.documentos);
            $('#tiposFaltantesContainer').html(response.tipos);
        }
    });
}
</script>




<script>
    function buscador(){
        let buscador = document.getElementById('idbuscador').value;
        location.href='index.php?page=listado_falta_documentacion&buscador='+ encodeURIComponent(buscador);
    }
</script>

<script>
function cambiarListado(estado) {
    const params = new URLSearchParams(window.location.search);
    params.set('estado', estado);
    params.set('pagina_actual', 1); // reinicia a la primera página
    window.location.search = params.toString();
}
</script>


