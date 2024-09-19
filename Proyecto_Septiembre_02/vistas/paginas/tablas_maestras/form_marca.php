<?php

ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
require_once('../modelos/tablas_maestras/marca.php');

$marca = new Marcas();

$result_marca = $marca->traer_marca();

if(isset($_GET['idmarcas'])){
    $marca_editar = $marca->traer_marca_id($_GET['idmarcas']);
}

$marcas = new Marcas();
$result_marca_ = $marcas->traer_cantidad_marca();
foreach($result_marca_ as $marca_1){
    $total_registros = $marca_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $marcas->pagina_actual = $_GET['pagina_actual'];
}
$result_marcas = $marcas->traer_marcas_paginacion();


?>

<div class="row">
    <div class="col">
        <h2>Registrar Marca</h2>
        <form id="id_form_marca" method="POST" action="../controladores/tablas_maestras/marca.controlador.php">
        <?php if(isset($_GET['idmarcas'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idmarcas" value="<?= $_GET['idmarcas'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Marca:</label>
                <input <?php if(isset($_GET['idmarcas'])){ foreach($marca_editar as $marca){ echo "value=".$marca['nombre'];}} ?> type="text" name="nombre" onfocusout="validate_marca(event)" class="form-control" id="idmarcas" aria-describedby="emailHelp">
                <p id="id_marca_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Marca</p>
            </div>
            <button onclick="validar_vacio_marca()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Marcas</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Marca</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_marcas as $marca_){
                ?>
                <tr>
                <td><?= $marca_['nombre']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_marca&idmarcas=<?=$marca_['idmarcas']; ?>&nombre=<?=$marca_['nombre'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/marca.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idmarcas" value="<?= $marca_['idmarcas'] ?>">
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
            <li class="page-item <?php if($marcas->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="form_tablas_maestras.php?page=form_marca&pagina_actual=<?= $marcas->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_marca&pagina_actual=<?= $marcas->pagina_actual - 1 ?>"><?= $marcas->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_marca&pagina_actual=<?= $marcas->pagina_actual ?>"><?= $marcas->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_marca&pagina_actual=<?= $marcas->pagina_actual + 1 ?>"><?= $marcas->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($marcas->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="form_tablas_maestras.php?page=form_marca&pagina_actual=<?= $marcas->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>