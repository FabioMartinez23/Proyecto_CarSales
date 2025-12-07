<?php
$vehiculo_precio = new PrecioVehiculo();
$autosArray = $vehiculo_precio->traer_vehiculos_con_precios();

$autoData = [];

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    foreach ($autosArray as $auto) {
        if ($auto['idvehiculos'] == $id) {
            $autoData = $auto;
            break;
        }
    }

    if (empty($autoData)) {
        echo "<div class='container mt-4 hacer_padding'>
                <div class='alert alert-warning'>No se encontró el vehículo solicitado.</div>
              </div>";
        return;
    }
} else {
    echo "<div class='container mt-4 hacer_padding'>
            <div class='alert alert-warning'>No se ha proporcionado el ID del vehículo.</div>
          </div>";
    return;
}

// Normalizamos nombres de campos para no romper si viene del otro método
$marca  = $autoData['nombre_marca']  ?? ($autoData['marca']  ?? '');
$modelo = $autoData['nombre_modelo'] ?? ($autoData['modelo'] ?? '');
$anio   = $autoData['año']           ?? ($autoData['anio']   ?? '');
$precio = $autoData['precio_publico'] ?? ($autoData['precio'] ?? 0);
?>

<main class="dashboard-main cliente-dashboard hacer_padding">

    <!-- HERO DEL VEHÍCULO -->
    <section class="vehiculo-sim-hero mb-4">
        <div class="vehiculo-sim-hero-main">
            <h2 class="vehiculo-sim-title mb-1">
                Simulador de financiación
            </h2>
            <p class="vehiculo-sim-subtitle mb-2">
                <?= htmlspecialchars($marca . ' ' . $modelo) ?>
            </p>

            <div class="vehiculo-sim-meta">
                <?php if (!empty($anio)): ?>
                    <span class="vehiculo-sim-chip">
                        Año <?= htmlspecialchars($anio) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="vehiculo-sim-hero-price">
            <span class="vehiculo-sim-price-label">Precio de contado</span>
            <span class="vehiculo-sim-price-value">
                $<?= number_format($precio, 0, ',', '.') ?>
            </span>
        </div>
    </section>

    <!-- SIMULADOR DE FINANCIACIÓN (MISMA ESTRUCTURA QUE bienvenida_cliente) -->
    <section class="simulador-section simulador-section--vehiculo">
        <div class="simulador-header">
            <h4 class="simulador-title">Simulador de financiación</h4>
            <span class="simulador-badge">Estimativo</span>
        </div>

        <p class="simulador-description">
            Estás simulando financiación para este vehículo en particular.
            Indicá una entrega aproximada, la cantidad de cuotas y la tasa anual.
        </p>

        <form id="form-simulador-cuotas" onsubmit="return false;">
            <div class="simulador-grid">
                <!-- Vehículo (solo lectura, pero mantiene la misma estructura) -->
                <div>
                    <label class="form-label">Vehículo</label>
                    <select id="vehiculo_simulacion" class="form-select" disabled>
                        <option
                            value="<?= htmlspecialchars($autoData['idvehiculos']) ?>"
                            data-precio="<?= htmlspecialchars($precio) ?>">
                            <?= htmlspecialchars($marca . ' ' . $modelo . ' (' . $anio . ')') ?>
                            - $<?= number_format($precio, 0, ',', '.') ?>
                        </option>
                    </select>
                    <small class="text-muted">Simulando sobre este vehículo.</small>
                </div>

                <!-- Entrega (con puntos) -->
                <div>
                    <label class="form-label">Entrega aproximada ($)</label>
                    <input
                        type="text"
                        id="entrega_simulacion"
                        class="form-control"
                        inputmode="numeric"
                        placeholder="Ej: 1.500.000">
                </div>

                <!-- Cuotas -->
                <div>
                    <label class="form-label">Cuotas</label>
                    <select id="cuotas_simulacion" class="form-select">
                        <option value="12">12 cuotas</option>
                        <option value="24">24 cuotas</option>
                        <option value="36">36 cuotas</option>
                    </select>
                </div>

                <!-- Tasa -->
                <div>
                    <label class="form-label">Tasa aprox. (anual)</label>
                    <select id="tasa_simulacion" class="form-select">
                        <option value="60">60% (Banco aprox.)</option>
                        <option value="75">75% (Banco aprox.)</option>
                        <option value="90">90% (Financiación directa)</option>
                    </select>
                </div>
            </div>

            <div class="simulador-actions">
                <button
                    type="button"
                    class="btn-simular-primary"
                    onclick="calcularCuotasCliente();">
                    Calcular cuotas
                </button>

                <div id="resultado_cuotas" class="simulador-result"></div>
            </div>
        </form>
    </section>

