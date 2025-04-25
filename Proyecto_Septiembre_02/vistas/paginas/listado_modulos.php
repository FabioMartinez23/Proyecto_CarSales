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

<div class="row">
    <div class="col">
        <h2>Registrar Modulos</h2>
        <form method="POST" action="controladores/modulos/modulos.controlador.php">
        <?php if(isset($_GET['idperfiles'])){ ?>
        <input type="hidden" name="action" value="actualizar"/>
        <input type="hidden" name="perfiles_idperfiles" value="<?= $_GET['idperfiles'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Nombre de Perfil</label>
                <input <?php if(isset($_GET['idperfiles'])){ foreach($perfiles_editar as $perfil){ echo "value=".$perfil['descripcion'];}} ?> type="text" name="descripcion" class="form-control" id="id_nombre_perfil" aria-describedby="emailHelp">
            </div>

            <div class="mb-3">
                <label for="disabledSelect" class="form-label">Modulos</label>
                <select multiple id="idperfiles" name="modulos_idmodulos[]" class="form-control">
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
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Modulos</h2>        
        <table class="table table-striped">
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
                    foreach($perfiles as $perfil){
                ?>
                <tr>
                <td><?= $perfil['descripcion']; ?></td>
                <td><?php
                    $modulos = new Modulo();
                    $result_modulos = $modulos->traer_modulos_por_perfil($perfil['idperfiles']);
                    foreach($result_modulos as $modulos){
                        echo $modulos['descripcion'], "<br>";
                    }
                
                    ?>
                </td>
                <td>
                    <a href="index.php?page=listado_modulos&idperfiles=<?=$perfil['idperfiles']; ?>" class="btn btn-success" type="button"><i class="fa-solid fa-pen-to-square"></i></a>
                </td>
                <td>
                    <form method="POST" action="controladores/usuarios/usuarios.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="id" value="<?= $usuario['usuarios_id'] ?>">
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