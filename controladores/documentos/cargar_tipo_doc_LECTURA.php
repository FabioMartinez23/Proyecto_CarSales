<?php
require_once('../../modelos/documentaciones.php');

$idvehiculo = $_GET['idvehiculo'] ?? 0;
$documentacion = new Documentacion();
$docs = $documentacion->traer_doc_completa_vehiculo($idvehiculo);

if ($docs && $docs->num_rows > 0): ?>

    <div class="row g-3">
        <?php while ($doc = $docs->fetch_assoc()):
            $nombre = htmlspecialchars($doc['tipo_doc']);
            $estado = ($doc['estado_doc'] == 1);
            // Normalizar URL para que siempre sea ruta web válida
            if (!empty($doc['URL_descripcion'])) {

                // Elimina ../../ si existe
                $ruta = str_replace('../', '', $doc['URL_descripcion']);
                $ruta = str_replace('./', '', $ruta);

                // Ruta web base del proyecto
                $baseURL = "/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/";

                // Construye la URL final correcta
                $url = $baseURL . $ruta;

            } else {
                $url = "";
            }
        ?>
        <div class="col-md-6">
            <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                <strong><?= $nombre ?></strong>

                <div class="text-end">
                    <?php if (!empty($url)): ?>
                        <a href="<?= $url ?>" class="btn btn-sm btn-primary" target="_blank">Ver</a>
                    <?php endif; ?>

                    <?php if ($estado): ?>
                        <i class="fa-solid fa-check text-success fs-5 ms-2"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-xmark text-danger fs-5 ms-2"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

<?php else: ?>
    <p class="text-muted text-center">No hay documentación registrada.</p>
<?php endif; ?>
