<?php
require_once('modelos/comprar_vehiculos.php');

$idcompra = $_GET['idcompra'] ?? null;

$compraModelo = new ComprarVehiculo();
$compra = $compraModelo->traer_compra_detallada($idcompra);
?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="index.php?page=listado_compras">Compras / Consignaciones</a></li>
        <li class="breadcrumb-item active">Anular consignación</li>
    </ol>
</nav>

<div class="container mt-4 form-anular-container hacer_padding">

    <div class="header-anular">
        <i class="fa-solid fa-ban"></i> Anulación de Consignación
    </div>

    <?php if (!$compra): ?>
        <div class="alert alert-danger">No se encontró la consignación.</div>
    <?php else: ?>

    <form id="form-anular-compra" method="POST" action="controladores/compras/anular_compra.controlador.php">

        <input type="hidden" name="action" value="anular_compra">
        <input type="hidden" name="idcompra" value="<?= $compra['idcompras'] ?>">
        <input type="hidden" name="idusuario" value="<?= $_SESSION['idusuarios'] ?>">

        <!-- Vehículo -->
        <div class="mb-3">
            <label class="label-strong">Vehículo</label>
            <div class="readonly-box">
                <?= $compra['marca'] . " " . $compra['modelo'] ?>
                | Año <?= $compra['anio'] ?>
                | Patente <?= $compra['patente'] ?>
                | Tipo <?= $compra['tipo_vehiculo'] ?>
            </div>
        </div>

        <!-- Titular -->
        <div class="mb-3">
            <label class="label-strong">Titular / Propietario</label>
            <div class="readonly-box">
                <?= $compra['nombre_persona'] . " " . $compra['apellido_persona'] ?>
                <?php if (!empty($compra['dni'])): ?>
                    — DNI: <?= $compra['dni'] ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Datos básicos de la consignación -->
        <div class="mb-3">
            <label class="label-strong">Datos de la consignación</label>
            <div class="readonly-box">
                Fecha registro: <?= date('d/m/Y', strtotime($compra['fecha_compra'])) ?><br>
                Tipo de pago: <?= $compra['tipo_pago_idtipo_pago'] ?? 'N/D' ?><br>
                Observaciones: <?= !empty($compra['descripcion']) ? $compra['descripcion'] : 'Sin observaciones' ?>
            </div>
        </div>

        <!-- Fecha de anulación -->
        <div class="mb-3">
            <label class="label-strong">Fecha de anulación</label>
            <input type="date" class="form-control" name="fecha_anulacion" id="fecha_anulacion" required>
        </div>

        <!-- Motivo -->
        <select class="form-select" name="tipo_anulacion" required>
            <option value="">Seleccione motivo</option>
            <option value="1">Desistimiento del cliente</option>   <!-- DESISTIMIENTO -->
            <option value="2">Reemplazo por otra operación</option><!-- REEMPLAZO -->
            <option value="3">Error de carga</option>              <!-- ERROR DE CARGA -->
            <option value="5">Otro motivo</option>                 <!-- OTRO -->
        </select>

        <!-- Detalle motivo -->
        <div class="mb-3" id="motivo_detalle_group" style="display:none;">
            <label class="label-strong">Detalle del motivo</label>
            <textarea class="form-control" name="motivo_detalle" rows="3"
                      placeholder="Describa el motivo de la anulación..."></textarea>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="index.php?page=listado_compras" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
            <button type="button" id="btn-anular-compra" class="btn btn-danger">
                <i class="fa-solid fa-ban"></i> Anular consignación
            </button>
        </div>

    </form>

    <?php endif; ?>

</div>

<script>
document.getElementById('tipo_anulacion').addEventListener('change', function() {
    const detalle = document.getElementById('motivo_detalle_group');
    if (this.value === '5') {
        detalle.style.display = 'block';
    } else {
        detalle.style.display = 'none';
    }
});

document.getElementById("btn-anular-compra").addEventListener("click", function() {
    let fecha = document.getElementById("fecha_anulacion").value;

    if (fecha.trim() === "") {
        Swal.fire({
            icon: "warning",
            title: "Falta la fecha",
            text: "Debe ingresar una fecha de anulación.",
            confirmButtonColor: "#333A56"
        });
        return;
    }

    Swal.fire({
        title: "¿Confirmar anulación?",
        html: `
            Esta acción:<br>
            <b>- Marcará la consignación como ANULADA</b><br>
            <b>- Pondrá el vehículo en estado de BAJA</b><br><br>
            <span class="text-danger fw-bold">Esto no puede deshacerse.</span>
        `,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#B33030",
        cancelButtonColor: "#52658F",
        confirmButtonText: "Sí, anular",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("loader-overlay").style.display = "flex";
            document.getElementById("form-anular-compra").submit();
        }
    });
});
</script>
