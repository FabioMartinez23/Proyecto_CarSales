<?php

ini_set('display_errors', 1);

// Inicializar la clase Vehiculos
$vehiculos = new PrecioVehiculo();
$filas_por_pagina = 6;  // Número de filas que se muestran por página
$total_registros = 0;
$pagina_actual = isset($_GET['pagina_actual']) ? (int)$_GET['pagina_actual'] : 1;

// Asegúrate de que la página actual nunca sea menor que 1
if ($pagina_actual < 1) {
    $pagina_actual = 1;
}

// Calcular el OFFSET
$inicio = ($pagina_actual - 1) * $filas_por_pagina;

// Si hay una búsqueda activa
if (isset($_GET['buscador']) && !empty($_GET['buscador'])) {
    $busqueda = $_GET['buscador'];
    $result_vehiculos = $vehiculos->buscar_vehiculo_precio($busqueda);
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
        $result_vehiculos = $vehiculos->traer_los_vehiculos_con_precio_filtrado($filtros, $inicio, $filas_por_pagina);
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


// Resto del código sigue igual

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

?>
                
    <main class="container mt-4 hacer_padding">
        <h1 class="text-center mb-4">Vehículos en Venta</h1>
        <section class="row">
            <aside class="col-md-3">
                <!-- Botón para mostrar/ocultar filtros -->
                <div class="d-grid gap-2">
                    <button class="btn btn-info mb-3" type="button" id="toggleFiltros" onclick="toggleFiltros()">Mostrar Filtros&nbsp;&nbsp;<i class="fa-solid fa-chevron-down"></i></button>
                </div>

                <!-- Filtros ocultables -->
                <div id="filtros" style="display: none;">
                    <form method="GET" action="index.php">
                        <input type="hidden" name="page" value="comprar_vehiculo">
                        
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

            <section class="col-md-8">
                
                <div class="form-floating mb-3 mt-3">
                    <select id="sort" name="sort" class="form-select">
                        <option value="">Seleccionar una Opción</option>
                        <option value="precio_asc">Mayor Precio</option>
                        <option value="precio_desc">Menor Precio</option>
                        <option value="año_asc">Mayor Año</option>
                        <option value="año_desc">Menor Año</option>
                    </select>
                    <label for="sort" class="form-label">Ordenar por:</label>
                </div>
                <div class="row mt-3">
                <?php
                    foreach ($result_vehiculos as $key => $auto) {

                        include('cards.php');
                    }
                ?>
                </div>
            </section>
        </section>

        <!-- Paginación centrada -->
        <nav aria-label="..." class="d-flex justify-content-center">
            <ul class="pagination">
                <!-- Botón "Anterior" -->
                <li class="page-item <?php if ($pagina_actual <= 1) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?page=comprar_vehiculo&pagina_actual=<?= $pagina_actual - 1 ?>" aria-disabled="true">Previo</a>
                </li>

                <!-- Botones de número de página -->
                <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                    <li class="page-item <?php if ($pagina_actual == $i) { echo 'active'; } ?>">
                        <a class="page-link" href="index.php?page=comprar_vehiculo&pagina_actual=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php } ?>

                <!-- Botón "Siguiente" -->
                <li class="page-item <?php if ($pagina_actual >= $total_paginas) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?page=comprar_vehiculo&pagina_actual=<?= $pagina_actual + 1 ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </main>

    <script src="assets/js/validaciones/validar_marca_filtro.ajax.js"></script>

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

    function mostrarModal(auto) {
            console.log(auto);
            $("#miModal").modal('show');
            $("#nombre_marca").text(auto.nombre_marca);
            $("#nombre_modelo").text(auto.nombre_modelo);
            $("#precio_actual").text(auto.precio);
            $("#anios").text(auto.anio);
            $("#tipo_vehiculo").text(auto.nombre_tipo);
            $("#kilometrajes").text(auto.kilometraje);
            $("#nombre_color").text(auto.nombre_color);
            $("#btn-simulacion").attr("data-id", auto.idvehiculos);
            
            let id_vehiculo = auto.idvehiculos;
            
            // Enviar el idvehiculo con la clave correcta (idvehiculo en vez de idvehiculos)
            guardar_clicks(id_vehiculo);

            // Cargar imagen correspondiente usando idvehiculos
            cargarImagen(auto.idvehiculos);
        }

        function guardar_clicks(id_vehiculo) {
            fetch('controladores/vehiculos/reportes/guardar_clicks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ idvehiculos: id_vehiculo })
            })
                .then(response => {
                    console.log('Estado de la respuesta:', response.status);
                    return response.text(); // Leer como texto para depuración
                })
                .then(data => {
                    console.log('Respuesta del servidor:', data); // Mostrar respuesta cruda
                    try {
                        const jsonData = JSON.parse(data);
                        if (jsonData.error) {
                            console.error('Error en el servidor:', jsonData.error);
                        } else {
                            console.log('Éxito:', jsonData);
                        }
                    } catch (error) {
                        console.error('Error al analizar JSON:', error.message, data);
                    }
                })
                .catch(error => {
                    console.error('Error al comunicarse con el servidor:', error);
                });
        }



        function redireccionarSimulacion(element) {
            let autoId = $(element).data('id');
            // Redirigimos al archivo PHP que se encargará de verificar la sesión
            window.location = "vistas/paginas/verificar_sesion.php?id=" + autoId;
        }

        function cargarImagen(idvehiculos) {
            $.ajax({
                url: 'controladores/imagen/obtener_imagen.php',  // La ruta correcta al archivo PHP
                type: 'GET',
                data: { idvehiculos: idvehiculos },  // Pasar el id del vehículo
                dataType: 'json',
                success: function(data) {
                    console.log(data);  // Verifica que los datos estén correctos

                    // Si la respuesta contiene la URL de la imagen, actualizar el src del img
                    if (data.url) {
                        // Establecer la URL de la imagen en el modal
                        $('#imagen_modal').attr('src', data.url);
                    } else {
                        // En caso de error o imagen no encontrada, poner la imagen predeterminada
                        $('#imagen_modal').attr('src', 'assets/img/default.jpg');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener la imagen: ', error);
                }
            });
        }


    </script>


