<?php
ini_set('display_errors', 1);

session_start(); // 🔹 Necesario para tener $_SESSION en esta request

require_once('../../modelos/conexion.php');
require_once('../../modelos/usuarios.php');
require_once('../../modelos/personas.php');
require_once('../../modelos/contactos.php');
require_once('../../modelos/domicilio.php');
require_once('../../modelos/documentos.php');
require_once('../../modelos/auditoria_edicion.php'); // 🔹 Auditoría

if (isset($_POST['action'])) {

    $usuario_controlador = new UsuarioControlador();

    switch ($_POST['action']) {
        case 'guardar':
            $usuario_controlador->guardar();
            break;

        case 'guardar_cliente':
            $usuario_controlador->guardar_cliente();
            break;

        case 'guardar_empleado':
            $usuario_controlador->guardar_empleado();
            break;

        case 'eliminar':
            $usuario_controlador->eliminar();
            break;

        case 'eliminar_cliente':
            $usuario_controlador->eliminar_cliente();
            break;

        case 'eliminar_empleado':
            $usuario_controlador->eliminar_empleado();
            break;

        case 'resetear':
            $usuario_controlador->resetear();
            break;

        case 'resetear_cliente':
            $usuario_controlador->resetear_cliente();
            break;

        case 'resetear_empleado':
            $usuario_controlador->resetear_empleado();
            break;

        case 'cambiar_password':
            $usuario_controlador->cambiar_password();
            break;

        case 'actualizar':
            $usuario_controlador->actualizar();
            break;

        case 'actualizar_cliente':
            $usuario_controlador->actualizar_cliente();
            break;

        case 'actualizar_empleado':
            $usuario_controlador->actualizar_empleado();
            break;

        case 'recuperar_password':
            $usuario_controlador->recuperar_password();
            break;
    }
}

class UsuarioControlador
{
    /* ============================================================
       GUARDAR USUARIO (ADMIN -> listado_usuarios)
    ============================================================ */
    public function guardar()
    {
        if (
            empty($_POST['username']) ||
            empty($_POST['email']) ||
            empty($_POST['perfiles_idperfiles']) ||
            empty($_POST['nombre']) ||
            empty($_POST['apellido']) ||
            empty($_POST['fecha_nacimiento']) ||
            empty($_POST['tipo_sexo_idtipo_sexo'])
        ) {
            header('location: ../../index.php?page=listado_usuarios&mensaje=Todos los datos obligatorios.&status=error');
            return;
        }

        // Validar mayoría de edad
        $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y;

        if ($edad < 18) {
            header('location: ../../index.php?page=listado_usuarios&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
            return;
        }

        // Persona
        $persona = new Persona();
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->agregar_persona();
        $personas_idpersonas = $persona->getIdpersonas();

        if (!$personas_idpersonas) {
            header('location: ../../index.php?page=listado_usuarios&mensaje=Error al cargar Persona.&status=error');
            return;
        }

        // Usuario
        $usuarios = new Usuario();
        $usuarios->setUsername($_POST['username']);
        $usuarios->setEmail($_POST['email']);
        $usuarios->setPassword($_POST['username']); // password = username (luego la cambia)
        $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
        $usuarios->setPersonas_idpersonas($personas_idpersonas);
        $usuarios->guardar();

        // Obtener idusuarios recién creado
        $id_usuario = $usuarios->obtener_id_por_username($_POST['username']);

        // 🔹 Datos "planchados" para auditoría (alta de cliente)
        if ($id_usuario) {
            $datos_nuevos = [
                'usuario' => [
                    'idusuarios'  => (int)$id_usuario,
                    'username'    => $_POST['username'],
                    'email'       => $_POST['email'],
                    'perfiles_id' => $_POST['perfiles_idperfiles'],
                ],
                'persona' => [
                    'idpersonas'       => (int)$personas_idpersonas,
                    'nombre'           => $_POST['nombre'],
                    'apellido'         => $_POST['apellido'],
                    'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                    'tipo_sexo_id'     => $_POST['tipo_sexo_idtipo_sexo'],
                ],
                'contacto' => [
                    'tipo_contacto_id' => $_POST['tipo_contacto_idtipo_contacto'] ?? null,
                    'valor'            => $_POST['contacto'] ?? null,
                ],
                'documento' => [
                    'tipo_documento_id' => $_POST['tipo_documento_idtipo_documento'] ?? null,
                    'valor'             => $documento_sin_puntos ?? null,
                ],
                'domicilio' => [
                    'descripcion'           => $_POST['domicilio'] ?? null,
                    'tipo_domicilio_id'     => $_POST['tipo_domicilio_idtipo_domicilio'] ?? null,
                    'barrios_id'            => $_POST['barrios_idbarrios'] ?? null,
                ],
            ];

            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'usuarios',                 // tabla
                $id_usuario,                // id_registro
                'Clientes',                 // módulo lógico
                'INSERT',                   // acción
                null,                       // datos_anteriores (no había)
                $datos_nuevos,              // datos_nuevos
                'Alta de cliente',          // descripción
                $_SESSION['idusuarios'] ?? null   // usuario que hizo la alta
            );
        }

        header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario registrado correctamente.&status=success');
    }

