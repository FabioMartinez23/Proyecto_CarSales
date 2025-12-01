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
    $result_vehiculos = $vehiculos->buscar_vehiculo_precio_compra($busqueda);
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
        $result_vehiculos = $vehiculos->traer_los_vehiculos_con_precio_compra_filtrado($filtros, $inicio, $filas_por_pagina);
    } else {
        // Si no hay filtros, obtener el total de vehículos
        $result_vehiculos_total = $vehiculos->traer_cantidad_vehiculo();
        foreach ($result_vehiculos_total as $vehiculo_1) {
            $total_registros = $vehiculo_1['total'];
        }
        // Traer vehículos con paginación
        $result_vehiculos = $vehiculos->traer_los_vehiculos_con_precio_compra($inicio, $filas_por_pagina);
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
                

<main class="catalogo-wrapper container hacer_padding">

    <h1 class="catalogo-title">Vehículos Disponibles</h1>

    <div class="catalogo-grid">

        <!-- ===================================================== -->
        <!-- SIDEBAR FILTROS                                       -->
        <!-- ===================================================== -->
        <aside class="sidebar-filtros">

            <button class="btn-filtros" onclick="toggleFiltros()">
                <i class="fa-solid fa-filter"></i> Filtros
            </button>

            <div id="filtros" class="filtros-box" style="display:none;">

                <form method="GET" action="index.php">
                    <input type="hidden" name="page" value="comprar_vehiculo">

                    <!-- MARCA -->
                    <div class="filtro-item">
                        <label>Marca</label>
                        <select name="marca" class="form-select form-select-sm">
                            <option value="">Seleccionar Marca</option>
                            <?php foreach ($result_marca as $marca) { ?>
                                <option value="<?= $marca['nombre'] ?>"><?= $marca['nombre'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- MODELO -->
                    <div class="filtro-item">
                        <label>Modelo</label>
                        <select name="modelo" class="form-select form-select-sm">
                            <option value="">Seleccionar Modelo</option>
                            <?php foreach ($result_modelo as $modelo) { ?>
                                <option value="<?= $modelo['nombre'] ?>"><?= $modelo['nombre'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- COLOR -->
                    <div class="filtro-item">
                        <label>Color</label>
                        <select name="color" class="form-select form-select-sm">
                            <option value="">Seleccionar Color</option>
                            <?php foreach ($result_color as $color) { ?>
                                <option value="<?= $color['descripcion'] ?>"><?= $color['descripcion'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- AÑO -->
                    <div class="filtro-item">
                        <label>Año</label>
                        <select name="anio" class="form-select form-select-sm">
                            <option value="">Seleccionar Año</option>
                            <?php foreach ($result_años as $año) { ?>
                                <option value="<?= $año['anio'] ?>"><?= $año['anio'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- TIPO -->
                    <div class="filtro-item">
                        <label>Tipo de Vehículo</label>
                        <select name="tipo" class="form-select form-select-sm">
                            <option value="">Seleccionar Tipo</option>
                            <?php foreach ($result_tipo_vehiculo as $tipo) { ?>
                                <option value="<?= $tipo['nombre'] ?>"><?= $tipo['nombre'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <button class="btn-aplicar">Aplicar Filtros</button>
                </form>
            </div>

        </aside>

        <!-- ===================================================== -->
        <!-- CONTENIDO PRINCIPAL                                   -->
        <!-- ===================================================== -->
        <section class="catalogo-contenido">

            <!-- SORT -->
            <div class="sort-box">
                <label>Ordenar por:</label>
                <select id="sort" name="sort" class="form-select">
                    <option value="">Seleccionar una Opción</option>
                    <option value="precio_asc">Mayor Precio</option>
                    <option value="precio_desc">Menor Precio</option>
                    <option value="año_asc">Mayor Año</option>
                    <option value="año_desc">Menor Año</option>
                </select>
            </div>

            <!-- TARJETAS -->
            <div class="cards-grid">
                <?php foreach ($result_vehiculos as $auto) { include('cards.php'); } ?>
            </div>

            <!-- PAGINACIÓN -->
            <nav class="pagination-box">
                <ul class="pagination">

                    <!-- Anterior -->
                    <li class="page-item <?= ($pagina_actual<=1?'disabled':'') ?>">
                        <a class="page-link" href="index.php?page=comprar_vehiculo&pagina_actual=<?= $pagina_actual-1 ?>">Anterior</a>
                    </li>

                    <!-- Números -->
                    <?php for ($i=1; $i <= $total_paginas; $i++) { ?>
                        <li class="page-item <?= ($pagina_actual==$i?'active':'') ?>">
                            <a class="page-link" href="index.php?page=comprar_vehiculo&pagina_actual=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php } ?>

                    <!-- Siguiente -->
                    <li class="page-item <?= ($pagina_actual>=$total_paginas?'disabled':'') ?>">
                        <a class="page-link" href="index.php?page=comprar_vehiculo&pagina_actual=<?= $pagina_actual+1 ?>">Siguiente</a>
                    </li>

                </ul>
            </nav>

        </section>
    </div>
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
            $("#precio_actual").text(
            new Intl.NumberFormat('es-AR', { minimumFractionDigits: 0 }).format(auto.precio_publico)
            );
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


