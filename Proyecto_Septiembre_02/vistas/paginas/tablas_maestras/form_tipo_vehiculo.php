<?php

ini_set('display_errors', 1);
require_once('../modelos/tablas_maestras/tipo_vehiculo.php');

$tipo_vehiculo = new Tipo_Vehiculos();

$result_tipo_vehiculo = $tipo_vehiculo->traer_tipo_vehiculo();
if(isset($_GET['idtipo_vehiculos'])){
    $tipo_vehiculo_editar = $tipo_vehiculo->traer_tipo_vehiculo_id($_GET['idtipo_vehiculos']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Tipo de Vehiculo</h2>
        <form id="id_form_tipo_vehiculo" method="POST" action="../controladores/tablas_maestras/tipo_vehiculo.controlador.php">
        <?php if(isset($_GET['idtipo_vehiculos'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idtipo_vehiculos" value="<?= $_GET['idtipo_vehiculos'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Tipo de Vehiculo:</label>
                <input <?php if(isset($_GET['idtipo_vehiculos'])){ foreach($tipo_vehiculo_editar as $tipo_vehiculo){ echo "value=".$tipo_vehiculo['nombre'];}} ?> type="text" name="nombre" onfocusout="validate_tipo_vehiculo(event)" class="form-control" id="idtipo_vehiculos" aria-describedby="emailHelp">
                <p id="id_tipo_vehiculo_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Tipo de Vehiculo</p>
            </div>
            <button type="button" onclick="validar_vacio_tipo_vehiculo()" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Tipo de Vehiculos</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Tipo de Vehiculo</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_tipo_vehiculo as $tipo_vehiculo){
                ?>
                <tr>
                <td><?= $tipo_vehiculo['nombre']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_tipo_vehiculo&idtipo_vehiculos=<?=$tipo_vehiculo['idtipo_vehiculos']; ?>&nombre=<?=$tipo_vehiculo['nombre'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/tipo_vehiculo.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idtipo_vehiculos" value="<?= $tipo_vehiculo['idtipo_vehiculos'] ?>">
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
