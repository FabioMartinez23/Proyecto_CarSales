<?php
ini_set('display_errors', 1);
$idvehiculos_taller = (int)($_GET['idvehiculos_taller'] ?? 0);

$vtModel = new Vehiculos_Taller();
$detalle = $vtModel->traer_por_id($idvehiculos_taller);

$gastosModel = new Gastos_Taller();
$gastos = $gastosModel->traer_gastos_por_vehiculo_taller($idvehiculos_taller);

// Traer tipo_gasto solo para aplica_en = 'taller'
$conexion = new Conexion();
$conexion->conectar();
$con = $conexion->_con;
$tiposGasto = $con->query("
    SELECT * FROM tipo_gasto
    WHERE aplica_en = 'taller' AND activo_gasto = 1
    ORDER BY descripcion
");
?>

<nav style="--bs-breadcrumb-divider: '';" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Taller</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=listado_vehiculos_taller">Vehículos en Taller</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gastos del Vehículo</li>
    </ol>
</nav>

<main class="hacer_padding">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="mb-0">Gastos de Taller</h1>
            <?php if ($detalle): ?>
                <p class="text-muted mb-0">
                    Vehículo: <strong><?= htmlspecialchars($detalle['patente']); ?></strong> ·
                    <?= htmlspecialchars($detalle['nombre_marca'] . ' ' . $detalle['nombre_modelo'] . ' ' . $detalle['anio']); ?>
                </p>
            <?php endif; ?>
        </div>

        <a href="index.php?page=listado_vehiculos_taller" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver al Taller
        </a>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-<?= $_GET['status'] ?? 'info'; ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= htmlspecialchars($_GET['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ==================== FORMULARIO NUEVO GASTO ==================== -->
    <section class="mb-4">
        <div class="bg-white shadow-sm rounded-3 p-3">
            <h5 class="mb-3"><i class="bi bi-plus-circle me-2"></i> Registrar nuevo gasto</h5>

            <form method="POST" action="controladores/taller/gastos_taller.controlador.php" class="row g-3">
                <input type="hidden" name="action" value="registrar_gasto_taller">
                <input type="hidden" name="vehiculos_taller_id" value="<?= $idvehiculos_taller; ?>">

                <div class="col-md-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha_gasto" class="form-control" value="<?= date('Y-m-d'); ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tipo de gasto</label>
                    <select name="tipo_gasto_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php while ($tg = $tiposGasto->fetch_assoc()): ?>
                            <option value="<?= $tg['idtipo_gasto']; ?>">
                                <?= htmlspecialchars($tg['descripcion']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Monto</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" name="monto" class="form-control" required oninput="formatearNumero(this)">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Forma de pago</label>
                    <select name="tipo_pago" class="form-select">
                        <option value="1">Efectivo</option>
                        <option value="2">Transferencia</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Proveedor</label>
                    <input type="text" name="proveedor" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Comprobante Nº</label>
                    <input type="text" name="comprobante_numero" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Descripción / Detalle</label>
                    <textarea name="descripcion" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-action">
                        <i class="bi bi-save me-1"></i> Guardar gasto
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- ==================== LISTADO DE GASTOS ==================== -->
    <section>
        <div class="bg-white shadow-sm rounded-3 p-3">
            <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i> Historial de gastos</h5>

            <?php if ($gastos && $gastos->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Proveedor</th>
                                <th>Comprobante</th>
                                <th>Monto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($g = $gastos->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($g['fecha_gasto'])); ?></td>
                                    <td><?= htmlspecialchars($g['tipo_gasto']); ?></td>
                                    <td><?= htmlspecialchars($g['proveedor']); ?></td>
                                    <td><?= htmlspecialchars($g['comprobante_numero']); ?></td>
                                    <td class="fw-bold text-danger">
                                        $<?= number_format($g['monto'], 2, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <form id="form-anular-<?= $g['idgastos_taller']; ?>"
                                              method="POST"
                                              action="controladores/taller/gastos_taller.controlador.php"
                                              class="d-inline">
                                            <input type="hidden" name="action" value="anular_gasto_taller">
                                            <input type="hidden" name="idgastos_taller" value="<?= $g['idgastos_taller']; ?>">
                                            <input type="hidden" name="vehiculos_taller_id" value="<?= $idvehiculos_taller; ?>">
                                            <input type="hidden" name="motivo_anulacion" value="Anulación manual de gasto taller">

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmarAnularGastoTaller('form-anular-<?= $g['idgastos_taller']; ?>')">
                                                <i class="bi bi-x-circle"></i> Anular
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">Aún no hay gastos cargados para este vehículo.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    function formatearNumero(input) {
        let valor = input.value.replace(/\D/g, '');
        valor = new Intl.NumberFormat('es-AR').format(valor);
        input.value = valor;
    }
</script>

<script>
function confirmarAnularGastoTaller(formId) {
    Swal.fire({
        title: "¿Anular gasto de taller?",
        text: "Se generará un reverso en Caja para compensar este gasto.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, anular",
        cancelButtonText: "Cancelar",
        reverseButtons: true,
        customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: "btn btn-secondary"
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Procesando...",
                text: "Aplicando reverso en caja",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            document.getElementById(formId).submit();
        }
    });
}
</script>
