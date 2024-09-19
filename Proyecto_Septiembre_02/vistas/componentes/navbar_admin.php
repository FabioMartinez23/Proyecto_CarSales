<nav class="navbar navbar-expand-lg bg-body-tertiary">
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
                <li><a class="dropdown-item" href="index.php?page=listado_usuarios">Creacion de Usuarios</a></li>
                <li><a class="dropdown-item" href="index.php?page=listado_modulos">Asignacion de Modulos</a></li>
            </ul>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Vehiculos
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="index.php?page=listado_vehiculos">Listado de Vehiculos</a></li>
                <li><a class="dropdown-item" href="#">Listado de Compras</a></li>
                <li><a class="dropdown-item" href="#">Listado de Ventas</a></li>
            </ul>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Sistema
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="vistas/form_tablas_maestras.php">Tablas Maestras</a></li>
                <li><a class="dropdown-item" href="#">Registros Empleados</a></li>
                <li><a class="dropdown-item" href="#">Registros Compras</a></li>
            </ul>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="index.php?page=formulario_perfil">Mis Datos</a>
            </li>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="index.php?page=salida">Cerrar Sesion</a>
            </li>
        </ul>
        </div>
    </div>
</nav>

</header>
