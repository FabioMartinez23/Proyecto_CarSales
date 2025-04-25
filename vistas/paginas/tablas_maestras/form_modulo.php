<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/modulo.php');

$modulo = new Modulos();

$result_modulo = $modulo->traer_modulo();
if(isset($_GET['idmodulos'])){
    $modulo_editar = $modulo->traer_modulos_id($_GET['idmodulos']);
}

$modulos = new Modulos();
$result_modulo_ = $modulos->traer_cantidad_modulo();
foreach($result_modulo_ as $modulo_1){
    $total_registros = $modulo_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $modulos->pagina_actual = $_GET['pagina_actual'];
}
$result_modulos = $modulos->traer_modulos_paginacion();

?>

<div class="row">
    <div class="col">
        <h2>Registrar Modulo</h2>
        <form id="id_form_modulo" method="POST" action="../controladores/tablas_maestras/modulo.controlador.php">
        <?php if(isset($_GET['idmodulos'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idmodulos" value="<?= $_GET['idmodulos'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Modulo:</label>
                <input <?php if(isset($_GET['idmodulos'])){ foreach($modulo_editar as $modulo){ echo "value=".$modulo['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_modulo(event)" class="form-control" id="idmodulos" aria-describedby="emailHelp">
                <p id="id_modulo_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar un Modulo</p>
            </div>
            <button onclick="validar_vacio_modulo()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Modulos</h2>      
        <table class="table table-hover">
            <thead>
                <tr>
                <th>Modulo</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_modulo as $modulo_){
                ?>
                <tr>
                <td><?= $modulo_['descripcion']; ?></td>
                <td>
                    <a href="form_datos_para_perfiles.php?page=form_modulo&idmodulos=<?=$modulo_['idmodulos']; ?>&descripcion=<?=$modulo_['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form id="formulario-eliminar-<?= $modulo_['idmodulos']; ?>" method="POST" action="../controladores/tablas_maestras/modulo.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idmodulos" value="<?= $modulo_['idmodulos'] ?>">
                        <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $modulo_['idmodulos']; ?>')" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
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
            <li class="page-item <?php if($modulos->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="form_datos_para_perfiles.php?page=form_modulo&pagina_actual=<?= $modulos->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="form_datos_para_perfiles.php?page=form_modulo&pagina_actual=<?= $modulos->pagina_actual - 1 ?>"><?= $modulos->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="form_datos_para_perfiles.php?page=form_modulo&pagina_actual=<?= $modulos->pagina_actual ?>"><?= $modulos->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="form_datos_para_perfiles.php?page=form_modulo&pagina_actual=<?= $modulos->pagina_actual + 1 ?>"><?= $modulos->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($modulos->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="form_datos_para_perfiles.php?page=form_modulo&pagina_actual=<?= $modulos->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>

    </div>
</div>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>