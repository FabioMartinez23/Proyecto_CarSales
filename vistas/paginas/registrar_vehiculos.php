<?php

ini_set('display_errors', 1);

$total_registros = 0;

$vehiculo = new Vehiculos();
$todos_vehiculos = $vehiculo->traer_todos_vehiculos();

if(isset($_GET['idvehiculos'])){
    $vehiculo_editar = $vehiculo->traer_vehiculos_por_id($_GET['idvehiculos']);
}

//$vehiculos = new Vehiculos();
//$result_vehiculos_ = $vehiculos->traer_cantidad_vehiculo();
//foreach($result_vehiculos_ as $vehiculo_1){
//    $total_registros = $vehiculo_1['total'];
//}
//if (isset($_GET['pagina_actual'])){
//    $vehiculos->pagina_actual = $_GET['pagina_actual'];
//}
//$result_vehiculos = $vehiculos->traer_vehiculos();

$color = new Colores();
$result_color = $color->traer_color();

$marca = new Marcas();
$result_marca = $marca->traer_marca();

$tipo_vehiculo = new Tipo_Vehiculos();
$result_tipo_vehiculo = $tipo_vehiculo->traer_tipo_vehiculo();

?>


<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Vehículos</a></li>
        <li class="breadcrumb-item"><a href="#">Gestión de Vehículos</a></li>
        <?php 
        if(isset($_GET['accion']) && $_GET['accion'] === 'registrar'){
        ?>
        <li class="breadcrumb-item"><a href="index.php?page=listado_vehiculos">Vehículos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registrar Vehículo</li>
        <?php
        }else{
        ?>
        <li class="breadcrumb-item active" aria-current="page">Registrar Vehículo</li>
        <?php
        }
        ?>
    </ol>
</nav>

<div class="d-flex justify-content-center align-items-center hacer_padding">
        <div class="col-md-8 col-lg-6">
            <h1 class="text-center mb-4">Registrar Vehiculo</h1>
            <form method="POST" action="controladores/vehiculos/vehiculos.controlador.php">
                <?php if(isset($_GET['idvehiculos'])){ ?>
                    <input type="hidden" name="action" value="actualizar"/>
                    <input type="hidden" name="idvehiculos" value="<?= $_GET['idvehiculos'] ?>"/>
                <?php }else{?>
                    <input type="hidden" name="action" value="guardar"/>

                <?php }?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idvehiculos'])){ foreach($vehiculo_editar as $vehiculo){ echo "value=".$vehiculo['patente'];}} ?> type="text" name="patente" onfocusout="validate_patente(event)" class="form-control" id="id_patente" aria-describedby="emailHelp" placeholder="patente">
                            <label for="floatingInput">Patente</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idvehiculos'])){ foreach($vehiculo_editar as $vehiculo){ echo "value=".$vehiculo['chasis'];}} ?> type="text" name="chasis" class="form-control" id="id_chasis" aria-describedby="emailHelp" placeholder="chasis">
                            <label for="floatingInput">Chasis</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input <?php if(isset($_GET['idvehiculos'])){ foreach($vehiculo_editar as $vehiculo){ echo "value=".$vehiculo['motor'];}} ?> type="text" name="motor" class="form-control" id="id_motor" aria-describedby="emailHelp" placeholder="motor">
                            <label for="floatingInput">Motor</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input maxlength="4" <?php if(isset($_GET['idvehiculos'])){ foreach($vehiculo_editar as $vehiculo){ echo "value=".$vehiculo['anio'];}} ?> type="text" name="año" class="form-control" id="id_año" aria-describedby="emailHelp" min="1960" max="2024" placeholder="anio">
                            <label for="floatingInput">Año</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <input oninput="formatearKilometraje(this)" maxlength="10" <?php if(isset($_GET['idvehiculos'])){ foreach($vehiculo_editar as $vehiculo){ echo "value=".$vehiculo['kilometraje'];}} ?> type="text" name="kilometraje" class="form-control" id="id_kilometraje" aria-describedby="emailHelp" placeholder="kilometraje">
                            <label for="floatingInput">Kilometraje</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <select name="colores_idcolores" id="idcolores" class="form-select">
                                <option value="">Seleccione un Color</option>
                                <?php
                                foreach($result_color as $color){
                                    $selected = '';
                                    if (isset($_GET['idvehiculos']) && $vehiculo['colores_idcolores'] == $color['idcolores']) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $color['idcolores']?>" <?php echo $selected ?>><?php echo $color['descripcion']?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        <label for="floatingInput">Color</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <select name="idmarcas" id="idmarcas" onchange="validar_marca(this.value)" class="form-select">
                                <option value="">Seleccione una Marca</option>
                                <?php
                                foreach($result_marca as $marca){
                                    $selected = '';
                                    if (isset($_GET['idvehiculos']) && $vehiculo['idmarcas'] == $marca['idmarcas']) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $marca['idmarcas']?>" <?php echo $selected ?>><?php echo $marca['nombre']?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        <label for="floatingInput">Marca</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <select name="modelos_idmodelos" id="idmodelos" class="form-select">
                                <option value="">Seleccione un Modelo</option>
                                <!-- Aquí iría el código AJAX que cargará los modelos de acuerdo a la marca seleccionada -->
                                <?php
                                if (isset($_GET['idvehiculos'])) {
                                    // Aquí debes asegurarte de que el modelo correspondiente al vehículo editado se cargue
                                    echo "<option value='{$vehiculo['modelos_idmodelos']}' selected>{$vehiculo['modelo_nombre']}</option>";
                                }
                                ?>
                            </select>
                        <label for="floatingInput">Modelo</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3 mt-3">
                            <select name="tipo_vehiculos_idtipo_vehiculos" id="idtipo_vehiculos" class="form-select">
                                <option value="">Seleccione un Tipo</option>
                                <?php
                                foreach($result_tipo_vehiculo as $tipo_vehiculo){
                                    $selected = '';
                                    if (isset($_GET['idvehiculos']) && $vehiculo['tipo_vehiculos_idtipo_vehiculos'] == $tipo_vehiculo['idtipo_vehiculos']) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $tipo_vehiculo['idtipo_vehiculos']?>" <?php echo $selected ?>><?php echo $tipo_vehiculo['nombre']?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        <label for="floatingInput">Tipo</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4">
                <a type="button" class="btn btn-dark me-4" href="index.php?page=listado_vehiculos">Volver</a>
                    <button type="submit" class="btn btn-action">
                        Guardar
                    </button>
                </div>

            </form>
        </div>
</div>

<script>
        function formatearKilometraje(input) {
            // Remueve cualquier carácter que no sea número
            let valor = input.value.replace(/\D/g, '');
            
            // Formatea el número con separadores de miles
            valor = new Intl.NumberFormat('es-ES').format(valor);
            
            // Agrega " km" al final
            input.value = valor + ' km';
        }
</script>

<script src="assets/js/imask.js"></script>
<script src="assets/js/validaciones/patente.js"></script>
<script src="assets/js/validaciones/validar_marca.ajax.js"></script>




