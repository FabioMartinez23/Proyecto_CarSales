<?php
ini_set('display_errors', 1);

$ventas = new VenderVehiculo();

// Paginación
$filas_por_pagina = 10;
$pagina_actual = isset($_GET['pagina_actual']) ? (int)$_GET['pagina_actual'] : 1;
if ($pagina_actual < 1) { $pagina_actual = 1; }

$inicio = ($pagina_actual - 1) * $filas_por_pagina;

// Filtro por estado de crédito
$estadoFiltro = $_GET['estado_credito'] ?? 'todos';  // pendiente / aprobado / rechazado / todos

// Buscador
$busqueda = isset($_GET['buscador']) ? trim($_GET['buscador']) : '';

if ($busqueda !== '') {
    // 🔍 Si hay búsqueda, traemos TODOS los resultados sin paginación
    $result_creditos = $ventas->buscar_creditos($busqueda, $estadoFiltro);
    $total_registros = $result_creditos ? $result_creditos->num_rows : 0;
    $total_paginas   = 1;
} else {
    // 📄 Sin búsqueda: usamos paginación clásica
    $total_registros = $ventas->contar_creditos($estadoFiltro);
    $total_paginas   = $total_registros > 0 ? ceil($total_registros / $filas_por_pagina) : 1;

    $result_creditos = $ventas->traer_creditos($estadoFiltro, $inicio, $filas_por_pagina);
}
?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Gestión de Ventas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Créditos Bancarios</li>
    </ol>
</nav>

