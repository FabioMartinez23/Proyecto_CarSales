<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/modulo.php');

$modulo = new Modulos();

$result_modulo = $modulo->traer_modulo();
if(isset($_GET['idmodulos'])){
    $modulo_editar = $modulo->traer_modulos_id($_GET['idmodulos']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Modulo</h2>
        <form id="id_modulo_sexo" method="POST" action="../controladores/tablas_maestras/tipo_sexo.controlador.php">
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
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Modulo</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_modulo as $modulo){
                ?>
                <tr>
                <td><?= $modulo['descripcion']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_modulo&idmodulos=<?=$modulo['idmodulos']; ?>&descripcion=<?=$modulo['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/modulo.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idmodulos" value="<?= $modulo['idmodulos'] ?>">
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