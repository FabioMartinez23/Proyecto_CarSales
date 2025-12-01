<div class="col-md-6 mb-4">
    <div style="height: 452px; width: 350px;" class="card-2">
        <?php 
        $idvehiculo = $auto['idvehiculos'];
        $imagenes_vehiculo = new Documentacion();
        $resultado_imagenes = $imagenes_vehiculo->mostrar_img_vehiculos($idvehiculo);
        ?>

        <!-- Usa el idvehiculo para que el ID del carrusel sea único -->
        <div id="carouselExample-<?php echo $idvehiculo; ?>" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $active = true;
                foreach ($resultado_imagenes as $imagen) {
                    // Quitar el prefijo "../../" y construir la URL completa
                    $url_imagen = str_replace("../../", "", $imagen['URL_descripcion']);
                    
                    // Marcar la primera imagen como activa
                    $activeClass = $active ? 'active' : '';
                    $active = false;
                    echo "<div class='carousel-item $activeClass'>";
                    echo "<img src='{$url_imagen}' class='d-block w-100' width='100' height='250' alt='Imagen de vehículo' class='img-fluid'>";
                    echo "</div>";
                }
                ?>
            </div>
            <!-- Actualiza los botones de control para que usen el id único -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample-<?php echo $idvehiculo; ?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample-<?php echo $idvehiculo; ?>" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <div class="card-body">
            <h5 class="card-title"><?php echo $auto['nombre_marca'].' - '. $auto['nombre_modelo']; ?></h5>
            <p>Tipo: <?php echo $auto['nombre_tipo']; ?></p>
            <p>Año: <?php echo $auto['anio']; ?></p>
            
            <!-- Input oculto para almacenar el idvehiculo -->
            <input type="hidden" id="idvehiculo_<?php echo $auto['idvehiculos']; ?>" value="<?php echo $auto['idvehiculos']; ?>">

            <a href="#" class="btn btn-action" onclick='mostrarModal(<?php echo json_encode($auto, JSON_HEX_TAG); ?>)'>
                Ver más
            </a>
        </div>
    </div>
</div>

<div class="modal fade" id="miModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content modal-modern">

      <div class="modal-header modal-modern-header">
        <h5 class="modal-title" id="nombre_auto"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body modal-modern-body">

        <div class="row">
            <div class="col-md-6">
                <img id="imagen_modal" class="modal-img" src="" alt="Vehículo">
            </div>

            <div class="col-md-6">
                <ul class="modal-list">
                    <li><strong>Precio:</strong> $<span id="precio_actual"></span></li>
                    <li><strong>Marca:</strong> <span id="nombre_marca"></span></li>
                    <li><strong>Modelo:</strong> <span id="nombre_modelo"></span></li>
                    <li><strong>Tipo:</strong> <span id="tipo_vehiculo"></span></li>
                    <li><strong>Kilometraje:</strong> <span id="kilometrajes"></span></li>
                    <li><strong>Año:</strong> <span id="anios"></span></li>
                    <li><strong>Color:</strong> <span id="nombre_color"></span></li>
                </ul>
            </div>
        </div>

      </div>

      <div class="modal-footer modal-modern-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <a href="#" id="btn-simulacion" onclick="redireccionarSimulacion(this)" class="btn btn-success">
            Crear Simulación
        </a>
      </div>

    </div>
  </div>
</div>


<script>
    function registrarClick(idvehiculo) {
        // Obtener el valor del input oculto
        const idvehiculoInput = document.getElementById('idvehiculo_' + idvehiculo);
        const id = idvehiculoInput ? idvehiculoInput.value : null;
        
        if (!id) {
            console.error("No se recibió un ID de vehículo válido");
            return;
        }

        fetch('controladores/vehiculos/reportes/guardar_clicks.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idvehiculo: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Servidor:", data.error);
            } else {
                console.log("Registro de clics exitoso:", data);
            }
        })
        .catch(error => {
            console.error("Error al comunicarse con el servidor:", error);
        });
    }


</script>
