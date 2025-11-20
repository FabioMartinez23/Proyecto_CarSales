<?php
require_once('../../modelos/documentaciones.php');
require_once('../../modelos/tablas_maestras/tipo_documentacion.php');
require_once('../../modelos/conexion.php');

$idvehiculo = intval($_GET['idvehiculo']);

$doc        = new Documentacion();
$tipo_doc   = new Tipo_Documentacion();

/* ============================================================
   1) Lista completa de tipos de documentación (excepto imagen)
   ============================================================ */
$tipos = $tipo_doc->traer_todos_los_tipos();

/* ============================================================
   2) Documentación existente del vehículo
   ============================================================ */
$docs_existentes = $doc->traer_doc_completa_vehiculo($idvehiculo);

$estado_actual = [];  // idtipo_documentacion => estado_doc

if ($docs_existentes) {
    while ($fila = $docs_existentes->fetch_assoc()) {
        // Puede venir NULL si nunca fue registrado → tratamos como 0 (faltante)
        $estado_actual[$fila['idtipo_documentacion']] = intval($fila['estado_doc'] ?? 0);
    }
}

/* ============================================================
   3) Construcción del HTML
   ============================================================ */
$html = '';

foreach ($tipos as $t) {

    // excluimos imágenes
    if ($t['descripcion'] === 'imagen') continue;

    $idtipo  = $t['idtipo_documentacion'];
    $checked = (!empty($estado_actual[$idtipo]) && $estado_actual[$idtipo] == 1)
                ? "checked"
                : "";

    $html .= "
        <div class='doc-item'>
            <div>
                <span class='doc-name'>{$t['descripcion']}</span>
                " . ($checked ? "<span class='doc-status ok'>Completado</span>" 
                            : "<span class='doc-status pending'>Faltante</span>") . "
            </div>

            <label class='form-check form-switch'>
                <input class='form-check-input switch-doc'
                    type='checkbox'
                    data-idtipo='{$idtipo}'
                    {$checked}>
            </label>
        </div>
        ";
}

echo $html;
exit;
?>
