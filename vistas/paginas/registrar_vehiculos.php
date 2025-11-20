<?php
ini_set('display_errors', 1);

$vehiculoModel = new Vehiculos();
$colorModel = new Colores();
$marcaModel = new Marcas();
$tipoModel = new Tipo_Vehiculos();

// 🔹 Si viene ID → estamos editando
$vehiculo_editar = null;
if (isset($_GET['idvehiculos'])) {
    $vehiculo_editar = $vehiculoModel->traer_vehiculo_por_id_modificar($_GET['idvehiculos']);
}

// Listas para selects
$result_color = $colorModel->traer_color();
$result_marca = $marcaModel->traer_marca();
$result_tipo = $tipoModel->traer_tipo_vehiculo();

?>

<nav style="--bs-breadcrumb-divider: '';" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        
        <li class="breadcrumb-item"><a href="#">Vehículos</a></li>
        <li class="breadcrumb-item"><a href="#">Gestión de Vehículos</a></li>

        <?php if ($vehiculo_editar): ?>
            <!-- Estamos modificando -->
            <li class="breadcrumb-item"><a href="index.php?page=listado_vehiculos">Listado de Vehículos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modificar Vehículo</li>

        <?php else: ?>
            <!-- Estamos registrando -->
            <li class="breadcrumb-item"><a href="index.php?page=listado_vehiculos">Listado de Vehículos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Registrar Vehículo</li>
        <?php endif; ?>

    </ol>
</nav>


<div class="d-flex justify-content-center align-items-center hacer_padding">
    <div class="col-md-8 col-lg-6">
        <h1 class="text-center mb-4">
            <?= $vehiculo_editar ? "Modificar Vehículo" : "Registrar Vehículo"; ?>
        </h1>

        <form method="POST" action="controladores/vehiculos/vehiculos.controlador.php">

            <!-- Hidden inputs -->
            <?php if ($vehiculo_editar): ?>
                <input type="hidden" name="action" value="actualizar">
                <input type="hidden" name="idvehiculos" value="<?= $vehiculo_editar['idvehiculos'] ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="guardar">
            <?php endif; ?>

            <!-- ===================== -->
            <!-- DATOS PRINCIPALES -->
            <!-- ===================== -->
            <div class="row">
                <!-- Patente -->
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input 
                            type="text" 
                            name="patente"
                            id="id_patente"
                            class="form-control"
                            placeholder="patente"
                            onfocusout="validate_patente(event)"
                            value="<?= $vehiculo_editar['patente'] ?? '' ?>">
                        <label for="id_patente">Patente</label>
                    </div>
                </div>

                <!-- Chasis -->
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input 
                            type="text" 
                            name="chasis"
                            id="id_chasis"
                            class="form-control"
                            placeholder="chasis"
                            value="<?= $vehiculo_editar['chasis'] ?? '' ?>">
                        <label for="id_chasis">Chasis</label>
                    </div>
                </div>

                <!-- Motor -->
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input 
                            type="text" 
                            name="motor"
                            id="id_motor"
                            class="form-control"
                            placeholder="motor"
                            value="<?= $vehiculo_editar['motor'] ?? '' ?>">
                        <label for="id_motor">Motor</label>
                    </div>
                </div>

                <!-- Año -->
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input 
                            type="number"
                            min="1960"
                            max="<?= date('Y') ?>"
                            name="año"
                            id="id_año"
                            class="form-control"
                            placeholder="año"
                            value="<?= $vehiculo_editar['anio'] ?? '' ?>">
                        <label for="id_año">Año</label>
                    </div>
                </div>

                <!-- Kilometraje -->
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <input 
                            type="text"
                            name="kilometraje"
                            id="id_kilometraje"
                            class="form-control"
                            maxlength="12"
                            placeholder="kilometraje"
                            oninput="formatearKilometraje(this)"
                            value="<?= $vehiculo_editar['kilometraje'] ?? '' ?>">
                        <label for="id_kilometraje">Kilometraje</label>
                    </div>
                </div>

                <!-- Color -->
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select 
                            name="colores_idcolores" 
                            id="idcolores"
                            class="form-select">

                            <option value="">Seleccione un Color</option>
                            <?php foreach ($result_color as $c): 
                                $selected = ($vehiculo_editar['colores_idcolores'] ?? null) == $c['idcolores'] ? 'selected' : '';
                            ?>
                                <option value="<?= $c['idcolores'] ?>" <?= $selected ?>><?= $c['descripcion'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="idcolores">Color</label>
                    </div>
                </div>
            </div>

            <!-- ===================== -->
            <!-- MARCA / MODELO / TIPO -->
            <!-- ===================== -->
            <div class="row">

                <!-- Marca -->
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select name="idmarcas"
                                id="idmarcas"
                                class="form-select"
                                onchange="validar_marca(this.value)">


                            <option value="">Seleccione una Marca</option>

                            <?php foreach ($result_marca as $m):
                                $selected = ($vehiculo_editar['idmarcas'] ?? null) == $m['idmarcas'] ? 'selected' : '';
                            ?>
                                <option value="<?= $m['idmarcas'] ?>" <?= $selected ?>><?= $m['nombre'] ?></option>
                            <?php endforeach; ?>

                        </select>
                        <label for="idmarcas">Marca</label>
                    </div>
                </div>

                <!-- Modelo -->
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select 
                            name="modelos_idmodelos" 
                            id="idmodelos"
                            class="form-select"
                            data-modelo-actual="<?= $vehiculo_editar['modelos_idmodelos'] ?? '' ?>"
                        >
                            <?php if ($vehiculo_editar): ?>
                                <option value="<?= $vehiculo_editar['modelos_idmodelos'] ?>" selected>
                                    <?= $vehiculo_editar['nombre_modelo'] ?>
                                </option>
                            <?php else: ?>
                                <option value="">Seleccione un Modelo</option>
                            <?php endif; ?>
                        </select>

                        <label for="idmodelos">Modelo</label>
                    </div>
                </div>

                <!-- Tipo de Vehículo -->
                <div class="col-md-6">
                    <div class="form-floating mb-3 mt-3">
                        <select 
                            name="tipo_vehiculos_idtipo_vehiculos" 
                            id="idtipo_vehiculos"
                            class="form-select">

                            <option value="">Seleccione un Tipo</option>

                            <?php foreach ($result_tipo as $t):
                                $selected = ($vehiculo_editar['tipo_vehiculos_idtipo_vehiculos'] ?? null) == $t['idtipo_vehiculos'] ? 'selected' : '';
                            ?>
                                <option value="<?= $t['idtipo_vehiculos'] ?>" <?= $selected ?>><?= $t['nombre'] ?></option>
                            <?php endforeach; ?>

                        </select>
                        <label for="idtipo_vehiculos">Tipo</label>
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="d-flex justify-content-center mt-4">
                <a href="index.php?page=listado_vehiculos" class="btn btn-dark me-4">Volver</a>
                <button type="submit" class="btn btn-action">Guardar</button>
            </div>

        </form>
    </div>
</div>


<!-- ============
     SCRIPTS
=========== -->
<script>
function formatearKilometraje(input) {
    let valor = input.value.replace(/\D/g, '');
    valor = new Intl.NumberFormat('es-AR').format(valor);
    input.value = valor;
}
</script>

<script src="assets/js/imask.js"></script>
<script src="assets/js/validaciones/patente.js"></script>
<script src="assets/js/validaciones/validar_marca.ajax.js"></script>
