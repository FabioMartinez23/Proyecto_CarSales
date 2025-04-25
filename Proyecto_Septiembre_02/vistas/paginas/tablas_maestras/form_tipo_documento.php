<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/tipo_documento.php');

$tipo_documento = new Tipo_Documentos();

$result_tipo_documento = $tipo_documento->traer_tipo_documento();
if(isset($_GET['idTipo_documento'])){
    $tipo_documento_editar = $tipo_documento->traer_tipo_documento_id($_GET['idTipo_documento']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Tipo de Documento</h2>
        <form id="id_form_tipo_documento" method="POST" action="../controladores/tablas_maestras/tipo_documento.controlador.php">
        <?php if(isset($_GET['idTipo_documento'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idTipo_documento" value="<?= $_GET['idTipo_documento'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Tipo de Documento:</label>
                <input <?php if(isset($_GET['idTipo_documento'])){ foreach($tipo_documento_editar as $tipo_documento){ echo "value=".$tipo_documento['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_tipo_documento(event)" class="form-control" id="idTipo_documento" aria-describedby="emailHelp">
                <p id="id_tipo_documento_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Tipo de Documento</p>
            </div>
            <button onclick="validar_vacio_tipo_documento()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Tipo de Documento</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Tipo de Documento</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_tipo_documento as $tipo_documento){
                ?>
                <tr>
                <td><?= $tipo_documento['descripcion']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_tipo_documento&idTipo_documento=<?=$tipo_documento['idTipo_documento']; ?>&descripcion=<?=$tipo_documento['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/tipo_documento.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idTipo_documento" value="<?= $tipo_documento['idTipo_documento'] ?>">
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