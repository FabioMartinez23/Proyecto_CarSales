<?php
require_once('modelos/vehiculos_taller.php');

$vtModel = new Vehiculos_Taller();
$result  = $vtModel->traer_vehiculos_en_taller();
?>

<!-- ===================== BREADCRUMB ===================== -->
<nav style="--bs-breadcrumb-divider: '';" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Vehículos</a></li>
        <li class="breadcrumb-item"><a href="#">Taller</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vehículos en Taller</li>
    </ol>
</nav>

<main class="hacer_padding">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Vehículos en Taller</h1>

        <a href="index.php?page=listado_vehiculos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>

    <!-- ===================== ALERTA MENSAJE ===================== -->
    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-<?= $_GET['status'] ?? 'info'; ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= htmlspecialchars($_GET['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ===================== TABLA ===================== -->
    <div class="table-responsive shadow-sm rounded-3 bg-white p-3">
        <?php if ($result && $result->num_rows > 0): ?>
            <table class="table table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th># Taller</th>
                        <th>Fecha Ingreso</th>
                        <th>Patente</th>
                        <th>Vehículo</th>
                        <th>Color</th>
                        <th>Estado Taller</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-semibold"><?= $row['idvehiculos_taller']; ?></td>

                            <td><?= date('d/m/Y H:i', strtotime($row['fecha_ingreso'])); ?></td>

                            <td class="fw-bold"><?= htmlspecialchars($row['patente']); ?></td>

                            <td>
                                <?= htmlspecialchars($row['nombre_marca'] . ' ' . $row['nombre_modelo'] . ' - ' . $row['anio']); ?>
                            </td>

                            <td><?= htmlspecialchars($row['nombre_color']); ?></td>

                            <td>
                                <?php if ($row['estado_taller'] === 'en_proceso'): ?>
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                        En proceso
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success px-3 py-2 rounded-pill">
                                        Finalizado
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <!-- Próximamente: gastos -->
                                <a href="index.php?page=gastos_taller&idvehiculos_taller=<?= $row['idvehiculos_taller']; ?>"
                                class="btn btn-sm btn-action">
                                    <i class="bi bi-cash-stack"></i> Gastos
                                </a>

                                <!-- Botón Finalizar Taller (abre modal) -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#salidaTallerModal"
                                        data-idvt="<?= $row['idvehiculos_taller']; ?>"
                                        data-vehiculo="<?= htmlspecialchars($row['patente'] . ' - ' . $row['nombre_marca'] . ' ' . $row['nombre_modelo'] . ' ' . $row['anio']); ?>">
                                    <i class="bi bi-check-circle"></i> Finalizar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p class="text-center text-muted mb-0 py-3">No hay vehículos en taller actualmente.</p>
        <?php endif; ?>
    </div>
</main>

<!-- ===================================================== -->
<!-- MODAL: Salida de Taller                               -->
<!-- ===================================================== -->
<div class="modal fade" id="salidaTallerModal" tabindex="-1" aria-labelledby="salidaTallerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-modal text-white">
                <h5 class="modal-title" id="salidaTallerModalLabel">
                    <i class="bi bi-tools me-2"></i> Finalizar trabajo de taller
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formSalidaTaller" method="POST" action="controladores/taller/salida_taller.controlador.php">
                <div class="modal-body">
                    <input type="hidden" name="idvehiculos_taller" id="salida_idvt">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Vehículo</label>
                        <p class="mb-0" id="salida_vehiculo_text" style="font-size: 0.95rem;"></p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="km_salida" class="form-label">Kilometraje de salida</label>
                            <input type="number"
                                   class="form-control"
                                   id="km_salida"
                                   name="km_salida"
                                   min="0"
                                   placeholder="Ej: 123456">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado taller actual</label>
                            <p class="mb-0">
                                <span class="badge bg-warning text-dark">En proceso</span>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="trabajo_realizado" class="form-label">Descripción del trabajo realizado</label>
                        <textarea class="form-control"
                                  id="trabajo_realizado"
                                  name="trabajo_realizado"
                                  rows="3"
                                  placeholder="Ejemplo: Cambio de aceite y filtros, alineación, revisión de frenos, reparación de abolladura lateral, etc."></textarea>
                    </div>

                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Al confirmar, el sistema:
                        <ul class="mb-0">
                            <li>Marca el trabajo de taller como finalizado.</li>
                            <li>Evalúa la documentación del vehículo.</li>
                            <li>Actualiza el estado a <strong>falta_documento</strong>, <strong>falta_digitalizacion</strong> o <strong>disponible</strong>.</li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2-circle me-1"></i> Confirmar salida de taller
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // Referencia al modal
    const salidaModal = document.getElementById('salidaTallerModal');
    const idvtInput   = document.getElementById('salida_idvt');
    const vehiculoTxt = document.getElementById('salida_vehiculo_text');
    const kmSalidaInp = document.getElementById('km_salida');
    const trabajoTxt  = document.getElementById('trabajo_realizado');
    const formSalida  = document.getElementById('formSalidaTaller');

    // Cada vez que se abre el modal, cargamos datos del botón
    salidaModal.addEventListener('show.bs.modal', function (event) {
        const button   = event.relatedTarget;
        const idvt     = button.getAttribute('data-idvt');
        const vehiculo = button.getAttribute('data-vehiculo');

        idvtInput.value   = idvt;
        vehiculoTxt.textContent = vehiculo;

        // Limpiamos campos
        kmSalidaInp.value = '';
        trabajoTxt.value  = '';
    });

    // Interceptar submit para usar SweetAlert de confirmación
    if (formSalida) {
        formSalida.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: "¿Confirmar salida de taller?",
                text: "Se finalizará el trabajo y se actualizará el estado del vehículo según su documentación.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Sí, finalizar",
                cancelButtonText: "Cancelar",
                reverseButtons: true,
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-secondary"
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Opcional: loader mientras envía
                    Swal.fire({
                        title: "Procesando...",
                        text: "Registrando salida del taller",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    formSalida.submit();
                }
            });
        });
    }
});
</script>

