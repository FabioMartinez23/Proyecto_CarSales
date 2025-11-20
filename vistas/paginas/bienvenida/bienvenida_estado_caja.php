<?php
$estado_caja = 'Sin datos';
$fecha_apertura = '-';
$saldo_actual = 0.00;

if ($caja_actual && $caja_actual->num_rows > 0) {
    $datos_caja = $caja_actual->fetch_assoc();
    $estado_caja = ucfirst($datos_caja['estado']);
    $fecha_apertura = date('d/m/Y H:i', strtotime($datos_caja['fecha_apertura']));
    $saldo_actual = $datos_caja['saldo_actual'];
} else {
    $ultima_cerrada = $caja->traer_cajas_cerradas();
    if ($ultima_cerrada && $ultima_cerrada->num_rows > 0) {
        $fila = $ultima_cerrada->fetch_assoc();
        $estado_caja = 'Cerrada';
        $fecha_apertura = date('d/m/Y H:i', strtotime($fila['fecha_apertura']));
        $saldo_actual = $fila['saldo_actual'];
    }
}
?>

<section class="estado-caja my-4">
    <div class="card text-center p-4 shadow-sm"
         style="border-left: 6px solid <?= ($estado_caja == 'Abierta' ? '#198754' : '#dc3545') ?>;">
        <h4 class="fw-bold mb-3">
            <i class="fa-solid fa-cash-register me-2"></i>
            Estado de la Caja:
            <span class="text-<?= ($estado_caja == 'Abierta' ? 'success' : 'danger') ?>">
                <?= strtoupper($estado_caja) ?>
            </span>
        </h4>

        <p><strong>Fecha de apertura:</strong> <?= $fecha_apertura ?></p>
        <p><strong>Saldo actual:</strong> $<?= number_format($saldo_actual, 2, ',', '.') ?></p>

        <?php if ($estado_caja == 'Cerrada'): ?>
            <button class="btn btn-sm btn-outline-primary mt-2"
                onclick="window.location.href='index.php?page=caja/cierres_caja'">
                <i class="fa-solid fa-folder-open me-1"></i> Ver cierres de caja
            </button>
        <?php else: ?>
            <button class="btn btn-sm btn-outline-danger mt-2" id="btnCerrarCaja">
                <i class="fa-solid fa-lock me-1"></i> Cerrar Caja
            </button>
        <?php endif; ?>
    </div>
</section>
