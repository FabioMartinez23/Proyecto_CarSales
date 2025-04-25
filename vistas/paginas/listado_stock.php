<?php
ini_set('display_errors', 1);

// Inicializar la clase Vehiculos
$vehiculos = new Vehiculos();
$filas_por_pagina = 5;  // Número de filas que se muestran por página
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
    $result_vehiculos = $vehiculos->buscar_vehiculo($busqueda);
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
        $result_vehiculos = $vehiculos->traer_vehiculos_filtrados($filtros, $inicio, $filas_por_pagina);
    } else {
        // Si no hay filtros, obtener el total de vehículos
        $result_vehiculos_total = $vehiculos->traer_cantidad_vehiculo();
        foreach ($result_vehiculos_total as $vehiculo_1) {
            $total_registros = $vehiculo_1['total'];
        }
        // Traer vehículos con paginación
        $result_vehiculos = $vehiculos->traer_vehiculos($inicio, $filas_por_pagina);
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

?>

<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Vehículos</a></li>
        <li class="breadcrumb-item"><a href="#">Gestión de Vehículos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Stock</li>
    </ol>
</nav>

<!-- Sección del título, botón de registrar y buscador -->
<div class="col">
    <h1 class="text-center mb-4">Listado de Vehículos</h1>

    <!-- Contenedor para el botón de registrar y el buscador -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Botón de Registrar Nuevo Vehículo -->
        <a type="button" class="btn btn-action" href="index.php?page=registrar_vehiculos">Registrar Nuevo Vehículo</a>

        <!-- Buscador -->
        <div class="d-flex">
            <input name="buscador" id="idbuscador" class="form-control form-control-sm me-2" type="search" placeholder="Buscar" aria-label="Buscar" style="width: 250px;">
            <button class="btn btn-success btn-sm" type="submit" onclick="buscador()">Buscar</button>
        </div>
    </div>
</div>

<!-- Estructura en dos columnas para el filtro y la tabla -->
<div class="row">
    <!-- Columna izquierda para los filtros, ahora más pequeña y alineada a la tabla -->
    <div class="col-md-2">
        <!-- Botón para mostrar/ocultar filtros -->
        <div class="d-grid gap-2">
            <button class="btn btn-info mb-3" type="button" id="toggleFiltros" onclick="toggleFiltros()">Mostrar Filtros</button>
        </div>

        <!-- Filtros ocultables -->
        <div id="filtros" style="display: none;">
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="listado_stock">
                
                <div class="mb-3">
                    <label for="marca" class="form-label">Marca</label>
                    <select name="marca" class="form-select form-select-sm" id="marca">
                        <option value="">Seleccionar Marca</option>
                        <?php foreach ($result_marca as $marca) { ?>
                            <option value="<?= $marca['nombre'] ?>"><?= $marca['nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo</label>
                    <select name="modelo" class="form-select form-select-sm" id="modelo">
                        <option value="">Seleccionar Modelo</option>
                        <?php foreach ($result_modelo as $modelo) { ?>
                            <option value="<?= $modelo['nombre'] ?>"><?= $modelo['nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="color" class="form-label">Color</label>
                    <select name="color" class="form-select form-select-sm" id="color">
                        <option value="">Seleccionar Color</option>
                        <?php foreach ($result_color as $color) { ?>
                            <option value="<?= $color['descripcion'] ?>"><?= $color['descripcion'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="anio" class="form-label">Año</label>
                    <input type="number" name="año" class="form-control form-control-sm" id="anio" placeholder="Año">
                </div>

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Vehículo</label>
                    <select name="tipo" class="form-select form-select-sm" id="tipo">
                        <option value="">Seleccionar Tipo</option>
                        <?php foreach ($result_tipo_vehiculo as $tipo) { ?>
                            <option value="<?= $tipo['nombre'] ?>"><?= $tipo['nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <button class="btn btn-primary btn-sm" type="submit">Aplicar Filtros</button>
            </form>
        </div>
    </div>

    <!-- Columna derecha para la tabla, ahora con más espacio (col-md-10) -->
    <div class="col-md-10">
        <div class="table-responsive">
            <table class="table table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>Patente</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Color</th>
                        <th>Tipo</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result_vehiculos as $vehiculo_) { ?>
                        <tr>
                            <td><?= $vehiculo_['patente']; ?></td>
                            <td><?= $vehiculo_['nombre_marca']; ?></td>
                            <td><?= $vehiculo_['nombre_modelo']; ?></td>
                            <td><?= $vehiculo_['año']; ?></td>
                            <td><?= $vehiculo_['nombre_color']; ?></td>
                            <td><?= $vehiculo_['nombre_tipo']; ?></td>
                            <td>
                                <a href="index.php?page=registrar_vehiculos&idvehiculos=<?= $vehiculo_['idvehiculos']; ?>" class="btn btn-success" title="Modificar Auto">
                                    <i class="fa-solid fa-pen-to-square"></i>
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
            <!-- Paginación centrada -->
            <nav aria-label="..." class="d-flex justify-content-center">
                <ul class="pagination">
                    <!-- Botón "Anterior" -->
                    <li class="page-item <?php if ($pagina_actual <= 1) { echo 'disabled'; } ?>">
                        <a class="page-link" href="index.php?page=listado_stock&pagina_actual=<?= $pagina_actual - 1 ?>" aria-disabled="true">Previo</a>
                    </li>

                    <!-- Botones de número de página -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                        <li class="page-item <?php if ($pagina_actual == $i) { echo 'active'; } ?>">
                            <a class="page-link" href="index.php?page=listado_stock&pagina_actual=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php } ?>

                    <!-- Botón "Siguiente" -->
                    <li class="page-item <?php if ($pagina_actual >= $total_paginas) { echo 'disabled'; } ?>">
                        <a class="page-link" href="index.php?page=listado_stock&pagina_actual=<?= $pagina_actual + 1 ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
    function buscador(){
        let buscador = document.getElementById('idbuscador').value;
        location.href='index.php?page=listado_stock&buscador='+buscador;
    }

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

