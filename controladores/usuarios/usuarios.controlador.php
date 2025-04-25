<?php

ini_set('display_errors', 1);
require_once('../../modelos/conexion.php');
require_once('../../modelos/usuarios.php');
require_once('../../modelos/personas.php');
require_once('../../modelos/contactos.php');
require_once('../../modelos/domicilio.php');
require_once('../../modelos/documentos.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->guardar();
    }
    if ($_POST['action'] == 'guardar_cliente'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->guardar_cliente();
    }
    if ($_POST['action'] == 'guardar_empleado'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->guardar_empleado();
    }
    if ($_POST['action'] == 'eliminar'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->eliminar();
    }
    if ($_POST['action'] == 'eliminar_cliente'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->eliminar_cliente();
    }
    if ($_POST['action'] == 'eliminar_empleado'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->eliminar_empleado();
    }
    if ($_POST['action'] == 'resetear'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->resetear();
    }
    if ($_POST['action'] == 'resetear_cliente'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->resetear_cliente();
    }
    if ($_POST['action'] == 'resetear_empleado'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->resetear_empleado();
    }
    if ($_POST['action'] == 'cambiar_password'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->cambiar_password();
    }
    if ($_POST['action'] == 'actualizar_cliente'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->actualizar_cliente();
    }
    if ($_POST['action'] == 'actualizar_empleado'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->actualizar_empleado();
    }
    if ($_POST['action'] == 'recuperar_password'){
        $usuario_controlador = new UsuarioControlador();
        $usuario_controlador->recuperar_password();
    }
}


class UsuarioControlador {

