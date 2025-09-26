<?php
// Definir tiempo máximo de inactividad (en segundos)
$tiempo_inactividad = 300; // 5 minutos

// Verificar que el usuario esté logueado
if (isset($_SESSION['idusuarios'])) {

    // Verificar si existe la marca de tiempo de última actividad
    if (isset($_SESSION['ultimo_movimiento'])) {
        $inactivo = time() - $_SESSION['ultimo_movimiento'];

        if ($inactivo > $tiempo_inactividad) {
            // Si excedió el tiempo, destruir sesión y redirigir
            session_unset();
            session_destroy();

            // Evitar caché
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");

            header("Location: index.php?page=inicio&mensaje=Sesión expirada.&status=warning");
            exit();
        }
    }

    // Actualizar el tiempo de última actividad
    $_SESSION['ultimo_movimiento'] = time();
}
?>

