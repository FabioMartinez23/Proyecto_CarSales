<?php

ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
require_once('../modelos/perfiles.php');

$perfil = new Perfil();

$result_perfil = $perfil->traer_perfiles();

if(isset($_GET['idperfiles'])){
    $perfil_editar = $perfil->traer_perfil($_GET['idperfiles']);
}


?>

<div class="row">
    <div class="col">
        <h2>Registrar Perfiles</h2>
        <form id="id_form_perfil" method="POST" action="../controladores/tablas_maestras/perfil.controlador.php">
        <?php if(isset($_GET['idperfiles'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idperfiles" value="<?= $_GET['idperfiles'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Perfil:</label>
                <input <?php if(isset($_GET['idperfiles'])){ foreach($perfil_editar as $perfil){ echo "value=".$perfil['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_perfil(event)" class="form-control" id="idperfiles" aria-describedby="emailHelp">
                <p id="id_perfil_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar un Perfil</p>
            </div>
            <button onclick="validar_vacio_perfil()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Perfiles</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Perfil</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_perfil as $perfil){
                ?>
                <tr>
                <td><?= $perfil['descripcion']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_perfiles&idperfiles=<?=$perfil['idperfiles']; ?>&descripcion=<?=$perfil['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/perfil.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idperfiles" value="<?= $perfil['idperfiles'] ?>">
                        <button onclick="return eliminar()" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>