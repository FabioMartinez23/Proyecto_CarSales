<?php
ini_set('display_errors', 1);

$auditoriaModel = new AuditoriaEdiciones();

// Paginación
$filas_por_pagina = 10;
$pagina_actual = isset($_GET['pagina_actual']) ? (int)$_GET['pagina_actual'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

$inicio = ($pagina_actual - 1) * $filas_por_pagina;

// =============================
// Filtros (GET)
// =============================
$tabla        = isset($_GET['tabla'])        && $_GET['tabla']        !== '' ? $_GET['tabla']        : null;
$modulo       = isset($_GET['modulo'])       && $_GET['modulo']       !== '' ? $_GET['modulo']       : null;
$accion       = isset($_GET['accion'])       && $_GET['accion']       !== '' ? $_GET['accion']       : null;
$idusuario    = isset($_GET['idusuario'])    && $_GET['idusuario']    !== '' ? (int)$_GET['idusuario'] : null;
$fecha_desde  = isset($_GET['fecha_desde'])  && $_GET['fecha_desde']  !== '' ? $_GET['fecha_desde']  : null;
$fecha_hasta  = isset($_GET['fecha_hasta'])  && $_GET['fecha_hasta']  !== '' ? $_GET['fecha_hasta']  : null;
$buscar       = isset($_GET['buscar'])       && $_GET['buscar']       !== '' ? $_GET['buscar']       : null;

// Armamos array de filtros para enviar al modelo
$filtros = [
    'tabla'       => $tabla,
    'modulo'      => $modulo,
    'accion'      => $accion,
    'idusuario'   => $idusuario,
    'fecha_desde' => $fecha_desde,
    'fecha_hasta' => $fecha_hasta,
    'buscar'      => $buscar,
];

// Total de registros según filtros
$total_registros = $auditoriaModel->contar_auditorias($filtros);

// Traer auditorías paginadas
$result_auditoria = $auditoriaModel->traer_auditorias_paginacion(
    $inicio,
    $filas_por_pagina,
    $filtros
);

$total_paginas = $total_registros > 0 ? ceil($total_registros / $filas_por_pagina) : 1;

// Opciones para selects (podés ampliarlas según tu sistema)
$tablas_posibles = [
    ''                 => 'Todas',
    'vehiculos'        => 'Vehículos',
    'usuarios'         => 'Usuarios',
    'clientes'         => 'Clientes',
    'empleados'        => 'Empleados',
    'documentaciones'  => 'Documentos',
    'imagenes'         => 'Imágenes',
];

$acciones_posibles = [
    ''        => 'Todas',
    'INSERT'  => 'Altas',
    'UPDATE'  => 'Modificaciones',
    'DELETE'  => 'Bajas / Eliminaciones',
];

?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Auditoría</a></li>
        <li class="breadcrumb-item active" aria-current="page">Historial de Ediciones</li>
    </ol>
</nav>

<div class="mt-5 hacer_padding auditoria-container">
    <h1 class="text-center">Historial de Auditoría</h1>

    <!-- Filtros -->
    <div class="auditoria-filtros">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="listado_auditoria">

            <!-- Tabla / entidad -->
            <div class="col-md-3">
                <label class="form-label">Entidad / Tabla</label>
                <select name="tabla" class="form-select form-select-sm">
                    <?php foreach ($tablas_posibles as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value); ?>"
                            <?= ($tabla === $value) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Acción -->
            <div class="col-md-2">
                <label class="form-label">Acción</label>
                <select name="accion" class="form-select form-select-sm">
                    <?php foreach ($acciones_posibles as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value); ?>"
                            <?= ($accion === $value && $value !== '') ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Módulo -->
            <div class="col-md-2">
                <label class="form-label">Módulo</label>
                <input type="text" name="modulo" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($modulo ?? ''); ?>">
            </div>

            <!-- Usuario -->
            <div class="col-md-2">
                <label class="form-label">ID Usuario</label>
                <input type="number" name="idusuario" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($idusuario ?? ''); ?>">
            </div>

            <!-- Fechas -->
            <div class="col-md-3 d-flex gap-2">
                <div class="flex-fill">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($fecha_desde ?? ''); ?>">
                </div>
                <div class="flex-fill">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($fecha_hasta ?? ''); ?>">
                </div>
            </div>

            <!-- Buscar -->
            <div class="col-md-6">
                <label class="form-label">Buscar texto (descr. / datos)</label>
                <input type="text" name="buscar" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($buscar ?? ''); ?>"
                    placeholder="Ej: precio, documento, imagen...">
            </div>

            <!-- Botones -->
            <div class="col-md-3 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
                <a href="index.php?page=listado_auditoria"
                class="btn btn-sm btn-outline-secondary">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-striped align-middle auditoria-tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha / Hora</th>
                    <th>Entidad</th>
                    <th>Módulo</th>
                    <th>Acción</th>
                    <th>ID Registro</th>
                    <th>Usuario</th>
                    <th>IP</th>
                    <th>Descripción</th>
                    <th>Datos</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_auditoria && $result_auditoria->num_rows > 0): ?>
                    <?php while ($row = $result_auditoria->fetch_assoc()): ?>
                        <tr>
                            <td class="text-end"><?= (int)$row['idauditoria_ediciones']; ?></td>
                            <td>
                                <?= date('d-m-Y H:i:s', strtotime($row['fecha'])); ?>
                            </td>
                            <td>
                                <span class="badge-entidad">
                                    <?= htmlspecialchars($row['tabla']); ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['modulo'] ?? '-'); ?></td>
                            <td>
                                <?php if ($row['accion'] === 'INSERT'): ?>
                                    <span class="badge-accion-insert">Alta</span>
                                <?php elseif ($row['accion'] === 'UPDATE'): ?>
                                    <span class="badge-accion-update">Modificación</span>
                                <?php elseif ($row['accion'] === 'DELETE'): ?>
                                    <span class="badge-accion-delete">Baja</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted">
                                        <?= htmlspecialchars($row['accion']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end"><?= (int)$row['id_registro']; ?></td>
                            <td class="auditoria-usuario">
                                <?php if (!empty($row['username'])): ?>
                                    <?= htmlspecialchars($row['username']); ?>
                                    <?php if (!empty($row['email'])): ?>
                                        <br><small class="text-muted">
                                            <?= htmlspecialchars($row['email']); ?>
                                        </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Sistema / Desconocido</span>
                                <?php endif; ?>
                            </td>
                            <td class="auditoria-ip">
                                <?= !empty($row['ip']) 
                                    ? htmlspecialchars($row['ip']) 
                                    : '<span class="text-muted">-</span>'; ?>
                            </td>
                            <td class="auditoria-descripcion">
                                <?= !empty($row['descripcion']) 
                                    ? htmlspecialchars($row['descripcion']) 
                                    : '<span class="text-muted">Sin descripción</span>'; ?>
                            </td>
                            <td>
                                <?php
                                // Mostramos un resumen de los JSON (primeros 80 chars)
                                $resumen_ant = '';
                                if (!empty($row['datos_anteriores'])) {
                                    $resumen_ant = mb_strimwidth($row['datos_anteriores'], 0, 80, '...');
                                }
                                $resumen_nue = '';
                                if (!empty($row['datos_nuevos'])) {
                                    $resumen_nue = mb_strimwidth($row['datos_nuevos'], 0, 80, '...');
                                }
                                ?>

                                <?php if ($resumen_ant || $resumen_nue): ?>
                                    <button class="btn btn-sm btn-outline-secondary btn-ver-detalle"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDetalleAuditoria<?= $row['idauditoria_ediciones']; ?>">
                                        Ver
                                    </button>

                                    <!-- Modal de detalle -->
                                    <div class="modal fade" id="modalDetalleAuditoria<?= $row['idauditoria_ediciones']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                            <div class="modal-content auditoria-modal">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        Detalle de cambios (ID <?= $row['idauditoria_ediciones']; ?>)
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-xs mb-1">
                                                        <strong>Entidad:</strong> <?= htmlspecialchars($row['tabla']); ?> | 
                                                        <strong>Registro ID:</strong> <?= (int)$row['id_registro']; ?>
                                                    </p>
                                                    <p class="text-xs">
                                                        <strong>Acción:</strong> <?= htmlspecialchars($row['accion']); ?>
                                                    </p>

                                                    <hr class="auditoria-divider">

                                                    <h6>Datos anteriores</h6>
                                                    <pre><?=
                                                        $row['datos_anteriores'] 
                                                            ? htmlspecialchars($row['datos_anteriores']) 
                                                            : 'NULL';
                                                    ?></pre>

                                                    <h6>Datos nuevos</h6>
                                                    <pre><?=
                                                        $row['datos_nuevos'] 
                                                            ? htmlspecialchars($row['datos_nuevos']) 
                                                            : 'NULL';
                                                    ?></pre>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                                        Cerrar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Sin datos</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted">
                            No se encontraron registros de auditoría.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav aria-label="..." class="d-flex justify-content-center auditoria-pagination">
            <ul class="pagination">
                <li class="page-item <?php if ($pagina_actual <= 1) echo 'disabled'; ?>">
                    <a class="page-link"
                       href="index.php?page=listado_auditoria&pagina_actual=<?= max(1, $pagina_actual - 1); ?>&tabla=<?= urlencode($tabla ?? ''); ?>&accion=<?= urlencode($accion ?? ''); ?>&modulo=<?= urlencode($modulo ?? ''); ?>&idusuario=<?= urlencode($idusuario ?? ''); ?>&fecha_desde=<?= urlencode($fecha_desde ?? ''); ?>&fecha_hasta=<?= urlencode($fecha_hasta ?? ''); ?>&buscar=<?= urlencode($buscar ?? ''); ?>">
                        Previo
                    </a>
                </li>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php if ($pagina_actual == $i) echo 'active'; ?>">
                        <a class="page-link"
                           href="index.php?page=listado_auditoria&pagina_actual=<?= $i; ?>&tabla=<?= urlencode($tabla ?? ''); ?>&accion=<?= urlencode($accion ?? ''); ?>&modulo=<?= urlencode($modulo ?? ''); ?>&idusuario=<?= urlencode($idusuario ?? ''); ?>&fecha_desde=<?= urlencode($fecha_desde ?? ''); ?>&fecha_hasta=<?= urlencode($fecha_hasta ?? ''); ?>&buscar=<?= urlencode($buscar ?? ''); ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php if ($pagina_actual >= $total_paginas) echo 'disabled'; ?>">
                    <a class="page-link"
                       href="index.php?page=listado_auditoria&pagina_actual=<?= min($total_paginas, $pagina_actual + 1); ?>&tabla=<?= urlencode($tabla ?? ''); ?>&accion=<?= urlencode($accion ?? ''); ?>&modulo=<?= urlencode($modulo ?? ''); ?>&idusuario=<?= urlencode($idusuario ?? ''); ?>&fecha_desde=<?= urlencode($fecha_desde ?? ''); ?>&fecha_hasta=<?= urlencode($fecha_hasta ?? ''); ?>&buscar=<?= urlencode($buscar ?? ''); ?>">
                        Siguiente
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
