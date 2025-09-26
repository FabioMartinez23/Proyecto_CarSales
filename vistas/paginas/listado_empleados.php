<?php

ini_set('display_errors', 1);

$total_registros = 0;

$perfil = new Perfil();
$perfiles = $perfil->traer_perfiles();

$usuario = new Usuario();
$filas_por_pagina = 5;  // Número de filas que se muestran por página
$total_registros = 0;
$pagina_actual = isset($_GET['pagina_actual']) ? (int)$_GET['pagina_actual'] : 1;

// Asegúrate de que la página actual nunca sea menor que 1
if ($pagina_actual < 1) {
    $pagina_actual = 1;
}
// Calcular el OFFSET
$inicio = ($pagina_actual - 1) * $filas_por_pagina;

$busqueda = ''; // Variable para la búsqueda
if (isset($_GET['buscador']) && !empty($_GET['buscador'])) {
    $busqueda = $_GET['buscador'];  // Recoge la búsqueda del input
    $result_usuarios = $usuario->consultar_usuario($busqueda); // Ejecuta la búsqueda
} else {
    // Si no hay búsqueda, carga todos los usuarios de forma predeterminada
    $result_usuarios_ = $usuario->traer_cantidad_usuario();
    foreach ($result_usuarios_ as $usuario_1) {
        $total_registros = $usuario_1['total'];
    }
    if (isset($_GET['pagina_actual'])) {
        $usuario->pagina_actual = $_GET['pagina_actual'];
    }
    $result_usuarios = $usuario->traer_empleados($inicio, $filas_por_pagina);
}

// Calcular el número total de páginas
$total_paginas = ceil($total_registros / $filas_por_pagina);

$tipo_sexo = new Tipo_Sexos();
$result_tipo_sexo = $tipo_sexo->traer_tipo_sexo();

$tipo_puesto = new Tipo_De_Puestos();
$tipos_puestos = $tipo_puesto->traer_tipo_puesto();

?>
    <!-- MODAL DE TIPO DE PUESTO PARA LEGAJO -->
    <div class="modal fade" id="modalGenerarLegajo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Asignar Legajo y Puesto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="formGenerarLegajo" method="POST" action="controladores/empleados/generar_legajo.controlador.php">
            <div class="modal-body">
            <input type="hidden" name="idempleados" id="idusuarios">
            
            <div class="mb-3">
                <label for="idtipo_puesto" class="form-label">Tipo de Puesto</label>
                <select class="form-select" name="idtipo_puesto" id="idtipo_puesto" required>
                <option value="">Seleccione...</option>
                <?php foreach ($tipos_puestos as $puesto) { ?>
                    <option value="<?= $puesto['idtipo_de_puestos']; ?>">
                        <?= $puesto['descripcion']; ?>
                    </option>
                <?php } ?>
                </select>
            </div>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Generar</button>
            </div>
        </form>
        </div>
    </div>
    </div>


<nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-glass">
        <li class="breadcrumb-item"><a href="#">Usuarios</a></li>
        <li class="breadcrumb-item active" aria-current="page">Empleados</li>
    </ol>
</nav>

