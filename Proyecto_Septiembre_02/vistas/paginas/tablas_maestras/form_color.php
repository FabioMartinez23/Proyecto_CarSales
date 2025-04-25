<?php

ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
require_once('../modelos/tablas_maestras/color.php');

$color = new Colores();

$result_color = $color->traer_color();

if(isset($_GET['idmarcas'])){
    $color_editar = $color->traer_color_id($_GET['idcolores']);
}

$colores = new Colores();
$result_color_ = $colores->traer_cantidad_color();
foreach($result_color_ as $color_1){
    $total_registros = $color_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $colores->pagina_actual = $_GET['pagina_actual'];
}
$result_colores = $colores->traer_colores_paginacion();


?>

<div class="row">
    <div class="col">
        <h2>Registrar Color</h2>
        <form id="id_form_color" method="POST" action="../controladores/tablas_maestras/color.controlador.php">
        <?php if(isset($_GET['idcolores'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idcolores" value="<?= $_GET['idcolores'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Color:</label>
                <input <?php if(isset($_GET['idcolores'])){ foreach($color_editar as $color){ echo "value=".$color['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_color(event)" class="form-control" id="idcolores" aria-describedby="emailHelp">
                <p id="id_color_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Color</p>
            </div>
            <button onclick="validar_vacio_color()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Colores</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Color</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_colores as $color_){
                ?>
                <tr>
                <td><?= $color_['descripcion']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_color&idcolores=<?=$color_['idcolores']; ?>&nombre=<?=$color_['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/marca.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idcolores" value="<?= $marca_['idcolores'] ?>">
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
            <li class="page-item <?php if($colores->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="form_tablas_maestras.php?page=form_color&pagina_actual=<?= $colores->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_color&pagina_actual=<?= $colores->pagina_actual - 1 ?>"><?= $colores->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_color&pagina_actual=<?= $colores->pagina_actual ?>"><?= $colores->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_color&pagina_actual=<?= $colores->pagina_actual + 1 ?>"><?= $colores->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($colores->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="form_tablas_maestras.php?page=form_color&pagina_actual=<?= $colores->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>