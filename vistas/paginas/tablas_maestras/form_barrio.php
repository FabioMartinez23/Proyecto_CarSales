<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../modelos/tablas_maestras/localidad.php');
require_once('../modelos/tablas_maestras/provincia.php');
require_once('../modelos/tablas_maestras/barrio.php');

$localidad = new Localidades();
$result_localidad = $localidad->traer_localidad();


$barrio = new Barrios();

$result_barrio = $barrio->traer_barrio();

if(isset($_GET['idbarrios'])){
    $barrio_editar = $barrio->traer_barrio_id($_GET['idbarrios']);
}

$barrios = new Barrios();
$result_barrios_ = $barrios->traer_cantidad_barrio();
foreach($result_barrios_ as $barrios_1){
    $total_registros = $barrios_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $barrios->pagina_actual = $_GET['pagina_actual'];
}
$result_barrios = $barrios->traer_barrios_paginacion();

$provincia = new Provincias();
$result_provincia = $provincia->traer_provincia();

?>

<div class="row">
    <div class="col">
        <h2>Registrar Barrios</h2>
        <form id="id_form_barrio" method="POST" action="../controladores/tablas_maestras/barrio.controlador.php">
        <?php if(isset($_GET['idbarrios'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idbarrios" value="<?= $_GET['idbarrios'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>

            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Barrio:</label>
                <input <?php if(isset($_GET['idbarrios'])){ foreach($barrio_editar as $barrio){ echo "value='".$barrio['descripcion']."'";}} ?> type="text" name="descripcion" onfocusout="validate_barrio(event)" class="form-control" id="idbarrios" aria-describedby="emailHelp">
                <p id="id_barrio_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Barrio</p>
            </div>

            <div class="mb-3">
                    <label for="disabledSelect" class="form-label">Provincias</label>
                    <select name="idprovincias" id="idprovincias" onchange="validar_provincia(this.value)" class="form-select">
                        <option value="">Seleccione un Provincia</option>
                    <?php
                        foreach($result_provincia as $provincia){
                    ?>
                        <option value="<?php echo $provincia['idprovincias']?>"><?php echo $provincia['descripcion']?></option>
                    <?php
                        }
                    ?>
                    </select>
            </div>

            <div class="mb-3">
                    <label for="disabledSelect" class="form-label">Localidades</label>
                    <select name="localidades_idlocalidades" id="idlocalidades" class="form-select">
                        <option value="">Seleccione una Localidad</option>
                    <!-- Aqui iría la respuesta de ajax -->
                    </select>
            </div>

            <button onclick="validar_vacio_barrio()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Barrios</h2>
        <table class="table table-hover">
            <thead>
                <tr>
                <th>Barrio</th>
                <th>Localidad</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_barrios as $barrios_){
                ?>
                <tr>
                <td><?= $barrios_['nombre_barrio']; ?></td>
                <td><?= $barrios_['nombre_localidad']; ?></td>
                <td>
                    <a href="form_datos_para_domicilios.php?page=form_barrio&idbarrios=<?=$barrios_['idbarrios']; ?>&descripcion=<?=$barrios_['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form id="formulario-eliminar-<?= $barrios_['idbarrios']; ?>" method="POST" action="../controladores/tablas_maestras/barrio.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idbarrios" value="<?= $barrios_['idbarrios'] ?>">
                        <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $barrios_['idbarrios']; ?>')" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>


        <nav aria-label="...">
            <ul class="pagination justify-content-center">
            <li class="page-item <?php if($barrios->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="form_datos_para_domicilios.php?page=form_barrio&pagina_actual=<?= $barrios->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="form_datos_para_domicilios.php?page=form_barrio&pagina_actual=<?= $barrios->pagina_actual - 1 ?>"><?= $barrios->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="form_datos_para_domicilios.php?page=form_barrio&pagina_actual=<?= $barrios->pagina_actual ?>"><?= $barrios->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="form_datos_para_domicilios.php?page=form_barrio&pagina_actual=<?= $barrios->pagina_actual + 1 ?>"><?= $barrios->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($barrios->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="form_datos_para_domicilios.php?page=form_barrio&pagina_actual=<?= $barrios->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>
<script src="../assets/js/validaciones/validar_provincia.ajax.js"></script>