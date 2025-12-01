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
    // En modo búsqueda no usamos paginación total_registros (podrías calcularlo si querés)
} else {
    // Si no hay filtros, obtener el total de compras
    $result_compras_total = $compras->traer_cantidad_compras();
    foreach ($result_compras_total as $compra_1) {
        $total_registros = $compra_1['total'];
    }
    // Traer compras con paginación
    $result_compras = $compras->traer_compras_paginacion($inicio, $filas_por_pagina);
}

// Calcular el número total de páginas
$total_paginas = $total_registros > 0 ? ceil($total_registros / $filas_por_pagina) : 1;

?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Gestión de Ingresos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nuevos Ingresos</li>
    </ol>
</nav>

<div class="mt-5 hacer_padding">
    <h1 class="text-center">Nuevos Ingresos</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a type="button" href="index.php?page=registrar_compras" class="btn mt-2 mb-2 btn-action">
            Registrar Nuevo Ingreso
        </a>

        <!-- Buscador -->
        <div class="d-flex">
            <input name="buscador" id="idbuscador" class="form-control form-control-sm me-2"
                   type="search" placeholder="Buscar por Nombre / Apellido / Patente"
                   aria-label="Buscar" style="width: 250px;">
            <button class="btn btn-success btn-sm" type="submit" onclick="buscador()">Buscar</button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="comprasTable" class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Nro de Ingreso</th>
                    <th>Precio Tomado</th>
                    <th>Titular del Vehículo</th>
                    <th>Vehículo Ingresado</th>
                    <th>Patente</th>
                    <th>Fecha de Ingreso</th>
                    <!-- NUEVO: Estado -->
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result_compras as $compra): ?>
                    <?php
                        $estado = $compra['estado_compra'] ?? 'Realizada';
                        $esRealizada = ($estado === 'Realizada');
                    ?>
                    <tr>
                        <td><?= $compra['idcompras']; ?></td>
                        <td><?= number_format($compra['precio'], 0, ',', '.'); ?></td>
                        <td><?= $compra['nombre'] . " " . $compra['apellido']; ?></td>
                        <td><?= $compra['nombre_marca'] . " " . $compra['nombre_modelo']; ?></td>
                        <td><?= $compra['patente']; ?></td>
                        <td><?= date('d-m-Y', strtotime($compra['fecha_compra'])); ?></td>

                        <!-- Columna ESTADO con badge -->
                        <td>
                            <?php if ($esRealizada): ?>
                                <span class="badge bg-success-subtle text-success">Realizada</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">Anulada</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <!-- Ver detalle -->
                            <a href="index.php?page=detalle_compras&idcompra=<?= $compra['idcompras']; ?>"
                               class="text-primary me-2" title="Ver más">
                                <i class="fas fa-eye"></i>
                            </a>

                            <!-- SOLO mostrar el botón de anular si está Realizada -->
                            <?php if ($esRealizada): ?>
                                <a href="index.php?page=anular_compras&idcompra=<?= $compra['idcompras']; ?>"
                                   class="text-danger" title="Anular consignación">
                                    <i class="fa-solid fa-ban"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!isset($result_compras) || $result_compras->num_rows === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No se encontraron ingresos.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginación centrada (solo cuando no hay búsqueda) -->
        <?php if (!isset($busqueda) || empty($busqueda)): ?>
            <nav aria-label="..." class="d-flex justify-content-center">
                <ul class="pagination">
                    <!-- Botón "Anterior" -->
                    <li class="page-item <?php if ($pagina_actual <= 1) { echo 'disabled'; } ?>">
                        <a class="page-link"
                           href="index.php?page=listado_compras&pagina_actual=<?= max(1, $pagina_actual - 1); ?>">
                            Previo
                        </a>
                    </li>

                    <!-- Botones de número de página -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?php if ($pagina_actual == $i) { echo 'active'; } ?>">
                            <a class="page-link"
                               href="index.php?page=listado_compras&pagina_actual=<?= $i; ?>">
                                <?= $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Botón "Siguiente" -->
                    <li class="page-item <?php if ($pagina_actual >= $total_paginas) { echo 'disabled'; } ?>">
                        <a class="page-link"
                           href="index.php?page=listado_compras&pagina_actual=<?= min($total_paginas, $pagina_actual + 1); ?>">
                            Siguiente
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
function buscador() {
    let buscador = document.getElementById('idbuscador').value.trim();
    location.href = 'index.php?page=listado_compras&buscador=' + encodeURIComponent(buscador);
}
</script>

