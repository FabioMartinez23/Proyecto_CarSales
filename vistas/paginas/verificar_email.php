<?php

ini_set('display_errors', 1);
require_once('modelos/conexion.php');

$conexion = new Conexion();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $query = "SELECT * FROM tokens_recuperacion WHERE token = '$token'";
    $resultado = $conexion->consultar($query);

    if ($resultado && $resultado->num_rows > 0) {
        $token_data = $resultado->fetch_assoc();
        //var_dump($token_data);
        //exit();
        $id_usuario = $token_data['Usuarios_idusuarios'];
        $fecha_expiracion = $token_data['fecha_expiracion'];

        if (strtotime($fecha_expiracion) > time()) {
            $conexion->insertar("UPDATE usuarios SET verificado_email = 1 WHERE idusuarios = $id_usuario");
            $conexion->insertar("DELETE FROM tokens_recuperacion WHERE token = '$token'");

            echo "
                <h2>¡Correo verificado con éxito!</h2>
                <p>Ya podés iniciar sesión en el sistema.</p>
                <a href='index.php?page=login'>Ir al login</a>
            ";
        } else {
            echo "
                <h2>Token expirado</h2>
                <p>El enlace ha caducado. Por favor registrate nuevamente.</p>
            ";
        }
    } else {
        echo "
            <h2>Token inválido</h2>
            <p>El token ya fue usado o no es válido.</p>
        ";
    }
} else {
    echo "
        <h2>Error</h2>
        <p>No se ha recibido ningún token.</p>
    ";
}

