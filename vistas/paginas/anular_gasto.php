<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('modelos/gastos_generales.php');

$idgasto = $_GET['idgasto'] ?? null;

$gastoModel = new GastoGeneral();

// Necesitamos un método que traiga un solo gasto por ID con info detallada
// Lo vamos a llamar traer_gasto_detallado($idgasto)
$gasto = $gastoModel->traer_gasto_detallado($idgasto);
?>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=listado_gastos">Gastos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Anular gasto</li>
    </ol>
</nav>

<div class="container mt-4 form-anular-container hacer_padding">

    <div class="header-anular">
        <i class="fa-solid fa-ban"></i> Anulación de Gasto
    </div>

    <?php if (!$gasto): ?>
        <div class="alert alert-danger mt-3">No se encontró el gasto.</div>
    <?php else: ?>

    <form id="form-anular-gasto" method="POST" action="controladores/gastos/anular_gasto.controlador.php">

        <input type="hidden" name="action" value="anular_gasto">
        <input type="hidden" name="idgasto" value="<?= $gasto['idgasto']; ?>">
        <input type="hidden" name="idusuario" value="<?= $_SESSION['idusuarios'] ?? ''; ?>">

        <!-- Datos principales del gasto -->
        <div class="mb-3">
            <label class="label-strong">Descripción del gasto</label>
            <div class="readonly-box">
                <?= htmlspecialchars($gasto['descripcion']); ?>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Monto -->
            <div class="col-md-4">
                <label class="label-strong">Monto</label>
                <div class="readonly-box">
                    $<?= number_format($gasto['monto'], 2, ',', '.'); ?>
                </div>
            </div>

            <!-- Fecha original -->
            <div class="col-md-4">
                <label class="label-strong">Fecha del gasto</label>
                <div class="readonly-box">
                    <?= date('d/m/Y', strtotime($gasto['fecha_gasto'])); ?>
                </div>
            </div>

            <!-- Tipo de gasto -->
            <div class="col-md-4">
                <label class="label-strong">Tipo de gasto</label>
                <div class="readonly-box">
                    <?= htmlspecialchars($gasto['tipo_gasto']); ?>
                </div>
            </div>
        </div>

        <!-- Origen y relación -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="label-strong">Origen</label>
                <div class="readonly-box">
                    <?= ucfirst($gasto['origen']); ?>
                </div>
            </div>

            <div class="col-md-8">
                <label class="label-strong">Relacionado con</label>
                <div class="readonly-box">
                    <?php
                    if ($gasto['origen'] === 'vehiculo' && !empty($gasto['vehiculos_idvehiculos'])) {
                        echo "Vehículo ID: <strong>{$gasto['vehiculos_idvehiculos']}</strong>";
                    } elseif ($gasto['origen'] === 'venta' && !empty($gasto['ventas_idventas'])) {
                        echo "Venta ID: <strong>{$gasto['ventas_idventas']}</strong>";
                    } elseif ($gasto['origen'] === 'empleado' && !empty($gasto['empleados_idempleados'])) {
                        echo "Empleado ID: <strong>{$gasto['empleados_idempleados']}</strong>";
                    } else {
                        echo "-";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Fecha de anulación -->
        <div class="mb-3">
            <label class="label-strong">Fecha de anulación</label>
            <input type="date" class="form-control" name="fecha_anulacion" id="fecha_anulacion" required>
        </div>

        <!-- Motivo tipificado -->
        <div class="mb-3">
            <label class="label-strong">Motivo de anulación</label>
            <select class="form-select" name="tipo_anulacion" id="tipo_anulacion" required>
                <option value="">Seleccione motivo</option>
                <option value="3">Error de carga</option>     <!-- ERROR DE CARGA -->
                <option value="4">Carga duplicada</option>    <!-- DUPLICADO -->
                <option value="2">Reemplazo por otro gasto</option> <!-- REEMPLAZO -->
                <option value="5">Otro motivo</option>        <!-- OTRO -->
            </select>
        </div>

        <!-- Detalle motivo -->
        <div class="mb-3" id="motivo_detalle_group" style="display:none;">
            <label class="label-strong">Detalle del motivo</label>
            <textarea class="form-control" name="motivo_detalle" rows="3"
                      placeholder="Describa brevemente por qué se anula este gasto..."></textarea>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="index.php?page=listado_gastos" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
            <button type="button" id="btn-anular-gasto" class="btn btn-danger">
                <i class="fa-solid fa-ban"></i> Anular gasto
            </button>
        </div>

    </form>

    <?php endif; ?>

</div>

<script>
// Mostrar / ocultar detalle del motivo
document.addEventListener('DOMContentLoaded', () => {

    const selectMotivo = document.getElementById('tipo_anulacion');
    const detalleGroup = document.getElementById('motivo_detalle_group');

    selectMotivo.addEventListener('change', () => {
        if (selectMotivo.value === '5') {
            detalleGroup.style.display = 'block';
        } else {
            detalleGroup.style.display = 'none';
        }
    });

    // Botón de anulación
    const btnAnular = document.getElementById('btn-anular-gasto');
    btnAnular.addEventListener('click', () => {

        const fecha = document.getElementById('fecha_anulacion').value.trim();

        if (fecha === '') {
            Swal.fire({
                icon: "warning",
                title: "Falta la fecha",
                text: "Debe ingresar una fecha de anulación.",
                confirmButtonColor: "#333A56"
            });
            return;
        }

        Swal.fire({
            title: "¿Confirmar anulación del gasto?",
            html: `
                Esta acción realizará:<br>
                <b>- Marcar el gasto como ANULADO</b><br>
                <b>- Registrar un reverso en caja por el mismo monto</b><br><br>
                <span class="text-danger fw-bold">Esto no puede deshacerse.</span>
            `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#B33030",
            cancelButtonColor: "#52658F",
            confirmButtonText: "Sí, anular gasto",
            cancelButtonText: "Cancelar"
        }).then((result) => {

            if (result.isConfirmed) {

                // Si tenés un loader global como en ventas:
                const loader = document.getElementById("loader-overlay");
                if (loader) {
                    loader.style.display = "flex";
                }

                document.getElementById("form-anular-gasto").submit();
            }
        });
    });
});
</script>
