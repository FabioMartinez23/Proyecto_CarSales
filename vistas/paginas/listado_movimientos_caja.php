<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/caja.php");

$caja = new Caja();

// =========================
// Filtros
// =========================
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$tipo  = $_GET['tipo']  ?? ''; // ingreso / egreso / ''

// =========================
// Paginación
// =========================
$filas_por_pagina = 10;
$pagina_actual = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
$offset = ($pagina_actual - 1) * $filas_por_pagina;

$total_mov = $caja->contar_movimientos($desde, $hasta, $tipo);
$total_paginas = max(1, ceil($total_mov / $filas_por_pagina));

$movimientos = $caja->traer_movimientos($desde, $hasta, $tipo, $filas_por_pagina, $offset);
?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Caja</a></li>
        <li class="breadcrumb-item active" aria-current="page">Movimientos de Caja</li>
    </ol>
</nav>

<div class="hacer_padding mt-4">
    <h3 class="mb-4">
        <i class="fa-solid fa-list-ul me-2"></i> Movimientos de Caja
    </h3>

    <!-- =========================
         FILTROS
    ========================== -->
    <form method="GET" action="index.php" class="mb-4">
        <input type="hidden" name="page" value="listado_movimientos_caja">

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" class="form-control" name="desde" value="<?= htmlspecialchars($desde) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" class="form-control" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="ingreso" <?= $tipo === 'ingreso' ? 'selected' : '' ?>>Ingresos</option>
                    <option value="egreso"  <?= $tipo === 'egreso'  ? 'selected' : '' ?>>Egresos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-filter me-2"></i> Aplicar filtros
                </button>
            </div>
        </div>
    </form>

    <!-- =========================
         TABLA
    ========================== -->
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Tipo movimiento</th>
                    <th>Referencia</th>
                    <th>Caja</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $total_ingresos = 0;
            $total_egresos  = 0;

            if ($movimientos && $movimientos->num_rows > 0):
                while ($m = $movimientos->fetch_assoc()):
                    if ($m['tipo'] === 'ingreso') {
                        $total_ingresos += $m['monto'];
                    } else {
                        $total_egresos  += $m['monto'];
                    }

                    $esReverso = isset($m['tipo_movimiento']) && stripos($m['tipo_movimiento'], 'reverso') !== false;
            ?>
                <tr>
                    <!-- Fecha -->
                    <td><?= date('d/m/Y H:i', strtotime($m['fecha_movimiento'])); ?></td>

                    <!-- Tipo ingreso/egreso -->
                    <td>
                        <?php if ($m['tipo'] === 'ingreso'): ?>
                            <span class="badge bg-success">Ingreso</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Egreso</span>
                        <?php endif; ?>
                    </td>

                    <!-- Monto -->
                    <td>
                        $<?= number_format($m['monto'], 2, ',', '.'); ?>
                    </td>

                    <!-- Descripción -->
                    <td>
                        <?= htmlspecialchars($m['descripcion']); ?>
                        <?php if ($esReverso): ?>
                            <br><span class="badge bg-warning text-dark">Reverso</span>
                        <?php endif; ?>
                    </td>

                    <!-- Tipo movimiento -->
                    <td><?= htmlspecialchars($m['tipo_movimiento'] ?? '-'); ?></td>

                    <!-- Referencia -->
                    <td>
                        <?php
                        if (!empty($m['referencia_tabla']) && !empty($m['referencia_id'])) {
                            $refTexto = htmlspecialchars($m['referencia_tabla']) . " #<strong>" . intval($m['referencia_id']) . "</strong>";

                            if ($m['referencia_tabla'] === 'ventas') {
                                echo '<a href="index.php?page=detalle_ventas&idventa=' . intval($m['referencia_id']) . '">'
                                     . $refTexto . '</a>';
                            } elseif ($m['referencia_tabla'] === 'gastos_generales') {
                                echo '<a href="index.php?page=listado_gastos&idgasto=' . intval($m['referencia_id']) . '">'
                                     . $refTexto . '</a>';
                            } else {
                                echo $refTexto;
                            }
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>

                    <!-- Caja -->
                    <td>
                        <?php
                        $idCaja = $m['caja_idcaja'] ?? $m['idcaja'] ?? null;
                        if ($idCaja) {
                            echo "Caja #<strong>" . intval($idCaja) . "</strong>";
                            if (!empty($m['fecha_apertura'])) {
                                echo "<br><small>Apertura: " . date('d/m/Y', strtotime($m['fecha_apertura'])) . "</small>";
                            }
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>

                    <!-- Usuario -->
                    <td><?= htmlspecialchars($m['username'] ?? '-'); ?></td>

                    <!-- Acciones -->
                    <td>
                        <button 
                            type="button" 
                            class="btn btn-sm btn-outline-secondary btn-detalle-mov"
                            data-id="<?= $m['idcaja_movimientos']; ?>"
                            title="Ver detalle">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </td>
                </tr>
            <?php
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="9" class="text-center text-muted p-4">
                        No se encontraron movimientos con los filtros aplicados.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- =========================
         TOTALES
    ========================== -->
    <div class="mt-3 text-end">
        <p>
            Total ingresos: 
            <span class="badge bg-success">
                $<?= number_format($total_ingresos, 2, ',', '.'); ?>
            </span>
        </p>
        <p>
            Total egresos: 
            <span class="badge bg-danger">
                $<?= number_format($total_egresos, 2, ',', '.'); ?>
            </span>
        </p>
        <p>
            Saldo (ingresos - egresos): 
            <span class="badge bg-primary">
                $<?= number_format($total_ingresos - $total_egresos, 2, ',', '.'); ?>
            </span>
        </p>
    </div>

    <!-- =========================
         PAGINACIÓN
    ========================== -->
    <?php if ($total_paginas > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <!-- Anterior -->
                <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="index.php?<?= http_build_query(array_merge($_GET, ['pag' => max(1, $pagina_actual - 1)])) ?>">
                        &laquo; Anterior
                    </a>
                </li>

                <!-- Páginas -->
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                        <a class="page-link"
                           href="index.php?<?= http_build_query(array_merge($_GET, ['pag' => $i])) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <!-- Siguiente -->
                <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="index.php?<?= http_build_query(array_merge($_GET, ['pag' => min($total_paginas, $pagina_actual + 1)])) ?>">
                        Siguiente &raquo;
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

</div>

<!-- =========================
     MODAL DETALLE MOVIMIENTO
========================== -->
<div class="modal fade" id="modalDetalleMovimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa-solid fa-magnifying-glass-dollar me-2"></i>
                    Detalle del movimiento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">ID Movimiento</dt>
                    <dd class="col-sm-8" id="det-id"></dd>

                    <dt class="col-sm-4">Fecha / Hora</dt>
                    <dd class="col-sm-8" id="det-fecha"></dd>

                    <dt class="col-sm-4">Tipo</dt>
                    <dd class="col-sm-8" id="det-tipo"></dd>

                    <dt class="col-sm-4">Monto</dt>
                    <dd class="col-sm-8" id="det-monto"></dd>

                    <dt class="col-sm-4">Tipo movimiento</dt>
                    <dd class="col-sm-8" id="det-tipo-mov"></dd>

                    <dt class="col-sm-4">Tipo de pago</dt>
                    <dd class="col-sm-8" id="det-tipo-pago"></dd>

                    <dt class="col-sm-4">Descripción</dt>
                    <dd class="col-sm-8" id="det-descripcion"></dd>

                    <dt class="col-sm-4">Referencia</dt>
                    <dd class="col-sm-8" id="det-referencia"></dd>

                    <dt class="col-sm-4">Caja</dt>
                    <dd class="col-sm-8" id="det-caja"></dd>

                    <dt class="col-sm-4">Usuario</dt>
                    <dd class="col-sm-8" id="det-usuario"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =========================
     SCRIPT DETALLE (AJAX)
========================== -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const botones = document.querySelectorAll('.btn-detalle-mov');
    const modalEl = document.getElementById('modalDetalleMovimiento');
    let modalBootstrap = null;

    if (modalEl) {
        modalBootstrap = new bootstrap.Modal(modalEl);
    }

    botones.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;

            fetch('controladores/caja/detalle_movimiento.controlador.php?id=' + encodeURIComponent(id))
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'success') {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.mensaje || 'No se pudo cargar el detalle del movimiento.'
                            });
                        } else {
                            alert(data.mensaje || 'No se pudo cargar el detalle del movimiento.');
                        }
                        return;
                    }

                    const m = data.movimiento;

                    document.getElementById('det-id').innerHTML          = m.id;
                    document.getElementById('det-fecha').innerHTML       = m.fecha_formateada;
                    document.getElementById('det-tipo').innerHTML        = (m.tipo === 'ingreso')
                        ? '<span class="badge bg-success">Ingreso</span>'
                        : '<span class="badge bg-danger">Egreso</span>';

                    document.getElementById('det-monto').innerHTML       = m.monto_formateado;
                    document.getElementById('det-tipo-mov').textContent  = m.tipo_movimiento || '-';
                    document.getElementById('det-tipo-pago').textContent = m.tipo_pago || '-';
                    document.getElementById('det-descripcion').textContent = m.descripcion || '-';

                    // referencia
                    if (m.referencia_tabla && m.referencia_id) {
                        document.getElementById('det-referencia').innerHTML =
                            m.referencia_tabla + ' #' + m.referencia_id;
                    } else {
                        document.getElementById('det-referencia').textContent = '-';
                    }

                    // caja
                    let textoCaja = '-';
                    if (m.caja_id) {
                        textoCaja = 'Caja #' + m.caja_id;
                        if (m.fecha_apertura_caja) {
                            textoCaja += ' (apertura: ' + m.fecha_apertura_caja + ')';
                        }
                    }
                    document.getElementById('det-caja').textContent = textoCaja;

                    // usuario
                    document.getElementById('det-usuario').textContent = m.usuario || '-';

                    if (modalBootstrap) {
                        modalBootstrap.show();
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al consultar el detalle.'
                        });
                    } else {
                        alert('Ocurrió un error al consultar el detalle.');
                    }
                });
        });
    });
});
</script>

