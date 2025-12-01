<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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

    <!-- Balance Mensual -->
    <div class="caja-section">
      <div class="caja-header"><i class="fa-solid fa-chart-pie me-2"></i> Balance del Mes</div>
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
          <button class="caja-btn" id="btnVerBalance"><i class="fa-solid fa-magnifying-glass me-2"></i> Ver Balance</button>
        </div>
        <div class="col-md-3 d-grid">
          <button class="caja-btn-outline" id="btnExportarPDF"><i class="fa-solid fa-file-pdf me-2"></i> Exportar PDF</button>
        </div>
      </div>

      <div id="resultadoBalance" class="mt-4 text-center"></div>
    </div>

    <!-- Cierre de Caja -->
    <div class="caja-section">
      <div class="caja-header"><i class="fa-solid fa-lock me-2"></i> Cerrar Caja Mensual</div>
      <form id="formCierreCaja">
        <label class="form-label">Observaciones</label>
        <textarea name="observaciones" id="observaciones" class="form-control mb-3" rows="3"></textarea>
        <div class="d-grid">
          <button class="caja-btn"><i class="fa-solid fa-circle-check me-2"></i> Cerrar Caja</button>
        </div>
      </form>
    </div>

    <!-- Historial de Cierres -->
    <div class="caja-section">
      <div class="caja-header"><i class="fa-solid fa-clock-rotate-left me-2"></i> Historial de Cierres</div>
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
document.addEventListener('DOMContentLoaded', ()=> {
  cargarCierres();
});

/* ======================== VER BALANCE ======================== */
document.getElementById('btnVerBalance').addEventListener('click', async ()=> {
  const fd = new FormData();
  fd.append('action','traer_balance_mensual');
  fd.append('anio', document.getElementById('anio').value);
  fd.append('mes', document.getElementById('mes').value);

  const res = await fetch('controladores/caja/caja.controlador.php', {method:'POST', body:fd});
  const data = await res.json();

  if(data.length>0){
    const b = data[0];
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
    document.getElementById('resultadoBalance').innerHTML = `<p class="text-muted">No hay movimientos registrados para este mes.</p>`;
  }
});

/* ======================== CIERRE DE CAJA ======================== */
document.getElementById('formCierreCaja').addEventListener('submit', async (e)=> {
  e.preventDefault();
  const fd = new FormData(e.target);
  fd.append('action','cerrar_caja_mensual');
  fd.append('anio', document.getElementById('anio').value);
  fd.append('mes', document.getElementById('mes').value);

  const res = await fetch('controladores/caja/caja.controlador.php', {method:'POST', body:fd});
  const data = await res.json();

  Swal.fire({
    icon: data.status === 'success' ? 'success' : 'error',
    title: data.status === 'success' ? 'Cierre realizado' : 'Error',
    text: data.mensaje,
    confirmButtonColor: '#52658F'
  });
  e.target.reset();
  cargarCierres();
});

/* ======================== HISTORIAL DE CIERRES ======================== */
async function cargarCierres(){
  const fd = new FormData();
  fd.append('action','traer_historial_cierres');

  const res = await fetch('controladores/caja/caja.controlador.php', {method:'POST', body:fd});
  const data = await res.json();

  const tbody = document.getElementById('tablaCierres');
  tbody.innerHTML = '';

  if(data.length === 0){
    tbody.innerHTML = `
      <tr>
        <td colspan="7" class="text-center text-muted py-3">
          <i class="fa-solid fa-circle-info me-2"></i> No hay cierres registrados aún.
        </td>
      </tr>`;
    return;
  }

  data.forEach(c=>{
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

/* ======================== EXPORTAR PDF ======================== */
document.getElementById('btnExportarPDF').addEventListener('click', ()=> {
  const anio = document.getElementById('anio').value;
  const mes = document.getElementById('mes').value;
  window.open(`reportes_caja/caja_reporte_mensual.php?anio=${anio}&mes=${mes}`, '_blank');
});
</script>
