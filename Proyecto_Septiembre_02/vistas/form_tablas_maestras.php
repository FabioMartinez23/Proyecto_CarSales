<?php
ini_set('display_errors', 0);
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablas Maestras</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/select2.min.css" rel="stylesheet" />
    <script src="../assets/js/22690a9605.js" crossorigin="anonymous"></script>
</head>
<body>

<?php

    include('componentes/navbar_tablas_maestras.php');

?>

</header>

    <script>
        function eliminar(){
            var respuesta = confirm("Esta seguro que deseas eliminar esto?");
            return respuesta;
        }
    </script>

    <?php
        if(isset($_GET['mensaje'])){
            echo '<div class="alert alert-'.$_GET['status'].'" role="alert">'.$_GET['mensaje'].'</div>';
        }
    ?>


    <div class="container">
        <?php
            if(isset($_GET['page'])){
                    if($_GET['page'] == 'form_tipo_sexo'
                    || $_GET['page'] == 'form_marca'
                    || $_GET['page'] == 'form_tipo_vehiculo'
                    || $_GET['page'] == 'form_modelo_vehiculo'
                    || $_GET['page'] == 'form_tipo_documento'
                    || $_GET['page'] == 'form_tipo_contacto'
                    || $_GET['page'] == 'form_tipo_domicilio'
                    || $_GET['page'] == 'form_barrio'
                    || $_GET['page'] == 'form_localidad'    
                    || $_GET['page'] == 'form_provincia'    
                    || $_GET['page'] == 'form_pais'
                    || $_GET['page'] == 'form_tipo_pago'
                    || $_GET['page'] == 'form_tipo_puesto'    
                    || $_GET['page'] == 'form_modulo'    
                    || $_GET['page'] == 'form_perfiles'
                    || $_GET['page'] == 'form_color'
                    ){
                        include('paginas/tablas_maestras/'.$_GET['page'].'.php');
                    }
                }
        ?>


    <!-- Jquery -->
    <script src="../assets/js/jquery-3.7.1.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/select2.min.js"></script>
    <script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>
    <script>
        $(document).ready(function() {
        $('#id_tipo_').select2();
        $('#id_perfil').select2();
        });
    </script>

</body>
</html>