<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("modelos/gastos_generales.php");

$gasto = new GastoGeneral();

/* ================================
   FILTROS PRINCIPALES
================================ */
$desde       = $_GET['desde']       ?? '';
$hasta       = $_GET['hasta']       ?? '';
$tipo_gasto  = $_GET['tipo_gasto']  ?? '';
$origen      = $_GET['origen']      ?? '';

/* ================================
   FILTROS AVANZADOS POR RELACIÓN
================================ */
$idvehiculo  = $_GET['idvehiculo']  ?? '';
$idventa     = $_GET['idventa']     ?? '';
$idempleado  = $_GET['idempleado']  ?? '';

/* ================================
   TRAER TIPOS DE GASTO PARA FILTRO
================================ */
$tipos = $gasto->traer_tipos_gasto();


// ===============================================
// PAGINACIÓN
// ===============================================
$filas_por_pagina = 10;
$pagina_actual = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
$offset = ($pagina_actual - 1) * $filas_por_pagina;

// Total de filas
$total_gastos = $gasto->contar_gastos($desde, $hasta);
$total_paginas = ceil($total_gastos / $filas_por_pagina);

/* ================================
   TRAER GASTOS CON FILTROS DE FECHA
================================ */
$gastos = $gasto->traer_gastos($desde, $hasta, $filas_por_pagina, $offset);
?>

<!-- ================================
     CSS PERSONALIZADO
================================ -->
<link rel="stylesheet" href="assets/css/gastos.css">

    <!-- Breadcrumb -->
    <nav style="--bs-breadcrumb-divider: ;" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-glass">
            <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>

            <?php 
            if ($_GET['e'] == 'lv') {
                echo '<li class="breadcrumb-item"><a href="index.php?page=listado_vehiculos">Listado Vehiculos</a></li>';
            } elseif ($_GET['e'] == 'lfc') {
                echo '<li class="breadcrumb-item"><a href="index.php?page=listado_falta_documentacion">Listado Falta Documentacion</a></li>';
            } else {
                echo '<li class="breadcrumb-item"><a href="#">Gastos</a></li>';
            }
            ?>

            <li class="breadcrumb-item active" aria-current="page">Listado de Gastos</li>
        </ol>
    </nav>

