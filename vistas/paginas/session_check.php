<?php
session_start();

// Definir tiempo máximo de inactividad (en segundos)
$tiempo_inactividad = 300; // 5 minutos

// Solo hacer la verificación si el usuario está logueado
if (isset($_SESSION['idusuario'])) {  // <-- Ajusta 'idusuario' a tu variable de sesión real
    if (isset($_SESSION['ultimo_movimiento'])) {
        $inactivo = time() - $_SESSION['ultimo_movimiento'];
        if ($inactivo > $tiempo_inactividad) {
            // Destruir sesión y redirigir
            session_unset();
            session_destroy();

            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");

            header("Location: index.php?page=inicio&mensaje=Sesion Expirada.&status=warning");
            exit();
        }
    }
    // Actualizar tiempo de última actividad
    $_SESSION['ultimo_movimiento'] = time();
}
?>
