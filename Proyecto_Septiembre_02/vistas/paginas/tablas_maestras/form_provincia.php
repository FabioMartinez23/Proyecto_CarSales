<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../modelos/tablas_maestras/provincia.php');
require_once('../modelos/tablas_maestras/pais.php');

$pais = new Paises();
$result_pais = $pais->traer_pais();


$provincia = new Provincias();

$result_provincia = $provincia->traer_provincia();

if(isset($_GET['idprovincias'])){
    $provincia_editar = $provincia->traer_provincia_id($_GET['idprovincias']);
}

$provincias = new Provincias();
$result_provincias_ = $provincias->traer_cantidad_provincia();
foreach($result_provincias_ as $provincias_1){
    $total_registros = $provincias_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $provincias->pagina_actual = $_GET['pagina_actual'];
}
$result_provincias = $provincias->traer_provincias_paginacion();


?>

<div class="row">
    <div class="col">
        <h2>Registrar Provincias</h2>
        <form id="id_form_provincia" method="POST" action="../controladores/tablas_maestras/provincia.controlador.php">
        <?php if(isset($_GET['idprovincias'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idprovincias" value="<?= $_GET['idprovincias'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Provincia:</label>
                <input <?php if(isset($_GET['idprovincias'])){ foreach($provincia_editar as $provincia){ echo "value=".$provincia['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_provincia(event)" class="form-control" id="idprovincias" aria-describedby="emailHelp">
                <p id="id_provincia_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Provincia</p>
            </div>
            <div class="mb-3">
                <label for="disabledSelect" class="form-label">Ingresar Pais</label>
                <select name="paises_idpaises" id="id_pais" class="form-select">
                    <option value="">Seleccione un Pais</option>
                <?php
                    foreach($result_pais as $pais){
                ?>
                    <option value="<?php echo $pais['idpaises']?>"><?php echo $pais['descripcion']?></option>
                <?php
                    }
                ?>
                </select>

            </div>

            <button onclick="validar_vacio_provincia()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Provincias</h2>      
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Provincia</th>
                <th>Pais</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_provincias as $provincias_){
                ?>
                <tr>
                <td><?= $provincias_['nombre_provincia']; ?></td>
                <td><?= $provincias_['nombre_pais']; ?></td>
                <td>
                    <a href="form_tablas_maestras.php?page=form_provincia&idprovincias=<?=$provincias_['idprovincias']; ?>&descripcion=<?=$provincias_['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form method="POST" action="../controladores/tablas_maestras/provincia.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idprovincias" value="<?= $provincias_['idprovincias'] ?>">
                        <button onclick="return eliminar()" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>


        <nav aria-label="...">
            <ul class="pagination">
            <li class="page-item <?php if($provincias->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="form_tablas_maestras.php?page=form_provincia&pagina_actual=<?= $provincias->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_provincia&pagina_actual=<?= $provincias->pagina_actual - 1 ?>"><?= $provincias->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_provincia&pagina_actual=<?= $provincias->pagina_actual ?>"><?= $provincias->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="form_tablas_maestras.php?page=form_provincia&pagina_actual=<?= $provincias->pagina_actual + 1 ?>"><?= $provincias->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($provincias->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="form_tablas_maestras.php?page=form_provincia&pagina_actual=<?= $provincias->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>