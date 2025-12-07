<main class="dashboard-main cliente-dashboard hacer_padding">

    <!-- HEADER / PANEL CLIENTE -->
    <header class="dashboard-header dashboard-header--client d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="dashboard-title cliente-dashboard-title mb-1">
                ¡Bienvenido a CarSales!
            </h3>
            <p class="dashboard-subtitle cliente-dashboard-subtitle mb-0">
                Desde aquí podés ver tu resumen, simular financiación y calcular la toma de tu vehículo usado.
            </p>
        </div>
        <div class="dashboard-meta text-end d-none d-md-block">
            <span class="dashboard-tag">Cliente</span>
        </div>
    </header>

    <!-- TARJETAS RESUMEN (mismo layout que admin/empleado) -->
    <section class="stat-grid cliente-stat-grid">
        <!-- Vehículos disponibles -->
        <article class="stat-item stat-item--clickable"
                 onclick="window.location.href='index.php?page=comprar_vehiculo'">
            <div class="stat-label">Vehículos disponibles</div>
            <div class="stat-value">
                <?= isset($total_vehiculos_count) ? $total_vehiculos_count : 0 ?>
            </div>
            <div class="stat-footer">
                Ver catálogo completo
            </div>
        </article>

        <!-- Mis compras -->
        <article class="stat-item">
            <div class="stat-label">Mis compras</div>
            <div class="stat-value">
                <?= isset($mis_compras_count) ? $mis_compras_count : 0 ?>
            </div>
            <div class="stat-footer">
                Historial de operaciones
            </div>
        </article>

        <!-- Recomendados -->
        <article class="stat-item">
            <div class="stat-label">Recomendados para ti</div>
            <div class="stat-value">
                <?= isset($vehiculos_sugeridos) ? $vehiculos_sugeridos : 0 ?>
            </div>
            <div class="stat-footer">
                Según tu perfil y preferencias
            </div>
        </article>
    </section>

    <!-- SIMULADOR DE FINANCIACIÓN -->
    <section class="simulador-section">
        <div class="simulador-header">
            <h4 class="simulador-title">Simulador de financiación</h4>
            <span class="simulador-badge">Estimativo</span>
        </div>

        <p class="simulador-description">
            Elegí un vehículo, indicá una entrega aproximada y un plan de cuotas.
            Los valores son estimados y podrán variar según la entidad financiera.
        </p>

        <form id="form-simulador-cuotas" onsubmit="return false;">
            <div class="simulador-grid">
                <!-- Vehículo -->
                <div>
                    <label class="form-label">Vehículo</label>
                    <select id="vehiculo_simulacion" class="form-select">
                        <option value="">Seleccioná un vehículo</option>
                        <?php if (!empty($vehiculos_disponibles)): ?>
                            <?php foreach ($vehiculos_disponibles as $veh): ?>
                                <option
                                    value="<?= $veh['idvehiculos'] ?>"
                                    data-precio="<?= $veh['precio_publico'] ?? $veh['precio'] ?? 0 ?>">
                                    <?= $veh['marca'] . ' ' . $veh['modelo'] . ' (' . $veh['anio'] . ')' ?>
                                    - $<?= number_format($veh['precio_publico'] ?? $veh['precio'] ?? 0, 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="text-muted">Usaremos el precio público como base.</small>
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

    <!-- SIMULADOR DE TOMA EN PARTE DE PAGO -->
    <section class="simulador-section">
        <div class="simulador-header">
            <h4 class="simulador-title">Simulador de toma de vehículo usado</h4>
            <span class="simulador-badge">Tasación estimativa</span>
        </div>

        <p class="simulador-description">
            Ingresá los datos de tu vehículo y un valor de referencia de mercado.
            Te mostraremos un precio de toma estimado según el estado declarado.
        </p>

        <form id="form-simulador-toma" onsubmit="return false;">

            <!-- Primera fila de datos del vehículo -->
            <div class="simulador-grid">
                <div>
                    <label class="form-label">Marca</label>
                    <input
                        type="text"
                        id="toma_marca"
                        class="form-control"
                        placeholder="Ej: Fiat"
                        required>
                </div>

                <div>
                    <label class="form-label">Modelo</label>
                    <input
                        type="text"
                        id="toma_modelo"
                        class="form-control"
                        placeholder="Ej: Cronos"
                        required>
                </div>

                <div>
                    <label class="form-label">Año</label>
                    <input
                        type="number"
                        id="toma_anio"
                        class="form-control"
                        min="1980"
                        max="<?= date('Y') ?>"
                        placeholder="Ej: 2019"
                        required>
                </div>

                <div>
                    <label class="form-label">Kilometraje</label>
                    <input
                        type="text"
                        id="toma_km"
                        class="form-control"
                        inputmode="numeric"
                        placeholder="Ej: 85.000"
                        required>
                </div>
            </div>

            <!-- Segunda fila: color + estados -->
            <div class="simulador-grid-3" style="margin-top: 0.8rem;">
                <div>
                    <label class="form-label">Color</label>
                    <input
                        type="text"
                        id="toma_color"
                        class="form-control"
                        placeholder="Ej: Blanco"
                        required>
                </div>

                <div>
                    <label class="form-label">Estado carrocería</label>
                    <select id="toma_carroceria" class="form-select" required>
                        <option value="1">Excelente</option>
                        <option value="0.95">Buena</option>
                        <option value="0.85">Regular</option>
                        <option value="0.7">Mala</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Estado neumáticos</label>
                    <select id="toma_neumaticos" class="form-select" required>
                        <option value="1">Excelente</option>
                        <option value="0.95">Buena</option>
                        <option value="0.85">Regular</option>
                        <option value="0.7">Mala</option>
                    </select>
                </div>
            </div>

            <!-- Tercera fila: cristales + valor referencia -->
            <div class="simulador-grid-2" style="margin-top: 0.8rem;">
                <div>
                    <label class="form-label">Estado cristales</label>
                    <select id="toma_cristales" class="form-select" required>
                        <option value="1">Excelente</option>
                        <option value="0.95">Buena</option>
                        <option value="0.85">Regular</option>
                        <option value="0.7">Mala</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Valor de referencia mercado ($)</label>
                    <input
                        type="text"
                        id="toma_valor_referencia"
                        class="form-control"
                        inputmode="numeric"
                        placeholder="Ej: 10.000.000"
                        required>
                    <small class="text-muted">
                        Podés usar valores de listas oficiales o publicaciones online.
                    </small>
                </div>
            </div>

            <div class="simulador-actions">
                <button
                    type="button"
                    class="btn-simular-secondary"
                    onclick="calcularTomaCliente();">
                    Calcular precio de toma estimado
                </button>

                <div id="resultado_toma" class="simulador-result"></div>
            </div>
        </form>
    </section>

</main>

<!-- JS simuladores + formateo -->
<script>
// ================================
// HELPERS DE FORMATEO
// ================================
function formatearEnteroConPuntos(valor) {
    // Deja solo números
    const soloNumeros = (valor || '').toString().replace(/\D/g, '');
    if (!soloNumeros) return '';
    // Agrega puntos como miles
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
    // Nos aseguramos de trabajar con número entero
    const numero = Math.round(Number(valor));
    return '$' + numero.toLocaleString('es-AR', { maximumFractionDigits: 0 });
}

document.addEventListener('DOMContentLoaded', function() {
    // Inputs que deben mostrar puntos mientras el usuario escribe
    const inputsConPuntos = ['entrega_simulacion', 'toma_km', 'toma_valor_referencia'];

    inputsConPuntos.forEach(function(id) {
        const input = document.getElementById(id);
        if (!input) return;

        input.addEventListener('input', function(e) {
            const pos = input.selectionStart;
            const antes = input.value;
            input.value = formatearEnteroConPuntos(input.value);

            // Intento mantener el cursor lo más cerca posible del final
            const diff = antes.length - input.value.length;
            input.selectionEnd = input.selectionStart = pos - diff >= 0 ? pos - diff : input.value.length;
        });
    });

    // Validación extra para el año (máx 4 dígitos y no superar año actual)
    const inputAnio = document.getElementById('toma_anio');
    if (inputAnio) {
        const currentYear = <?= date('Y') ?>;

        inputAnio.addEventListener('input', function() {
            // Solo dígitos
            let value = inputAnio.value.replace(/\D/g, '');
            // Limitar a 4 dígitos
            if (value.length > 4) {
                value = value.slice(0, 4);
            }
            inputAnio.value = value;
        });

        inputAnio.addEventListener('blur', function() {
            if (!inputAnio.value) return;
            const year = parseInt(inputAnio.value, 10);
            if (year > currentYear) {
                alert('El año no puede ser mayor a ' + currentYear + '.');
                inputAnio.value = currentYear;
            }
        });
    }
});

// ================================
// SIMULADOR DE CUOTAS
// ================================
function calcularCuotasCliente() {
    const selectVehiculo = document.getElementById('vehiculo_simulacion');
    const entregaInput   = document.getElementById('entrega_simulacion');
    const cuotasSelect   = document.getElementById('cuotas_simulacion');
    const tasaSelect     = document.getElementById('tasa_simulacion');
    const resultadoDiv   = document.getElementById('resultado_cuotas');

    const opcion = selectVehiculo.options[selectVehiculo.selectedIndex];

    const precio = parseFloat(opcion ? (opcion.getAttribute('data-precio') || 0) : 0);
    const entrega = obtenerEnteroDesdeInput('entrega_simulacion');
    const cuotas = parseInt(cuotasSelect.value);
    const tasaAnual = parseFloat(tasaSelect.value);

    if (!precio || precio <= 0) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">Seleccioná un vehículo válido.</span>';
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


// ================================
// SIMULADOR DE TOMA EN PARTE DE PAGO
// ================================
function calcularTomaCliente() {
    const km = obtenerEnteroDesdeInput('toma_km');
    const factorCarroceria = parseFloat(document.getElementById('toma_carroceria').value || 1);
    const factorNeumaticos = parseFloat(document.getElementById('toma_neumaticos').value || 1);
    const factorCristales  = parseFloat(document.getElementById('toma_cristales').value || 1);
    const valorRef = obtenerEnteroDesdeInput('toma_valor_referencia');
    const resultadoDiv = document.getElementById('resultado_toma');

    if (!valorRef || valorRef <= 0) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">Ingresá un valor de referencia válido.</span>';
        return;
    }

    // Factor por kilometraje (ejemplo simple)
    let factorKm = 1;
    if (km > 80000 && km <= 150000) factorKm = 0.9;
    else if (km > 150000 && km <= 220000) factorKm = 0.8;
    else if (km > 220000) factorKm = 0.7;

    // Promedio de factores de estado
    const factorEstadoPromedio = (factorCarroceria + factorNeumaticos + factorCristales) / 3;

    // Factor general de toma (ejemplo: concesionaria toma algo más bajo que mercado)
    const factorNegocio = 0.85; // 85% del valor luego de estados

    const valorAjustado = valorRef * factorKm * factorEstadoPromedio * factorNegocio;

    resultadoDiv.innerHTML = `
        <span>Precio de toma estimado: 
            <span class="monto-cuota">${formatoPesosEntero(valorAjustado)}</span>
        </span><br>
        <small>
            Valor aproximado sujeto a inspección física y tasación final en la concesionaria.
        </small>
    `;
}
</script>
