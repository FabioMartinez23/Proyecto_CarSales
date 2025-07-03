<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/tipo_anulacion.php');

$tipo_anulacion = new Tipo_Anulaciones();

$result_tipo_anulacion = $tipo_anulacion->traer_tipo_anulacion();
if(isset($_GET['idtipo_anulacion'])){
    $tipo_anulacion_editar = $tipo_anulacion->traer_tipo_anulacion_id($_GET['idtipo_anulacion']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Tipo de Anulación</h2>
        <form id="id_form_tipo_anulacion" method="POST" action="../controladores/tablas_maestras/tipo_anulacion.controlador.php">
        <?php if(isset($_GET['idtipo_anulacion'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idtipo_anulacion" value="<?= $_GET['idtipo_anulacion'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Tipo de anulación:</label>
                <input <?php if(isset($_GET['idtipo_anulacion'])){ foreach($tipo_anulacion_editar as $tipo_anulacion){ echo "value=".$tipo_anulacion['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_tipo_anulacion(event)" class="form-control" id="idtipo_anulacion" aria-describedby="emailHelp">
                <p id="id_tipo_anulacion_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Tipo de Anulación</p>
            </div>
            <button onclick="validar_vacio_tipo_anulacion()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Tipo de Anulaciones</h2>      
        <table class="table table-hover">
            <thead>
                <tr>
                <th>Tipo de Anulación</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_tipo_anulacion as $tipo_anulacion){
                ?>
                <tr>
                <td><?= $tipo_anulacion['descripcion']; ?></td>
                <td>
                    <a href="form_datos_para_operaciones.php?page=form_tipo_anulacion&idtipo_anulacion=<?=$tipo_anulacion['idtipo_anulacion']; ?>&descripcion=<?=$tipo_anulacion['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form id="formulario-eliminar-<?= $tipo_anulacion['idtipo_anulacion']; ?>" method="POST" action="../controladores/tablas_maestras/tipo_anulacion.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idtipo_anulacion" value="<?= $tipo_anulacion['idtipo_anulacion'] ?>">
                        <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $tipo_anulacion['idtipo_anulacion']; ?>')" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
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