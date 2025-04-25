<?php

ini_set('display_errors', 1);

$total_registros = 0;

$vehiculo = new Vehiculos();
$todos_vehiculos = $vehiculo->traer_todos_vehiculos();

if(isset($_GET['idvehiculos'])){
    $vehiculo_editar = $vehiculo->traer_vehiculos_por_id($_GET['idvehiculos']);
}

$vehiculos = new Vehiculos();
$result_vehiculos_ = $vehiculos->traer_cantidad_vehiculo();
foreach($result_vehiculos_ as $vehiculo_1){
    $total_registros = $vehiculo_1['total'];
}
if (isset($_GET['pagina_actual'])){
    $vehiculos->pagina_actual = $_GET['pagina_actual'];
}
$result_vehiculos = $vehiculos->traer_vehiculos($inicio, $cantidad);

$ficha_tecnica = new Fichas_Tenicas();
$result_ficha_tecnica = $ficha_tecnica->traer_fichas_tecnicas();


?>

    <div class="col">
        <h1 class="text-center mb-4">Fichas Tecnicas</h1>      
        <table class="table table-hover">
            <thead>
                <tr>
                <th>Patente</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Descripcion Carroceria</th>
                <th>Descripcion de Neumaticos</th>
                <th>Vencimiento de RTO</th>
                <th>Modificar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_ficha_tecnica as $ficha_tecnica){
                ?>
                <tr>
                <td><?= $ficha_tecnica['patente']; ?></td>
                <td><?= $ficha_tecnica['nombre_marca']; ?></td>
                <td><?= $ficha_tecnica['nombre_modelo']; ?></td>
                <td><?= $ficha_tecnica['año']; ?></td>
                <td><?= $ficha_tecnica['descripcion_carroceria']; ?></td>
                <td><?= $ficha_tecnica['descripcion_neumatico']; ?></td>
                <td><?= $ficha_tecnica['vencimiento_RTO']; ?></td>
                <td>
                    <a href="index.php?page=registrar_vehiculos&idvehiculos=<?=$vehiculo_['idvehiculos']; ?>" class="btn btn-success" type="button">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>

        <nav aria-label="...">
            <ul class="pagination justify-content-center">
            <li class="page-item <?php if($vehiculos->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual - 1 ?>"><?= $vehiculos->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual ?>"><?= $vehiculos->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual + 1 ?>"><?= $vehiculos->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($vehiculos->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="index.php?page=listado_vehiculos&pagina_actual=<?= $vehiculos->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>