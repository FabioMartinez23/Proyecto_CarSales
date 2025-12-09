<?php

ini_set('display_errors', 1);

$total_registros = 0;

$perfil = new Perfil();
$perfiles = $perfil->traer_perfiles();

$usuario1 = new Usuario();
$usuario_editar = [];
if (isset($_GET['idusuarios'])) {
    $usuario_editar = $usuario1->traer_usuario_por_id($_GET['idusuarios']);
}




$usuario = new Usuario();
$busqueda = ''; // Variable para la búsqueda
if (isset($_GET['buscador']) && !empty($_GET['buscador'])) {
    $busqueda = $_GET['buscador'];  // Recoge la búsqueda del input
    $result_usuarios = $usuario->consultar_usuario($busqueda); // Ejecuta la búsqueda
} else {
    // Manejo de filtro por perfil
    $filtro_perfil = $_GET['filtro_perfil'] ?? '';

    if (!empty($filtro_perfil)) {
        $result_usuarios = $usuario->traer_usuarios_por_perfil($filtro_perfil);
    } else {
        // Paginación
        $filas_por_pagina = 10;
        $usuario->pagina_actual = isset($_GET['pagina_actual']) ? intval($_GET['pagina_actual']) : 0;
        $inicio = $usuario->pagina_actual * $filas_por_pagina;

        // Obtener el total de registros
        $result_usuarios_total = $usuario->traer_cantidad_usuario();
        if ($usuario_ = $result_usuarios_total->fetch_assoc()) {
            $total_registros = $usuario_['total'];
        }

        // Traer usuarios con paginación
        $result_usuarios = $usuario->traer_usuarios($inicio, $filas_por_pagina);
    }
}
$tipo_sexo = new Tipo_Sexos();
$result_tipo_sexo = $tipo_sexo->traer_tipo_sexo();

?>

<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Sistemas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Creación de Usuarios</li>
    </ol>
</nav>