<div class="gastos-container">

    <div class="gastos-box">

        <!-- Título -->
        <h4 class="gastos-title">
            <i class="fa-solid fa-wallet me-2"></i> Listado de Gastos
        </h4>

        <!-- ================================
             FILTROS
        ================================= -->
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="listado_gastos">

            <div class="row mb-4">

                <!-- Desde -->
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control" name="desde" value="<?= $desde ?>">
                </div>

                <!-- Hasta -->
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="hasta" value="<?= $hasta ?>">
                </div>

                <!-- Tipo -->
                <div class="col-md-3">
                    <label class="form-label">Tipo de gasto</label>
                    <select class="form-select" name="tipo_gasto">
                        <option value="">Todos</option>
                        <?php while ($tg = $tipos->fetch_assoc()) { ?>
                            <option value="<?= $tg['idtipo_gasto'] ?>"
                                <?= ($tipo_gasto == $tg['idtipo_gasto']) ? 'selected' : '' ?>>
                                <?= $tg['descripcion'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Origen -->
                <div class="col-md-3">
                    <label class="form-label">Origen</label>
                    <select class="form-select" name="origen">
                        <option value="">Todos</option>
                        <option value="general"  <?= $origen == 'general'  ? 'selected' : '' ?>>General</option>
                        <option value="vehiculo" <?= $origen == 'vehiculo' ? 'selected' : '' ?>>Vehículo</option>
                        <option value="venta"    <?= $origen == 'venta'    ? 'selected' : '' ?>>Venta</option>
                        <option value="empleado" <?= $origen == 'empleado' ? 'selected' : '' ?>>Empleado</option>
                    </select>
                </div>

            </div>

            <!-- FILTROS POR ID DE RELACIÓN -->
            <div class="row mb-4">

                <div class="col-md-4">
                    <label class="form-label">Vehículo ID (opcional)</label>
                    <input type="number" name="idvehiculo" class="form-control" value="<?= $idvehiculo ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Venta ID (opcional)</label>
                    <input type="number" name="idventa" class="form-control" value="<?= $idventa ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Empleado ID (opcional)</label>
                    <input type="number" name="idempleado" class="form-control" value="<?= $idempleado ?>">
                </div>

            </div>

            <button class="gastos-btn-save mb-4" type="submit">
                <i class="fa-solid fa-filter me-2"></i> Aplicar filtros
            </button>
        </form>


        <!-- ================================
             TABLA DE GASTOS
        ================================= -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Tipo</th>
                        <th>Origen</th>
                        <th>Relacionado con</th>
                        <th>Estado</th>   <!-- NUEVO -->
                        <th></th>         <!-- Acciones -->
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $total = 0;

                    if ($gastos && $gastos->num_rows > 0) {

                        while ($row = $gastos->fetch_assoc()) {

                            /* ================================
                               FILTROS DINÁMICOS
                            ================================ */

                            // Tipo de gasto
                            if ($tipo_gasto != '' && $row['tipo_gasto_idtipo_gasto'] != $tipo_gasto) continue;

                            // Origen
                            if ($origen != '' && $row['origen'] != $origen) continue;

                            // Gasto por vehículo
                            if ($idvehiculo != '' && $row['vehiculos_idvehiculos'] != $idvehiculo) continue;

                            // Gasto por venta
                            if ($idventa != '' && $row['ventas_idventas'] != $idventa) continue;

                            // Gasto por empleado
                            if ($idempleado != '' && $row['empleados_idempleados'] != $idempleado) continue;

                            $total += $row['monto'];
                    ?>
                    <tr>
                        <td><?= date("d/m/Y", strtotime($row['fecha_gasto'])) ?></td>

                        <td>
                            <?= $row['descripcion'] ?>
                            <br>
                            <?php if (str_starts_with(trim($row['descripcion']), "[Automático]")) { ?>
                                <span class="badge bg-primary">Automático</span>
                            <?php } else { ?>
                                <span class="badge bg-secondary">Manual</span>
                            <?php } ?>
                        </td>

                        <td><strong>$<?= number_format($row['monto'], 2, ',', '.') ?></strong></td>
                        <td><?= $row['tipo_gasto'] ?></td>
                        <td><?= ucfirst($row['origen']) ?></td>

                        <td>
                            <?php
                            if ($row['origen'] == 'vehiculo') {
                                echo "Vehículo ID: <strong>{$row['vehiculos_idvehiculos']}</strong>";
                            }
                            elseif ($row['origen'] == 'venta') {
                                echo "Venta ID: <strong>{$row['ventas_idventas']}</strong>";
                            }
                            elseif ($row['origen'] == 'empleado') {
                                echo "Empleado ID: <strong>{$row['empleados_idempleados']}</strong>";
                            }
                            else {
                                echo "-";
                            }
                            ?>
                        </td>

                        <!-- ESTADO DEL GASTO -->
                        <td>
                            <?php
                            $estado = $row['estado_gasto'] ?? 'Registrado';
                            if ($estado === 'Registrado') {
                                echo '<span class="badge bg-success-subtle text-success">Registrado</span>';
                            } else {
                                echo '<span class="badge bg-danger-subtle text-danger">Anulado</span>';
                            }
                            ?>
                        </td>

                        <!-- ACCIONES -->
                        <td>
                            <!-- Ver detalle (a futuro) -->
                            <button class="btn btn-sm btn-primary" title="Ver detalle">
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            <?php
                            // Solo permitir anular si está registrado y el usuario es admin
                            $esAdmin = isset($_SESSION['descripcion']) && $_SESSION['descripcion'] === "Administrador";
                            if ($estado === 'Registrado' && $esAdmin): ?>
                                <a href="index.php?page=anular_gasto&idgasto=<?= $row['idgastos_generales']; ?>"
                                class="btn btn-sm btn-outline-danger ms-1"
                                title="Anular gasto">
                                    <i class="fa-solid fa-ban"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php } } else { ?>

                    <tr>
                        <td colspan="7" class="text-center text-muted p-4">
                            No se encontraron gastos con los filtros aplicados.
                        </td>
                    </tr>

                    <?php } ?>
                </tbody>

            </table>
            <?php if ($total_paginas > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">

                        <!-- Botón Anterior -->
                        <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link"
                            href="<?= http_build_query(array_merge($_GET, ['pag' => $pagina_actual - 1])) ?>">
                                &laquo; Anterior
                            </a>
                        </li>

                        <!-- Páginas -->
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                                <a class="page-link"
                                href="index.php?<?= http_build_query(array_merge($_GET, ['pag' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Botón Siguiente -->
                        <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                            <a class="page-link"
                            href="<?= http_build_query(array_merge($_GET, ['pag' => $pagina_actual + 1])) ?>">
                                Siguiente &raquo;
                            </a>
                        </li>

                    </ul>
                </nav>
            <?php endif; ?>

        </div>

        <!-- TOTAL -->
        <div class="mt-4 text-end">
            <h5>Total filtrado: 
                <span class="badge bg-success">$<?= number_format($total, 2, ',', '.') ?></span>
            </h5>
        </div>

    </div>

</div>