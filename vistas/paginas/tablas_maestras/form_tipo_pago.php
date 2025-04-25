<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/tipo_pago.php');

$tipo_pago = new Tipo_Pagos();

$result_tipo_pago = $tipo_pago->traer_tipo_pago();
if(isset($_GET['idtipo_pago'])){
    $tipo_pago_editar = $tipo_pago->traer_tipo_pago_id($_GET['idtipo_pago']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Tipo de Pago</h2>
        <form id="id_form_tipo_pago" method="POST" action="../controladores/tablas_maestras/tipo_pago.controlador.php">
        <?php if(isset($_GET['idtipo_pago'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idtipo_pago" value="<?= $_GET['idtipo_pago'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Tipo de Pago:</label>
                <input <?php if(isset($_GET['idtipo_pago'])){ foreach($tipo_pago_editar as $tipo_pago){ echo "value=".$tipo_pago['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_tipo_pago(event)" class="form-control" id="idtipo_pago" aria-describedby="emailHelp">
                <p id="id_tipo_pago_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Tipo de Pago</p>
            </div>
            <button onclick="validar_vacio_tipo_pago()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Tipo de Pagos</h2>      
        <table class="table table-hover">
            <thead>
                <tr>
                <th>Tipo de Pago</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_tipo_pago as $tipo_pago){
                ?>
                <tr>
                <td><?= $tipo_pago['descripcion']; ?></td>
                <td>
                    <a href="form_datos_para_operaciones.php?page=form_tipo_pago&idtipo_pago=<?=$tipo_pago['idtipo_pago']; ?>&descripcion=<?=$tipo_pago['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form id="formulario-eliminar-<?= $tipo_pago['idtipo_pago']; ?>" method="POST" action="../controladores/tablas_maestras/tipo_pago.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idtipo_pago" value="<?= $tipo_pago['idtipo_pago'] ?>">
                        <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $tipo_pago['idtipo_pago']; ?>')" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
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