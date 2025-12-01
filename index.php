<?php


require_once('modelos/usuarios.php');
require_once('modelos/gastos_generales.php');
require_once('modelos/empleados.php');
require_once('modelos/personas.php');
require_once('modelos/contactos.php');
require_once('modelos/domicilio.php');
require_once('modelos/perfiles.php');
require_once('modelos/modulos.php');
require_once('modelos/vehiculos.php');
require_once('modelos/documentaciones.php');
require_once('modelos/ficha_tecnica.php');
require_once('modelos/precios_vehiculos.php');
require_once('modelos/vender_vehiculos.php');
require_once('modelos/comprar_vehiculos.php');
require_once('modelos/reportes_costos.php');
require_once('modelos/ventas_forma_pagos.php');
require_once('modelos/anular_operaciones.php');
require_once('modelos/tablas_maestras/modelo_vehiculo.php');
require_once('modelos/tablas_maestras/marca.php');
require_once('modelos/tablas_maestras/color.php');
require_once('modelos/tablas_maestras/carroceria.php');
require_once('modelos/tablas_maestras/cristal.php');
require_once('modelos/tablas_maestras/neumatico.php');
require_once('modelos/tablas_maestras/tipo_vehiculo.php');
require_once('modelos/tablas_maestras/tipo_sexo.php');
require_once('modelos/tablas_maestras/tipo_contacto.php');
require_once('modelos/tablas_maestras/tipo_domicilio.php');
require_once('modelos/tablas_maestras/tipo_documento.php');
require_once('modelos/tablas_maestras/tipo_pago.php');
require_once('modelos/tablas_maestras/tipo_anulacion.php');
require_once('modelos/tablas_maestras/tipo_de_puesto.php');
require_once('modelos/tablas_maestras/tipo_documentacion.php');
require_once('modelos/tablas_maestras/tipo_precio.php');
require_once('modelos/tablas_maestras/estado_vehiculo.php');
require_once('modelos/tablas_maestras/interes.php');
require_once('modelos/tablas_maestras/tipo_comision.php');
require_once('modelos/comision_venta.php');
require_once('modelos/documentos.php');
require_once('modelos/tablas_maestras/pais.php');
require_once('modelos/tablas_maestras/provincia.php');
require_once('modelos/tablas_maestras/localidad.php');
require_once('modelos/tablas_maestras/barrio.php');
require_once('modelos/caja.php');
require_once('controladores/plantilla.controlador.php');
# require_once('controladores/login.controlador.php');

$plantilla = new PlantillaControlador();
$plantilla->traer_plantilla();




?>
