<?php


require_once('modelos/usuarios.php');
require_once('modelos/perfiles.php');
require_once('modelos/modulos.php');
require_once('modelos/vehiculos.php');
require_once('modelos/tablas_maestras/modelo_vehiculo.php');
require_once('controladores/plantilla.controlador.php');
# require_once('controladores/login.controlador.php');

$plantilla = new PlantillaControlador();
$plantilla->traer_plantilla();

?>

<!-- 
<div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
        <img src="assets/img/auto1.jpg" class="d-block w-100" alt="auto1">
        </div>
        <div class="carousel-item">
        <img src="assets/img/auto2.jpg" class="d-block w-100" alt="auto2">
        </div>
        <div class="carousel-item">
        <img src="assets/img/auto3.jpg" class="d-block w-100" alt="auto3">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div> -->