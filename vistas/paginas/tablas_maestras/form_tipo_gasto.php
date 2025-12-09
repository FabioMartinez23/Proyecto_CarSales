<?php

ini_set('display_errors', 1);

$tipo_gasto = new Tipo_Gasto();

$result_tipo_gasto = $tipo_gasto->traer_tipo_gasto();

$tipo_gasto_editar = null;
if (isset($_GET['idtipo_gasto'])) {
    $tipo_gasto_editar = $tipo_gasto->traer_tipo_gasto_id($_GET['idtipo_gasto']);
}

// Valores para selects
$aplica_en_opciones = [
    'manual'   => 'Manual',
    'venta'    => 'Venta',
    'vehiculo' => 'Vehículo',
    'empleado' => 'Empleado',
    'taller'   => 'Taller'
];

$modo_calculo_opciones = [
    'monto_fijo'                 => 'Monto fijo',
    'porcentaje_sobre_venta'     => 'Porcentaje sobre venta',
    'porcentaje_sobre_comision'  => 'Porcentaje sobre comisión'
];

?>

<div class="row hacer_padding">
    <!-- FORMULARIO -->
    <div class="col">
        <h2><?= isset($tipo_gasto_editar) ? 'Modificar Tipo de Gasto' : 'Registrar Tipo de Gasto'; ?></h2>

        <form id="id_form_tipo_gasto" method="POST" action="../controladores/tablas_maestras/tipo_gasto.controlador.php">
            <?php if (isset($tipo_gasto_editar)) { ?>
                <input type="hidden" name="action" value="modificar"/>
                <input type="hidden" name="idtipo_gasto" value="<?= $tipo_gasto_editar['idtipo_gasto']; ?>"/>
            <?php } else { ?>
                <input type="hidden" name="action" value="guardar"/>
            <?php } ?>

            <!-- Descripción -->
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción del Gasto:</label>
                <input
                    type="text"
                    name="descripcion"
                    id="descripcion"
                    class="form-control"
                    value="<?= isset($tipo_gasto_editar) ? htmlspecialchars($tipo_gasto_editar['descripcion']) : ''; ?>"
                >
            </div>

            <!-- Aplica en -->
            <div class="mb-3">
                <label for="aplica_en" class="form-label">Aplica en:</label>
                <select name="aplica_en" id="aplica_en" class="form-select">
                    <?php
                    $aplica_actual = $tipo_gasto_editar['aplica_en'] ?? 'manual';
                    foreach ($aplica_en_opciones as $valor => $texto) {
                        $selected = ($aplica_actual === $valor) ? 'selected' : '';
                        echo "<option value='$valor' $selected>$texto</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Modo de cálculo -->
            <div class="mb-3">
                <label for="modo_calculo" class="form-label">Modo de cálculo:</label>
                <select name="modo_calculo" id="modo_calculo" class="form-select">
                    <?php
                    $modo_actual = $tipo_gasto_editar['modo_calculo'] ?? 'monto_fijo';
                    foreach ($modo_calculo_opciones as $valor => $texto) {
                        $selected = ($modo_actual === $valor) ? 'selected' : '';
                        echo "<option value='$valor' $selected>$texto</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Valor de cálculo -->
            <div class="mb-3">
                <label for="valor_calculo" class="form-label">Valor de cálculo:</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="valor_calculo"
                    id="valor_calculo"
                    class="form-control"
                    value="<?= isset($tipo_gasto_editar) ? htmlspecialchars($tipo_gasto_editar['valor_calculo']) : '0.00'; ?>"
                >
                <small class="form-text text-muted">
                    Si el modo es porcentaje, usar valores como 10.00 (equivale a 10%).
                </small>
            </div>

            <button type="submit" class="btn btn-primary">
                Guardar
            </button>
        </form>
    </div>

    <!-- LISTADO -->
    <div class="col">
        <h2>Listado de Tipos de Gasto</h2>      
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Aplica en</th>
                    <th>Modo cálculo</th>
                    <th>Valor</th>
                    <th>Modificar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_tipo_gasto && $result_tipo_gasto->num_rows > 0): ?>
                    <?php while ($row = $result_tipo_gasto->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['descripcion']); ?></td>
                            <td><?= htmlspecialchars($row['aplica_en']); ?></td>
                            <td><?= htmlspecialchars($row['modo_calculo']); ?></td>
                            <td><?= number_format($row['valor_calculo'], 2, ',', '.'); ?></td>

                            <!-- Modificar -->
                            <td>
                                <a 
                                    href="index.php?page=tablas_maestras/form_tipo_gasto&idtipo_gasto=<?= $row['idtipo_gasto']; ?>" 
                                    class="btn btn-success" 
                                    type="button"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>

                            <!-- Eliminar -->
                            <td>
                                <form id="formulario-eliminar-<?= $row['idtipo_gasto']; ?>" method="POST" action="../controladores/tablas_maestras/tipo_gasto.controlador.php">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="idtipo_gasto" value="<?= $row['idtipo_gasto']; ?>">
                                    <button 
                                        onclick="confirmarAccion(event, 'eliminar', 'formulario-eliminar-<?= $row['idtipo_gasto']; ?>')" 
                                        class="btn btn-danger" 
                                        type="submit"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay tipos de gasto cargados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../assets/js/validaciones/tablas_maestras.validaciones.ajax.js"></script>
