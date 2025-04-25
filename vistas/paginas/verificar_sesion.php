<?php
session_start();

// Verificamos si el usuario está registrado (si existe la sesión con su ID de usuario)
if (!isset($_SESSION['idusuarios']) || empty($_SESSION['idusuarios'])) {
    // Si no está registrado, lo redirigimos al login con un mensaje de advertencia
    header('Location: ../../index.php?page=registrarse&mensaje=Debes registrarte primero.&status=warning');
    exit;
}

// Si está registrado, continuamos con la redirección al simulador
if (isset($_GET['id'])) {
    $autoId = $_GET['id'];
    header("Location: ../../index.php?page=simulador&id=$autoId");
    exit;
}
?>