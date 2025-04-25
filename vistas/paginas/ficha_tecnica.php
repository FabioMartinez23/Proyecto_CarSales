<?php

if(isset($_GET['idvehiculos'])){
    $idvehiculos = $_GET['idvehiculos'];
}else{
    echo "error";
}

$vehiculo = new Vehiculos();
$result_vehiculo = $vehiculo->traer_vehiculos_por_id($idvehiculos);

foreach($result_vehiculo as $vehiculo){

?>

<h1>
    <?php echo $vehiculo['nombre_marca']." - ".$vehiculo['nombre_modelo'];  ?>
</h1>

    <div class="container mt-4">
        <form id="nuevo-vehiculo-form" method="POST" action="controladores/ficha_tecnica/ficha_tecnica.controlador.php" >
        <input type="hidden" name="action" value="guardar">
            <!-- Revisión Técnica -->
            <h2>Revisión Técnica</h2>
            <div class="mb-3">
                <label for="estado-bateria" class="form-label">Descripcion de la Carroceria:</label>
                <input type="text" id="id_descripcion_carroceria" name="descripcion_carroceria" class="form-control">
            </div>
            <div class="mb-3">
                <label for="estado-bateria" class="form-label">Estado de la batería:</label>
                <input type="text" id="id_estado_bateria" name="estado_bateria" class="form-control">
            </div>
            <div class="mb-3">
                <label for="anio-bateria" class="form-label">Año de la batería:</label>
                <input type="number" id="id_año_bateria" name="año_bateria" class="form-control">
            </div>
            <div class="mb-3">
                <label for="estado-neumaticos" class="form-label">Descripcion de los neumáticos:</label>
                <input type="text" id="id_descripcion_neumatico" name="descripcion_neumatico" class="form-control">
            </div>
            <div class="mb-3">
                <label for="anio-neumaticos" class="form-label">Año de los neumáticos:</label>
                <input type="number" id="id_año_neumatico" name="año_neumatico" class="form-control">
            </div>
            <div class="mb-3">
                <label for="estado-rto" class="form-label">Vencimiento de la RTO:</label>
                <input type="date" id="id_vencimiento_RTO" name="vencimiento_RTO" class="form-control">
            </div>
            <!-- Foto del Vehículo -->
            <div class="mb-3">
                <label for="foto-vehiculo" class="form-label">Fotos del Vehículo:</label>
                <input type="file" id="id_foto_vehiculo" name="foto_vehiculo" class="form-control">
            </div>
            <input type="hidden" name="vehiculos_idvehiculos" value="<?= $idvehiculos?>">
            <!-- Botón de Enviar -->
            <button type="submit" class="btn btn-primary">Guardar Datos</button>
        </form>
    </div>
<?php }?>