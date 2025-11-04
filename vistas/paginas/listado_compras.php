<?php
ini_set('display_errors', 1);

$compras = new ComprarVehiculo();
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
    $result_compras = $compras->buscar_compra($busqueda);
} else {
    // Si no hay filtros, obtener el total de vehículos
    $result_compras_total = $compras->traer_cantidad_compras();
    foreach ($result_compras_total as $compra_1) {
        $total_registros = $compra_1['total'];
    }
    // Traer vehículos con paginación
    $result_compras = $compras->traer_compras_paginacion($inicio, $filas_por_pagina);
}

// Calcular el número total de páginas
$total_paginas = ceil($total_registros / $filas_por_pagina);

?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Gestión de Ingresos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registrar Ingreso Nuevo</li>
    </ol>
</nav>

    <div class="mt-5 hacer_padding">
        <h1 class="text-center">Nuevos Ingresos</h1>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a type="button" href="index.php?page=registrar_compras" class="btn mt-2 mb-2 btn-action">Registrar Nuevo Ingreso</a>

            <!-- Buscador -->
            <div class="d-flex">
                <input name="buscador" id="idbuscador" class="form-control form-control-sm me-2" type="search" placeholder="Buscar por Nombre - Apellido" aria-label="Buscar" style="width: 250px;">
                <button class="btn btn-success btn-sm" type="submit" onclick="buscador()">Buscar</button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="ventasTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Nro de Ingreso</th>
                        <th>Precio de Ingreso</th>
                        <th>Titular del Vehiculo</th>
                        <th>Vehículo Ingresado</th>
                        <th>Patente</th>
                        <th>Fecha de Ingreso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($result_compras as $compra) { ?>
                    <tr>
                        <td><?= $compra['idcompras'];?></td>
                        <td><?= $compra['precio'];?></td>
                        <td><?= $compra['nombre']." ".$compra['apellido'];?></td>
                        <td><?= $compra['nombre_marca']." ".$compra['nombre_modelo'];?></td>
                        <td><?= $compra['patente'];?></td>
                        <td><?= date('d-m-Y', strtotime($compra['fecha_compra'])); ?></td>
                        <td>
                            <a href="index.php?page=detalle_compras&idcompra=<?= $compra['idcompras']; ?>" class="text-primary" title="Ver Más">
                                <i class="fas fa-eye"></i>
                            </a>
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
                        <a class="page-link" href="index.php?page=listado_compras&pagina_actual=<?= $pagina_actual - 1 ?>" aria-disabled="true">Previo</a>
                    </li>

                    <!-- Botones de número de página -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                        <li class="page-item <?php if ($pagina_actual == $i) { echo 'active'; } ?>">
                            <a class="page-link" href="index.php?page=listado_compras&pagina_actual=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php } ?>

                    <!-- Botón "Siguiente" -->
                    <li class="page-item <?php if ($pagina_actual >= $total_paginas) { echo 'disabled'; } ?>">
                        <a class="page-link" href="index.php?page=listado_compras&pagina_actual=<?= $pagina_actual + 1 ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
        
    </div>

    <script>
        function buscador(){
        let buscador = document.getElementById('idbuscador').value;
        location.href='index.php?page=listado_compras&buscador='+buscador;
        }

    </script>