    /* ============================================================
       GUARDAR EMPLEADO
    ============================================================ */
    public function guardar_empleado()
    {
        if (
            empty($_POST['username']) || empty($_POST['email']) ||
            empty($_POST['perfiles_idperfiles']) || empty($_POST['nombre']) ||
            empty($_POST['apellido']) || empty($_POST['fecha_nacimiento']) ||
            empty($_POST['tipo_sexo_idtipo_sexo'])
        ) {
            header('location: ../../index.php?page=registrar_empleados&mensaje=Todos los datos son obligarios.&status=error');
            return;
        }

        // Mayor de 18
        $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y;

        if ($edad < 18) {
            header('location: ../../index.php?page=registrar_empleados&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
            return;
        }

        // Persona
        $persona = new Persona();
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->agregar_persona();
        $personas_idpersonas = $persona->getIdpersonas();

        if (!$personas_idpersonas) {
            header('location: ../../index.php?page=registrar_empleados&mensaje=Error al cargar Persona.&status=error');
            return;
        }

        // Contacto
        $contactos = new Contacto();
        $contactos->setPersonas_idPersonas($personas_idpersonas);
        $contactos->setValor($_POST['contacto']);
        $contactos->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
        $contactos->agregar_contato();

        // Documento
        $documento = new Documento();
        $documento->setPersonas_idPersonas($personas_idpersonas);
        $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);
        $documento_sin_puntos = str_replace('.', '', $_POST['documento']);
        $documento->setValor($documento_sin_puntos);
        $documento->agregar_documento();

        // Domicilio
        $domicilio = new Domicilios();
        $domicilio->setPersonas_idPersonas($personas_idpersonas);
        $domicilio->setDescripcion($_POST['domicilio']);
        $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
        $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
        $domicilio->agregar_domicilio();

        // Usuario
        $usuarios = new Usuario();
        $usuarios->setUsername($_POST['username']);
        $usuarios->setEmail($_POST['email']);
        $usuarios->setPassword($_POST['username']);
        $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
        $usuarios->setPersonas_idpersonas($personas_idpersonas);
        $usuarios->guardar();

        $id_usuario = $usuarios->obtener_id_por_username($_POST['username']);

        // 🔹 Datos "planchados" para auditoría (alta de cliente)
        if ($id_usuario) {
            $datos_nuevos = [
                'usuario' => [
                    'idusuarios'  => (int)$id_usuario,
                    'username'    => $_POST['username'],
                    'email'       => $_POST['email'],
                    'perfiles_id' => $_POST['perfiles_idperfiles'],
                ],
                'persona' => [
                    'idpersonas'       => (int)$personas_idpersonas,
                    'nombre'           => $_POST['nombre'],
                    'apellido'         => $_POST['apellido'],
                    'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                    'tipo_sexo_id'     => $_POST['tipo_sexo_idtipo_sexo'],
                ],
                'contacto' => [
                    'tipo_contacto_id' => $_POST['tipo_contacto_idtipo_contacto'] ?? null,
                    'valor'            => $_POST['contacto'] ?? null,
                ],
                'documento' => [
                    'tipo_documento_id' => $_POST['tipo_documento_idtipo_documento'] ?? null,
                    'valor'             => $documento_sin_puntos ?? null,
                ],
                'domicilio' => [
                    'descripcion'           => $_POST['domicilio'] ?? null,
                    'tipo_domicilio_id'     => $_POST['tipo_domicilio_idtipo_domicilio'] ?? null,
                    'barrios_id'            => $_POST['barrios_idbarrios'] ?? null,
                ],
            ];

            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'usuarios',                 // tabla
                $id_usuario,                // id_registro
                'Clientes',                 // módulo lógico
                'INSERT',                   // acción
                null,                       // datos_anteriores (no había)
                $datos_nuevos,              // datos_nuevos
                'Alta de cliente',          // descripción
                $_SESSION['idusuarios'] ?? null   // usuario que hizo la alta
            );
        }

