<?php
ini_set('display_errors', 0);
session_start();

require_once('modelos/modulos.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAR SALES - PROYECTO</title>
        <!-- Latest compiled and minified CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/datatables.min.css">
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    <link href="assets/css/sweetalert2.min.css" rel="stylesheet">
    <link href="assets/css/font-family-Sans-Montserrat-Roboto.css" rel="stylesheet">
    <!-- Agrega Font Awesome para los íconos de redes sociales -->
    <link href="assets/css/all.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    
    <script src="assets/js/chart.js"></script>
    <script src="assets/js/imask.js"></script>


    <script src="assets/js/22690a9605.js" crossorigin="anonymous"></script>
    

    <link rel="shortcut icon" href="assets/img/Logos/Icono_Movil.png" type="image/x-icon">

    <style>
        /* ajuste en el body del codigo, se realizo un background de color gris y se puso un imagen de fondo, ademas de determinar el tipo de letra y el tamaño de letras para todo el html */
        body{
            background-color: #DBD7D2;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
        }

        /* los titulos en h1 se le cambio el tipo de letra y tambien se le aumento el tañao y se detaca el negrita en el mismo ademas de que sean de color negro. */
        h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 32px;
            font-weight: 700;
        }

        /* Subtítulos tambien se le cambio el tipo de letra y el tamaño es mas pequeño que el titulo principal para destacar las jerarquias de letras */
        h2, h3 {
            font-family: 'Open Sans', sans-serif;
            font-size: 20px;
            font-weight: 500;
        }

        /* NAVBAR */
        .navbar {
            background-color: #333A56;
        }

        .navbar .nav-link, .navbar .navbar-brand {
            color: #E8E8E8 !important;
        }

        .navbar ul li:hover{
            background-color: #52658F;
            border-radius: 20px;
            transition: 0.3s;
        }


        .navbar-toggler{
            background-color: #52658F !important;
        }

        .dropdown-menu {
            background-color: #333A56;
        }

        .dropdown-item {
            color: #E8E8E8 !important;
        }


        .dropdown-item:hover {
            background-color: #52658F !important;
            color: #E8E8E8 !important;
            transition: all 0.5s;
        }

        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
        }


        /* CONTENEDOR PRINCIPAL */
        .container{
            display: block; /* o flex/inline-block dependiendo del diseño */
            width: 100%; /* o una anchura específica */
            /*min-height: 100vh; Si quieres que siempre ocupe al menos la altura de la ventana */
            box-sizing: border-box;
            background-color: #DBD7D2;
            color: #000000;
            border-radius: 10px;
            margin: 20px auto;
            padding: 0;
            height: auto;
            box-shadow: 2px 2px 10px black;
        }

        /* Encabezado */
        #encabezado {
            height: 400px;
            background: linear-gradient(to top right, #ffffff, #52658F);
            color: #000;
        }

        #encabezado div {
            align-items: center;
            margin: auto;
        }

        #encabezado h1 {
            padding-top: 20px;
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        #encabezado p {
            font-size: 1.2rem;
        }

        #encabezado a {
            margin-bottom: 20px;
        }

        /* Catálogo */
        #catalogo div {
            align-items: center;
            margin: auto;
        }

        #catalogo h2 {
            font-weight: bold;
            margin-bottom: 30px;
        }

        .catalog-item {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
            animation-delay: 0s;
        }

        .catalog-item.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Animación opcional (puedes usar transition o @keyframes) */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }



        /* Testimonios */
        #testimonios div {
            height: 400px;
            align-items: center;
            margin: auto;
        }
        #testimonios blockquote {
            font-style: italic;
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 5px solid #007bff;
        }

        /* Header Styles */
        .dashboard-header {
            background-color: #003366;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            font-size: 32px;
        }

        .dashboard-header p {
            font-size: 18px;
            margin-top: 10px;
        }

        /* Stats Section - contenedor de la cartas*/
        .stats {
            display: flex;
            gap: 20px;
            justify-content: space-around;
            margin: 20px 0;
        }

        .card {
            background: linear-gradient(to top right, #ffffff, #52658F);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
            max-width: 300px;
            cursor: pointer;
        }

        .card:hover{
            transform: scale(1.1);
            transition: 0.3s;
        }

        .card h2 {
            color: #003366;
            margin: 0;
            font-size: 2rem;
        }

        .card p {
            color: #4d4d4d;
            margin-top: 10px;
            font-size: 1rem;
        }

        .card-2 {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 2px 2px 10px black;
            flex: 1;
            max-width: 400px;
        }

        .card-img-top {
            height: 173px;
            width: 258px;
        }

        /* Charts Section */
        .charts {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-around;
            margin: 20px 0;
        }

        .charts canvas {
            max-width: 100%;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }

        /* Quick Actions Section */
        .quick-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .quick-actions .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
        }

        .quick-actions .btn-primary {
            background-color: #333A56;
            color: white;
            border: none;
        }

        .quick-actions .btn-primary:hover {
            background-color: #52658F;
            color: #fafbfc;
            box-shadow: 1px 5px 5px rgba(0, 0, 0, 0.5);
        }


        /* LOGIN */
        .login {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
            padding: 10px 20px; /* Mantén un espacio alrededor, si es necesario */
            margin: 20px auto; /* Centra horizontal y verticalmente */
            max-width: 400px;
            box-sizing: border-box;
            box-shadow: 2px 2px 10px black;
        }

        .hacer_padding{
            padding: 20px;
        }


        /* BOTONES */

        .btn-action {
            background-color: #333A56; /* azul oscuro */
            color: #E8E8E8;
            border: none;
        }

        .btn-action:hover {
            box-shadow: 2px 2px 10px black;
            background-color: #52658F;
            color: #E8E8E8;
        }

        .btn:hover{
            box-shadow: 2px 2px 10px black;
        }

        .pagination .page-link {
            color: #fff;
            background-color: #333A56;
            border: 1px solid #333A56;
        }

        .pagination .page-link:hover {
            color: #ffffff;
            background-color: #52658F;
            border-color: #52658F;
        }

        .pagination .page-item.active .page-link {
            color: #ffffff;
            background-color: #52658F;
            border-color: #52658F;
        }

        .pagination .page-item.disabled .page-link {
            color: #888888;
            background-color: #f0f0f0;
            border-color: #dddddd;
        }

        /* MODAL E IMAGENES */
        .modal{
            color: #000;
        }

        #imagenesRelacionadas {
        display: flex;
        flex-wrap: wrap;
        max-width: 100%;
        }

        #imagenesRelacionadas img {
            border: 1px solid #ddd;
            margin-right: 10px;
        }

        .form-check-input.bg-success {
            background-color: #198754 !important;
            border-color: #198754 !important;
        }

        .form-check-input.bg-danger {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }


        /* PROBLEMA CON EL SELECT2 EN LISTADO MODULOS */
        .select2-selection__rendered {
            color: #000 !important; 
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            color: #000;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000;
        }

        .select2-container--default .select2-results__option--highlighted {
            background-color: #52658F;
            color: #fff;
        }


        /* DASHBOARD */
        .main-content {
        margin-top: 80px;
        padding: 20px;
        }


        .dashboard-tarjetas {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
            margin-top: 80px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .tarjeta {
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .tarjeta i {
            font-size: 4rem;
            opacity: 0.2;
        }

        .tarjeta:nth-child(1):hover {
            box-shadow: 1px 5px 5px rgba(0, 0, 0, 0.4);
            transition: 0.2s;
        }

        .tarjeta:nth-child(2):hover {
            box-shadow: 1px 5px 5px rgba(0, 0, 0, 0.4);
            transition: 0.2s;
        }

        .tarjeta:nth-child(3):hover {
            box-shadow: 1px 5px 5px rgba(0, 0, 0, 0.4);
            transition: 0.2s;
        }

        .tarjeta:nth-child(4):hover {
            box-shadow: 1px 5px 5px rgba(0, 0, 0, 0.5);
            transition: 0.2s;
        }

        .tarjeta:nth-child(1) {
            background: linear-gradient(to top right, #1CE1DC, #597CEA);
        }

        .tarjeta:nth-child(2) {
            background: linear-gradient(to top right, #FD3A95, #FF7C5A);
        }

        .tarjeta:nth-child(3) {
            background: linear-gradient(to top right, #41E197, #36B6B4);
        }

        .tarjeta:nth-child(4) {
            background: linear-gradient(to top right, #FFD640, #FF8850);
        }

        div.contenido-tarjeta p{
            font-weight: bold;
        }

        /* TABLAS DE CONTENIDOS */
        .table{
            border: 1px solid black;
        }

        .table th{
            background-color: #52658F;
            color: black;
        }

        /* INPUTS SELECT Y LABEL */
        .form-control {
            box-shadow: 0.5px 0.5px 5px black;
        }

        .form-select{
            box-shadow: 0.5px 0.5px 5px black;
        }

        .form-label-list {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 2px solid #000;
        }


        /* CARTAS DE REPORTES */
        .card{
            box-shadow: 0.5px 0.5px black;
        }

        .card-report {
            box-shadow: 0.5px -2px 5px black; /* Sombra solo en la parte superior y los costados */
        }

        /* GRAFICOS */
        .gf-sexo{
            box-shadow: 0.5px 0.5px 5px black;
        }

        .gf-ventas{
            box-shadow: 0.5px 0.5px 5px black;
        }


        .hidden {
            display: none;
        }


        /* PIE DE PAGINA O FOOTER */
        .footer_principal {
            background-color: #333A56;
            color: #ced4da;
            width: auto;
        }

        .footer_principal a {
            text-decoration: none;
        }

        .footer_principal a:hover {
            background-color: #52658F;
            padding: 5px;
            border-radius: 5px;
            transition: 0.3s;
        }

        /* MODIFICACION DE INPUT PARA MIS DATOS  */
        .form-control-no-edit {
            background-color: transparent;
            color: #000000;
            border: none;
            outline: none;
            box-shadow: none;
            caret-color: #f0f0f0;
            cursor: default;
        }

        /* Para el estado de enfoque (click) - sin borde ni fondo */
        .form-control-no-edit:focus {
            background-color: transparent;
            outline: none;
            box-shadow: none;
        }


        /* MODIFICACIONES DE IMAGENES Y DOCUMENTOS EN EL MODAL DE CARGAR */
        .image-item, .document-item {
            display: inline-block;
            width: 150px;
            text-align: center;
            margin: 10px;
            vertical-align: top;
        }

        .image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }


        .btn-delete {
            color: red;
            text-decoration: none;
            font-size: 0.9em;
        }


        /* BREAD CRUMB */
        .breadcrumb-glass {
            display: flex;
            list-style: none;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            font-family: 'Open Sans', sans-serif;
            font-size: 1rem;
            color: #333;
            width: auto;
        }

        .breadcrumb-glass .breadcrumb-item a {
            text-decoration: none;
            color: #333A56;
            font-weight: bold;
        }

        .breadcrumb-glass .breadcrumb-item.active {
            color: #333;
        }

        .breadcrumb-glass .breadcrumb-item::after {
            content: "▶";
            margin: 0 8px;
            color: #333A56;
        }

        .breadcrumb-glass .breadcrumb-item:last-child::after {
            content: "";
        }


        /* RESPONSIVE */

        @media (max-width: 1180px){
            .navbar ul li:hover{
            background-color: #52658F;
            transition: all ease 0.5s;
            border-radius: 5px;
        }
        }


    </style>
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
                case "Cliente":
                    include('vistas/componentes/navbar_cliente.php');
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
        
        <!-- Modal de Confirmación -->
        <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Confirmación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="mensajeConfirmacion">¿Estás seguro de que deseas realizar esta acción?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="btnConfirmar">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>

        
        <?php

        // Página por defecto si no se especifica ninguna página
            if (!isset($_GET['page'])) {
                if (isset($_SESSION['username'])) {
                    include('vistas/paginas/bienvenida.php');
                } else {
                    include('vistas/paginas/inicio.php');
                }
            }

        
        $paginas_publicas = ['inicio', 'nosotros', 'contacto', 'login', 'registrarse', 'form_contacto', 'comprar_vehiculo', 'olvidar_contraseña', 'recuperar_password'];
    
        // Comprobamos si se ha solicitado una página
        if (isset($_GET['page'])) {
            $pagina_solicitada = $_GET['page'];
    
            // Verificamos si la página solicitada está en el arreglo de páginas públicas
            if (in_array($pagina_solicitada, $paginas_publicas)) {
                if (file_exists('vistas/paginas/' . $pagina_solicitada . '.php')) {
                    include('vistas/paginas/' . $pagina_solicitada . '.php');
                } else {
                    // Error 404 si la página no existe físicamente
                    include('vistas/paginas/errores/404.php');
                }
            } else {
                // Verificamos si el usuario ha iniciado sesión
                if (isset($_SESSION['username'])) {
                    // Verificamos los permisos de acceso a la página
                    $permiso_ingreso = guard($_SESSION['idperfiles'], $pagina_solicitada);
    
                    if ($permiso_ingreso) {
                        if (file_exists('vistas/paginas/' . $pagina_solicitada . '.php')) {
                            include('vistas/paginas/' . $pagina_solicitada . '.php');
                        } else {
                            include('vistas/paginas/errores/404.php');
                        }
                    } else {
                        include('vistas/paginas/errores/403.php');
                    }
                } else {
                    include('vistas/paginas/errores/403.php');
                }
            }
    
        } else {
            // Página por defecto si no se especifica ninguna página
            if (isset($_SESSION['username'])) {
                include('vistas/paginas/bienvenida.php'); // Página por defecto si está autenticado
            } else {
                include('vistas/paginas/inicio.php'); // Página por defecto si no está autenticado
            }
        }
    
        // Función para verificar si el perfil del usuario tiene permiso para la página solicitada
        function guard($idperfiles, $paginaSolicitada) {
            $modulo = new Modulo();
            $resultados = $modulo->traer_modulos_por_perfil($idperfiles);
            $permiso_ingreso = false;
    
            foreach ($resultados as $row) {
                if ($row['descripcion'] == $paginaSolicitada) {
                    $permiso_ingreso = true;
                    break;
                }
            }
    
            return $permiso_ingreso; // Retornamos si tiene o no permiso
        }
    
        ?>

    <!-- Footer -->
        <footer class="pt-4 mt-5 footer_principal  hacer_padding">
                <div class="row">
                    <div class="col-md-4">
                        <h4>Car Sales</h4>
                        <p>Sistema de Gestion para Consecionarias de Autos Usados.</p>
                        <p>&copy; <?php echo date("Y"); ?> Car Sales. Todos los derechos reservados.</p>
                    </div>
                    
                    <div class="col-md-4">
                        <h4>Navegación</h4>
                        <ul class="list-unstyled">
                            <li><a href="index.php?page=inicio" class="text-light">Inicio</a></li>
                            <li><a href="index.php?page=nosotros" class="text-light">Sobre Nosotros</a></li>
                            <li><a href="index.php?page=comprar_vehiculo" class="text-light">Vehículos</a></li>
                            <li><a href="index.php?page=contacto" class="text-light">Contacto</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-md-4">
                        <h4>Conéctate con nosotros</h4>
                        <p>+54 9 370 407 3160</p>
                        <p>Calle Arenales 1815, Formosa, Argentina</p>
                        <ul class="list-unstyled d-flex">
                            <li><a href="#" class="text-light me-3"><i class="fab fa-facebook"></i> Facebook</a></li>
                            <li><a href="#" class="text-light me-3"><i class="fab fa-instagram"></i> Instagram</a></li>
                            <li><a href="https://wa.me/+5493704073160?text=Hola,%20estoy%20interesado%20en%20un%20vehiculo" class="text-light me-3">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a></li>
                        </ul>
                    </div>
                </div>
        </footer>




</body>
        <!-- Jquery -->
        <script src="assets/js/jquery-3.7.1.js"></script>
        <!-- Latest compiled JavaScript -->
        <script src="assets/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/select2.min.js"></script>
        <script src="assets/js/datatables.min.js"></script>
        <script src="assets/js/sweetalert2.all.min.js"></script>


        <script>
            $(document).ready(function() {
            $('#idperfiles').select2({
            theme: 'default' // Asegúrate de que no haya conflictos de tema
            });
            $('#tabla_cliente').DataTable();
            });
        </script>
        

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let accionPendiente = '';
                let formularioId = '';

                // Función para mostrar el modal de confirmación
                function mostrarModal(mensaje, accion, formId) {
                    document.getElementById('mensajeConfirmacion').textContent = mensaje;
                    accionPendiente = accion;
                    formularioId = formId;
                    var modal = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
                    modal.show();
                }

                // Evento para confirmar la acción dentro del modal
                document.getElementById('btnConfirmar').addEventListener('click', function() {
                    if (accionPendiente && formularioId) {
                        const formulario = document.getElementById(formularioId);
                        if (formulario) {
                            formulario.submit();
                        } else {
                            console.error('Formulario no encontrado: ' + formularioId);
                        }
                    }
                });

                // Función para confirmar la acción y mostrar el modal
                window.confirmarAccion = function(event, accion, formId) {
                    event.preventDefault(); // Previene el envío inmediato del formulario
                    let mensaje = '';

                    // Definir el mensaje según el tipo de acción
                    if (accion === 'eliminar') {
                        mensaje = '¿Estás seguro de que deseas eliminar este elemento?';
                    } else if (accion === 'resetear') {
                        mensaje = '¿Estás seguro de que deseas resetear la contraseña?';
                    }

                    mostrarModal(mensaje, accion, formId);
                };
            });
        </script>



    <script>
        // Obtiene los parámetros de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const mensaje = urlParams.get('mensaje');
        const status = urlParams.get('status');

        // Si existen parámetros, muestra la alerta con SweetAlert2
        if (mensaje && status) {
            let icon;
            switch (status) {
                case 'success':
                    icon = 'success';
                    break;
                case 'error':
                    icon = 'error';
                    break;
                case 'warning':
                    icon = 'warning';
                    break;
                case 'info':
                    icon = 'info';
                    break;
                case 'question':
                    icon = 'question';
                    break;
                default:
                    icon = 'info'; // Ícono por defecto si no se reconoce el estado
            }
            Swal.fire({
                icon: status,
                title: mensaje,
                showConfirmButton: false,
                timer: 3000 // La alerta desaparece automáticamente después de 2 segundos
            });
        }
    </script>
</html>