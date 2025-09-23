

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img style="width: 60px; height:60px;" src="assets/img/LOGO-Modificado.png" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php?page=bienvenida">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Usuarios
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php?page=registrar_clientes">Registrar Clientes</a></li>
                        <li><a class="dropdown-item" href="index.php?page=listado_clientes">Clientes</a></li>
                        <li><a class="dropdown-item" href="index.php?page=registrar_empleados">Registrar Empleados</a></li>
                        <li><a class="dropdown-item" href="index.php?page=listado_empleados">Empleados</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Vehículos
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gestión de Vehiculos</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=registrar_vehiculos">Registrar Vehículos</a></li>
                                <li><a class="dropdown-item" href="index.php?page=listado_vehiculos">Vehículos Disponibles</a></li>
                                <li><a class="dropdown-item" href="index.php?page=gestion_stock">Gestión de Stock</a></li>
                                <!-- <li><a class="dropdown-item" href="index.php?page=listado_precios">Actualización de Precios</a></li> -->
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gestión de Compras</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=listado_compras">Registrar Compras</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gestión de Ventas</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=listado_ventas">Registrar Ventas</a></li>
                            </ul>
                        </li>
                        <!-- <li><a class="dropdown-item" href="index.php?page=form_financiamiento">Gestión de Financiamiento</a></li> -->
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sistema
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gestión de Datos</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="vistas/form_datos_para_personas.php">Datos para Personas</a></li>
                                <li><a class="dropdown-item" href="vistas/form_datos_para_domicilios.php">Datos para Domicilios</a></li>
                                <li><a class="dropdown-item" href="vistas/form_datos_para_vehiculos.php">Datos para Vehículos</a></li>
                                <li><a class="dropdown-item" href="vistas/form_datos_para_operaciones.php">Datos para Operaciones</a></li>
                                <li><a class="dropdown-item" href="vistas/form_datos_para_perfiles.php">Datos para Perfiles</a></li>
                            </ul>
                        </li>
                        <li><a class="dropdown-item" href="index.php?page=listado_modulos">Asignación de Módulos</a></li>
                        <li><a class="dropdown-item" href="index.php?page=listado_usuarios">Creación de Usuarios</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php?page=reportes">Reportes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php?page=graficos">Estadísticas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=form_mis_datos">Mis Datos</a>
                </li>
                <?php
                session_start();

                // Convertir la fecha actual al formato Y-m-d
                $fecha_actual = date('Y-m-d');

                // Instancia de Usuario y obtener las nuevas notificaciones del día
                $registros = new Usuario();
                $resultado = $registros->nuevos_usuarios_registrados($fecha_actual);

                // Contar las notificaciones nuevas
                $total = $resultado->num_rows;

                // Verificar si ya se han marcado como vistas las notificaciones de hoy
                if (isset($_SESSION['viewed_notifications_date']) && $_SESSION['viewed_notifications_date'] === $fecha_actual) {
                    $new_notifications = 0;
                } else {
                    $new_notifications = $total;
                }
                ?>

                <!-- Notificación con ícono de campana -->
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationDropdown">
                        <i class="fa-regular fa-bell"></i>
                        <span class="badge bg-danger" id="label-count"><?php echo $new_notifications; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown"
                    style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-item text-center">Notificaciones</li>
                        <li><hr class="dropdown-divider"></li>
                        <div id="lista-notificaciones">
                            <?php if ($total > 0): ?>
                                <?php while ($usuario = $resultado->fetch_assoc()): ?>
                                    <li class="dropdown-item">
                                        <a href="index.php?page=ver_usuario&accion=ver_cliente&usuario=<?php echo $usuario['idusuarios']; ?>">
                                            Nuevo usuario registrado - Username: <?php echo htmlspecialchars($usuario['username']); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="dropdown-item">No hay notificaciones</li>
                            <?php endif; ?>
                        </div>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </li>




                <li class="nav-item">
                    <a class="nav-link" href="vistas/paginas/salida.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<script>
    // Habilitar el comportamiento de submenú desplegable
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownSubmenus = document.querySelectorAll('.dropdown-submenu');

        dropdownSubmenus.forEach(function(submenu) {
            submenu.addEventListener('mouseenter', function() {
                var dropdownMenu = submenu.querySelector('.dropdown-menu');
                dropdownMenu.classList.add('show');
            });

            submenu.addEventListener('mouseleave', function() {
                var dropdownMenu = submenu.querySelector('.dropdown-menu');
                dropdownMenu.classList.remove('show');
            });
        });
    });
</script>

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script>
Pusher.logToConsole = true;

var pusher = new Pusher('c28c5dc6a68db65e2590', {
    cluster: 'us2'
});

var channel = pusher.subscribe('notificaciones');

let badge = document.getElementById('label-count');
let lista = document.getElementById('lista-notificaciones');
let contador = parseInt(badge.textContent) || 0;

channel.bind('nuevo-evento', function(data) {
    contador++;
    badge.textContent = contador;

    let li = document.createElement('li');
    li.classList.add('dropdown-item', 'p-2', 'text-wrap');
    li.innerHTML = `
        <a href="index.php?page=ver_usuario&accion=ver_cliente&usuario=${data.idusuario}">
            <strong>[${data.tipo}]</strong> ${data.mensaje}<br>
            <small class="text-muted">${data.fecha}</small>
        </a>
    `;
    lista.prepend(li);
});

// Resetear contador al abrir el dropdown
document.getElementById('notificationDropdown').addEventListener('click', function() {
    contador = 0;
    badge.textContent = '0';
});
</script>







