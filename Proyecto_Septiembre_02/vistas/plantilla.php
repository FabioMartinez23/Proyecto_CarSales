<?php
ini_set('display_errors', 0);
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAR SALES - PROYECTO</title>
        <!-- Latest compiled and minified CSS -->

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/22690a9605.js" crossorigin="anonymous"></script>
    <!-- <link rel="shortcut icon" href="assets/img/LOGO-Modificado.png" type="image/x-icon"> -->
    <!-- <link rel="shortcut icon" href="assets/img/CAR_SALE-1-1.png" type="image/x-icon"> -->

</head>
<body>

    <!-- / navbar-start -->
    <?php

        // Comprobar si el usuario está autenticado
        if(isset($_SESSION['descripcion'])){
            switch($_SESSION['descripcion']){
                case "Administrador":
                    include('vistas/componentes/navbar_admin.php');
                    break;
                case "Empleado":
                    include('vistas/componentes/navbar_empleado.php');
                    break;
                case "Usuario":
                    include('vistas/componentes/navbar_usuario.php');
                    break;
                default:
                    include('vistas/componentes/navbar_principal.php');
                    break;
            }
        } else {
            include('vistas/componentes/navbar_principal.php');
        }
    ?>
    <!-- / navbar end -->
    <script>
        function eliminar(){
            var respuesta = confirm("Esta seguro que deseas eliminar esto?");
            return respuesta;
        }

        function resetear(){
            var respuesta = confirm("Esta seguro que deseas resetear la contraseña?");
            return respuesta;
        }
    </script>
    
    <!-- Mensaje de alerta -->
    <?php
        if(isset($_GET['mensaje'])){
            echo '<div class="alert alert-'.$_GET['status'].'" role="alert">'.$_GET['mensaje'].'</div>';
        }
    ?>

    <div class="container">

        <?php

            if(isset($_GET['page']))
                    if($_GET['page'] == 'login'
                    || $_GET['page'] == 'contacto'
                    || $_GET['page'] == 'nosotros'
                    || $_GET['page'] == 'registrarse'){
                        include('vistas/paginas/'.$_GET['page'].'.php');
                    }


            elseif(isset($_GET['page'])){
                if(isset($_SESSION['username'])){
                    if($_GET['page'] == 'login' 
                    || $_GET['page'] == 'listado_usuarios'
                    || $_GET['page'] == 'listado_modulos'
                    || $_GET['page'] == 'listado_vehiculos'
                    || $_GET['page'] == 'cambiar_password'  
                    || $_GET['page'] == 'bienvenida'
                    || $_GET['page'] == 'formulario_perfil'
                    || $_GET['page'] == 'form_tablas_maestras'
                    || $_GET['page'] == 'salida'){
                        include('vistas/paginas/'.$_GET['page'].'.php');
                    }else{
                        include('vistas/paginas/errores/404.php');
                    }
                }else{
                    include('vistas/paginas/errores/403.php');
                }
            }else{
                if(isset($_SESSION['username'])){
                    include('vistas/paginas/bienvenida.php');
                }else{
                    exit();
                }
            }
        ?>
    </div>


        <!-- Jquery -->
        <script src="assets/js/jquery-3.7.1.js"></script>
        <!-- Latest compiled JavaScript -->
        <script src="assets/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
            $('#id_perfiles').select2();
            $('#idperfiles').select2();
            });
        </script>


</body>
</html>