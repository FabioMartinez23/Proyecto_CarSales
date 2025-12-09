<?php
ini_set('display_errors', 0);
session_start();

?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Vehículos</a></li>
        <li class="breadcrumb-item"><a href="#">Gestión de Vehículos</a></li>
        <?php 
        if(isset($_GET['accion']) && $_GET['accion'] === 'registrar'){
        ?>
        <li class="breadcrumb-item"><a href="index.php?page=listado_vehiculos">Vehículos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Stock</li>
        <?php
        }else{
        ?>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Stock</li>
        <?php
        }
        ?>
    </ol>
</nav>

<div class="hacer_padding">
    <main class="main">
        <section class="stats d-flex justify-content-between">
            
            <!-- Vehículos Disponibles -->
            <div class="card text-center p-3" onclick="window.location.href='index.php?page=listado_vehiculos'">
                <div class="contenido-tarjeta">
                    <div class="icono">
                        <i class="fa-solid fa-car"></i>
                    </div>
                    <div>
                        <p>Vehículos Disponibles</p>
                    </div>
                </div>
            </div>

            <!-- Vehículos con Documentación Faltante -->
            <div class="card text-center p-3" onclick="window.location.href='index.php?page=listado_falta_documentacion'">
                <div class="contenido-tarjeta">
                    <div class="icono">
                        <i class="fa-solid fa-file-circle-exclamation"></i>
                    </div>
                    <div>
                        <p>Documentación Faltante</p>
                    </div>
                </div>
            </div>

            <!-- Vehículos en Reparación -->
            <div class="card text-center p-3" onclick="window.location.href='index.php?page=listado_taller'">
                <div class="contenido-tarjeta">
                    <div class="icono">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <div>
                        <p>Vehículos en Reparación</p>
                    </div>
                </div>
            </div>

            <!-- Vehículos Vendidos -->
            <div class="card text-center p-3" onclick="window.location.href='index.php?page=listado_ventas'">
                <div class="contenido-tarjeta">
                    <div class="icono">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <div>
                        <p>Vehículos Vendidos</p>
                    </div>
                </div>
            </div>

        </section>
    </main>
</div>
