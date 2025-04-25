<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('../../modelos/tablas_maestras/tipo_sexo.php');
require_once('../../modelos/tablas_maestras/marca.php');
require_once('../../modelos/tablas_maestras/tipo_vehiculo.php');
require_once('../../modelos/tablas_maestras/tipo_contacto.php');
require_once('../../modelos/tablas_maestras/tipo_documento.php');
require_once('../../modelos/tablas_maestras/modelo_vehiculo.php');
require_once('../../modelos/tablas_maestras/tipo_domicilio.php');
require_once('../../modelos/tablas_maestras/pais.php');
require_once('../../modelos/tablas_maestras/provincia.php');
require_once('../../modelos/perfiles.php');
require_once('../../modelos/tablas_maestras/tipo_de_puesto.php');
require_once('../../modelos/tablas_maestras/color.php');

$form_data = array();

if(isset($_POST['action'])){
    switch($_POST['action']){
        case 'perfil.ajax':
            $perfil = new Perfil();
            $perfil->setDescripcion($_POST['descripcion']);
            $resultado = $perfil->validar_perfil();
                if($resultado->num_rows > 0){
    
                    $form_data['data'] = 'error';
                }else{
    
                    $form_data['data'] = 'success';
                }
                break;
        case 'tipo_sexo.ajax':
            $tipo_sexo = new Tipo_Sexos();
            $tipo_sexo->setDescripcion($_POST['descripcion']);
            $resultado = $tipo_sexo->validar_tipo_sexo();
            if($resultado->num_rows > 0){

                $form_data['data'] = 'error';
            }else{

                $form_data['data'] = 'success';
            }
            break;
        case 'marca.ajax':
            $marca = new Marcas();
            $marca->setNombre($_POST['nombre']);
            $resultado = $marca->validar_marca();
            if($resultado->num_rows > 0){

                $form_data['data'] = 'error';
            }else{

                $form_data['data'] = 'success';
            }
            break;
        case 'tipo_vehiculo.ajax':
            $tipo_vehiculo = new Tipo_Vehiculos();
            $tipo_vehiculo->setNombre($_POST['nombre']);
            $resultado = $tipo_vehiculo->validar_tipo_vehiculo();
            if($resultado->num_rows > 0){

                $form_data['data'] = 'error';
            }else{

                $form_data['data'] = 'success';
            }
            break;
        case 'tipo_documento.ajax':
            $tipo_documento = new Tipo_Documentos();
            $tipo_documento->setDescripcion($_POST['descripcion']);
            $resultado = $tipo_documento->validar_tipo_documento();
                if($resultado->num_rows > 0){

                    $form_data['data'] = 'error';
                }else{

                    $form_data['data'] = 'success';
                }
                break;
        case 'tipo_contacto.ajax':
            $tipo_contacto = new Tipo_Contactos();
            $tipo_contacto->setDescripcion($_POST['descripcion']);
            $resultado = $tipo_contacto->validar_tipo_contacto();
                if($resultado->num_rows > 0){

                    $form_data['data'] = 'error';
                }else{

                    $form_data['data'] = 'success';
                }
                break;
        case 'modelo_vehiculo.ajax':
            $modelo = new Modelos_Vehiculos();
            $modelo->setNombre($_POST['nombre']);
            $resultado = $modelo->validar_modelo();
                if($resultado->num_rows > 0){

                    $form_data['data'] = 'error';
                }else{

                    $form_data['data'] = 'success';
                }
                break;
        case 'tipo_domicilio.ajax':
            $tipo_domicilio = new Tipo_Domicilios();
            $tipo_domicilio->setDescripcion($_POST['descripcion']);
            $resultado = $tipo_domicilio->validar_tipo_domicilio();
                if($resultado->num_rows > 0){
    
                    $form_data['data'] = 'error';
                }else{
    
                    $form_data['data'] = 'success';
                }
                break;
        case 'pais.ajax':
            $pais = new Paises();
            $pais->setDescripcion($_POST['descripcion']);
            $resultado = $pais->validar_pais();
                if($resultado->num_rows > 0){
    
                    $form_data['data'] = 'error';
                }else{
    
                    $form_data['data'] = 'success';
                }
                break;
        case 'provincia.ajax':
            $provincia = new Provincias();
            $provincia->setDescripcion($_POST['descripcion']);
            $resultado = $provincia->validar_provincia();
                if($resultado->num_rows > 0){
    
                    $form_data['data'] = 'error';
                }else{
    
                    $form_data['data'] = 'success';
                }
                break;
        case 'tipo_puesto.ajax':
            $tipo_puesto = new Tipo_De_Puestos();
            $tipo_puesto->setDescripcion($_POST['descripcion']);
            $resultado = $tipo_puesto->validar_tipo_puesto();
                if($resultado->num_rows > 0){
    
                    $form_data['data'] = 'error';
                }else{
    
                    $form_data['data'] = 'success';
                }
                break;
        case 'color.ajax':
            $color = new Colores();
            $color->setDescripcion($_POST['descripcion']);
            $resultado = $color->validar_color();
                if($resultado->num_rows > 0){
    
                    $form_data['data'] = 'error';
                }else{
    
                    $form_data['data'] = 'success';
                }
                break;
    }
    echo json_encode($form_data);
}





?>



