<?php
ini_set('display_errors', 1);

$anulacionesModel = new AnularOperacion();

$filas_por_pagina = 10;
$total_registros = 0;
$pagina_actual = isset($_GET['pagina_actual']) ? (int)$_GET['pagina_actual'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

$inicio = ($pagina_actual - 1) * $filas_por_pagina;

// Filtro por entidad (opcional)
$entidad = isset($_GET['entidad']) && $_GET['entidad'] !== '' ? $_GET['entidad'] : null;

// Total de registros
$total_registros = $anulacionesModel->contar_anulaciones($entidad);

// Traer anulaciones paginadas
$result_anulaciones = $anulacionesModel->traer_anulaciones_paginacion(
    $inicio,
    $filas_por_pagina,
    $entidad
);

$total_paginas = $total_registros > 0 ? ceil($total_registros / $filas_por_pagina) : 1;
?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Auditoría</a></li>
        <li class="breadcrumb-item active" aria-current="page">Anulaciones de Operaciones</li>
    </ol>
</nav>

<div class="mt-5 hacer_padding">
    <h1 class="text-center">Anulaciones de Operaciones</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Filtro por entidad -->
        <form method="GET" class="d-flex align-items-center">
            <input type="hidden" name="page" value="listado_anulaciones">
            <label class="me-2 mb-0">Tipo de operación:</label>
            <select name="entidad" class="form-select form-select-sm me-2" style="width: 200px;">
                <option value="">Todas</option>
                <option value="venta"  <?= $entidad === 'venta'  ? 'selected' : ''; ?>>Ventas</option>
                <option value="compra" <?= $entidad === 'compra' ? 'selected' : ''; ?>>Consignaciones</option>
                <!-- A futuro podrías usar 'gasto','comision','caja' -->
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID Anulación</th>
                    <th>Fecha Anulación</th>
                    <th>Entidad</th>
                    <th>ID Operación</th>
                    <th>Tipo de Motivo</th>
                    <th>Detalle</th>
                    <th>Usuario</th>
                    <!-- Acciones como "Ver detalle" (y a futuro, si quisieras, Revertir) -->
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_anulaciones && $result_anulaciones->num_rows > 0): ?>
                    <?php while ($row = $result_anulaciones->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['idanular_operacion']; ?></td>
                            <td><?= date('d-m-Y', strtotime($row['fecha_anulacion'])); ?></td>
                            <td>
                                <?php if ($row['entidad'] === 'venta'): ?>
                                    <span class="badge bg-info-subtle text-info">Venta</span>
                                <?php elseif ($row['entidad'] === 'compra'): ?>
                                    <span class="badge bg-warning-subtle text-warning">Consignación</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <?= ucfirst($row['entidad']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['id_entidad']; ?></td>
                            <td><?= $row['nombre_tipo_anulacion']; ?></td>
                            <td>
                                <?= !empty($row['motivo_detalle']) 
                                    ? htmlspecialchars($row['motivo_detalle']) 
                                    : '<span class="text-muted">Sin detalle</span>'; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['username']); ?>
                                <?php if (!empty($row['email'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($row['email']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['entidad'] === 'venta'): ?>
                                    <a href="index.php?page=detalle_ventas&idventa=<?= $row['id_entidad']; ?>"
                                       class="text-primary" title="Ver venta">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php elseif ($row['entidad'] === 'compra'): ?>
                                    <a href="index.php?page=detalle_compras&idcompra=<?= $row['id_entidad']; ?>"
                                       class="text-primary" title="Ver consignación">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Sin vínculo</span>
                                <?php endif; ?>

                                <!-- A FUTURO: botón "Revertir" si decidís implementarlo
                                <?php if (isset($_SESSION['descripcion']) && $_SESSION['descripcion'] === 'Administrador'): ?>
                                    <a href="index.php?page=revertir_anulacion&idanulacion=<?= $row['idanular_operacion']; ?>"
                                       class="text-danger ms-2" title="(Futuro) Revertir anulación">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                <?php endif; ?>
                                -->
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No se encontraron anulaciones.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav aria-label="..." class="d-flex justify-content-center">
            <ul class="pagination">
                <li class="page-item <?php if ($pagina_actual <= 1) echo 'disabled'; ?>">
                    <a class="page-link"
                       href="index.php?page=listado_anulaciones&pagina_actual=<?= max(1, $pagina_actual - 1); ?>&entidad=<?= urlencode($entidad ?? ''); ?>">
                        Previo
                    </a>
                </li>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php if ($pagina_actual == $i) echo 'active'; ?>">
                        <a class="page-link"
                           href="index.php?page=listado_anulaciones&pagina_actual=<?= $i; ?>&entidad=<?= urlencode($entidad ?? ''); ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php if ($pagina_actual >= $total_paginas) echo 'disabled'; ?>">
                    <a class="page-link"
                       href="index.php?page=listado_anulaciones&pagina_actual=<?= min($total_paginas, $pagina_actual + 1); ?>&entidad=<?= urlencode($entidad ?? ''); ?>">
                        Siguiente
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
