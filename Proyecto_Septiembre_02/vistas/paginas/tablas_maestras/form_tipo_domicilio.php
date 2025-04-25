<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/tipo_domicilio.php');

$tipo_domicilio = new Tipo_Domicilios();

$result_tipo_domicilio = $tipo_domicilio->traer_tipo_domicilio();
if(isset($_GET['idtipo_domicilio'])){
    $tipo_domicilio_editar = $tipo_domicilio->traer_tipo_domicilio_id($_GET['idtipo_domicilio']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Tipo de Domicilio</h2>
        <form id="id_form_tipo_domicilio" method="POST" action="../controladores/tablas_maestras/tipo_domicilio.controlador.php">
        <?php if(isset($_GET['idtipo_domicilio'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idtipo_domicilio" value="<?= $_GET['idtipo_domicilio'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Tipo de Domicilio:</label>
                <input <?php if(isset($_GET['idtipo_domicilio'])){ foreach($tipo_domicilio_editar as $tipo_domicilio){ echo "value=".$tipo_domicilio['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_tipo_domicilio(event)" class="form-control" id="idtipo_domicilio" aria-describedby="emailHelp">
                <p id="id_tipo_domicilio_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Tipo de Domicilio</p>
            </div>
            <button onclick="validar_vacio_tipo_domicilio()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Tipo de Domicilio</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Tipo de Domicilio</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_tipo_domicilio as $tipo_domicilio){
                ?>
                <tr>
                <td><?= $tipo_domicilio['descripcion']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_tipo_domicilio&idtipo_domicilio=<?=$tipo_domicilio['idtipo_domicilio']; ?>&descripcion=<?=$tipo_domicilio['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/tipo_domicilio.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idtipo_domicilio" value="<?= $tipo_domicilio['idtipo_domicilio'] ?>">
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