<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['idventa'])) {
    echo "<div class='alert alert-danger mt-5'>No se especificó la venta.</div>";
    exit;
}

$idventa = (int) $_GET['idventa'];

require_once('modelos/vender_vehiculos.php');

$ventaModel = new VenderVehiculo();
$venta = $ventaModel->traer_venta_por_id($idventa);

if (!$venta) {
    echo "<div class='alert alert-danger mt-5'>No se encontró la venta seleccionada.</div>";
    exit;
}

// Solo tiene sentido gestionar crédito si es tipo pago 3
if ((int)$venta['tipo_pago_idtipo_pago'] !== 3) {
    echo "<div class='alert alert-warning mt-5'>La venta seleccionada no es de tipo Crédito Bancario.</div>";
    exit;
}

// Estado de crédito actual
$estadoCredito = $venta['estado_venta_credito'] ?? 'ninguno';
?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Gestión de Ventas</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=listado_ventas">Ventas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Crédito Bancario</li>
    </ol>
</nav>

<main class="dashboard-main hacer_padding">
    <div class="view-container-centered">
        <!-- HEADER -->
        <header class="view-header mb-4">
            <h1 class="view-title">
                Gestión de Crédito Bancario
            </h1>
            <p class="view-subtitle">
                Venta N° <?= htmlspecialchars($venta['idventas']); ?> — Estado actual: 
                <strong><?= htmlspecialchars($estadoCredito); ?></strong>
            </p>
        </header>

        <!-- DATOS DE LA VENTA / CLIENTE / VEHÍCULO -->
        <div class="user-detail-grid mb-4">
            <!-- Venta -->
            <section class="detail-card">
                <h2 class="detail-title">Datos de la Venta</h2>
                <ul class="detail-list">
                    <li><span>N° Venta:</span> <?= $venta['idventas']; ?></li>
                    <li><span>Fecha de Venta:</span> <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])); ?></li>
                    <li><span>Precio de Venta:</span> $<?= number_format($venta['precio_venta'], 0, ',', '.'); ?></li>
                    <li><span>Tipo de Pago:</span> Crédito Bancario</li>
                    <li><span>Estado Venta:</span> <?= htmlspecialchars($venta['estado_venta']); ?></li>
                </ul>
            </section>

            <!-- Cliente -->
            <section class="detail-card">
                <h2 class="detail-title">Cliente</h2>
                <ul class="detail-list">
                    <li><span>Nombre:</span> <?= $venta['nombre'] . " " . $venta['apellido']; ?></li>
                    <li><span>DNI:</span> <?= $venta['valor_documento']; ?></li>
                    <li><span>Contacto:</span> <?= $venta['valor_contacto']; ?></li>
                    <li><span>Email:</span> <?= $venta['email'] ?? ''; ?></li>
                </ul>
            </section>

            <!-- Vehículo -->
            <section class="detail-card full-width">
                <h2 class="detail-title">Vehículo</h2>
                <ul class="detail-list">
                    <li><span>Patente:</span> <?= $venta['patente']; ?></li>
                    <li><span>Vehículo:</span> <?= $venta['nombre_marca'] . " " . $venta['nombre_modelo']; ?></li>
                    <li><span>Año:</span> <?= $venta['anio'] ?? ''; ?></li>
                    <li><span>Color:</span> <?= $venta['nombre_descripcion'] ?? ''; ?></li>
                </ul>
            </section>
        </div>

        <!-- DATOS DEL CRÉDITO -->
        <section class="detail-card full-width mb-4">
            <h2 class="detail-title">Datos del Crédito</h2>
            <ul class="detail-list">
                <li><span>Banco / Entidad:</span> <?= htmlspecialchars($venta['banco_credito'] ?? '—'); ?></li>
                <li>
                    <span>Fecha de solicitud:</span> 
                    <?= $venta['fecha_solicitud_credito'] 
                        ? date('d/m/Y H:i', strtotime($venta['fecha_solicitud_credito'])) 
                        : '—'; ?>
                </li>
                <li><span>Estado de crédito:</span> <?= htmlspecialchars($estadoCredito); ?></li>
                <li>
                    <span>Observación / Nota crédito:</span>
                    <?= nl2br(htmlspecialchars($venta['observacion_credito'] ?? '—')); ?>
                </li>
                <?php if (!empty($venta['fecha_respuesta_credito'])): ?>
                    <li>
                        <span>Fecha de respuesta:</span>
                        <?= date('d/m/Y H:i', strtotime($venta['fecha_respuesta_credito'])); ?>
                    </li>
                    <li>
                        <span>Monto aprobado:</span>
                        <?= $venta['monto_aprobado_credito'] !== null 
                            ? '$' . number_format($venta['monto_aprobado_credito'], 0, ',', '.') 
                            : '—'; ?>
                    </li>
                <?php endif; ?>
            </ul>
        </section>

        <!-- FORMULARIO DE GESTIÓN -->
        <section class="detail-card full-width">
            <h2 class="detail-title">Registrar resultado del crédito</h2>

            <?php if ($estadoCredito === 'aprobado' || $estadoCredito === 'rechazado'): ?>
                <div class="alert alert-info">
                    Esta operación ya fue marcada como <strong><?= htmlspecialchars($estadoCredito); ?></strong>.
                    Podés actualizar la información si fuese necesario.
                </div>
            <?php endif; ?>

            <form action="controladores/ventas/gestionar_credito.controlador.php" method="POST" class="mt-3">
                <input type="hidden" name="action" value="actualizar_credito">
                <input type="hidden" name="idventa" value="<?= $venta['idventas']; ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="resultado" class="form-label">Resultado del crédito</label>
                        <select name="resultado" id="resultado" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="rechazado">Rechazado</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="grupo_monto_aprobado" style="display:none;">
                        <label for="monto_aprobado_credito" class="form-label">Monto aprobado</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input 
                                type="number"
                                step="0.01"
                                min="0"
                                name="monto_aprobado_credito"
                                id="monto_aprobado_credito"
                                class="form-control text-end"
                            >
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="observacion_respuesta" class="form-label">
                            Observaciones / Respuesta del banco
                        </label>
                        <textarea 
                            class="form-control" 
                            id="observacion_respuesta" 
                            name="observacion_respuesta" 
                            rows="3"
                        ></textarea>
                    </div>
                </div>

                <div class="detail-actions mt-4">
                    <a href="index.php?page=listado_ventas" class="btn btn-secondary me-2">
                        Volver al listado
                    </a>
                    <button type="submit" class="btn btn-success">
                        Guardar resultado
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectResultado = document.getElementById('resultado');
    const grupoMonto = document.getElementById('grupo_monto_aprobado');

    selectResultado.addEventListener('change', function() {
        if (this.value === 'aprobado') {
            grupoMonto.style.display = 'block';
        } else {
            grupoMonto.style.display = 'none';
            document.getElementById('monto_aprobado_credito').value = '';
        }
    });
});
</script>
