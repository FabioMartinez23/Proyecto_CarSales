<?php
session_start();

// Traer los tipos de gasto
$gasto = new GastoGeneral();
$tipos_gasto = $gasto->traer_tipos_gasto();
?>

    <!-- Breadcrumb -->
<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
        <li class="breadcrumb-item"><a href="#">Gastos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registrar Gasto</li>
    </ol>
</nav>

<div class="gastos-container">

    <div class="gastos-box">

        <h4 class="gastos-title">
            <i class="fa-solid fa-wallet me-2"></i> Registrar Gasto
        </h4>

        <!-- ================================
             FORMULARIO DE GASTOS
        ================================== -->
        <form id="form-registrar-gasto">

            <!-- Descripción -->
            <div class="mb-3">
                <label class="form-label">Descripción del gasto</label>
                <input type="text" class="form-control" name="descripcion" required>
            </div>

            <!-- Monto -->
            <div class="mb-3">
                <label class="form-label">Monto</label>
                <input type="number" class="form-control" name="monto" step="0.01" required>
            </div>

            <!-- Tipo de gasto -->
            <div class="mb-3">
                <label class="form-label">Tipo de gasto</label>
                <select class="form-select" name="tipo_gasto" required>
                    <option value="">Seleccione...</option>
                    <?php while ($tg = $tipos_gasto->fetch_assoc()) { ?>
                        <option value="<?= $tg['idtipo_gasto'] ?>">
                            <?= $tg['descripcion'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <!-- Origen -->
            <div class="mb-3">
                <label class="form-label">Origen</label>
                <select class="form-select" name="origen" id="origen-select" required>
                    <option value="general">General (default)</option>
                    <option value="vehiculo">Asociado a Vehículo</option>
                    <option value="venta">Asociado a Venta</option>
                    <option value="empleado">Asociado a Empleado</option>
                </select>
            </div>

            <!-- ================================
                 CAMPOS DINÁMICOS SEGÚN ORIGEN
            ================================== -->

            <!-- Vehículo -->
            <div class="mb-3 d-none gastos-dynamic-box origen-field" id="origen-vehiculo">
                <label class="form-label">Buscar vehículo (por patente)</label>
                <div class="input-group">
                    <input type="text" id="buscar-patente" class="form-control" placeholder="ABC123">
                    <button type="button" class="btn btn-primary gastos-btn-buscar" onclick="buscarVehiculo()">Buscar</button>
                </div>

                <div class="gastos-resultado d-none" id="resultadoVehiculo"></div>

                <input type="hidden" name="vehiculos_idvehiculos" id="vehiculos_idvehiculos">
            </div>

            <!-- Venta -->
            <div class="mb-3 d-none gastos-dynamic-box origen-field" id="origen-venta">
                <label class="form-label">Número de Venta</label>
                <input type="number" class="form-control" id="ventas_idventas" name="ventas_idventas" placeholder="ID de venta">
            </div>

            <!-- Empleado -->
            <div class="mb-3 d-none gastos-dynamic-box origen-field" id="origen-empleado">
                <label class="form-label">Empleado</label>
                <input type="number" class="form-control" id="empleados_idempleados" name="empleados_idempleados" placeholder="ID empleado">
            </div>

            <!-- Tipo de pago -->
            <div class="mb-3">
                <label class="form-label">Tipo de pago</label>
                <select class="form-select" name="tipo_pago">
                    <option value="1">Efectivo</option>
                    <option value="2">Transferencia</option>
                    <option value="3">MercadoPago</option>
                </select>
            </div>

            <!-- Acción -->
            <input type="hidden" name="action" value="guardar">

            <!-- Botón -->
            <div class="text-end">
                <button type="submit" class="gastos-btn-save">
                    <i class="fa-solid fa-check me-2"></i> Registrar Gasto
                </button>
            </div>

        </form>

    </div>
</div>


<!-- ============================================================
     JAVASCRIPT — LÓGICA DEL FORMULARIO
=============================================================== -->
<script>
document.addEventListener("DOMContentLoaded", function() {

    /* ========================================
       MOSTRAR/OCULTAR CAMPOS SEGÚN ORIGEN
    ========================================= */
    document.getElementById('origen-select').addEventListener('change', function() {
        let valor = this.value;

        document.querySelectorAll('.origen-field').forEach(div => {
            div.classList.add('d-none');
        });

        if (valor === 'vehiculo') {
            document.getElementById('origen-vehiculo').classList.remove('d-none');
        }
        if (valor === 'venta') {
            document.getElementById('origen-venta').classList.remove('d-none');
        }
        if (valor === 'empleado') {
            document.getElementById('origen-empleado').classList.remove('d-none');
        }
    });

    /* ========================================
       BUSCAR VEHÍCULO — AJAX
    ========================================= */
    window.buscarVehiculo = function() {
        let patente = document.getElementById("buscar-patente").value.trim();

        if (patente === "") {
            Swal.fire("Atención", "Ingrese una patente para buscar.", "warning");
            return;
        }

        $.ajax({
            url: "controladores/vehiculos/vehiculos.controlador.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "buscar_por_patente",
                patente: patente
            },
            success: function(data) {

                let boxResultado = document.getElementById("resultadoVehiculo");
                boxResultado.classList.remove("d-none");

                if (data.status === "success") {

                    let veh = data.vehiculo;

                    boxResultado.innerHTML = `
                        <strong>Vehículo encontrado:</strong><br>
                        ${veh.nombre_marca} ${veh.nombre_modelo}<br>
                        Patente: <strong>${veh.patente}</strong>
                    `;

                    document.getElementById("vehiculos_idvehiculos").value = veh.idvehiculos;

                } else {
                    boxResultado.innerHTML = `<strong>${data.mensaje}</strong>`;
                    document.getElementById("vehiculos_idvehiculos").value = "";
                }
            }
        });
    }

    /* ========================================
       ENVIAR FORMULARIO — AJAX
    ========================================= */
    $("#form-registrar-gasto").on("submit", function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "controladores/gastos/gastos.controlador.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {

                console.log("Respuesta controlador:", data);

                if (data.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Gasto registrado",
                        text: data.mensaje
                    }).then(() => {
                        location.href = "index.php?page=listado_gastos";
                    });

                } else {
                    Swal.fire("Error", data.mensaje, "error");
                }
            },
            error: function(err) {
                console.error(err);
                Swal.fire("Error", "No se pudo conectar con el servidor", "error");
            }
        });
    });

});
</script>

