<?php
ini_set('display_errors', 1);
require_once('../../modelos/precios_vehiculos.php');
require_once('../../modelos/tablas_maestras/tipo_precio.php');
require_once('../../modelos/tablas_maestras/interes.php');

if (isset($_POST['action'])) {
    $precio_controlador = new PrecioVehiculoControlador();

    if ($_POST['action'] == 'agregar') {
        $precio_controlador->agregar();
    }
    if ($_POST['action'] == 'actualizar') {
        $precio_controlador->actualizar();
    }
    if ($_POST['action'] == 'publico') {
        $precio_controlador->agregar();
    }
}

class PrecioVehiculoControlador
{
    public function agregar() {
        if (empty($_POST['precio_nuevo'])) {
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Campo vacío.&status=error');
            return;
        }

        $idVehiculo = $_POST['idvehiculos'];
        $precioNuevo = str_replace(['.', ','], '', $_POST['precio_nuevo']);
        $idInteres = $_POST['interes_id'] ?? null;

        $precio = new PrecioVehiculo();

        if ($_POST['action'] === 'publico') {
            // Calcular precio público con interés
            $interesModel = new Intereses();
            $interes = $interesModel->obtenerPorId($idInteres);
            $porcentaje = $interes ? floatval($interes['porcentaje']) : 0;
            $precioPublico = $precioNuevo * (1 + $porcentaje / 100);

            $precio->guardarPrecioPublico($idVehiculo, $precioPublico, $idInteres);
        } else {
            $precio->guardarPrecioTomado($idVehiculo, $precioNuevo);
        }

        header('location: ../../index.php?page=listado_vehiculos&mensaje=Precio agregado correctamente.&status=success');
    }

    public function actualizar()
    {
        if (empty($_POST['precio_nuevo'])) {
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Campo vacío.&status=error');
            return;
        }

        $idVehiculo = $_POST['idvehiculos'];
        $idInteres = $_POST['interes_id'] ?? null;
        $tipoPrecio = new Tipo_Precios();

        // 🔹 1) Limpiar y convertir el precio ingresado (elimina puntos y comas)
        $precioTexto = $_POST['precio_nuevo'];
        $precioLimpio = str_replace(['.', ','], '', $precioTexto); // elimina separadores
        $precioNuevo = floatval($precioLimpio); // convierte a número puro

        // 1️⃣ Guardar PRECIO TOMADO (sin interés)
        $idTipoTomado = $tipoPrecio->obtenerIdPorDescripcion('tomado');

        $precioTomado = new PrecioVehiculo();
        $precioTomado->setPrecio($precioNuevo);
        $precioTomado->setVehiculos_idvehiculos($idVehiculo);
        $precioTomado->setTipo_precios_idtipo_precios($idTipoTomado);
        $precioTomado->setIntereses_idintereses(1); // ID del interés “Sin interés” o base
        $precioTomado->actualizar_precio();

        // 2️⃣ Si se seleccionó interés → calcular PRECIO PÚBLICO
        if (!empty($idInteres)) {
            $interesModel = new Intereses();
            $interes = $interesModel->obtenerPorId($idInteres);

            if ($interes && isset($interes['porcentaje'])) {
                $porcentaje = floatval($interes['porcentaje']);

                // Calcular nuevo precio con el porcentaje aplicado
                $precioPublico = $precioNuevo * (1 + $porcentaje / 100);

                $idTipoPublico = $tipoPrecio->obtenerIdPorDescripcion('publico');

                $precioPublicoObj = new PrecioVehiculo();
                $precioPublicoObj->setPrecio($precioPublico);
                $precioPublicoObj->setVehiculos_idvehiculos($idVehiculo);
                $precioPublicoObj->setTipo_precios_idtipo_precios($idTipoPublico);
                $precioPublicoObj->setIntereses_idintereses($idInteres);
                $precioPublicoObj->actualizar_precio();
            }
        }

        header('location: ../../index.php?page=listado_vehiculos&mensaje=Precio actualizado correctamente.&status=success');
    }
}
?>