<div class="row hacer_padding">
    <h1 class="text-center mb-4">Creación de Usuarios</h1>
    <div class="col-md-3 mb-4">
        <h2 class="text-center">Registrar Usuario</h2>

        <form method="POST" action="controladores/usuarios/usuarios.controlador.php">
        <?php if(isset($_GET['idusuarios'])){ ?>
                <input type="hidden" name="action" value="actualizar"/>
                <input type="hidden" name="idusuarios" value="<?= $_GET['idusuarios'] ?>"/>
                <input type="hidden" name="idpersonas" value="<?= $usuario_editar['personas_idpersonas'] ?? '' ?>">
            <?php } else { ?>
                <input type="hidden" name="action" value="guardar"/>
            <?php } ?>

            <div class="form-floating mb-3 mt-2">
                <input value="<?= $usuario_editar['nombre'] ?? '' ?>" type="text" name="nombre" class="form-control" id="id_nombre" aria-describedby="emailHelp">
                <label for="exampleInputEmail1" class="form-label">Nombre</label>
            </div>

            <div class="form-floating mb-3 mt-2">
                <input value="<?= $usuario_editar['apellido'] ?? '' ?>" type="text" name="apellido" class="form-control" id="id_apellido" aria-describedby="emailHelp">
                <label for="exampleInputEmail1" class="form-label">Apellido</label>
            </div>

            <div class="form-floating mb-3 mt-2">
                <input value="<?= $usuario_editar['fecha_nacimiento'] ?? '' ?>" type="date" name="fecha_nacimiento" class="form-control" id="id_fecha_nacimiento" aria-describedby="emailHelp" onchange="verificarEdad()">
                <label for="exampleInputEmail1" class="form-label">Fecha de Nacimiento</label>
            </div>

            <div class="form-floating mb-3 mt-2">
                <select name="tipo_sexo_idtipo_sexo" id="id_tipo_sexo" class="form-select">
                    <option value="">Seleccione un Sexo</option>
                    <?php foreach($result_tipo_sexo as $sexo): ?>
                        <option value="<?= $sexo['idtipo_sexo'] ?>"
                            <?= (isset($usuario_editar['tipo_sexo_idtipo_sexo']) && $usuario_editar['tipo_sexo_idtipo_sexo'] == $sexo['idtipo_sexo']) ? 'selected' : '' ?>>
                            <?= $sexo['descripcion'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="id_tipo_sexo">Sexo</label>
            </div>

            <div class="form-floating mb-3 mt-2">
                <input value="<?= $usuario_editar['username'] ?? '' ?>" type="text" name="username" onfocusout="validate_username(event)" class="form-control" id="id_username" aria-describedby="emailHelp">
                <label for="exampleInputEmail1" class="form-label">Nombre de Usuario</label>
            </div>

            <div class="form-floating mb-3 mt-2">
                <input value="<?= $usuario_editar['email'] ?? '' ?>" type="email" name="email" onfocusout="validate_email(event)" class="form-control" id="id_email" aria-describedby="emailHelp">
                <label for="exampleInputEmail1" class="form-label">Email</label>
            </div>

            <div class="form-floating mb-3 mt-2">
                <select name="perfiles_idperfiles" id="id_perfiles" class="form-select">
                    <option value="">Seleccione un Perfil</option>
                    <?php foreach ($perfiles as $p): ?>
                        <option value="<?= $p['idperfiles'] ?>"
                            <?= (isset($usuario_editar['perfiles_idperfiles']) && $usuario_editar['perfiles_idperfiles'] == $p['idperfiles']) ? 'selected' : '' ?>>
                            <?= $p['descripcion'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="id_perfiles">Perfiles</label>
            </div>

            <button type="submit" class="btn btn-primary btn-action" onclick="return validarFormulario()">
                Guardar
            </button>
        </form>
    </div>

    <div class="col">
        <h2 class="text-center mb-4">Usuarios</h2>
        
        <div class="d-flex justify-content-end mb-4">
            <input name="buscador" id="idbuscador" class="form-control form-control-sm me-2" type="search" placeholder="Buscar por Nombre - Apellido" aria-label="Buscar" style="width: 250px;" onkeydown="if(event.key === 'Enter'){ buscador(); }">
            <button class="btn btn-success btn-sm" type="submit" onclick="buscador()">Buscar</button>
        </div>
        <div id="filtro" class="d-flex justify-content-end mb-4">
            <select name="filtro_perfil" id="id_filtro_perfil" class="form-select form-select-sm me-2" style="width: 200px;">
                <option value="">Filtrar por Perfil</option>
                <?php foreach ($perfiles as $p): ?>
                    <option value="<?= $p['idperfiles'] ?>"><?= $p['descripcion'] ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary btn-sm" type="button" onclick="filtrarUsuarios()">Filtrar</button>
        </div>

        <table class="table table-hover text-center align-middle">
            <thead>
                <tr>
                <th>Nombre de Usuario</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Modificar</th>
                <th>Resetear Contraseña</th>
                <th>Eliminar</th>
                <th>Ver Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_usuarios as $usuario_){
                ?>
                <tr>
                <td><?= $usuario_['username']; ?></td>
                <td><?= $usuario_['email']; ?></td>
                <td><?= $usuario_['descripcion']; ?></td>
                <td>
                    <a href="index.php?page=listado_usuarios&idusuarios=<?=$usuario_['idusuarios']; ?>&nombre=<?=$usuario_['username'];?>" class="btn btn-success" type="button" title="Modificar Usuario">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>
                <td>
                    <form id="formulario-resetear-<?= $usuario_['idusuarios']; ?>" method="POST" action="controladores/usuarios/usuarios.controlador.php">
                        <input type="hidden" name="action" value="resetear">
                        <input type="hidden" name="idusuarios" value="<?= $usuario_['idusuarios'] ?>">
                        <button onclick="confirmarAccion(event, 'resetear', 'formulario-resetear-<?= $usuario_['idusuarios']; ?>')" class="btn btn-primary" type="button"><i class="fa-solid fa-rotate-right" title="Resetear Password"></i></button>
                    </form>
                </td>
                <td>
                    <form id="formulario-eliminar-<?= $usuario_['idusuarios']; ?>" method="POST" action="controladores/usuarios/usuarios.controlador.php">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="idusuarios" value="<?= $usuario_['idusuarios'] ?>">
                        <input type="hidden" name="perfil" value="<?= $usuario_['descripcion'] ?>">
                        <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $usuario_['idusuarios']; ?>')" class="btn btn-danger" type="button" title="Eliminar Usuario"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                <td>
                    <a href="index.php?page=ver_usuario&accion=ver_cliente&usuario=<?=$usuario_['idusuarios']; ?>" class="btn btn-info" type="button" title="Ver Usuario">
                        <i class="fa-regular fa-eye"></i>
                    </a>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>

        <nav aria-label="...">
            <ul class="pagination justify-content-center">
            <li class="page-item <?php if($usuario->pagina_actual == 0){echo 'disabled';} ?>"> <!-- disabled -->
                <a class="page-link" href="index.php?page=listado_usuarios&pagina_actual=<?= $usuario->pagina_actual - 1 ?>">Previo</a>
            </li>
            <li class="page-item"><a class="page-link" href="index.php?page=listado_usuarios&pagina_actual=<?= $usuario->pagina_actual - 1 ?>"><?= $usuario->pagina_actual ?></a></li>
            <li class="page-item"><a class="page-link" href="index.php?page=listado_usuarios&pagina_actual=<?= $usuario->pagina_actual ?>"><?= $usuario->pagina_actual + 1 ?></a></li>
            <li class="page-item"><a class="page-link" href="index.php?page=listado_usuarios&pagina_actual=<?= $usuario->pagina_actual + 1 ?>"><?= $usuario->pagina_actual + 2 ?></a></li>
            <li class="page-item <?php if($usuario->pagina_actual == $total_registros - 1){echo 'disabled';} ?>">
                <a class="page-link" href="index.php?page=listado_usuarios&pagina_actual=<?= $usuario->pagina_actual + 1 ?>">Siguiente</a>
            </li>
            </ul>
        </nav>
    </div>
</div>

<script src="assets/js/validaciones/verificar_edad.js"></script>
<script src="assets/js/validaciones/usuarios.js"></script>
<script src="assets/js/validaciones/email.js"></script>

<script>
    function buscador() {
        let buscador = document.getElementById('idbuscador').value;
        location.href = 'index.php?page=listado_usuarios&buscador=' + encodeURIComponent(buscador);
    }

    function filtrarUsuarios() {
        const filtroPerfil = document.getElementById('id_filtro_perfil').value;
        let url = 'index.php?page=listado_usuarios';

        if (filtroPerfil) {
            url += `&filtro_perfil=${filtroPerfil}`;
        }
        window.location.href = url;
    }
</script>
