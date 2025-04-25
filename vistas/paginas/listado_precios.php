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

<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Vehículos</a></li>
        <li class="breadcrumb-item"><a href="#">Gestión de Vehículos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Actualización de Precios</li>
    </ol>
</nav>

    <div class="col hacer_padding">
        <h1 class="text-center mb-4">Precios</h1>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            
            <!-- Botón de Registrar Nuevo Cliente-->
            <a type="button" class="btn btn-action" href="index.php?page=registrar_vehiculos">Registrar Nuevo Vehiculo</a>

            <!-- Buscador -->
            <div class="d-flex">
                <input name="buscador" id="idbuscador" class="form-control form-control-sm me-2" type="search" placeholder="Buscar por Patente - Marca - Modelo" aria-label="Buscar" style="width: 250px;">
                <button class="btn btn-success btn-sm" type="submit" onclick="buscador()">Buscar</button>
            </div>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                <th>Patente</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Tipo</th>
                <th>Año</th>
                <th>Kilometraje</th>
                <th>Precios</th>
                <th>Actualizar Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_vehiculos as $vehiculo_){
                ?>
                <tr>
                <td><?= $vehiculo_['patente']; ?></td>
                <td><?= $vehiculo_['nombre_marca']; ?></td>
                <td><?= $vehiculo_['nombre_modelo']; ?></td>
                <td><?= $vehiculo_['nombre_tipo']; ?></td>
                <td><?= $vehiculo_['anio']; ?></td>
                <td><?= $vehiculo_['kilometraje']; ?></td>
                <td>
                    <?php if(isset($vehiculo_['precio'])): ?>
                        <?= $vehiculo_['precio']; ?>
                    <?php else: ?>
                        <a title="Agregar Precio" href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ActualizarPrecioModal" 
                        data-marca="<?= $vehiculo_['nombre_marca']; ?>" 
                        data-modelo="<?= $vehiculo_['nombre_modelo']; ?>" 
                        data-año="<?= $vehiculo_['año']; ?>"
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

                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>


        <!-- Paginación centrada -->
        <nav aria-label="..." class="d-flex justify-content-center">
            <ul class="pagination">
                <!-- Botón "Anterior" -->
                <li class="page-item <?php if ($pagina_actual <= 1) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?page=listado_precios&pagina_actual=<?= $pagina_actual - 1 ?>" aria-disabled="true">Previo</a>
                </li>

                <!-- Botones de número de página -->
                <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                    <li class="page-item <?php if ($pagina_actual == $i) { echo 'active'; } ?>">
                        <a class="page-link" href="index.php?page=listado_precios&pagina_actual=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php } ?>

                <!-- Botón "Siguiente" -->
                <li class="page-item <?php if ($pagina_actual >= $total_paginas) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?page=listado_precios&pagina_actual=<?= $pagina_actual + 1 ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
</div>


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
    function buscador(){
        let buscador = document.getElementById('idbuscador').value;
        console.log(buscador);
        location.href='index.php?page=listado_precios&buscador='+buscador;
    }

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