    public function guardar(){

        if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['perfiles_idperfiles']) || empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['fecha_nacimiento']) || empty($_POST['tipo_sexo_idtipo_sexo'])){
            header('location: ../../index.php?page=listado_usuarios&mensaje=Todos los datos obligatorios.&status=error');
            return;
        }

            // Validar que el usuario tenga al menos 18 años
            $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nacimiento)->y; // Calcula la edad en años
    
            if ($edad < 18) {
                header('location: ../../index.php?page=listado_usuarios&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
                return;
            }

        $persona = new Persona();
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->agregar_persona();
        $personas_idpersonas = $persona->getIdpersonas();

        if (!$personas_idpersonas){
            header('location: ../../index.php?page=listado_usuarios&mensaje=Error al cargar Persona.&status=error');
            return;
        }else{
            $usuarios = new Usuario();
            $usuarios->setUsername($_POST['username']);
            $usuarios->setEmail($_POST['email']);
            $usuarios->setPassword($_POST['username']);
            $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
            $usuarios->setPersonas_idpersonas($personas_idpersonas);
            $usuarios->guardar();
            header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario registrado correctamente.&status=success');
        }

    }

    public function guardar_empleado(){

        if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['perfiles_idperfiles']) || empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['fecha_nacimiento']) || empty($_POST['tipo_sexo_idtipo_sexo'])){
            header('location: ../../index.php?page=registrar_clientes&mensaje=Todos los datos son obligarios.&status=error');
            return;
        }

            // Validar que el usuario tenga al menos 18 años
            $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nacimiento)->y; // Calcula la edad en años
    
            if ($edad < 18) {
                header('location: ../../index.php?page=registrar_clientes&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
                return;
            }

        $persona = new Persona();
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->agregar_persona();
        $personas_idpersonas = $persona->getIdpersonas();

        if (!$personas_idpersonas){
            header('location: ../../index.php?page=registrar_clientes&mensaje=Error al cargar Persona.&status=error');
            return;
        }else{
            $contactos = new Contacto();
            $contactos->setPersonas_idPersonas($personas_idpersonas);
            $contactos->setValor($_POST['contacto']);
            $contactos->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
            $contactos->agregar_contato();

            $documento = new Documento();
            $documento->setPersonas_idPersonas($personas_idpersonas);
            $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);

            // Eliminar los puntos
            $documento_sin_puntos = str_replace('.', '', $_POST['documento']);
            $documento->setValor($documento_sin_puntos);
            $documento->agregar_documento();

            $domicilio = new Domicilios();
            $domicilio->setPersonas_idPersonas($personas_idpersonas);
            $domicilio->setDescripcion($_POST['domicilio']);
            $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
            $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
            $domicilio->agregar_domicilio();

            $usuarios = new Usuario();
            $usuarios->setUsername($_POST['username']);
            $usuarios->setEmail($_POST['email']);
            $usuarios->setPassword($_POST['username']);
            $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
            $usuarios->setPersonas_idpersonas($personas_idpersonas);
            $usuarios->guardar();


            header('location: ../../index.php?page=listado_empleados&mensaje=Cliente registrado correctamente.&status=success');
        }

    }

    public function guardar_cliente(){

        if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['perfiles_idperfiles']) || empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['fecha_nacimiento']) || empty($_POST['tipo_sexo_idtipo_sexo'])){
            header('location: ../../index.php?page=registrar_clientes&mensaje=Todos los datos son obligarios.&status=error');
            return;
        }

            // Validar que el usuario tenga al menos 18 años
            $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nacimiento)->y; // Calcula la edad en años
    
            if ($edad < 18) {
                header('location: ../../index.php?page=registrar_clientes&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
                return;
            }

        $persona = new Persona();
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->agregar_persona();
        $personas_idpersonas = $persona->getIdpersonas();

        if (!$personas_idpersonas){
            header('location: ../../index.php?page=registrar_clientes&mensaje=Error al cargar Persona.&status=error');
            return;
        }else{
            $contactos = new Contacto();
            $contactos->setPersonas_idPersonas($personas_idpersonas);
            $contactos->setValor($_POST['contacto']);
            $contactos->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
            $contactos->agregar_contato();

            $documento = new Documento();
            $documento->setPersonas_idPersonas($personas_idpersonas);
            $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);

            // Eliminar los puntos
            $documento_sin_puntos = str_replace('.', '', $_POST['documento']);
            $documento->setValor($documento_sin_puntos);
            $documento->agregar_documento();

            $domicilio = new Domicilios();
            $domicilio->setPersonas_idPersonas($personas_idpersonas);
            $domicilio->setDescripcion($_POST['domicilio']);
            $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
            $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
            $domicilio->agregar_domicilio();

            $usuarios = new Usuario();
            $usuarios->setUsername($_POST['username']);
            $usuarios->setEmail($_POST['email']);
            $usuarios->setPassword($_POST['username']);
            $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
            $usuarios->setPersonas_idpersonas($personas_idpersonas);
            $usuarios->guardar();


            header('location: ../../index.php?page=listado_clientes&mensaje=Cliente registrado correctamente.&status=success');
        }

    }

    public function eliminar(){
        if ($_POST['perfil'] == 'Administrador'){
            header('location: ../../index.php?page=listado_usuarios&mensaje=No se puede eliminar un Administrador.&status=warning');
            exit();
        }else{
            $usuario = new Usuario();
            $usuario->setIdUsuarios($_POST['idusuarios']);
            $usuario->eliminar();
            header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario eliminado correctamente.&status=success');
        }
    }

    public function eliminar_cliente(){
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario->eliminar();
        header('location: ../../index.php?page=listado_clientes&mensaje=Cliente eliminado correctamente.&status=success');
    }

    public function eliminar_empleado(){
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario->eliminar();
        header('location: ../../index.php?page=listado_empleados&mensaje=Empleado eliminado correctamente.&status=success');
    }

    public function cambiar_password() {
        if (!isset($_POST['password_actual'], $_POST['new_password'], $_POST['new_password_confirm'])) {
            header('location: ../../index.php?page=cambiar_password&mensaje=Todos los datos son obligatorios&status=error');
            return;
        }
    
        if (empty($_POST['password_actual']) || empty($_POST['new_password']) || empty($_POST['new_password_confirm'])) {
            header('location: ../../index.php?page=cambiar_password&mensaje=Todos los datos son obligatorios&status=error');
            return;
        }
    

        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_actual = $usuario->validar_usuario_por_id(); 
    

        if (!$usuario_actual) {
            header('location: ../../index.php?page=cambiar_password&mensaje=Usuario no encontrado&status=error');
            return;
        }
    
        if (!password_verify($_POST['password_actual'], $usuario_actual['password']) && $_POST['password_actual'] != $usuario->getUsername()) {
            header('location: ../../index.php?page=cambiar_password&mensaje=La contraseña actual no coincide&status=error');
            return;
        }
    
        if ($_POST['new_password'] != $_POST['new_password_confirm']) {
            header('location: ../../index.php?page=cambiar_password&mensaje=Las contraseñas nuevas no coinciden&status=error');
            return;
        }
    
        $usuario->setPassword($_POST['new_password']);
        $usuario->cambiar_password();
    
        session_start();
        session_unset();
        session_destroy();
    
        header('location: ../../index.php?page=login&mensaje=Contraseña cambiada correctamente.&status=success');
    }
    
    public function resetear(){
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_info = $usuario->traer_usuario_id($_POST['idusuarios']);
        
        if ($usuario_info) {
            $nuevo_password = $usuario_info['username'];
            $usuario->setPassword($nuevo_password);
            $usuario->cambiar_password();
            // Redirigir con mensaje de éxito
            header('location: ../../index.php?page=listado_usuarios&mensaje=Contraseña reseteada correctamente.&status=success');
        } else {
            // Si no se encontró el usuario, redirigir con un mensaje de error
            header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario no encontrado.&status=error');
        }
    }

    public function resetear_cliente(){
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_info = $usuario->traer_usuario_id($_POST['idusuarios']);
        
        if ($usuario_info) {
            $nuevo_password = $usuario_info['username'];
            $usuario->setPassword($nuevo_password);
            $usuario->cambiar_password();
            // Redirigir con mensaje de éxito
            header('location: ../../index.php?page=listado_clientes&mensaje=Contraseña del Cliente reseteada correctamente.&status=success');
        } else {
            // Si no se encontró el usuario, redirigir con un mensaje de error
            header('location: ../../index.php?page=listado_empleados&mensaje=Cliente no encontrado.&status=error');
        }
    }

    public function resetear_empleado(){
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_info = $usuario->traer_usuario_id($_POST['idusuarios']);
        
        if ($usuario_info) {
            $nuevo_password = $usuario_info['username'];
            $usuario->setPassword($nuevo_password);
            $usuario->cambiar_password();
            // Redirigir con mensaje de éxito
            header('location: ../../index.php?page=listado_empleados&mensaje=Contraseña del Empleado reseteada correctamente.&status=success');
        } else {
            // Si no se encontró el usuario, redirigir con un mensaje de error
            header('location: ../../index.php?page=listado_empleados&mensaje=Empleado no encontrado.&status=error');
        }
    }

    public function actualizar_cliente(){

            // Validar que el usuario tenga al menos 18 años
            $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fecha_nacimiento)->y; // Calcula la edad en años
    
            if ($edad < 18) {
                header('location: ../../index.php?page=registrar_clientes&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
                return;
            }

        $persona = new Persona();
        $persona->setIdpersonas($_POST['idpersonas']);
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->actualizar_persona();
        $personas_idpersonas = $persona->getIdpersonas($_POST['idpersonas']);

        if (!$personas_idpersonas){
            header('location: ../../index.php?page=registrar_clientes&mensaje=Error al cargar Persona.&status=error');
            return;
        }else{
            $contactos = new Contacto();
            $contactos->setPersonas_idPersonas($personas_idpersonas);
            $contactos->setValor($_POST['contacto']);
            $contactos->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
            $contactos->agregar_contato();

            $documento = new Documento();
            $documento->setPersonas_idPersonas($personas_idpersonas);
            $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);
            $documento->setValor($_POST['documento']);
            $documento->agregar_documento();

            $domicilio = new Domicilios();
            $domicilio->setPersonas_idPersonas($personas_idpersonas);
            $domicilio->setDescripcion($_POST['domicilio']);
            $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
            $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
            $domicilio->agregar_domicilio();

            $usuarios = new Usuario();
            $usuarios->setIdUsuarios($_POST['idusuarios']);
            $usuarios->setUsername($_POST['username']);
            $usuarios->setEmail($_POST['email']);
            $usuarios->setPersonas_idpersonas($personas_idpersonas);
            $usuarios->actualizar_usuario();
            header('location: ../../index.php?page=listado_clientes&mensaje=Cliente modificado correctamente.&status=success');
        }
    }


    public function actualizar_empleado(){

        // Validar que el usuario tenga al menos 18 años
        $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y; // Calcula la edad en años

        if ($edad < 18) {
            header('location: ../../index.php?page=registrar_empleados&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
            return;
        }

    $persona = new Persona();
    $persona->setIdpersonas($_POST['idpersonas']);
    $persona->setNombre($_POST['nombre']);
    $persona->setApellido($_POST['apellido']);
    $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
    $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
    $persona->actualizar_persona();
    $personas_idpersonas = $persona->getIdpersonas($_POST['idpersonas']);

    if (!$personas_idpersonas){
        header('location: ../../index.php?page=registrar_empleados&mensaje=Error al cargar Persona.&status=error');
        return;
    }else{
        $contactos = new Contacto();
        $contactos->setPersonas_idPersonas($personas_idpersonas);
        $contactos->setValor($_POST['contacto']);
        $contactos->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
        $contactos->agregar_contato();

        $documento = new Documento();
        $documento->setPersonas_idPersonas($personas_idpersonas);
        $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);
        $documento->setValor($_POST['documento']);
        $documento->agregar_documento();

        $domicilio = new Domicilios();
        $domicilio->setPersonas_idPersonas($personas_idpersonas);
        $domicilio->setDescripcion($_POST['domicilio']);
        $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
        $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
        $domicilio->agregar_domicilio();

        $usuarios = new Usuario();
        $usuarios->setIdUsuarios($_POST['idusuarios']);
        $usuarios->setUsername($_POST['username']);
        $usuarios->setEmail($_POST['email']);
        $usuarios->setPersonas_idpersonas($personas_idpersonas);
        $usuarios->actualizar_usuario();
        header('location: ../../index.php?page=listado_empleados&mensaje=Cliente modificado correctamente.&status=success');
    }
}

