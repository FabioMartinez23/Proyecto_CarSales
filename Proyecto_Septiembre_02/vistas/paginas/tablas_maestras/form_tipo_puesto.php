<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/tipo_de_puesto.php');

$tipo_puesto = new Tipo_De_Puestos();

$result_tipo_puesto = $tipo_puesto->traer_tipo_puesto();
if(isset($_GET['idtipo_sexo'])){
    $tipo_puesto_editar = $tipo_puesto->traer_tipo_puesto_id($_GET['idtipo_de_puestos']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Tipo de Puesto</h2>
        <form id="id_form_tipo_puesto" method="POST" action="../controladores/tablas_maestras/tipo_puesto.controlador.php">
        <?php if(isset($_GET['idtipo_de_puestos'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idtipo_de_puestos" value="<?= $_GET['idtipo_de_puestos'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Tipo de Puesto:</label>
                <input <?php if(isset($_GET['idtipo_de_puestos'])){ foreach($tipo_puesto_editar as $tipo_puesto){ echo "value=".$tipo_puesto['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_tipo_puesto(event)" class="form-control" id="idtipo_de_puestos" aria-describedby="emailHelp">
                <p id="id_tipo_puesto_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Tipo de Puesto</p>
            </div>
            <button onclick="validar_vacio_tipo_puesto()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Tipo de Puestos</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Tipo de Puesto</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_tipo_puesto as $tipo_puesto){
                ?>
                <tr>
                <td><?= $tipo_puesto['descripcion']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_tipo_puesto&idtipo_de_puestos=<?=$tipo_puesto['idtipo_de_puestos']; ?>&descripcion=<?=$tipo_puesto['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/tipo_puesto.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idtipo_de_puestos" value="<?= $tipo_puesto['idtipo_de_puestos'] ?>">
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