</main>

<!-- JS simulador (mismo comportamiento que en bienvenida_cliente, pero sólo cuotas) -->
<script>
// ================================
// HELPERS DE FORMATEO
// ================================
function formatearEnteroConPuntos(valor) {
    const soloNumeros = (valor || '').toString().replace(/\D/g, '');
    if (!soloNumeros) return '';
    return soloNumeros.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function obtenerEnteroDesdeInput(id) {
    const el = document.getElementById(id);
    if (!el) return 0;
    const soloNumeros = (el.value || '').toString().replace(/\D/g, '');
    if (!soloNumeros) return 0;
    return parseInt(soloNumeros, 10);
}

function formatoPesosEntero(valor) {
    if (isNaN(valor)) return '$0';
    const numero = Math.round(Number(valor));
    return '$' + numero.toLocaleString('es-AR', { maximumFractionDigits: 0 });
}

document.addEventListener('DOMContentLoaded', function() {
    // Sólo necesitamos formatear la entrega en este simulador
    const inputEntrega = document.getElementById('entrega_simulacion');
    if (inputEntrega) {
        inputEntrega.addEventListener('input', function() {
            const pos = inputEntrega.selectionStart;
            const antes = inputEntrega.value;
            inputEntrega.value = formatearEnteroConPuntos(inputEntrega.value);

            const diff = antes.length - inputEntrega.value.length;
            const nuevaPos = (pos - diff >= 0) ? pos - diff : inputEntrega.value.length;
            inputEntrega.selectionStart = inputEntrega.selectionEnd = nuevaPos;
        });
    }
});

// ================================
// SIMULADOR DE CUOTAS (MISMA LÓGICA)
// ================================
function calcularCuotasCliente() {
    const selectVehiculo = document.getElementById('vehiculo_simulacion');
    const cuotasSelect   = document.getElementById('cuotas_simulacion');
    const tasaSelect     = document.getElementById('tasa_simulacion');
    const resultadoDiv   = document.getElementById('resultado_cuotas');

    if (!selectVehiculo || !cuotasSelect || !tasaSelect || !resultadoDiv) return;

    const opcion = selectVehiculo.options[selectVehiculo.selectedIndex];

    const precio  = parseFloat(opcion ? (opcion.getAttribute('data-precio') || 0) : 0);
    const entrega = obtenerEnteroDesdeInput('entrega_simulacion');
    const cuotas  = parseInt(cuotasSelect.value);
    const tasaAnual = parseFloat(tasaSelect.value);

    if (!precio || precio <= 0) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">No se pudo obtener el precio del vehículo.</span>';
        return;
    }

    if (entrega < 0) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">La entrega no puede ser negativa.</span>';
        return;
    }

    if (entrega >= precio) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">La entrega no puede ser mayor o igual al precio del vehículo.</span>';
        return;
    }

    if (!cuotas || cuotas <= 0) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">Seleccioná una cantidad de cuotas válida.</span>';
        return;
    }

    const montoFinanciar = precio - entrega;
    const tasaMensual = (tasaAnual / 12) / 100;

    let cuota = 0;

    if (tasaMensual > 0) {
        // Sistema francés
        cuota = montoFinanciar * (tasaMensual / (1 - Math.pow(1 + tasaMensual, -cuotas)));
    } else {
        cuota = montoFinanciar / cuotas;
    }

    resultadoDiv.innerHTML = `
        <span>Monto a financiar: 
            <span class="monto-principal">${formatoPesosEntero(montoFinanciar)}</span>
        </span><br>
        <span>Cuota estimada en ${cuotas} pagos: 
            <span class="monto-cuota">${formatoPesosEntero(cuota)}</span>
        </span>
    `;
}
</script>


