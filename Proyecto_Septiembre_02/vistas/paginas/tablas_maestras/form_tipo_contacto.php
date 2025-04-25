<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/tipo_contacto.php');

$tipo_contacto = new Tipo_Contactos();

$result_tipo_contacto = $tipo_contacto->traer_tipo_contacto();
if(isset($_GET['idtipo_contacto'])){
    $tipo_contacto_editar = $tipo_contacto->traer_tipo_contacto_id($_GET['idtipo_contacto']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Tipo de Contacto</h2>
        <form id="id_form_tipo_contacto" method="POST" action="../controladores/tablas_maestras/tipo_contacto.controlador.php">
        <?php if(isset($_GET['idtipo_contacto'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idtipo_contacto" value="<?= $_GET['idtipo_contacto'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Tipo de Contacto:</label>
                <input <?php if(isset($_GET['idtipo_contacto'])){ foreach($tipo_contacto_editar as $tipo_contacto){ echo "value=".$tipo_sexo['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_tipo_contacto(event)" class="form-control" id="idtipo_contacto" aria-describedby="emailHelp">
                <p id="id_tipo_contacto_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Tipo de Contacto</p>
            </div>
            <button onclick="validar_vacio_tipo_contacto()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Tipo de Contacto</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Tipo de Contacto</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_tipo_contacto as $tipo_contacto){
                ?>
                <tr>
                <td><?= $tipo_contacto['descripcion']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_tipo_contacto&idtipo_contacto=<?=$tipo_contacto['idtipo_contacto']; ?>&descripcion=<?=$tipo_contacto['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/tipo_contacto.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idtipo_contacto" value="<?= $tipo_sexo['idtipo_contacto'] ?>">
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