<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$usuario = new Usuario();
$resultado_usuario = $usuario->traer_usuario_por_id($_GET['usuario']);

if ($resultado_usuario) { 
?>

    <div class="container mt-5 hacer_padding">

        <h2>Datos Personales</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Nombre:</strong> <?=$resultado_usuario['nombre']; ?></li>
            <li class="list-group-item"><strong>Apellido:</strong> <?=$resultado_usuario['apellido']; ?></li>
            <li class="list-group-item"><strong>DNI:</strong> <?=$resultado_usuario['valor_documento']; ?></li>
            <li class="list-group-item"><strong>Fecha de Nacimiento:</strong> <?=$resultado_usuario['fecha_nacimiento']; ?></li>
            <li class="list-group-item"><strong>Sexo:</strong> <?=$resultado_usuario['nombre_tipo_sexo']; ?></li>
        </ul>

        <h2>Datos de Contacto</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Tipo de Contacto:</strong> <?=$resultado_usuario['nombre_tipo_contacto']; ?></li>
            <li class="list-group-item"><strong>Contacto:</strong> <?=$resultado_usuario['valor_contacto']; ?></li>
            <li class="list-group-item"><strong>Email:</strong> <?=$resultado_usuario['email']; ?></li>
        </ul>

        <h2>Datos de Domicilio</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Tipo de Domicilio:</strong> <?=$resultado_usuario['nombre_tipo_domicilio']; ?></li>
            <li class="list-group-item"><strong>Dirección:</strong> <?=$resultado_usuario['nombre_domicilio'].' - Barrio: '.$resultado_usuario['nombre_barrio'].' - Localidad: '.$resultado_usuario['nombre_localidad'].' - Provincia: '.$resultado_usuario['nombre_provincia']; ?></li>
        </ul>

        <div class="text-center">
            <?php
            if(isset($_GET['accion']) && $_GET['accion'] == 'ver_cliente'){
            ?>
            <a href="index.php?page=listado_clientes" class="btn btn-dark">Volver</a>
            <?php
            }else{
                ?>
            <a href="index.php?page=listado_empleados" class="btn btn-dark">Volver</a>
            <?php
            }
            ?>
            <a href="reportes_pdf/ver_usuarios.php?usuario=<?=$_GET['usuario'];?>" class="btn btn-success">Descargar en PDF</a>
        </div>
    </div>

<?php 
} else {
    echo "<div class='alert alert-danger'>No se encontró ningun usuario.</div>";
}
?>