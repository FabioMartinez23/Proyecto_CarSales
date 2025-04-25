<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../modelos/tablas_maestras/localidad.php');
require_once('../modelos/tablas_maestras/provincia.php');
require_once('../modelos/tablas_maestras/pais.php');

$provincia = new Provincias();
$result_provincia = $provincia->traer_provincia();

$localidad = new Localidades();

$result_localidad = $localidad->traer_localidad();

if(isset($_GET['idlocalidades'])){
    $provincia_editar = $localidad->traer_localidad_id($_GET['idlocalidades']);
}

$localidades = new Localidades();
$result_localidades_ = $localidades->traer_cantidad_localidad();
foreach($result_localidades_ as $localidades_1){
    $total_registros = $localidades_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $localidades->pagina_actual = $_GET['pagina_actual'];
}
$result_localidades = $localidades->traer_localidades_paginacion();

$pais = new Paises();
$result_pais = $pais->traer_pais();

?>

<div class="row">
    <div class="col">
        <h2>Registrar Localidades</h2>
        <form id="id_form_localidad" method="POST" action="../controladores/tablas_maestras/localidad.controlador.php">
        <?php if(isset($_GET['idlocalidades'])){ ?>
        <input type="hidden" name="action" value="modificar"/>
        <input type="hidden" name="idlocalidades" value="<?= $_GET['idlocalidades'] ?>"/>
        <?php }else{?>
            <input type="hidden" name="action" value="guardar"/>

        <?php }?>

            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Localidad:</label>
                <input <?php if(isset($_GET['idlocalidades'])){ foreach($localidad_editar as $localidad){ echo "value=".$localidad['descripcion'];}} ?> type="text" name="descripcion" onfocusout="validate_localidad(event)" class="form-control" id="idlocalidades" aria-describedby="emailHelp">
                <p id="id_localidad_parrafo" style="color:red; display:none;">Campo Vacio - Ingresar Localidad</p>
            </div>

            <div class="mb-3">
                    <label for="disabledSelect" class="form-label">Pais</label>
                    <select name="idpaises" id="idpaises" onchange="validar_pais(this.value)" class="form-select">
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

            <div class="mb-3">
                    <label for="disabledSelect" class="form-label">Provincias</label>
                    <select name="provincias_idprovincias" id="idprovincias" class="form-select">
                        <option value="">Seleccione una Provincia</option>
                    <!-- Aqui iría la respuesta de ajax -->
                    </select>
            </div>

            <button onclick="validar_vacio_localidad()" type="button" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2>Listado de Localidades</h2>      
        <table class="table table-hover">
            <thead>
                <tr>
                <th>Localidad</th>
                <th>Provincia</th>
                <th>Modificar</th>
                <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_localidades as $localidades_){
                ?>
                <tr>
                <td><?= $localidades_['nombre_localidad']; ?></td>
                <td><?= $localidades_['nombre_provincia']; ?></td>
                <td>
                    <a href="form_datos_para_domicilios.php?page=form_localidad&idlocalidades=<?=$localidades_['idlocalidades']; ?>&descripcion=<?=$localidades_['descripcion'];?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>

                <td>
                    <form id="formulario-eliminar-<?= $localidades_['idlocalidades']; ?>" method="POST" action="../controladores/tablas_maestras/localidad.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idlocalidades" value="<?= $localidades_['idlocalidades'] ?>">
                        <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $localidades_['idlocalidades']; ?>')" class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>


        <nav aria-label="...">
            <ul class="pagination justify-content-center">
            <li class="page-item <?php if($localidades->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="form_datos_para_domicilios.php?page=form_localidad&pagina_actual=<?= $localidades->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="form_datos_para_domicilios.php?page=form_localidad&pagina_actual=<?= $localidades->pagina_actual - 1 ?>"><?= $localidades->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="form_datos_para_domicilios.php?page=form_localidad&pagina_actual=<?= $localidades->pagina_actual ?>"><?= $localidades->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="form_datos_para_domicilios.php?page=form_localidad&pagina_actual=<?= $localidades->pagina_actual + 1 ?>"><?= $localidades->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($localidades->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="form_datos_para_domicilios.php?page=form_localidad&pagina_actual=<?= $localidades->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>


<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>
<script src="../assets/js/validaciones/validar_pais.ajax.js"></script>