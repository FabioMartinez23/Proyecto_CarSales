<?php

ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
require_once('../modelos/tablas_maestras/pais.php');

$pais = new Paises();

$result_pais = $pais->traer_pais();

if(isset($_GET['idpaises'])){
    $pais_editar = $pais->traer_pais_id($_GET['idpaises']);
}

$paises = new Paises();
$result_pais_ = $paises->traer_cantidad_pais();
foreach($result_pais_ as $pais_1){
    $total_registros = $pais_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $paises->pagina_actual = $_GET['pagina_actual'];
}
$result_paises = $paises->traer_paises_paginacion();


?>

<div class="row">
    <div class="col">
        <h2>Registrar Pais</h2>
        <form id="id_form_pais" method="POST" action="../controladores/tablas_maestras/pais.controlador.php">
        <?php if(isset($_GET['idpaises'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idpaises" value="<?= $_GET['idpaises'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Pais:</label>
                <input <?php if(isset($_GET['idpaises'])){ foreach($pais_editar as $pais){ echo "value=".$pais['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_pais(event)" class="form-control" id="idpaises" aria-describedby="emailHelp">
                <p id="id_pais_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Pais</p>
            </div>
            <button onclick="validar_vacio_pais()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Paises</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Pais</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_paises as $pais_){
                ?>
                <tr>
                <td><?= $pais_['descripcion']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_pais&idpaises=<?=$pais_['idpaises']; ?>&nombre=<?=$pais_['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/pais.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idpaises" value="<?= $pais_['idpaises'] ?>">
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
            <li class="page-item <?php if($paises->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="form_tablas_maestras.php?page=form_pais&pagina_actual=<?= $paises->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_pais&pagina_actual=<?= $paises->pagina_actual - 1 ?>"><?= $paises->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_pais&pagina_actual=<?= $paises->pagina_actual ?>"><?= $paises->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_pais&pagina_actual=<?= $paises->pagina_actual + 1 ?>"><?= $paises->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($paises->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="form_tablas_maestras.php?page=form_pais&pagina_actual=<?= $paises->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>