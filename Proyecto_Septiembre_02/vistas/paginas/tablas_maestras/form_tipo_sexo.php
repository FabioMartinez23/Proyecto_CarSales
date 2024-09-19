<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/tipo_sexo.php');

$tipo_sexo = new Tipo_Sexos();

$result_tipo_sexo = $tipo_sexo->traer_tipo_sexo();
if(isset($_GET['idtipo_sexo'])){
    $tipo_sexo_editar = $tipo_sexo->traer_tipo_sexo_id($_GET['idtipo_sexo']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Tipo de Sexo</h2>
        <form id="id_form_tipo_sexo" method="POST" action="../controladores/tablas_maestras/tipo_sexo.controlador.php">
        <?php if(isset($_GET['idtipo_sexo'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idtipo_sexo" value="<?= $_GET['idtipo_sexo'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Tipo de Sexo:</label>
                <input <?php if(isset($_GET['idtipo_sexo'])){ foreach($tipo_sexo_editar as $tipo_sexo){ echo "value=".$tipo_sexo['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_tipo_sexo(event)" class="form-control" id="idtipo_sexo" aria-describedby="emailHelp">
                <p id="id_tipo_sexo_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Tipo de Sexo</p>
            </div>
            <button onclick="validar_vacio_tipo_sexo()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Tipo de Sexo</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Tipo de Sexo</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_tipo_sexo as $tipo_sexo){
                ?>
                <tr>
                <td><?= $tipo_sexo['descripcion']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_tipo_sexo&idtipo_sexo=<?=$tipo_sexo['idtipo_sexo']; ?>&descripcion=<?=$tipo_sexo['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/tipo_sexo.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idtipo_sexo" value="<?= $tipo_sexo['idtipo_sexo'] ?>">
                        <button onclick="return eliminar()" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>

    </div>
</div>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>