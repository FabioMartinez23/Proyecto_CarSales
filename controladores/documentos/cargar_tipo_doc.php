<?php
require_once('../../modelos/documentaciones.php');

$idvehiculo = $_GET['idvehiculo'] ?? 0;
$documentacion = new Documentacion();
$docs = $documentacion->traer_doc_completa_vehiculo($idvehiculo);

if ($docs && $docs->num_rows > 0): ?>
    <div class="row">
        <?php while ($doc = $docs->fetch_assoc()):
            $checked = ($doc['estado_doc'] == 1) ? 'checked' : '';
            $idTipo = $doc['idtipo_documentacion'];
        ?>
        <div class="col-md-6 mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input switch-doc"
                       type="checkbox"
                       id="doc_<?= $idTipo ?>"
                       data-idtipo="<?= $idTipo ?>"
                       <?= $checked ?>>
                <label class="form-check-label fw-semibold" for="doc_<?= $idTipo ?>">
                    <?= htmlspecialchars($doc['tipo_doc']) ?>
                </label>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p class="text-muted">No hay tipos de documentación definidos.</p>
<?php endif; ?>
