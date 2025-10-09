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

$carroceria = new Carrocerias();
$result_carroceria =  $carroceria->traer_carroceria();

$cristal = new Cristales();
$result_cristal = $cristal->traer_cristal();

$neumatico = new Neumaticos();
$result_neumatico = $neumatico->traer_neumatico();


?>

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
                                <?php foreach($result_carroceria as $carroceria): ?>
                                    <option value="<?php echo $carroceria['idcarroceria']; ?>"><?php echo $carroceria['descripcion_carroceria']; ?></option>
                                <?php endforeach;?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="descripcionNeumaticos" class="form-label">Descripción de los Neumáticos:</label>
                                <select id="descripcionNeumaticos" name="descripcion_neumatico" class="form-select">
                                <?php foreach($result_neumatico as $neumatico): ?>
                                    <option value="<?php echo $neumatico['idneumaticos']; ?>"><?php echo $neumatico['descripcion_neumaticos']; ?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="descripcionCristales" class="form-label">Descripción de Cristales:</label>
                                <select id="descripcionCristales" name="descripcion_cristales" class="form-select">
                                <?php foreach($result_cristal as $cristal): ?>
                                    <option value="<?php echo $cristal['idcristales']; ?>"><?php echo $cristal['descripcion_cristales']; ?></option>
                                <?php endforeach; ?>
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
                    <div class="mb-3">
                        <label class="form-label">Subir Imágenes (JPG, PNG)</label>
                        <input type="file" class="form-control" name="imagen_vehiculo[]" accept=".jpg,.jpeg,.png" multiple>
                    </div>
                    <div id="imagenesSubidas" class="mb-4">
                        <!-- Aquí se mostrarán las imágenes subidas -->
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Imágenes</button>
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

                    <!-- Select para Tipo de Documento -->
                    <div class="mb-3">
                        <label class="form-label">Tipo de Documento</label>
                        <select class="form-select" name="tipo_documentacion_idtipo_documentacion" id="tipo_documentacion_idtipo_documentacion" required>
                            <option value="">Cargando tipos...</option>
                            <!-- Se rellena dinámicamente por AJAX -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subir Documentos (PDF)</label>
                        <input type="file" class="form-control" name="documentos_vehiculo[]" accept=".pdf" multiple required>
                    </div>

                    <div id="documentosSubidos" class="mb-4">
                        <!-- Aquí se mostrarán los documentos subidos -->
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Documentos</button>
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
                        <th>Ficha Técnica</th>
                        <th>Imágenes</th>
                        <th>Documentos</th>
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
                                <!-- Botón para abrir el modal, con los datos de marca y modelo -->
                                <a title="Agregar Ficha" href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fichaTecnicaModal" data-marca="<?= $vehiculo_['nombre_marca']; ?>" data-modelo="<?= $vehiculo_['nombre_modelo']; ?>" data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                    <i class="fa-solid fa-gears"></i>
                                </a>
                            </td>
                            <!-- Botón para agregar imágenes -->
                            <td>
                                <a title="Agregar Imágenes" href="#" 
                                   class="btn btn-info"
                                   data-bs-toggle="modal"
                                   data-bs-target="#ImagenesModal"
                                   data-marca="<?= $vehiculo_['nombre_marca']; ?>"
                                   data-modelo="<?= $vehiculo_['nombre_modelo']; ?>"
                                   data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                   <i class="fa-solid fa-image"></i>
                                </a>
                            </td>

                            <!-- Botón para agregar documentos -->
                            <td>
                                <a title="Agregar Documentos" href="#" 
                                   class="btn btn-secondary"
                                   data-bs-toggle="modal"
                                   data-bs-target="#DocumentosModal"
                                   data-marca="<?= $vehiculo_['nombre_marca']; ?>"
                                   data-modelo="<?= $vehiculo_['nombre_modelo']; ?>"
                                   data-idvehiculo="<?= $vehiculo_['idvehiculos']; ?>">
                                   <i class="fa-solid fa-file-pdf"></i>
                                </a>
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
        var button = event.relatedTarget;

        var marca = button.getAttribute('data-marca');
        var modelo = button.getAttribute('data-modelo');
        var idvehiculo = button.getAttribute('data-idvehiculo');

        var marcaModeloVehiculo = document.getElementById('marcaModeloVehiculo');
        marcaModeloVehiculo.textContent = marca + ' - ' + modelo;

        // ⚠️ Reforzar SIEMPRE estos hidden
        var inputIdVehiculo = document.getElementById('vehiculos_idvehiculos');
        inputIdVehiculo.value = idvehiculo;

        document.querySelector("#nuevo-vehiculo-form input[name='action']").value = "guardar";

        // 🔑 Traer ficha técnica por AJAX
        let formData = new FormData();
        formData.append("action", "consultar_ficha_tecnica");
        formData.append("id_vehiculo", idvehiculo);

        fetch('controladores/ventas/ventas.controlador.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (!data.error && data.ficha_tecnica) {
                    let f = data.ficha_tecnica;

                    // Si existe ficha, cambiamos el action a "actualizar"
                    document.querySelector("#nuevo-vehiculo-form input[name='action']").value = "actualizar";

                    document.getElementById("vencimientoBateria").value = f.vencimiento_bateria?.split(" ")[0] || '';
                    document.getElementById("vencimientoRTO").value = f.vencimiento_RTO?.split(" ")[0] || '';
                    document.getElementById("vencimientoService").value = f.vencimiento_service?.split(" ")[0] || '';

                    document.getElementById("switch08").checked = f.form_08 === "1";
                    document.getElementById("switch12").checked = f.form_12 === "1";
                    document.getElementById("switchTitulo").checked = f.titulo_vehiculo === "1";
                    document.getElementById("switchCedula").checked = f.cedula_vehiculo === "1";
                    document.getElementById("switchSeguro").checked = f.seguro === "1";
                    document.getElementById("switchMunicipalidad").checked = f.municipalidad === "1";
                    document.getElementById("switchDominio").checked = f.informe_dominio === "1";
                    document.getElementById("switchMultas").checked = f.form_13i === "1";
                    document.getElementById("switchPrenda").checked = f.prenda === "1";

                    document.getElementById("descripcionCarroceria").value = f.carroceria_idcarroceria || '';
                    document.getElementById("descripcionNeumaticos").value = f.neumaticos_idneumaticos || '';
                    document.getElementById("descripcionCristales").value = f.cristales_idcristales || '';
                } else {
                    // Si no hay ficha → limpiar SOLO visibles
                    document.querySelectorAll("#fichaTecnicaModal input, #fichaTecnicaModal select").forEach(el => {
                        if (el.type === "checkbox") {
                            el.checked = false;
                        } else if (el.type === "date" || el.tagName === "SELECT" || el.type === "text") {
                            el.value = "";
                        }
                    });

                    // ⚠️ Reforzar hidden (no perderlos nunca)
                    inputIdVehiculo.value = idvehiculo;
                    document.querySelector("#nuevo-vehiculo-form input[name='action']").value = "guardar";
                }
            } catch (e) {
                console.error("Error parseando JSON:", e, text);
            }
        })
        .catch(err => console.error("Error AJAX ficha técnica:", err));
    });
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
            data: { vehiculos_idvehiculos: idvehiculo, tipo: 'imagenes' },
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
                $('#tipo_documentacion_idtipo_documentacion').html(response.tipos);
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
</script>


<script>
    function eliminarDocumento(id) {
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Esta acción eliminará el documento permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'controladores/documentos/eliminar_documentos.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { idDocumentacion: id },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Eliminado',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // 🔄 Recargar página después de eliminar
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar el documento.', 'error');
                    }
                });
            }
        });
    }
</script>



<script>
    function buscador(){
        let buscador = document.getElementById('idbuscador').value;
        console.log(buscador);
        location.href='index.php?page=listado_vehiculos&buscador='+buscador;
    }
</script>


