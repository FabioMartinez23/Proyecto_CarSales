<?php

$perfil = new Perfil();
$perfiles = $perfil->traer_perfiles();

$usuarios = new Usuario();
$result_usuario = $usuarios->traer_usuarios();

$modulos = new Modulo();
$result_modulos = $modulos->traer_modulos();

if(isset($_GET['idperfiles'])){
    $perfiles_editar = $perfil->traer_perfil($_GET['idperfiles']);
    $result_modulos_editar = $modulos->traer_modulos_por_perfil($_GET['idperfiles']);
}

?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Sistemas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Asignación de Modulos</li>
    </ol>
</nav>

<h1 class="text-center mb-4">Asignación de Modulos</h1>

<div class="row hacer_padding">
    <div class="col">
        <h2 class="text-center">Registrar Modulos</h2>
        <form method="POST" action="controladores/modulos/modulos.controlador.php">
        <?php if(isset($_GET['idperfiles'])){ ?>
        <input type="hidden" name="action" value="actualizar"/>
        <input type="hidden" name="perfiles_idperfiles" value="<?= $_GET['idperfiles'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="form-floating mb-3 mt-2">
                <input <?php if(isset($_GET['idperfiles'])){ foreach($perfiles_editar as $perfil){ echo "value=".$perfil['descripcion'];}} ?> type="text" name="descripcion" class="form-control" id="id_nombre_perfil" aria-describedby="emailHelp">
                <label for="exampleInputEmail1" class="form-label">Nombre de Perfil</label>
            </div>

            <div class="form-floating mb-3 mt-2">
                <select multiple id="idperfiles" name="modulos_idmodulos[]" class="form-select">
                    <option value="">Seleccione un Modulo</option>
                    <?php
                        foreach($result_modulos as $modulos){
                            foreach($result_modulos_editar as $modulo_editar){
                                if($modulos['idmodulos'] == $modulo_editar['idmodulos']){
                                    echo "<option value='".$modulos['idmodulos']."' selected>".$modulos['descripcion']."</option>";
                                }else{
                                    //echo "<option value='".$permisos['id']."'>".$permisos['nombre']."</option>";
                                }
                            }
                            echo "<option value='".$modulos['idmodulos']."'>".$modulos['descripcion']."</option>";
                        }
                        ?>
                </select>
                <label for="disabledSelect" class="form-label">Modulos</label>
            </div>
            <button type="submit" class="btn btn-action">Guardar</button>
        </form>
    </div>

    <div class="col">
        <h2 class="text-center mb-4">Listado de Modulos</h2>        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Perfil</th>
                    <th>Modulo</th>
                    <th>Modificar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($perfiles as $perfil_){
                ?>
                <tr>
                <td><?= $perfil_['descripcion']; ?></td>
                <td><?php
                    $modulos = new Modulo();
                    $result_modulos = $modulos->traer_modulos_por_perfil($perfil_['idperfiles']);
                    foreach($result_modulos as $modulos){
                        echo $modulos['descripcion'], "<br>";
                    }
                
                    ?>
                </td>
                <td>
                    <a href="index.php?page=listado_modulos&idperfiles=<?=$perfil_['idperfiles']; ?>" class="btn btn-success" type="button"><i class="fa-solid fa-pen-to-square"></i></a>
                </td>
                <td>
                    <form id="formulario-eliminar-<?= $perfil_['idperfiles']; ?>" method="POST" action="controladores/modulos/modulos.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idperfiles" value="<?= $perfil_['idperfiles'] ?>">
                        <input type="hidden" name="perfil" value="<?= $perfil_['descripcion'] ?>">
                        <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $perfil_['idperfiles']; ?>')"class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
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