<div class="col hacer_padding">

        <h1 class="text-center mb-4">Empleados</h1>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Botón de Registrar Nuevo Empleado -->
            <a type="button" class="btn btn-action" href="index.php?page=registrar_empleados&accion=registrar">Registrar Nuevo Empleado</a>

            <!-- Buscador -->
            <div class="d-flex">
                <input name="buscador" id="idbuscador" class="form-control form-control-sm me-2" type="search" placeholder="Buscar por Nombre - Apellido" aria-label="Buscar" style="width: 250px;">
                <button class="btn btn-success btn-sm" type="submit" onclick="buscador()">Buscar</button>
            </div>
        </div>    
        
        <table class="table table-hover">
            <thead>
                <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Nombre de Usuario</th>
                <th>Fecha de Alta</th>
                <th>Puesto</th>
                <th>Legajo</th>
                <th>Modificar</th>
                <th>Resetear Contraseña</th>
                <th>Eliminar</th>
                <th>Ver</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($result_usuarios as $usuario_){
                ?>
                <tr>
                <td><?= $usuario_['nombre']; ?></td>
                <td><?= $usuario_['apellido']; ?></td>
                <td><?= $usuario_['email']; ?></td>
                <td><?= $usuario_['username']; ?></td>
                <td><?= $usuario_['fecha_alta']; ?></td>
                <td><?= $usuario_['tipo_puesto'] ?? 'No asignado'; ?></td>
                <td>
                    <?php if (empty($usuario_['legajo'])): ?>
                        <button class="btn btn-sm btn-success generar-legajo-btn" 
                                data-id="<?= $usuario_['idusuarios']; ?>">
                            Generar Legajo
                        </button>
                    <?php else: ?>
                        <?= $usuario_['legajo']; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?page=registrar_empleados&idusuarios=<?=$usuario_['idusuarios']; ?>&nombre=<?=$usuario_['username'];?>" class="btn btn-success" type="button" title="Modificar Empleado">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </td>
                <td>
                    <form id="formulario-resetear-<?= $usuario_['idusuarios']; ?>" method="POST" action="controladores/usuarios/usuarios.controlador.php">
                        <input type="hidden" name="action" value="resetear_empleado">
                        <input type="hidden" name="idusuarios" value="<?= $usuario_['idusuarios'] ?>">
                        <button onclick="confirmarAccion(event, 'resetear', 'formulario-resetear-<?= $usuario_['idusuarios']; ?>')" class="btn btn-primary" type="button" title="Resetear Password"><i class="fa-solid fa-rotate-right"></i></button>
                    </form>
                </td>
                <td>
                    <form id="formulario-eliminar-<?= $usuario_['idusuarios']; ?>" method="POST" action="controladores/usuarios/usuarios.controlador.php">
                        <input type="hidden" name="action" value="eliminar_empleado">
                        <input type="hidden" name="idusuarios" value="<?= $usuario_['idusuarios'] ?>">
                        <button onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $usuario_['idusuarios']; ?>')" class="btn btn-danger" type="button" title="Eliminar Empleado"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
                <td>
                    <a href="index.php?page=ver_usuario&usuario=<?=$usuario_['idusuarios']; ?>" class="btn btn-info" type="button" title="Ver Usuario">
                        <i class="fa-regular fa-eye"></i>
                    </a>
                </td>
                </tr>
                        <?php
                        }
                        ?>
            </tbody>
        </table>

        <!-- Paginación centrada -->
        <nav aria-label="..." class="d-flex justify-content-center">
            <ul class="pagination">
                <!-- Botón "Anterior" -->
                <li class="page-item <?php if ($pagina_actual <= 1) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?page=listado_empleados&pagina_actual=<?= $pagina_actual - 1 ?>" aria-disabled="true">Previo</a>
                </li>

                <!-- Botones de número de página -->
                <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                    <li class="page-item <?php if ($pagina_actual == $i) { echo 'active'; } ?>">
                        <a class="page-link" href="index.php?page=listado_empleados&pagina_actual=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php } ?>

                <!-- Botón "Siguiente" -->
                <li class="page-item <?php if ($pagina_actual >= $total_paginas) { echo 'disabled'; } ?>">
                    <a class="page-link" href="index.php?page=listado_empleados&pagina_actual=<?= $pagina_actual + 1 ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<script>
    function buscador(){
        let buscador = document.getElementById('idbuscador').value;
        console.log(buscador);
        location.href='index.php?page=listado_empleados&buscador='+buscador;
    }

</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".generar-legajo-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            let idempleados = btn.dataset.id;
            document.getElementById("idusuarios").value = idempleados;
            let modal = new bootstrap.Modal(document.getElementById("modalGenerarLegajo"));
            modal.show();
        });
    });
});
</script>