        header('location: ../../index.php?page=listado_empleados&mensaje=Empleado registrado correctamente.&status=success');
    }

    /* ============================================================
       GUARDAR CLIENTE
    ============================================================ */
    public function guardar_cliente()
    {
        if (
            empty($_POST['username']) || empty($_POST['email']) ||
            empty($_POST['perfiles_idperfiles']) || empty($_POST['nombre']) ||
            empty($_POST['apellido']) || empty($_POST['fecha_nacimiento']) ||
            empty($_POST['tipo_sexo_idtipo_sexo'])
        ) {
            header('location: ../../index.php?page=registrar_clientes&mensaje=Todos los datos son obligarios.&status=error');
            return;
        }

        // Mayor de 18
        $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y;

        if ($edad < 18) {
            header('location: ../../index.php?page=registrar_clientes&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
            return;
        }

        // Persona
        $persona = new Persona();
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->agregar_persona();
        $personas_idpersonas = $persona->getIdpersonas();

        if (!$personas_idpersonas) {
            header('location: ../../index.php?page=registrar_clientes&mensaje=Error al cargar Persona.&status=error');
            return;
        }

        // Contacto
        $contactos = new Contacto();
        $contactos->setPersonas_idPersonas($personas_idpersonas);
        $contactos->setValor($_POST['contacto']);
        $contactos->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
        $contactos->agregar_contato();

        // Documento
        $documento = new Documento();
        $documento->setPersonas_idPersonas($personas_idpersonas);
        $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);
        $documento_sin_puntos = str_replace('.', '', $_POST['documento']);
        $documento->setValor($documento_sin_puntos);
        $documento->agregar_documento();

        // Domicilio
        $domicilio = new Domicilios();
        $domicilio->setPersonas_idPersonas($personas_idpersonas);
        $domicilio->setDescripcion($_POST['domicilio']);
        $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
        $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
        $domicilio->agregar_domicilio();

        // Usuario
        $usuarios = new Usuario();
        $usuarios->setUsername($_POST['username']);
        $usuarios->setEmail($_POST['email']);
        $usuarios->setPassword($_POST['username']);
        $usuarios->setPerfiles_id($_POST['perfiles_idperfiles']);
        $usuarios->setPersonas_idpersonas($personas_idpersonas);
        $usuarios->guardar();

        $id_usuario = $usuarios->obtener_id_por_username($_POST['username']);

        // 🔹 Datos "planchados" para auditoría (alta de cliente)
        if ($id_usuario) {
            $datos_nuevos = [
                'usuario' => [
                    'idusuarios'  => (int)$id_usuario,
                    'username'    => $_POST['username'],
                    'email'       => $_POST['email'],
                    'perfiles_id' => $_POST['perfiles_idperfiles'],
                ],
                'persona' => [
                    'idpersonas'       => (int)$personas_idpersonas,
                    'nombre'           => $_POST['nombre'],
                    'apellido'         => $_POST['apellido'],
                    'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                    'tipo_sexo_id'     => $_POST['tipo_sexo_idtipo_sexo'],
                ],
                'contacto' => [
                    'tipo_contacto_id' => $_POST['tipo_contacto_idtipo_contacto'] ?? null,
                    'valor'            => $_POST['contacto'] ?? null,
                ],
                'documento' => [
                    'tipo_documento_id' => $_POST['tipo_documento_idtipo_documento'] ?? null,
                    'valor'             => $documento_sin_puntos ?? null,
                ],
                'domicilio' => [
                    'descripcion'           => $_POST['domicilio'] ?? null,
                    'tipo_domicilio_id'     => $_POST['tipo_domicilio_idtipo_domicilio'] ?? null,
                    'barrios_id'            => $_POST['barrios_idbarrios'] ?? null,
                ],
            ];

            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'usuarios',                 // tabla
                $id_usuario,                // id_registro
                'Clientes',                 // módulo lógico
                'INSERT',                   // acción
                null,                       // datos_anteriores (no había)
                $datos_nuevos,              // datos_nuevos
                'Alta de cliente',          // descripción
                $_SESSION['idusuarios'] ?? null   // usuario que hizo la alta
            );
        }

        header('location: ../../index.php?page=listado_clientes&mensaje=Cliente registrado correctamente.&status=success');
    }

    /* ============================================================
    ELIMINAR USUARIO (ADMIN / SISTEMA)
    ============================================================ */
    public function eliminar()
    {
        if ($_POST['perfil'] == 'Administrador') {
            header('location: ../../index.php?page=listado_usuarios&mensaje=No se puede eliminar un Administrador.&status=warning');
            exit();
        }

        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);

        // 🕓 Snapshot ANTERIOR
        $usuario_anterior = $usuario->traer_usuario_id($_POST['idusuarios']);

        // Ejecutar eliminación (lógica o física según tu modelo)
        $usuario->eliminar();

        // 🔹 Auditoría: baja de usuario
        if ($usuario_anterior) {
            $datos_anteriores = [
                'usuario' => [
                    'idusuarios'   => (int)$usuario_anterior['idusuarios'],
                    'username'     => $usuario_anterior['username'] ?? null,
                    'email'        => $usuario_anterior['email'] ?? null,
                    'perfiles_id'  => $usuario_anterior['perfiles_idperfiles'] ?? null,
                    'personas_id'  => $usuario_anterior['personas_idpersonas'] ?? null,
                    // si traer_usuario_id() ya te trae nombre/apellido:
                    'nombre'       => $usuario_anterior['nombre'] ?? null,
                    'apellido'     => $usuario_anterior['apellido'] ?? null,
                ]
            ];

            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'usuarios',                             // tabla
                $_POST['idusuarios'],                   // id_registro
                'Usuarios',                             // módulo
                'DELETE',                               // acción
                $datos_anteriores,                      // datos_anteriores
                null,                                   // datos_nuevos
                'Baja lógica / eliminación de usuario', // descripción
                $_SESSION['idusuarios'] ?? null         // usuario que ejecuta
            );
        }

        header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario eliminado correctamente.&status=success');
    }

    /* ============================================================
    ELIMINAR CLIENTE
    ============================================================ */
    public function eliminar_cliente()
    {
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);

        // 🕓 Snapshot ANTERIOR
        $usuario_anterior = $usuario->traer_usuario_id($_POST['idusuarios']);

        $usuario->eliminar();

        // 🔹 Auditoría: baja de cliente
        if ($usuario_anterior) {
            $datos_anteriores = [
                'usuario' => [
                    'idusuarios'   => (int)$usuario_anterior['idusuarios'],
                    'username'     => $usuario_anterior['username'] ?? null,
                    'email'        => $usuario_anterior['email'] ?? null,
                    'perfiles_id'  => $usuario_anterior['perfiles_idperfiles'] ?? null,
                    'personas_id'  => $usuario_anterior['personas_idpersonas'] ?? null,
                    'nombre'       => $usuario_anterior['nombre'] ?? null,
                    'apellido'     => $usuario_anterior['apellido'] ?? null,
                ]
            ];

            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'usuarios',
                $_POST['idusuarios'],
                'Clientes',
                'DELETE',
                $datos_anteriores,
                null,
                'Baja lógica / eliminación de cliente',
                $_SESSION['idusuarios'] ?? null
            );
        }

        header('location: ../../index.php?page=listado_clientes&mensaje=Cliente eliminado correctamente.&status=success');
    }

    /* ============================================================
    ELIMINAR EMPLEADO
    ============================================================ */
    public function eliminar_empleado()
    {
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);

        // 🕓 Snapshot ANTERIOR
        $usuario_anterior = $usuario->traer_usuario_id($_POST['idusuarios']);

        $usuario->eliminar();

        // 🔹 Auditoría: baja de empleado
        if ($usuario_anterior) {
            $datos_anteriores = [
                'usuario' => [
                    'idusuarios'   => (int)$usuario_anterior['idusuarios'],
                    'username'     => $usuario_anterior['username'] ?? null,
                    'email'        => $usuario_anterior['email'] ?? null,
                    'perfiles_id'  => $usuario_anterior['perfiles_idperfiles'] ?? null,
                    'personas_id'  => $usuario_anterior['personas_idpersonas'] ?? null,
                    'nombre'       => $usuario_anterior['nombre'] ?? null,
                    'apellido'     => $usuario_anterior['apellido'] ?? null,
                ]
            ];

            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'usuarios',
                $_POST['idusuarios'],
                'Empleados',
                'DELETE',
                $datos_anteriores,
                null,
                'Baja lógica / eliminación de empleado',
                $_SESSION['idusuarios'] ?? null
            );
        }

        header('location: ../../index.php?page=listado_empleados&mensaje=Empleado eliminado correctamente.&status=success');
    }


    /* ============================================================
       CAMBIAR PASSWORD (usuario logueado)
    ============================================================ */
    public function cambiar_password()
    {
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

        if (!password_verify($_POST['password_actual'], $usuario_actual['password']) &&
            $_POST['password_actual'] != $usuario->getUsername()
        ) {
            header('location: ../../index.php?page=cambiar_password&mensaje=La contraseña actual no coincide&status=error');
            return;
        }

        if ($_POST['new_password'] != $_POST['new_password_confirm']) {
            header('location: ../../index.php?page=cambiar_password&mensaje=Las contraseñas nuevas no coinciden&status=error');
            return;
        }

        // Cambiar password
        $usuario->setPassword($_POST['new_password']);
        $usuario->cambiar_password();

        // 🔹 Auditoría: cambio de contraseña (sin guardar la contraseña)
        $auditoria = new AuditoriaEdiciones();
        $auditoria->registrar(
            'usuarios',
            $_POST['idusuarios'],
            'Usuarios',
            'UPDATE',
            [
                'idusuarios' => $_POST['idusuarios'],
                'accion'     => 'password_change_before'
            ],
            [
                'idusuarios' => $_POST['idusuarios'],
                'accion'     => 'password_change_after'
            ],
            'Cambio de contraseña por el propio usuario',
            $_SESSION['idusuarios'] ?? null
        );

        // Cerrar sesión
        session_unset();
        session_destroy();

        header('location: ../../index.php?page=login&mensaje=Contraseña cambiada correctamente.&status=success');
    }

    /* ============================================================
       RESETEAR PASSWORD (admin sobre usuario)
    ============================================================ */
    public function resetear()
    {
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_info = $usuario->traer_usuario_id($_POST['idusuarios']);

        if ($usuario_info) {
            $nuevo_password = $usuario_info['username'];
            $usuario->setPassword($nuevo_password);
            $usuario->cambiar_password();

            // 🔹 Auditoría: reset de contraseña de usuario
            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'usuarios',
                $_POST['idusuarios'],
                'Usuarios',
                'UPDATE',
                [
                    'idusuarios' => $_POST['idusuarios'],
                    'accion'     => 'reset_password_before'
                ],
                [
                    'idusuarios' => $_POST['idusuarios'],
                    'accion'     => 'reset_password_after'
                ],
                'Reseteo de contraseña de usuario (password = username)',
                $_SESSION['idusuarios'] ?? null
            );

            header('location: ../../index.php?page=listado_usuarios&mensaje=Contraseña reseteada correctamente.&status=success');
        } else {
            header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario no encontrado.&status=error');
        }
    }

    public function resetear_cliente()
    {
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_info = $usuario->traer_usuario_id($_POST['idusuarios']);

        if ($usuario_info) {
            $nuevo_password = $usuario_info['username'];
            $usuario->setPassword($nuevo_password);
            $usuario->cambiar_password();

            // 🔹 Auditoría: reset de contraseña de cliente
            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'usuarios',
                $_POST['idusuarios'],
                'Clientes',
                'UPDATE',
                [
                    'idusuarios' => $_POST['idusuarios'],
                    'accion'     => 'reset_password_before'
                ],
                [
                    'idusuarios' => $_POST['idusuarios'],
                    'accion'     => 'reset_password_after'
                ],
                'Reseteo de contraseña de cliente (password = username)',
                $_SESSION['idusuarios'] ?? null
            );

            header('location: ../../index.php?page=listado_clientes&mensaje=Contraseña del Cliente reseteada correctamente.&status=success');
        } else {
            header('location: ../../index.php?page=listado_clientes&mensaje=Cliente no encontrado.&status=error');
        }
    }

    public function resetear_empleado()
    {
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_info = $usuario->traer_usuario_id($_POST['idusuarios']);

        if ($usuario_info) {
            $nuevo_password = $usuario_info['username'];
            $usuario->setPassword($nuevo_password);
            $usuario->cambiar_password();

            // 🔹 Auditoría: reset de contraseña de empleado
            $auditoria = new AuditoriaEdiciones();
            $auditoria->registrar(
                'usuarios',
                $_POST['idusuarios'],
                'Empleados',
                'UPDATE',
                [
                    'idusuarios' => $_POST['idusuarios'],
                    'accion'     => 'reset_password_before'
                ],
                [
                    'idusuarios' => $_POST['idusuarios'],
                    'accion'     => 'reset_password_after'
                ],
                'Reseteo de contraseña de empleado (password = username)',
                $_SESSION['idusuarios'] ?? null
            );

            header('location: ../../index.php?page=listado_empleados&mensaje=Contraseña del Empleado reseteada correctamente.&status=success');
        } else {
            header('location: ../../index.php?page=listado_empleados&mensaje=Empleado no encontrado.&status=error');
        }
    }

    /* ============================================================
       ACTUALIZAR USUARIO (listado_usuarios)
    ============================================================ */
    public function actualizar()
    {
        // Traer datos anteriores de usuario
        $usuarios = new Usuario();
        $usuarios->setIdUsuarios($_POST['idusuarios']);
        $usuario_anterior = $usuarios->traer_usuario_id($_POST['idusuarios']);

        // Persona
        $persona = new Persona();
        $persona->setIdpersonas($_POST['idpersonas']);
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->actualizar_persona();
        $personas_idpersonas = $persona->getIdpersonas($_POST['idpersonas']);

        if (!$personas_idpersonas) {
            header('location: ../../index.php?page=listado_usuarios&mensaje=Error al cargar Persona.&status=error');
            return;
        }

        // Usuario actualizado
        $usuarios->setUsername($_POST['username']);
        $usuarios->setEmail($_POST['email']);
        $usuarios->setPersonas_idpersonas($personas_idpersonas);
        $usuarios->actualizar_usuario();

        // 🔹 Auditoría: modificación de usuario
        $datos_nuevos = [
            'username'    => $_POST['username'],
            'email'       => $_POST['email'],
            'personas_id' => $personas_idpersonas,
        ];

        $auditoria = new AuditoriaEdiciones();
        $auditoria->registrar(
            'usuarios',
            $_POST['idusuarios'],
            'Usuarios',
            'UPDATE',
            $usuario_anterior
                ? [
                    'username'    => $usuario_anterior['username'] ?? null,
                    'email'       => $usuario_anterior['email'] ?? null,
                    'perfiles_id' => $usuario_anterior['perfiles_idperfiles'] ?? null,
                    'personas_id' => $usuario_anterior['personas_idpersonas'] ?? null,
                ]
                : null,
            $datos_nuevos,
            'Modificación de datos de usuario',
            $_SESSION['idusuarios'] ?? null
        );

        header('location: ../../index.php?page=listado_usuarios&mensaje=Usuario modificado correctamente.&status=success');
    }

    /* ============================================================
       ACTUALIZAR CLIENTE
    ============================================================ */
    public function actualizar_cliente()
    {
        // ============================================================
        // 1) Validar que sea mayor de 18
        // ============================================================
        $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y;

        if ($edad < 18) {
            header('location: ../../index.php?page=registrar_clientes&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
            return;
        }

        // ============================================================
        // 2) Traer datos anteriores del usuario (para auditoría)
        // ============================================================
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_anterior = $usuario->traer_usuario_id($_POST['idusuarios']); 
        // 👆 Esto debería devolverte un fetch_assoc con:
        // idusuarios, username, email, perfiles_idperfiles, personas_idpersonas, etc.

        // ============================================================
        // 3) Actualizar PERSONA
        // ============================================================
        $persona = new Persona();
        $persona->setIdpersonas($_POST['idpersonas']);
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->actualizar_persona();

        // En tu modelo getIdpersonas() no necesita parámetro, pero vos le pasabas uno.
        // Dejo como lo usabas, adaptado:
        $personas_idpersonas = $persona->getIdpersonas($_POST['idpersonas']);

        if (!$personas_idpersonas) {
            header('location: ../../index.php?page=registrar_clientes&mensaje=Error al cargar Persona.&status=error');
            return;
        }

        // ============================================================
        // 4) Actualizar / agregar CONTACTO
        //    (vos estás agregando un nuevo contacto con agregar_contato())
        // ============================================================
        $contactos = new Contacto();
        $contactos->setPersonas_idPersonas($personas_idpersonas);
        $contactos->setValor($_POST['contacto']);
        $contactos->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
        $contactos->agregar_contato();

        // ============================================================
        // 5) Actualizar / agregar DOCUMENTO
        // ============================================================
        $documento = new Documento();
        $documento->setPersonas_idPersonas($personas_idpersonas);
        $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);

        // Eliminar puntos del documento
        $documento_sin_puntos = str_replace('.', '', $_POST['documento']);
        $documento->setValor($documento_sin_puntos);
        $documento->agregar_documento();

        // ============================================================
        // 6) Actualizar / agregar DOMICILIO
        // ============================================================
        $domicilio = new Domicilios();
        $domicilio->setPersonas_idPersonas($personas_idpersonas);
        $domicilio->setDescripcion($_POST['domicilio']);
        $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
        $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
        $domicilio->agregar_domicilio();

        // ============================================================
        // 7) Actualizar USUARIO
        // ============================================================
        $usuario->setUsername($_POST['username']);
        $usuario->setEmail($_POST['email']);
        $usuario->setPersonas_idpersonas($personas_idpersonas);
        $usuario->actualizar_usuario();

        // ============================================================
        // 8) Armar datos_anteriores y datos_nuevos para AUDITORÍA
        // ============================================================

        // 🕓 Datos ANTERIORES (lo que teníamos en la tabla usuarios antes del cambio)
        $datos_anteriores = null;
        if ($usuario_anterior) {
            $datos_anteriores = [
                'usuario' => [
                    'idusuarios'  => (int)$usuario_anterior['idusuarios'],
                    'username'    => $usuario_anterior['username'] ?? null,
                    'email'       => $usuario_anterior['email'] ?? null,
                    'perfiles_id' => $usuario_anterior['perfiles_idperfiles'] ?? null,
                    'personas_id' => $usuario_anterior['personas_idpersonas'] ?? null,
                ],
                // Si más adelante tenés métodos para traer persona/contacto/doc/domicilio "antes",
                // podés agregarlos acá también.
            ];
        }

        // 🕒 Datos NUEVOS (lo que quedó luego de actualizar)
        $datos_nuevos = [
            'usuario' => [
                'idusuarios'  => (int)$_POST['idusuarios'],
                'username'    => $_POST['username'],
                'email'       => $_POST['email'],
                'perfiles_id' => $_POST['perfiles_idperfiles'] ?? ($usuario_anterior['perfiles_idperfiles'] ?? null),
                'personas_id' => $personas_idpersonas,
            ],
            'persona' => [
                'idpersonas'       => (int)$personas_idpersonas,
                'nombre'           => $_POST['nombre'],
                'apellido'         => $_POST['apellido'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'tipo_sexo_id'     => $_POST['tipo_sexo_idtipo_sexo'],
            ],
            'contacto' => [
                'tipo_contacto_id' => $_POST['tipo_contacto_idtipo_contacto'] ?? null,
                'valor'            => $_POST['contacto'] ?? null,
            ],
            'documento' => [
                'tipo_documento_id' => $_POST['tipo_documento_idtipo_documento'] ?? null,
                'valor'             => $documento_sin_puntos ?? null,
            ],
            'domicilio' => [
                'descripcion'       => $_POST['domicilio'] ?? null,
                'tipo_domicilio_id' => $_POST['tipo_domicilio_idtipo_domicilio'] ?? null,
                'barrios_id'        => $_POST['barrios_idbarrios'] ?? null,
            ],
        ];

        // ============================================================
        // 9) Registrar AUDITORÍA
        // ============================================================
        $auditoria = new AuditoriaEdiciones();
        $auditoria->registrar(
            'usuarios',                             // tabla
            $_POST['idusuarios'],                   // id_registro
            'Clientes',                             // módulo lógico
            'UPDATE',                               // acción
            $datos_anteriores,                      // datos_anteriores (snapshot previo)
            $datos_nuevos,                          // datos_nuevos (snapshot nuevo)
            'Modificación de datos de cliente',     // descripción
            $_SESSION['idusuarios'] ?? null         // usuario que realizó la acción
        );

        // ============================================================
        // 10) Redirección final
        // ============================================================
        header('location: ../../index.php?page=listado_clientes&mensaje=Cliente modificado correctamente.&status=success');
    }


    /* ============================================================
       ACTUALIZAR EMPLEADO
    ============================================================ */
    public function actualizar_empleado()
    {
        // ============================================================
        // 1) Validar que sea mayor de 18 años
        // ============================================================
        $fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y;

        if ($edad < 18) {
            header('location: ../../index.php?page=registrar_empleados&mensaje=Debes ser mayor de 18 años para registrarte.&status=error');
            return;
        }

        // ============================================================
        // 2) Traer datos ANTERIORES del usuario (para auditoría)
        // ============================================================
        $usuario = new Usuario();
        $usuario->setIdUsuarios($_POST['idusuarios']);
        $usuario_anterior = $usuario->traer_usuario_id($_POST['idusuarios']); 
        // Debe devolver algo tipo:
        // idusuarios, username, email, perfiles_idperfiles, personas_idpersonas, etc.

        // ============================================================
        // 3) Actualizar PERSONA
        // ============================================================
        $persona = new Persona();
        $persona->setIdpersonas($_POST['idpersonas']);
        $persona->setNombre($_POST['nombre']);
        $persona->setApellido($_POST['apellido']);
        $persona->setFecha_nacimiento($_POST['fecha_nacimiento']);
        $persona->setTipo_sexo_idtipo_sexo($_POST['tipo_sexo_idtipo_sexo']);
        $persona->actualizar_persona();

        // Según tu modelo, vos usabas getIdpersonas($_POST['idpersonas']),
        // lo mantengo igual para no romper nada:
        $personas_idpersonas = $persona->getIdpersonas($_POST['idpersonas']);

        if (!$personas_idpersonas) {
            header('location: ../../index.php?page=registrar_empleados&mensaje=Error al cargar Persona.&status=error');
            return;
        }

        // ============================================================
        // 4) Actualizar / agregar CONTACTO
        // ============================================================
        $contactos = new Contacto();
        $contactos->setPersonas_idPersonas($personas_idpersonas);
        $contactos->setValor($_POST['contacto']);
        $contactos->setTipo_contactos_idtipo_contactos($_POST['tipo_contacto_idtipo_contacto']);
        $contactos->agregar_contato();

        // ============================================================
        // 5) Actualizar / agregar DOCUMENTO
        // ============================================================
        $documento = new Documento();
        $documento->setPersonas_idPersonas($personas_idpersonas);
        $documento->setTipo_documento_idTipo_documento($_POST['tipo_documento_idtipo_documento']);

        // Eliminar los puntos del documento
        $documento_sin_puntos = str_replace('.', '', $_POST['documento']);
        $documento->setValor($documento_sin_puntos);
        $documento->agregar_documento();

        // ============================================================
        // 6) Actualizar / agregar DOMICILIO
        // ============================================================
        $domicilio = new Domicilios();
        $domicilio->setPersonas_idPersonas($personas_idpersonas);
        $domicilio->setDescripcion($_POST['domicilio']);
        $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['tipo_domicilio_idtipo_domicilio']);
        $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
        $domicilio->agregar_domicilio();

        // ============================================================
        // 7) Actualizar USUARIO
        // ============================================================
        $usuario->setUsername($_POST['username']);
        $usuario->setEmail($_POST['email']);
        $usuario->setPersonas_idpersonas($personas_idpersonas);
        $usuario->actualizar_usuario();

        // ============================================================
        // 8) Armar datos_anteriores y datos_nuevos para AUDITORÍA
        // ============================================================

        // 🕓 Snapshot ANTERIOR (lo que había antes del update)
        $datos_anteriores = null;
        if ($usuario_anterior) {
            $datos_anteriores = [
                'usuario' => [
                    'idusuarios'  => (int)$usuario_anterior['idusuarios'],
                    'username'    => $usuario_anterior['username'] ?? null,
                    'email'       => $usuario_anterior['email'] ?? null,
                    'perfiles_id' => $usuario_anterior['perfiles_idperfiles'] ?? null,
                    'personas_id' => $usuario_anterior['personas_idpersonas'] ?? null,
                ],
                // Si más adelante querés, podés traer y guardar también
                // snapshot de persona/contacto/doc/domicilio ANTES del cambio.
            ];
        }

        // 🕒 Snapshot NUEVO (lo que quedó después del update)
        $datos_nuevos = [
            'usuario' => [
                'idusuarios'  => (int)$_POST['idusuarios'],
                'username'    => $_POST['username'],
                'email'       => $_POST['email'],
                'perfiles_id' => $_POST['perfiles_idperfiles'] ?? ($usuario_anterior['perfiles_idperfiles'] ?? null),
                'personas_id' => $personas_idpersonas,
            ],
            'persona' => [
                'idpersonas'       => (int)$personas_idpersonas,
                'nombre'           => $_POST['nombre'],
                'apellido'         => $_POST['apellido'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'tipo_sexo_id'     => $_POST['tipo_sexo_idtipo_sexo'],
            ],
            'contacto' => [
                'tipo_contacto_id' => $_POST['tipo_contacto_idtipo_contacto'] ?? null,
                'valor'            => $_POST['contacto'] ?? null,
            ],
            'documento' => [
                'tipo_documento_id' => $_POST['tipo_documento_idtipo_documento'] ?? null,
                'valor'             => $documento_sin_puntos ?? null,
            ],
            'domicilio' => [
                'descripcion'       => $_POST['domicilio'] ?? null,
                'tipo_domicilio_id' => $_POST['tipo_domicilio_idtipo_domicilio'] ?? null,
                'barrios_id'        => $_POST['barrios_idbarrios'] ?? null,
            ],
        ];

        // ============================================================
        // 9) Registrar AUDITORÍA
        // ============================================================
        $auditoria = new AuditoriaEdiciones();
        $auditoria->registrar(
            'usuarios',                                 // tabla
            $_POST['idusuarios'],                       // id_registro
            'Empleados',                                // módulo lógico
            'UPDATE',                                   // acción
            $datos_anteriores,                          // datos_anteriores
            $datos_nuevos,                              // datos_nuevos
            'Modificación de datos de empleado',        // descripción
            $_SESSION['idusuarios'] ?? null             // usuario que hizo el cambio
        );

        // ============================================================
        // 🔚 10) Redirección final
        // ============================================================
        header('location: ../../index.php?page=listado_empleados&mensaje=Empleado modificado correctamente.&status=success');
    }


    /* ============================================================
       RECUPERAR PASSWORD VIA TOKEN
    ============================================================ */
    public function recuperar_password()
    {
        if (
            !isset($_POST['new_password'], $_POST['new_password_confirm'], $_POST['token']) ||
            empty($_POST['new_password']) ||
            empty($_POST['new_password_confirm']) ||
            empty($_POST['token'])
        ) {
            header('location: ../../index.php?page=recuperar_password&mensaje=Todos los datos son obligatorios&status=error');
            return;
        }

        $conexion = new Conexion();
        $token = $_POST['token'];

        // Token válido
        $query = "SELECT Usuarios_idusuarios FROM tokens_recuperacion WHERE token = '$token' AND fecha_expiracion > NOW()";
        $resultado = $conexion->consultar($query);

        if ($resultado->num_rows === 0) {
            header('location: ../../index.php?page=recuperar_password&mensaje=El enlace de recuperación ha expirado o es inválido.&status=error');
            return;
        }

        $usuario_id = $resultado->fetch_assoc()['Usuarios_idusuarios'];

        if ($_POST['new_password'] !== $_POST['new_password_confirm']) {
            header('location: ../../index.php?page=recuperar_password&mensaje=Las contraseñas nuevas no coinciden&status=error');
            return;
        }

        $nueva_contraseña_hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        // Actualizar password
        $update_query = "UPDATE usuarios SET password = '$nueva_contraseña_hashed' WHERE idusuarios = '$usuario_id'";
        $conexion->consultar($update_query);

        // Eliminar token
        $conexion->consultar("DELETE FROM tokens_recuperacion WHERE token = '$token'");

        // 🔹 Auditoría: recuperación de contraseña por token
        $auditoria = new AuditoriaEdiciones();
        $auditoria->registrar(
            'usuarios',
            $usuario_id,
            'Usuarios',
            'UPDATE',
            [
                'idusuarios' => $usuario_id,
                'accion'     => 'recover_password_before'
            ],
            [
                'idusuarios' => $usuario_id,
                'accion'     => 'recover_password_after'
            ],
            'Recuperación de contraseña vía token de recuperación',
            $_SESSION['idusuarios'] ?? null // puede ser null si usuario no está logueado
        );

        header('location: ../../index.php?page=login&mensaje=Contraseña restablecida con éxito. Por favor, inicie sesión.&status=success');
    }
}

?>
