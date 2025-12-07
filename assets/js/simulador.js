// ================================
// HELPERS DE FORMATEO (mismos que bienvenida_cliente)
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

// ================================
// INICIALIZACIÓN DE CAMPOS
// ================================
document.addEventListener('DOMContentLoaded', function () {
    // Formatear precio de contado una sola vez
    const precioContadoInput = document.getElementById('precioContado');
    if (precioContadoInput) {
        precioContadoInput.value = formatearEnteroConPuntos(precioContadoInput.value);
        precioContadoInput.readOnly = true;
    }

    // Inputs que deben mostrar puntos mientras el usuario escribe
    const inputsConPuntos = ['anticipo', 'cuotas'];

    inputsConPuntos.forEach(function (id) {
        const input = document.getElementById(id);
        if (!input) return;

        input.addEventListener('input', function () {
            const posAntes = input.selectionStart;
            const valorAntes = input.value;

            input.value = formatearEnteroConPuntos(input.value);

            // Intento mantener el cursor lo más cerca posible del final
            const diff = valorAntes.length - input.value.length;
            const nuevaPos = (posAntes - diff >= 0) ? posAntes - diff : input.value.length;
            input.selectionStart = input.selectionEnd = nuevaPos;
        });

        // límite razonable de dígitos
        input.maxLength = 12;
    });
});

// ================================
// MANEJO DE VEHÍCULO EN PARTE DE PAGO
// ================================
function manejarVehiculoEntrega() {
    const selectEntrega    = document.getElementById('vehiculoEntrega');
    const opcionVenta      = document.getElementById('opcionVenta');
    const camposSimulacion = document.getElementById('camposSimulacion');
    const resultadoDiv     = document.getElementById('resultadoSimulacion');

    if (!selectEntrega || !opcionVenta || !camposSimulacion || !resultadoDiv) return;

    if (selectEntrega.value === 'si') {
        // Mostrar info y ocultar simulación
        opcionVenta.style.display = 'flex';
        camposSimulacion.style.display = 'none';
        resultadoDiv.innerHTML = '';
    } else {
        // Ocultar info y habilitar simulación
        opcionVenta.style.display = 'none';
        camposSimulacion.style.display = '';
        resultadoDiv.style.display = '';
    }
}

// ================================
// SIMULADOR POR VEHÍCULO
// ================================
function calcularSimulacion() {
    const resultadoDiv = document.getElementById('resultadoSimulacion');
    if (!resultadoDiv) return;

    const precioContado = obtenerEnteroDesdeInput('precioContado');
    const anticipo      = obtenerEnteroDesdeInput('anticipo');
    const cuotas        = obtenerEnteroDesdeInput('cuotas');

    // Validaciones básicas
    if (!precioContado || precioContado <= 0) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">No se pudo obtener el precio del vehículo.</span>';
        return;
    }

    if (anticipo < 0) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">El anticipo no puede ser negativo.</span>';
        return;
    }

    if (anticipo >= precioContado) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">El anticipo no puede ser mayor o igual al precio de contado.</span>';
        return;
    }

    if (!cuotas || cuotas <= 0) {
        resultadoDiv.innerHTML = '<span class="text-danger-custom">Ingresá una cantidad de cuotas válida.</span>';
        return;
    }

    const montoFinanciar = precioContado - anticipo;

    // Tasa fija de ejemplo (podés ajustarla o hacerla configurable)
    const tasaAnual  = 60; // 60% anual aprox
    const tasaMensual = (tasaAnual / 12) / 100;

    let cuota = 0;

    if (tasaMensual > 0) {
        // Sistema francés
        cuota = montoFinanciar * (tasaMensual / (1 - Math.pow(1 + tasaMensual, -cuotas)));
    } else {
        cuota = montoFinanciar / cuotas;
    }

    const totalEstimado = cuota * cuotas;

    resultadoDiv.innerHTML = `
        <span>Monto a financiar: 
            <span class="monto-principal">${formatoPesosEntero(montoFinanciar)}</span>
        </span><br>
        <span>Cuota estimada en ${cuotas} pagos: 
            <span class="monto-cuota">${formatoPesosEntero(cuota)}</span>
        </span><br>
        <small>
            Cálculo estimativo con tasa anual aproximada del ${tasaAnual}%. 
            Los valores pueden variar según la entidad financiera y la evaluación crediticia.
        </small>
    `;
}
