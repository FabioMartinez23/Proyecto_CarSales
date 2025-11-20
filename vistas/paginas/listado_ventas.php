<?php
ini_set('display_errors', 1);

$ventas = new VenderVehiculo();
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
    $result_ventas = $ventas->buscar_ventas($busqueda);
} else {
    // Si no hay filtros, obtener el total de vehículos
    $result_ventas_total = $ventas->traer_cantidad_ventas();
    foreach ($result_ventas_total as $venta_1) {
        $total_registros = $venta_1['total'];
    }
    // Traer vehículos con paginación
    $result_ventas = $ventas->traer_ventas_paginacion($inicio, $filas_por_pagina);
}

// Calcular el número total de páginas
$total_paginas = ceil($total_registros / $filas_por_pagina);

?>
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Gestión de Ventas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ventas Concretadas</li>
    </ol>
</nav>

    <div class="mt-5 hacer_padding">
        <h1 class="text-center">Ventas Concretadas</h1>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a type="button" href="index.php?page=registrar_ventas" class="btn mt-2 mb-2 btn-action">Registrar Nueva Venta</a>

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
                        <th>Nro de Venta</th>
                        <th>Precio de Venta</th>
                        <th>Cliente</th>
                        <th>Vehículo</th>
                        <th>Patente</th>
                        <th>Tipo de Pago</th>
                        <th>Fecha de Venta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($result_ventas as $venta) { ?>
                    <tr>
                        <td><?= $venta['idventas'];?></td>
                        <td>$<?= number_format($venta['precio'], 0, ',', '.');?></td>
                        <td><?= $venta['nombre']." ".$venta['apellido'];?></td>
                        <td><?= $venta['nombre_marca']." ".$venta['nombre_modelo'];?></td>
                        <td><?= $venta['patente'];?></td>
                        <td><?= $venta['nombre_pago'];?></td>
                        <td><?= date('d-m-Y', strtotime($venta['fecha_venta'])); ?></td>
                        <td>
                            <?php if ($_SESSION['descripcion'] === "Administrador") { ?>
                                <a href="index.php?page=anular_ventas&idventa=<?= $venta['idventas']; ?>" 
                                class="text-danger me-2 btn-anular" 
                                title="Anular Venta">
                                    <i class="fa-solid fa-ban"></i>
                                </a>
                            <?php } else { ?>
                                <a href="javascript:void(0);" 
                                class="text-muted btn-no-permiso" 
                                title="No autorizado">
                                    <i class="fa-solid fa-ban"></i>
                                </a>
                            <?php } ?>
                            <!-- Botón Costos del Vehículo -->
                            <a href="index.php?page=costo_vehiculo&idvehiculo=<?= $venta['vehiculos_idvehiculos'] ?>" 
                            class="btn btn-sm btn-outline-primary me-1"
                            title="Costos del Vehículo">
                                <i class="fa-solid fa-coins"></i>
                            </a>
                            <a href="index.php?page=detalle_ventas&idventa=<?= $venta['idventas']; ?>" class="text-primary" title="Ver Más">
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
                        <a class="page-link" href="index.php?page=listado_ventas&pagina_actual=<?= $pagina_actual - 1 ?>" aria-disabled="true">Previo</a>
                    </li>

                    <!-- Botones de número de página -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                        <li class="page-item <?php if ($pagina_actual == $i) { echo 'active'; } ?>">
                            <a class="page-link" href="index.php?page=listado_ventas&pagina_actual=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php } ?>

                    <!-- Botón "Siguiente" -->
                    <li class="page-item <?php if ($pagina_actual >= $total_paginas) { echo 'disabled'; } ?>">
                        <a class="page-link" href="index.php?page=listado_ventas&pagina_actual=<?= $pagina_actual + 1 ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
        
    </div>

    <script>
        function buscador(){
        let buscador = document.getElementById('idbuscador').value;
        location.href='index.php?page=listado_ventas&buscador='+buscador;
        }

    </script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".btn-no-permiso").forEach(btn => {
            btn.addEventListener("click", () => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Acceso denegado',
                    text: 'No tiene autorización para anular ventas.'
                });
            });
        });
    });
    </script>