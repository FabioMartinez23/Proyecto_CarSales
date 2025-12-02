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
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gestión de Ingresos</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=listado_compras">Registrar Ingreso Nuevo</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gestión de Ventas</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=listado_ventas">Registrar Ventas</a></li>
                            </ul>
                        </li>
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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Contabilidad
                    </a>

                    <ul class="dropdown-menu">

                        <!-- Gestión de Gastos -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gastos</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="index.php?page=registrar_gasto">
                                        <i class="fa-solid fa-money-bill-wave me-2 text-success"></i>
                                        Registrar Gasto
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?page=listado_gastos">
                                        <i class="fa-solid fa-list me-2 text-primary"></i>
                                        Listado de Gastos
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Tipos de gasto (CRUD) -->
                        <li>
                            <a class="dropdown-item" href="index.php?page=tipo_gasto">
                                <i class="fa-solid fa-tags me-2 text-warning"></i>
                                Administrar Tipos de Gasto
                            </a>
                        </li>

                        <!-- 🔹 SOLO ADMIN: Auditoría de anulaciones -->
                        <?php if (isset($_SESSION['descripcion']) && $_SESSION['descripcion'] === 'Administrador'): ?>
                            <li class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="#">
                                    <i class="fa-solid fa-file-circle-exclamation me-2 text-danger"></i>
                                    Auditoría
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="index.php?page=listado_auditoria">
                                            <i class="fa-solid fa-file-circle-exclamation me-2 text-danger"></i>
                                            Otras Auditorías
                                        </a>
                                    </li>
                                                                        <li>
                                        <a class="dropdown-item" href="index.php?page=listado_anulaciones">
                                            <i class="fa-solid fa-ban me-2 text-danger"></i>
                                            Anulaciones de operaciones
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Caja
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="index.php?page=listado_movimientos_caja">
                        <i class="fa-solid fa-cash-register me-2 text-primary"></i> Movimientos de Caja
                    </a></li>
                    <li><a class="dropdown-item" href="index.php?page=caja/cierres_caja">
                        <i class="fa-solid fa-cash-register me-2 text-primary"></i> Cierre de Caja
                    </a></li>
                </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php?page=reportes">Reportes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=form_mis_datos">Mis Datos</a>
                </li>

                <?php
                // 🔹 Notificaciones existentes (usuarios nuevos)
                $fecha_actual = date('Y-m-d');
                $registros = new Usuario();
                $resultado = $registros->nuevos_usuarios_registrados($fecha_actual);
                $total = $resultado->num_rows;

                if (isset($_SESSION['viewed_notifications_date']) && $_SESSION['viewed_notifications_date'] === $fecha_actual) {
                    $new_notifications = 0;
                } else {
                    $new_notifications = $total;
                }
                ?>

                <!-- 🔔 Notificación con ícono de campana (Pusher) -->
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationDropdown">
                        <i class="fa-regular fa-bell"></i>
                        <span class="badge bg-danger" id="label-count"><?php echo $new_notifications; ?></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="notificationDropdown" style="width: 300px;">
                        <li class="dropdown-item text-center">Notificaciones</li>
                        <li><hr class="dropdown-divider"></li>

                        <!-- Contenedor de notificaciones dinámicas -->
                        <ul id="lista-notificaciones" class="list-unstyled mb-0">
                            <?php if ($total > 0): ?>
                                <?php while ($usuario = $resultado->fetch_assoc()): ?>
                                    <li class="dropdown-item">
                                        <a href="index.php?page=ver_usuario&accion=ver_cliente&usuario=<?php echo $usuario['idusuarios']; ?>">
                                            Nuevo usuario - Username: <?php echo htmlspecialchars($usuario['username']); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="dropdown-item">No hay notificaciones</li>
                            <?php endif; ?>
                        </ul>

                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                    </ul>
                </li>

                <!-- 🔹 NUEVO: Notificación de documentación faltante -->
                <li class="nav-item">
                    <a class="nav-link position-relative" href="index.php?page=listado_falta_documentacion" id="docNotificationLink" title="Verificar documentación">
                        <i class="fa-solid fa-folder-open"></i>
                        <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle" id="doc-count" style="display:none;"></span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="vistas/paginas/salida.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- 🔹 Script para dropdowns -->
<script>
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

<!-- 🔔 Script Pusher (notificaciones existentes) -->
<script src="assets/js/pusher.min.js"></script>
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
    console.log("Notificación recibida:", data);

    contador++;
    badge.textContent = contador;

    let li = document.createElement('li');
    li.classList.add('dropdown-item');

    let detalle = data.detalle || {};
    let link = '#';

    switch (data.tipo) {
        case 'cliente':
            if (detalle.idusuario) link = `index.php?page=ver_usuario&accion=ver_cliente&usuario=${detalle.idusuario}`;
            break;
        case 'venta':
            if (detalle.idVenta) link = `index.php?page=listado_ventas&id=${detalle.idVenta}`;
            break;
        default:
            link = '#';
    }

    li.innerHTML = `
        <a href="${link}">
            [${data.tipo.toUpperCase()}] ${data.mensaje} 
            <br><small>${data.fecha}</small>
        </a>
    `;

    if (lista) lista.prepend(li);
});

document.getElementById('notificationDropdown').addEventListener('click', function() {
    contador = 0;
    badge.textContent = '0';
});
</script>

<!-- 🔹 Script para verificar documentación faltante -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const docCount = document.getElementById('doc-count');
    const docLink = document.getElementById('docNotificationLink');

    function verificarDocumentacion() {
        fetch('controladores/notificaciones/verificar_documentacion.php')
            .then(res => res.json())
            .then(data => {
                const faltantes = parseInt(data.faltantes) || 0;

                if (faltantes > 0) {
                    docCount.textContent = faltantes;
                    docCount.style.display = 'inline-block';
                    docLink.title = `Hay ${faltantes} vehículo(s) con documentación incompleta`;
                } else {
                    docCount.style.display = 'none';
                    docLink.title = 'Toda la documentación está completa';
                }
            })
            .catch(err => console.error('Error verificando documentación:', err));
    }

    verificarDocumentacion();
    setInterval(verificarDocumentacion, 300000); // cada 5 minutos
});
</script>