public function recuperar_password() {
    // Verificar que se han enviado los datos requeridos
    if (!isset($_POST['new_password'], $_POST['new_password_confirm'], $_POST['token'])) {
        header('location: ../../index.php?page=recuperar_password&mensaje=Todos los datos son obligatorios&status=error');
        return;
    }

    // Verificar si los campos están vacíos
    if (empty($_POST['new_password']) || empty($_POST['new_password_confirm']) || empty($_POST['token'])) {
        header('location: ../../index.php?page=recuperar_password&mensaje=Todos los datos son obligatorios&status=error');
        return;
    }

    $conexion = new Conexion();
    $token = $_POST['token'];

    // Validar si el token es válido y no ha expirado
    $query = "SELECT Usuarios_idusuarios FROM tokens_recuperacion WHERE token = '$token' AND fecha_expiracion > NOW()";
    $resultado = $conexion->consultar($query);

    if ($resultado->num_rows === 0) {
        header('location: ../../index.php?page=recuperar_password&mensaje=El enlace de recuperación ha expirado o es inválido.&status=error');
        return;
    }

    // Obtener el ID de usuario relacionado con el token
    $usuario_id = $resultado->fetch_assoc()['Usuarios_idusuarios'];

    // Verificar que las contraseñas nuevas coinciden
    if ($_POST['new_password'] !== $_POST['new_password_confirm']) {
        header('location: ../../index.php?page=recuperar_password&mensaje=Las contraseñas nuevas no coinciden&status=error');
        return;
    }

    // Hash de la nueva contraseña
    $nueva_contraseña_hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Actualizar la contraseña en la tabla `usuarios`
    $update_query = "UPDATE usuarios SET password = '$nueva_contraseña_hashed' WHERE idusuarios = '$usuario_id'";
    $conexion->consultar($update_query);

    // Eliminar el token de recuperación después de usarlo
    $conexion->consultar("DELETE FROM tokens_recuperacion WHERE token = '$token'");

    // Redirigir al usuario con un mensaje de éxito
    header('location: ../../index.php?page=login&mensaje=Contraseña restablecida con éxito. Por favor, inicie sesión.&status=success');
}

    
}


?>