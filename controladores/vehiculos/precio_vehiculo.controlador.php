<?php
ini_set('display_errors', 1);
require_once('../../modelos/precios_vehiculos.php');
require_once('../../modelos/tablas_maestras/tipo_precio.php');
require_once('../../modelos/tablas_maestras/interes.php');

/* ============================================================
   FUNCIÓN PARA LIMPIAR NÚMEROS (FORMATO AR: 20.500.000,00)
   Limpia NBSP, thin-spaces, puntos, comas y devuelve float.
============================================================ */
function limpiarNumero($valor)
{
    if ($valor === null || $valor === '') {
        return 0;
    }

    // Quitar espacios normales, NBSP, THIN SPACE, etc.
    $valor = preg_replace('/[\x{00A0}\x{202F}\s]+/u', '', $valor);

    // Eliminar puntos (separadores de miles)
    $valor = str_replace('.', '', $valor);

    // Convertir coma en punto (decimales estilo AR/ES)
    $valor = str_replace(',', '.', $valor);

    // Dejar solo dígitos, punto y signo menos, por seguridad extra
    $valor = preg_replace('/[^0-9\.\-]/', '', $valor);

    return floatval($valor);
}

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
    /* ============================================================
       AGREGAR PRECIO (TOMADO o PUBLICO)
    ============================================================ */
    public function agregar()
    {
        if (empty($_POST['precio_nuevo'])) {
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Campo vacío.&status=error');
            return;
        }

        $idVehiculo = $_POST['idvehiculos'];
        $idInteres = $_POST['interes_id'] ?? null;

        // LIMPIEZA PROFESIONAL DEL NÚMERO
        $precioNuevo = limpiarNumero($_POST['precio_nuevo']);

        $precio = new PrecioVehiculo();

        // ≡ AGREGAR PRECIO PÚBLICO
        if ($_POST['action'] === 'publico') {

            $interesModel = new Intereses();
            $interes = $interesModel->obtenerPorId($idInteres);
            $porcentaje = $interes ? floatval($interes['porcentaje']) : 0;

            $precioPublico = $precioNuevo * (1 + $porcentaje / 100);

            $precio->guardarPrecioPublico($idVehiculo, $precioPublico, $idInteres);
        }

        // ≡ AGREGAR PRECIO TOMADO
        else {
            $precio->guardarPrecioTomado($idVehiculo, $precioNuevo);
        }

        header('location: ../../index.php?page=listado_vehiculos&mensaje=Precio agregado correctamente.&status=success');
    }

    /* ============================================================
       ACTUALIZAR PRECIOS (TOMADO + PUBLICO)
    ============================================================ */
    public function actualizar()
    {
        if (empty($_POST['precio_nuevo'])) {
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Campo vacío.&status=error');
            return;
        }

        $idVehiculo = $_POST['idvehiculos'];
        $idInteres = $_POST['interes_id'] ?? null;

        $tipoPrecio = new Tipo_Precios();

        // LIMPIEZA DEL VALOR INGRESADO (FORMATO LOCAL)
        $precioNuevo = limpiarNumero($_POST['precio_nuevo']);

        /* ============================================================
           1️⃣ ACTUALIZAR PRECIO TOMADO
        ============================================================ */
        $idTipoTomado = $tipoPrecio->obtenerIdPorDescripcion('tomado');

        $precioTomado = new PrecioVehiculo();
        $precioTomado->setPrecio($precioNuevo);
        $precioTomado->setVehiculos_idvehiculos($idVehiculo);
        $precioTomado->setTipo_precios_idtipo_precios($idTipoTomado);

        // Usás "1" como interés base/sin interés
        $precioTomado->setIntereses_idintereses(1);
        $precioTomado->actualizar_precio();

        /* ============================================================
           2️⃣ SI HAY INTERÉS → CALCULAR Y ACTUALIZAR PRECIO PUBLICO
        ============================================================ */
        if (!empty($idInteres)) {
            $interesModel = new Intereses();
            $interes = $interesModel->obtenerPorId($idInteres);

            if ($interes && isset($interes['porcentaje'])) {

                $porcentaje = floatval($interes['porcentaje']);
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

