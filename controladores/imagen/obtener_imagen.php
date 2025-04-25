<?php
require_once ('../../modelos/documentaciones.php'); // Asegúrate de incluir la clase Documentacion

$idvehiculos = $_GET['idvehiculos'];  // Tomamos el id del vehículo
$imagenes_vehiculo = new Documentacion();

// Obtenemos las imágenes del vehículo usando el método ya creado
$resultado_imagenes = $imagenes_vehiculo->obtenerImagenAleatoria($idvehiculos);

if (is_array($resultado_imagenes)) {
    // Si es un array, toma la URL de la imagen
    $url_imagen = str_replace("../../", "", $resultado_imagenes['URL_descripcion']); // Quitar prefijo si es necesario
    
    // Devuelve la URL de la imagen en formato JSON
    echo json_encode(['url' => $url_imagen]);
} else {
    // En caso de no encontrar imágenes, devuelve una imagen predeterminada
    echo json_encode(['url' => 'assets/img/default.jpg']);
}
?>

