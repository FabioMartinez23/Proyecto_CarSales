<?php
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['idusuarios'])) {
    header('Location: ../../index.php?page=login');
    exit();
}
?>

<!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Caja</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cierre de Caja</li>
    </ol>
</nav>

<div class="hacer_padding">
    <div class="caja-container">
        <h1 class="caja-titulo">
            <i class="fa-solid fa-cash-register me-2"></i> Cierre y Balance Mensual de Caja
        </h1>

        <!-- ===================== BALANCE GENERAL ===================== -->
        <div class="caja-section">
            <div class="caja-header">
                <i class="fa-solid fa-chart-pie me-2"></i> Balance del Mes
            </div>

            <div class="row g-3 align-items-end justify-content-center">
                <div class="col-md-3">
                    <label class="form-label">Año</label>
                    <input type="number" id="anio" class="form-control" value="<?= date('Y') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mes</label>
                    <input type="number" id="mes" class="form-control" value="<?= date('m') ?>">
                </div>
                <div class="col-md-3 d-grid">
                    <button class="caja-btn" id="btnVerBalance">
                        <i class="fa-solid fa-magnifying-glass me-2"></i> Ver Balance
                    </button>
                </div>
                <div class="col-md-3 d-grid">
                    <button class="caja-btn-outline" id="btnExportarPDF">
                        <i class="fa-solid fa-file-pdf me-2"></i> Exportar PDF
                    </button>
                </div>
            </div>

            <div id="resultadoBalance" class="mt-4 text-center"></div>

            <!-- ===================== BALANCE POR TIPO DE PAGO ===================== -->
            <div id="balancePorPago" class="mt-5"></div>
        </div>


        <!-- ===================== CIERRE DE CAJA ===================== -->
        <div class="caja-section">
            <div class="caja-header"><i class="fa-solid fa-lock me-2"></i> Cerrar Caja Mensual</div>

            <form id="formCierreCaja">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" id="observaciones" class="form-control mb-3" rows="3"></textarea>

                <div class="d-grid">
                    <button class="caja-btn">
                        <i class="fa-solid fa-circle-check me-2"></i> Cerrar Caja
                    </button>
                </div>
            </form>
        </div>


        <!-- ===================== HISTORIAL ===================== -->
        <div class="caja-section">
            <div class="caja-header">
                <i class="fa-solid fa-clock-rotate-left me-2"></i> Historial de Cierres
            </div>

            <div class="table-responsive">
                <table class="caja-tabla">
                    <thead>
                        <tr>
                            <th>Fecha Cierre</th>
                            <th>Ingresos</th>
                            <th>Egresos</th>
                            <th>Balance</th>
                            <th>Saldo Final</th>
                            <th>Usuario</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCierres"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- ======================== SCRIPT ======================== -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    cargarCierres();
});

/* ============================================================
   1) BALANCE GENERAL DEL MES
============================================================ */
document.getElementById('btnVerBalance').addEventListener('click', async () => {
    const anio = document.getElementById('anio').value;
    const mes  = document.getElementById('mes').value;

    /* --------- A) BALANCE GENERAL --------- */
    const fd1 = new FormData();
    fd1.append('action', 'traer_balance_mensual');
    fd1.append('anio', anio);
    fd1.append('mes', mes);

    const res1  = await fetch('controladores/caja/caja.controlador.php', { method:'POST', body: fd1 });
    const data1 = await res1.json();

    if (data1.length > 0) {
        const b = data1[0];

        document.getElementById('resultadoBalance').innerHTML = `
            <div class="row justify-content-center text-center">
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light shadow-sm">
                        <h5 class="text-success">Ingresos</h5>
                        <h4>$${parseFloat(b.total_ingresos).toFixed(2)}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light shadow-sm">
                        <h5 class="text-danger">Egresos</h5>
                        <h4>$${parseFloat(b.total_egresos).toFixed(2)}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light shadow-sm">
                        <h5 class="${b.balance >= 0 ? 'text-success' : 'text-danger'}">Balance</h5>
                        <h4>$${parseFloat(b.balance).toFixed(2)}</h4>
                    </div>
                </div>
            </div>`;
    } else {
        document.getElementById('resultadoBalance').innerHTML =
            `<p class="text-muted">No hay movimientos registrados para este mes.</p>`;
    }


    /* --------- B) BALANCE POR TIPO DE PAGO --------- */
    const fd2 = new FormData();
    fd2.append('action', 'traer_balance_mensual_por_tipo_pago');
    fd2.append('anio', anio);
    fd2.append('mes', mes);

    const res2  = await fetch('controladores/caja/caja.controlador.php', { method:'POST', body: fd2 });
    const data2 = await res2.json();

    renderizarBalancePorPago(data2);
});


/* ============================================================
   2) RENDER DETALLE POR MÉTODO DE PAGO
============================================================ */
function renderizarBalancePorPago(data) {
    if (!data || data.length === 0) {
        document.getElementById('balancePorPago').innerHTML = `
            <p class="text-muted text-center mt-3">
                No hay movimientos por tipo de pago en este mes.
            </p>`;
        return;
    }

    let html = `
        <h4 class="mt-4"><i class="fa-solid fa-money-bill-wave me-2"></i>
        Detalle por Tipo de Pago</h4>

        <div class="table-responsive mt-3">
            <table class="caja-tabla">
                <thead>
                    <tr>
                        <th>Método de Pago</th>
                        <th>Ingresos</th>
                        <th>Egresos</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>`;

    data.forEach(p => {
        html += `
            <tr>
                <td>${p.metodo_pago}</td>
                <td class="text-success">$${parseFloat(p.total_ingresos).toFixed(2)}</td>
                <td class="text-danger">$${parseFloat(p.total_egresos).toFixed(2)}</td>
                <td class="${p.balance >= 0 ? 'text-success' : 'text-danger'} fw-bold">
                    $${parseFloat(p.balance).toFixed(2)}
                </td>
            </tr>`;
    });

    html += `
                </tbody>
            </table>
        </div>`;

    document.getElementById('balancePorPago').innerHTML = html;
}


/* ============================================================
   3) HISTORIAL DE CIERRES
============================================================ */
async function cargarCierres() {
    const fd = new FormData();
    fd.append('action', 'traer_historial_cierres');

    const res  = await fetch('controladores/caja/caja.controlador.php', { method:'POST', body: fd });
    const data = await res.json();

    const tbody = document.getElementById('tablaCierres');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-3">
                    <i class="fa-solid fa-circle-info me-2"></i> No hay cierres registrados aún.
                </td>
            </tr>`;
        return;
    }

    data.forEach(c => {
        tbody.innerHTML += `
            <tr>
                <td>${c.fecha_cierre}</td>
                <td>$${parseFloat(c.total_ingresos).toFixed(2)}</td>
                <td>$${parseFloat(c.total_egresos).toFixed(2)}</td>
                <td class="${c.balance_final >= 0 ? 'text-success' : 'text-danger'} fw-semibold">
                    $${parseFloat(c.balance_final).toFixed(2)}
                </td>
                <td>$${parseFloat(c.saldo_final).toFixed(2)}</td>
                <td>${c.usuario ?? '-'}</td>
                <td>${c.observaciones ?? '-'}</td>
            </tr>`;
    });
}


/* ============================================================
   4) EXPORTAR PDF
============================================================ */
document.getElementById('btnExportarPDF').addEventListener('click', () => {
    const anio = document.getElementById('anio').value;
    const mes  = document.getElementById('mes').value;

    window.open(`reportes_caja/caja_reporte_mensual.php?anio=${anio}&mes=${mes}`, '_blank');
});
</script>
