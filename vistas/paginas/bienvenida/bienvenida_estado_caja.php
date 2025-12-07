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

// Clase CSS según estado
$estado_class = 'cashbox-nav--empty';
if ($estado_caja === 'Abierta') {
    $estado_class = 'cashbox-nav--open';
} elseif ($estado_caja === 'Cerrada') {
    $estado_class = 'cashbox-nav--closed';
}
?>

<section class="cashbox-status my-4">
    <div class="cashbox-nav <?= $estado_class ?>">

        <div class="cashbox-info">
            <div class="cashbox-title">
                <i class="fa-solid fa-cash-register me-2"></i>
                Caja principal
            </div>

            <div class="cashbox-meta">
                <span class="cashbox-badge">
                    Estado: <strong><?= strtoupper($estado_caja) ?></strong>
                </span>

                <span class="cashbox-separator">•</span>

                <span class="cashbox-detail">
                    Apertura: <?= $fecha_apertura ?>
                </span>

                <span class="cashbox-separator">•</span>

                <span class="cashbox-detail">
                    Saldo actual: <strong>$<?= number_format($saldo_actual, 2, ',', '.') ?></strong>
                </span>
            </div>
        </div>

        <div class="cashbox-actions">
            <?php if ($estado_caja == 'Cerrada'): ?>
                <button class="cashbox-btn"
                        onclick="window.location.href='index.php?page=caja/cierres_caja'">
                    <i class="fa-solid fa-folder-open me-1"></i>
                    Ver cierres de caja
                </button>
            <?php elseif ($estado_caja == 'Abierta'): ?>
                <button class="cashbox-btn cashbox-btn--danger"
                        id="btnCerrarCaja"
                        onclick="window.location.href='index.php?page=caja/cierres_caja'">
                    <i class="fa-solid fa-lock me-1"></i>
                    Cerrar caja
                </button>
            <?php else: ?>
                <button class="cashbox-btn"
                        onclick="window.location.href='index.php?page=caja/cierres_caja'">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i>
                    Ver historial de caja
                </button>
            <?php endif; ?>
        </div>

    </div>
</section>
