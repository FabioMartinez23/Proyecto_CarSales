<?php
ini_set('display_errors', 0);
session_start();
require_once('paginas/session_check.php');
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
    <link href="assets/css/datatables.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/caja.css" rel="stylesheet">
    <link href="assets/css/gastos.css" rel="stylesheet">
    <link href="assets/css/detalle_compra_venta.css" rel="stylesheet">
    <link href="assets/css/costo_vehiculo.css" rel="stylesheet">
    <link href="assets/css/doc_entrega_fisica.css" rel="stylesheet">
    <link href="assets/css/anular_venta.css" rel="stylesheet">
    <link href="assets/css/inicio.css" rel="stylesheet">
    <link href="assets/css/contacto_estilos.css" rel="stylesheet">
    <link href="assets/css/nosotros_estilos.css" rel="stylesheet">
    <link href="assets/css/comprar_estilos.css" rel="stylesheet">
    <link href="assets/css/cards_modern.css" rel="stylesheet">
    <link href="assets/css/form_contacto.css" rel="stylesheet">
    <link href="assets/css/reportes.css" rel="stylesheet">
    <link href="assets/css/reporte_bases.css" rel="stylesheet">
    <link href="assets/css/reporte_auditoria.css" rel="stylesheet">
    <link href="assets/css/bienvenida_cliente.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <link href="assets/css/sweetalert2.min.css" rel="stylesheet">
    <link href="assets/css/font-family-Sans-Montserrat-Roboto.css" rel="stylesheet">
    <!-- Agrega Font Awesome para los íconos de redes sociales -->
    <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    
    <script src="assets/js/chart.js"></script>
    <script src="assets/js/imask.js"></script>


    <script src="assets/js/22690a9605.js" crossorigin="anonymous"></script>
    

    <link rel="shortcut icon" href="assets/img/Logos/Icono_Movil.png" type="image/x-icon">

</head>
<style>
.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(51, 58, 86, 0.85); /* tu color oscuro con transparencia */
    z-index: 999999;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    backdrop-filter: blur(2px);
}

.loader-spinner {
    width: 70px;
    height: 70px;
    border: 6px solid #ffffff;
    border-top-color: #52658F; /* tu color principal */
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loader-text {
    color: #fff;
    margin-top: 20px;
    font-size: 18px;
    font-weight: 600;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
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

        
        $paginas_publicas = ['inicio', 'nosotros', 'contacto', 'login', 'registrarse', 'form_contacto', 'comprar_vehiculo', 'olvidar_contraseña', 'recuperar_password', 'verificar_email'];
    
        // Comprobamos si se ha solicitado una página
        if (isset($_GET['page'])) {
            $pagina_solicitada = $_GET['page'];
    
            // Verificamos si la página solicitada está en el arreglo de páginas públicas
            if (in_array($pagina_solicitada, $paginas_publicas)) {
                // Intentamos primero buscar directamente en vistas/paginas/
                $ruta_base = 'vistas/paginas/' . $pagina_solicitada . '.php';
                $ruta_subcarpeta = 'vistas/paginas/caja/' . $pagina_solicitada . '.php';

                if (file_exists($ruta_base)) {
                    include($ruta_base);
                } elseif (file_exists($ruta_subcarpeta)) {
                    include($ruta_subcarpeta);
                } else {
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

            $paginaBase = basename($paginaSolicitada); // 🔹 Toma solo el nombre del archivo sin carpeta

            foreach ($resultados as $row) {
                if ($row['descripcion'] == $paginaBase) {
                    $permiso_ingreso = true;
                    break;
                }
            }

            return $permiso_ingreso;
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

    <!-- Loader Global -->
    <div id="loader-overlay" class="loader-overlay" style="display:none;">
        <div class="loader-spinner"></div>
        <p class="loader-text">Procesando, por favor espere...</p>
    </div>


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
                    } else if (accion === 'anular'){
                        mensaje = '¿Estás seguro que deseas anular la venta?';
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