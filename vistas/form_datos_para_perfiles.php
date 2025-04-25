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
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/select2.min.css" rel="stylesheet" />
    <link href="../assets/css/sweetalert2.min.css" rel="stylesheet">
    <link href="../assets/css/form_datos_style.css" rel="stylesheet">
    <script src="../assets/js/22690a9605.js" crossorigin="anonymous"></script>

</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img style="width: 60px; height:60px;" src="../assets/img/LOGO-Modificado.png" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <li style="background-color: #52658F; border-radius:20px;" class="nav-item">
            <a class="nav-link active" aria-current="page" href="../index.php?page=bienvenida">Volver al Inicio</a>
            </li>
            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="form_datos_para_perfiles.php?page=form_perfiles">Perfiles</a>
            </li>
            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="form_datos_para_perfiles.php?page=form_modulo">Modulos</a>
            </li>
        </ul>
    </div>
</nav>

<h1 class="text-center mb-4">Gestion de Datos para Perfiles</h1>
</header>

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



    <div class="container">
        <?php
            if(isset($_GET['page'])){
                    if($_GET['page'] == 'form_perfiles'
                    || $_GET['page'] == 'form_modulo'
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
    <script src="../assets/js/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
        $('#id_tipo_').select2();
        $('#id_perfil').select2();
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
                timer: 2000 // La alerta desaparece automáticamente después de 2 segundos
            });
        }
    </script>
</body>
</html>