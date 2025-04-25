// Función para formatear un número con puntos cada tres dígitos
function formatearNumero(valor) {
    // Remover todos los caracteres que no sean números
    valor = valor.replace(/[^0-9]/g, '');

    // Convertir a un número entero para evitar problemas de formato
    const numero = parseInt(valor, 10);

    // Si el número es NaN, retornar una cadena vacía
    if (isNaN(numero)) {
        return '';
    }

    // Convertir el número a una cadena con puntos cada tres dígitos
    return numero.toLocaleString('de-DE');
}

// Evento de escucha para formatear los números ingresados
function agregarEventosFormato() {
    // Agregar eventos de escucha a los campos de entrada
    document.getElementById('precioContado').addEventListener('keyup', function () {
        this.value = formatearNumero(this.value);
        precioContadoInput.maxleng = '12';
    });

    document.getElementById('anticipo').addEventListener('keyup', function () {
        this.value = formatearNumero(this.value);
    });

    document.getElementById('cuotas').addEventListener('keyup', function () {
        this.value = formatearNumero(this.value);
    });
}

// Llamar a la función para agregar eventos de formato de números
agregarEventosFormato();

function manejarVehiculoEntrega() {
    const vehiculoEntrega = document.getElementById("vehiculoEntrega").value;
    const opcionVenta = document.getElementById("opcionVenta");
    const simulacionContainer = document.querySelector(".simulacion-container");
    const anticipoInput = document.getElementById("anticipo");
    anticipoInput.maxleng = '12';
    const cuotasInput = document.getElementById("cuotas");
    cuotasInput.maxleng = '12';
    const resultadoSimulacion = document.getElementById("resultadoSimulacion");
    
    if (vehiculoEntrega === "si") {
        // Mostrar mensaje de advertencia y ocultar campos de entrada
        opcionVenta.style.display = "block";
        anticipoInput.style.display = "none";
        cuotasInput.style.display = "none";
        resultadoSimulacion.style.display = "none"; // Oculta el resultado de la simulación
    } else {
        // Ocultar mensaje de advertencia y mostrar campos de entrada
        opcionVenta.style.display = "none";
        anticipoInput.style.display = "block";
        cuotasInput.style.display = "block";
        resultadoSimulacion.style.display = "block"; // Muestra el resultado de la simulación
    }
}

function calcularSimulacion() {
    // Convierte los valores ingresados a números reemplazando los puntos por nada (para evitar problemas con decimales)
    const precioContado = parseFloat(document.getElementById("precioContado").value.replace(/\./g, ''));
    const anticipo = parseFloat(document.getElementById("anticipo").value.replace(/\./g, ''));
    const cuotas = parseInt(document.getElementById("cuotas").value, 10);

    // Verifica si los campos están vacíos o contienen valores no válidos
    if (isNaN(precioContado) || isNaN(anticipo) || isNaN(cuotas)) {
        document.getElementById("resultadoSimulacion").innerHTML = '<p style="color: red;">Por favor, ingrese valores válidos en todos los campos.</p>';
        return; // Salir de la función si hay un error
    }

    // Ajusta la tasa de interés según tus necesidades
    const tasaInteres = 0.05; // Por ejemplo, 5%

    // Calcula el saldo, el interés, el total y el pago mensual
    const saldo = precioContado - anticipo;
    const interes = saldo * tasaInteres;
    const total = saldo + interes;
    const pagoMensual = total / cuotas;

    // Muestra el resultado de la simulación en el div resultadoSimulacion
    document.getElementById("resultadoSimulacion").innerHTML = `
        <p>Saldo: $${saldo.toFixed(2)}</p>
        <p>Interés: $${interes.toFixed(2)}</p>
        <p>Total a pagar: $${total.toFixed(2)}</p>
        <p>Cuota mensual: $${pagoMensual.toFixed(2)}</p>
    `;
}

