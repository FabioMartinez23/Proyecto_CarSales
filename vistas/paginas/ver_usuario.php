<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$usuario = new Usuario();
$resultado_usuario = $usuario->traer_usuario_por_id($_GET['usuario']);

if ($resultado_usuario) { 

    // Perfil del usuario (Administrador / Empleado / Cliente)
    $perfil_nombre = $resultado_usuario['nombre_perfil'] ?? 'Sin perfil';
    $perfil_slug   = strtolower(str_replace(' ', '-', $perfil_nombre));

    // Administrador / Empleado tienen datos laborales
    $perfil_normalizado   = strtolower($perfil_nombre);
    $tieneDatosLaborales  = in_array($perfil_normalizado, ['administrador', 'empleado']);
?>

<main class="dashboard-main hacer_padding">
    <div class="view-container-centered">
        <!-- HEADER -->
        <header class="view-header mb-4">
            <h1 class="view-title">
                Detalle del Usuario
            </h1>
            <p class="view-subtitle">
                Información completa del perfil seleccionado
            </p>

            <!-- Badge de perfil -->
            <div class="user-role-wrapper">
                <span class="user-role-badge user-role-<?= $perfil_slug; ?>">
                    <?= htmlspecialchars($perfil_nombre); ?>
                </span>
            </div>
        </header>

        <div class="user-detail-grid">

            <!-- DATOS PERSONALES -->
            <section class="detail-card">
                <h2 class="detail-title">Datos Personales</h2>
                <ul class="detail-list">
                    <li><span>Perfil:</span> <?= htmlspecialchars($perfil_nombre); ?></li>
                    <li><span>Nombre:</span> <?=$resultado_usuario['nombre']; ?></li>
                    <li><span>Apellido:</span> <?=$resultado_usuario['apellido']; ?></li>
                    <li><span>DNI:</span> <?=$resultado_usuario['valor_documento']; ?></li>
                    <li><span>Fecha de Nacimiento:</span> <?=$resultado_usuario['fecha_nacimiento']; ?></li>
                    <li><span>Sexo:</span> <?=$resultado_usuario['nombre_tipo_sexo']; ?></li>
                </ul>
            </section>

            <!-- DATOS DE CONTACTO -->
            <section class="detail-card">
                <h2 class="detail-title">Datos de Contacto</h2>
                <ul class="detail-list">
                    <li><span>Tipo de Contacto:</span> <?=$resultado_usuario['nombre_tipo_contacto']; ?></li>
                    <li><span>Contacto:</span> <?=$resultado_usuario['valor_contacto']; ?></li>
                    <li><span>Email:</span> <?=$resultado_usuario['email']; ?></li>
                </ul>
            </section>

            <!-- DATOS LABORALES (solo Admin / Empleado) -->
            <?php if ($tieneDatosLaborales): ?>
            <section class="detail-card full-width">
                <h2 class="detail-title">Datos Laborales</h2>
                <ul class="detail-list">
                    <li>
                        <span>Legajo:</span>
                        <?= $resultado_usuario['legajo'] ?? '—'; ?>
                    </li>
                    <li>
                        <span>Puesto:</span>
                        <?= $resultado_usuario['nombre_puesto'] ?? '—'; ?>
                    </li>
                </ul>
            </section>
            <?php endif; ?>

            <!-- DOMICILIO -->
            <section class="detail-card full-width">
                <h2 class="detail-title">Domicilio</h2>
                <ul class="detail-list">
                    <li>
                        <span>Tipo de Domicilio:</span> <?=$resultado_usuario['nombre_tipo_domicilio']; ?>
                    </li>
                    <li>
                        <span>Dirección:</span> 
                        <?=$resultado_usuario['nombre_domicilio'].' - Barrio '.$resultado_usuario['nombre_barrio'].' - '.$resultado_usuario['nombre_localidad'].' - '.$resultado_usuario['nombre_provincia']; ?>
                    </li>
                </ul>
            </section>

        </div>

        <!-- BOTONES -->
        <div class="detail-actions mt-4">
            <?php if(isset($_GET['accion']) && $_GET['accion'] == 'ver_cliente'){ ?>
                <a href="index.php?page=listado_clientes" class="btn btn-dark">Volver</a>
            <?php } else { ?>
                <a href="index.php?page=listado_empleados" class="btn btn-dark">Volver</a>
            <?php } ?>

            <a href="reportes_pdf/ver_usuarios.php?usuario=<?= $_GET['usuario']; ?>" target="_blank" class="btn btn-success">
                Descargar en PDF
            </a>
        </div>
    </div>
</main>

<?php 
} else {
    echo "<div class='alert alert-danger mt-5'>No se encontró ningún usuario o no completó su perfil.</div>";
}
?>
