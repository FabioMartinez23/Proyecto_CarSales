<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../modelos/tablas_maestras/modelo_vehiculo.php');
require_once('../modelos/tablas_maestras/marca.php');
require_once('../modelos/tablas_maestras/tipo_vehiculo.php');

$marca = new Marcas();
$result_marca = $marca->traer_marca();
if(isset($_GET['idmarcas'])){
    $marca_editar = $marca->traer_marca_id($_GET['idmarcas']);
}

$tipo_vehiculo = new Tipo_Vehiculos();
$result_tipo_vehiculo = $tipo_vehiculo->traer_tipo_vehiculo();
if(isset($_GET['idtipo_vehiculos'])){
    $tipo_vehiculo_editar = $tipo_vehiculo->traer_tipo_vehiculo_id($_GET['idtipo_vehiculos']);
}

$modelo_vehiculo = new Modelos_Vehiculos();

$result_modelo_vehiculo = $modelo_vehiculo->traer_modelo();

if(isset($_GET['idmodelos'])){
    $modelo_editar = $modelo_vehiculo->traer_modelo_id($_GET['idmodelos']);
}

$modelos_vehiculos = new Modelos_Vehiculos();
$result_modelos_vehiculos_ = $modelos_vehiculos->traer_cantidad_modelo();
foreach($result_modelos_vehiculos_ as $modelos_vehiculos_1){
    $total_registros = $modelos_vehiculos_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $modelos_vehiculos->pagina_actual = $_GET['pagina_actual'];
}
$result_modelos_vehiculos = $modelos_vehiculos->traer_modelos_paginacion();


?>

<div class="row">
    <div class="col">
        <h2>Registrar Modelo de Vehiculo</h2>
        <form id="id_form_modelo" method="POST" action="../controladores/tablas_maestras/modelo_vehiculo.controlador.php">
        <?php if(isset($_GET['idmodelos'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idmodelos" value="<?= $_GET['idmodelos'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Modelo:</label>
                <input <?php if(isset($_GET['idmodelos'])){ foreach($modelo_editar as $modelo_vehiculo){ echo "value=".$modelo_vehiculo['nombre'];}} ?> type="text" name="nombre" onfocusout="validate_modelo_vehiculo(event)" class="form-control" id="idmodelos" aria-describedby="emailHelp">
                <p id="id_modelo_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Modelo</p>
            </div>

            <div class="mb-3">
                <label for="disabledSelect" class="form-label">Tipo de Vehiculo</label>
                <select name="tipo_vehiculos_idtipo_vehiculos" id="id_tipo_vehiculos" class="form-select">
                    <option value="">Seleccione un Tipo de Vehiculo</option>
                <?php
                    foreach($result_tipo_vehiculo as $tipo_vehiculo){
                ?>
                    <option value="<?php echo $tipo_vehiculo['idtipo_vehiculos']?>"><?php echo $tipo_vehiculo['nombre']?></option>
                <?php
                    }
                ?>
                </select>

            </div>

            <div class="mb-3">
                <label for="disabledSelect" class="form-label">Marca</label>
                <select name="marcas_idmarcas" id="id_marcas" class="form-select">
                    <option value="">Seleccione una Marca</option>
                <?php
                    foreach($result_marca as $marca){
                ?>
                    <option value="<?php echo $marca['idmarcas']?>"><?php echo $marca['nombre']?></option>
                <?php
                    }
                ?>
                </select>

            </div>

            <button onclick="validar_vacio_modelo()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Modelos</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Modelos</th>
                <th>Tipo de Vehiculo</th>
                <th>Marca</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_modelos_vehiculos as $modelos_){
                ?>
                <tr>
                <td><?= $modelos_['nombre_modelo']; ?></td>
                <td><?= $modelos_['nombre_tipo_vehiculo']; ?></td>
                <td><?= $modelos_['nombre_marca']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_modelo_vehiculo&idmodelos=<?=$modelos_['idmodelos']; ?>&nombre=<?=$modelos_['nombre'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/modelo_vehiculo.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idmodelos" value="<?= $modelos_['idmodelos'] ?>">
                        <button onclick="return eliminar()" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>


        <nav aria-label="...">
            <ul class="pagination">
            <li class="page-item <?php if($modelos_vehiculos->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="form_tablas_maestras.php?page=form_modelo_vehiculo&pagina_actual=<?= $modelos_vehiculos->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_modelo_vehiculo&pagina_actual=<?= $modelos_vehiculos->pagina_actual - 1 ?>"><?= $modelos_vehiculos->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_modelo_vehiculo&pagina_actual=<?= $modelos_vehiculos->pagina_actual ?>"><?= $modelos_vehiculos->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_modelo_vehiculo&pagina_actual=<?= $modelos_vehiculos->pagina_actual + 1 ?>"><?= $modelos_vehiculos->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($modelos_vehiculos->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="form_tablas_maestras.php?page=form_modelo_vehiculo&pagina_actual=<?= $modelos_vehiculos->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>