<div class="mt-5 hacer_padding">
    <h1 class="text-center mb-4">Gestión de Créditos Bancarios</h1>

    <!-- Filtros + Buscador -->
    <div class="d-flex justify-content-between align-items-end mb-4">

        <!-- Filtro estado crédito -->
        <form class="row g-2 align-items-end" method="GET" action="index.php">
            <input type="hidden" name="page" value="listado_creditos">

            <div class="col-auto">
                <label for="estado_credito" class="form-label mb-1">Estado del crédito</label>
                <select name="estado_credito" id="estado_credito" class="form-select form-select-sm">
                    <option value="todos"     <?= $estadoFiltro === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="pendiente" <?= $estadoFiltro === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="aprobado"  <?= $estadoFiltro === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                    <option value="rechazado" <?= $estadoFiltro === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                </select>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm mt-3">
                    <i class="fa-solid fa-filter me-1"></i> Aplicar filtro
                </button>
            </div>
        </form>

        <!-- Buscador -->
        <form class="d-flex" method="GET" action="index.php" onsubmit="return true;">
            <input type="hidden" name="page" value="listado_creditos">
            <input type="hidden" name="estado_credito" value="<?= htmlspecialchars($estadoFiltro); ?>">

            <input
                name="buscador"
                id="idbuscador"
                class="form-control form-control-sm me-2"
                type="search"
                placeholder="Buscar por Cliente / Patente / Marca / Modelo"
                aria-label="Buscar"
                style="width: 280px;"
                value="<?= htmlspecialchars($busqueda); ?>"
            >
            <button class="btn btn-success btn-sm" type="submit">
                Buscar
            </button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th># Venta</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Patente</th>
                    <th>Monto operación</th>
                    <th>Banco</th>
                    <th>Estado crédito</th>
                    <th>Estado venta</th>
                    <th>F. solicitud</th>
                    <th>F. respuesta</th>
                    <th>Monto aprobado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result_creditos && $result_creditos->num_rows > 0): ?>
                <?php while ($c = $result_creditos->fetch_assoc()): ?>
                    <?php
                        $estadoCredito = $c['estado_venta_credito'] ?? 'ninguno';
                        $estadoVenta   = $c['estado_venta'] ?? '';

                        // Badge crédito
                        $badgeTexto = 'Sin estado';
                        $badgeClase = 'badge bg-secondary-subtle text-secondary';

                        if ($estadoCredito === 'pendiente') {
                            $badgeTexto = 'Pendiente';
                            $badgeClase = 'badge bg-warning-subtle text-warning';
                        } elseif ($estadoCredito === 'aprobado') {
                            $badgeTexto = 'Aprobado';
                            $badgeClase = 'badge bg-success-subtle text-success';
                        } elseif ($estadoCredito === 'rechazado') {
                            $badgeTexto = 'Rechazado';
                            $badgeClase = 'badge bg-danger-subtle text-danger';
                        }

                        // Badge venta
                        $badgeVentaTexto = $estadoVenta;
                        $badgeVentaClase = 'badge bg-secondary-subtle text-secondary';
                        if ($estadoVenta === 'Realizada') {
                            $badgeVentaClase = 'badge bg-success-subtle text-success';
                        } elseif ($estadoVenta === 'Anulada') {
                            $badgeVentaClase = 'badge bg-danger-subtle text-danger';
                        }
                    ?>
                    <tr>
                        <td><?= $c['idventas']; ?></td>
                        <td><?= htmlspecialchars($c['apellido'] . ' ' . $c['nombre']); ?></td>
                        <td><?= htmlspecialchars($c['nombre_marca'] . ' ' . $c['nombre_modelo'] . ' ' . $c['anio']); ?></td>
                        <td><?= htmlspecialchars($c['patente']); ?></td>
                        <td>$<?= number_format($c['precio_venta'], 0, ',', '.'); ?></td>
                        <td><?= htmlspecialchars($c['banco_credito']); ?></td>
                        <td><span class="<?= $badgeClase; ?>"><?= $badgeTexto; ?></span></td>
                        <td><span class="<?= $badgeVentaClase; ?>"><?= $badgeVentaTexto; ?></span></td>
                        <td>
                            <?= $c['fecha_solicitud_credito'] 
                                ? date('d-m-Y H:i', strtotime($c['fecha_solicitud_credito'])) 
                                : '-'; ?>
                        </td>
                        <td>
                            <?= $c['fecha_respuesta_credito'] 
                                ? date('d-m-Y H:i', strtotime($c['fecha_respuesta_credito'])) 
                                : '-'; ?>
                        </td>
                        <td>
                            <?= $c['monto_aprobado_credito'] !== null
                                ? '$' . number_format($c['monto_aprobado_credito'], 0, ',', '.')
                                : '-'; ?>
                        </td>
                        <td>
                            <!-- Gestionar solo si está pendiente -->
                            <?php if ($estadoCredito === 'pendiente'): ?>
                                <a href="index.php?page=gestionar_credito&idventa=<?= $c['idventas']; ?>"
                                   class="btn btn-sm btn-outline-warning mb-1"
                                   title="Gestionar crédito">
                                    <i class="fa-solid fa-building-columns"></i>
                                </a>
                            <?php endif; ?>

                            <!-- Ver detalle de la venta -->
                            <a href="index.php?page=detalle_ventas&idventa=<?= $c['idventas']; ?>"
                               class="btn btn-sm btn-outline-primary"
                               title="Ver detalle de la venta">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12" class="text-center text-muted">
                        No se encontraron créditos para el filtro / búsqueda seleccionados.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación (solo cuando NO hay búsqueda) -->
    <?php if ($busqueda === '' && $total_paginas > 1): ?>
        <nav aria-label="..." class="d-flex justify-content-center">
            <ul class="pagination">
                <li class="page-item <?= $pagina_actual <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="index.php?page=listado_creditos&estado_credito=<?= urlencode($estadoFiltro); ?>&pagina_actual=<?= max(1, $pagina_actual - 1); ?>">
                        Previo
                    </a>
                </li>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= $pagina_actual == $i ? 'active' : '' ?>">
                        <a class="page-link"
                           href="index.php?page=listado_creditos&estado_credito=<?= urlencode($estadoFiltro); ?>&pagina_actual=<?= $i; ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $pagina_actual >= $total_paginas ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="index.php?page=listado_creditos&estado_credito=<?= urlencode($estadoFiltro); ?>&pagina_actual=<?= min($total_paginas, $pagina_actual + 1); ?>">
                        Siguiente
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
