<?php
require_once('../../modelos/documentaciones.php');

$idvehiculo = $_GET['idvehiculo'] ?? 0;
$documentacion = new Documentacion();
$docs = $documentacion->traer_doc_completa_vehiculo($idvehiculo);

if ($docs && $docs->num_rows > 0): ?>

<div class="row g-3">
<?php while ($doc = $docs->fetch_assoc()):
    $idTipo = $doc['idtipo_documentacion'];
    $nombreTipo = htmlspecialchars($doc['tipo_doc']);
    $tieneArchivo = !empty($doc['URL_descripcion']);
    $url = $doc['URL_descripcion'];
    $idDoc = $doc['idDocumentaciones'];
?>
    <div class="col-md-6">
        <div class="p-3 border rounded bg-light h-100">
            <strong><?= $nombreTipo ?></strong>

            <div class="mt-3 d-flex flex-column gap-2">

                <?php if ($tieneArchivo): ?>
                    <a href="<?= $url ?>" class="btn btn-primary btn-sm" target="_blank">
                        Ver Documento
                    </a>

                    <button type="button"
                            class="btn btn-danger btn-sm"
                            onclick="eliminarDocumento(<?= $idDoc ?>)">
                        Eliminar
                    </button>

                <?php else: ?>

                    <input type="file"
                           name="documentos_vehiculo_tipo[<?= $idTipo ?>]"
                           accept=".pdf"
                           class="form-control form-control-sm">

                <?php endif; ?>

            </div>
        </div>
    </div>

<?php endwhile; ?>
</div>

<?php else: ?>

<p class="text-muted text-center">No hay tipos de documentación definidos.</p>

<?php endif; ?>


