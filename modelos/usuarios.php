<?php

require_once('conexion.php');
require_once('paginacion.php');

class Usuario extends Paginacion {
private $idusuarios;
private $username;
private $email;
private $password;
private $perfiles_idperfiles;
private $personas_idpersonas;

public function __construct($idusuarios='', $username='', $email='', $password='',$perfiles_idperfiles='', $personas_idpersonas='') {
        $this->idusuarios = $idusuarios;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->perfiles_idperfiles = $perfiles_idperfiles;
        $this->personas_idpersonas = $personas_idpersonas;
}

public function guardar(){
        $conexion = new Conexion();
        $password = password_hash($this->password, PASSWORD_DEFAULT);
        $query = "INSERT INTO usuarios (username, email, password,fecha_alta, perfiles_idperfiles, personas_idpersonas) VALUES ('$this->username','$this->email','$password', CURDATE(),'$this->perfiles_idperfiles', '$this->personas_idpersonas')";
        return $conexion->insertar($query);
}

public function actualizar(){
        $conexion = new Conexion();
        $password = password_hash($this->password, PASSWORD_DEFAULT);
        $query = "UPDATE usuarios SET username = '$this->username', email = '$this->email', password = '$password', perfiles_idperfiles = '$this->perfiles_idperfiles'";
        return $conexion->actualizar($query);
}

public function actualizar_usuario(){
        $conexion = new Conexion();
        $query = "UPDATE usuarios SET username = '$this->username', email = '$this->email' WHERE idusuarios = '$this->idusuarios'";
        $conexion->actualizar($query);
}

public function eliminar(){
        $conexion = new Conexion();
        $fecha_baja = date('Y-m-d');
        $query = "UPDATE usuarios SET activo_usuario = 0, fecha_baja = '$fecha_baja' WHERE idusuarios = '$this->idusuarios'";
        return $conexion->actualizar($query);
}

public function validar_usuario(){
        $conexion = new Conexion;
        $query = "SELECT * FROM usuarios WHERE username = '$this->username'";
        return $conexion->consultar($query);
}

public function validar_usuario_por_id(){
        $conexion = new Conexion;
        $query = "SELECT password FROM usuarios WHERE idusuarios = '$this->idusuarios'";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
                return $resultado->fetch_assoc();
        }
        return null; 
}

public function traer_usuario_por_id($idusuarios){
        $conexion = new Conexion();
        $query = "SELECT usuarios.*, personas.*,tipo_sexo.*, tipo_sexo.descripcion as nombre_tipo_sexo, documentos.*, documentos.valor as valor_documento, Tipo_documento.*, Tipo_documento.descripcion as nombre_tipo_documento, contactos.*, contactos.valor as valor_contacto, tipo_contacto.*, tipo_contacto.descripcion as nombre_tipo_contacto, tipo_domicilio.*, tipo_domicilio.descripcion as nombre_tipo_domicilio, domicilios.*, domicilios.descripcion as nombre_domicilio, barrios.*, barrios.descripcion as nombre_barrio, localidades.*, localidades.descripcion as nombre_localidad, provincias.*, provincias.descripcion as nombre_provincia, paises.*, paises.descripcion as nombre_pais FROM usuarios INNER JOIN personas on usuarios.personas_idpersonas = personas.idpersonas INNER JOIN tipo_sexo on personas.tipo_sexo_idtipo_sexo = tipo_sexo.idtipo_sexo INNER JOIN documentos on documentos.Personas_idPersonas = personas.idpersonas INNER JOIN Tipo_documento on documentos.Tipo_documento_idTipo_documento = Tipo_documento.idTipo_documento INNER JOIN contactos on contactos.Personas_idPersonas = personas.idpersonas INNER JOIN tipo_contacto on contactos.tipo_contactos_idtipo_contactos = tipo_contacto.idtipo_contacto INNER JOIN domicilios on domicilios.Personas_idPersonas = personas.idpersonas INNER JOIN tipo_domicilio on domicilios.tipo_domicilio_idtipo_domicilio = tipo_domicilio.idtipo_domicilio INNER JOIN barrios on domicilios.barrios_idbarrios = barrios.idbarrios INNER JOIN localidades on barrios.localidades_idlocalidades = localidades.idlocalidades INNER JOIN provincias on localidades.provincias_idprovincias = provincias.idprovincias INNER JOIN paises on provincias.paises_idpaises = paises.idpaises WHERE idusuarios = $idusuarios";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
                return $resultado->fetch_assoc();
        }
        return null; 
}

public function traer_usuario_por_idpersona_json($personas_idpersonas){
        $conexion = new Conexion();
        $query = "SELECT * FROM usuarios INNER JOIN personas on usuarios.personas_idpersonas = personas.idpersonas INNER JOIN tipo_sexo on personas.tipo_sexo_idtipo_sexo = tipo_sexo.idtipo_sexo WHERE personas_idpersonas = $personas_idpersonas";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
                return $resultado->fetch_assoc();
        }
        return null; 
}


public function validar_email(){
        $conexion = new Conexion;
        $query = "SELECT * FROM usuarios WHERE email = '$this->email'";
        return $conexion->consultar($query);
}

public function traer_cantidad_usuario(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM usuarios WHERE activo_usuario = 1";
        return $conexion->consultar($query);
}

public function traer_usuarios(){
        $conexion = new Conexion();
        $query = "SELECT * FROM usuarios INNER JOIN perfiles on perfiles.idperfiles = usuarios.perfiles_idperfiles WHERE activo_usuario = 1 limit $this->pagina_actual,$this->paginacion";
        return $conexion->consultar($query);
}

public function cambiar_password(){
        $conexion = new Conexion();
        $password = password_hash($this->password, PASSWORD_DEFAULT);
        $query = "UPDATE usuarios SET password = '$password' WHERE idusuarios = '$this->idusuarios'";
        return $conexion->actualizar($query);
}

public function traer_usuario_id($idusuarios){
        $conexion = new Conexion();
        $query = "SELECT * FROM usuarios WHERE idusuarios = '$idusuarios' AND activo_usuario = 1";
        $resultado = $conexion->consultar($query);
        
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc(); // Devuelve el array con los datos del usuario
        }
        return null; // Si no se encuentra el usuario}
}

public function traer_usuarios_y_personas($idusuarios){
        $conexion = new Conexion();
        $query = "SELECT * FROM usuarios INNER JOIN personas on usuarios.personas_idpersonas = personas.idpersonas WHERE idusuarios = $idusuarios";
        return $conexion->consultar($query);
}

public function traer_clientes($inicio,$cantidad){
        $conexion = new Conexion();
        $query = "SELECT * FROM usuarios INNER JOIN personas on usuarios.Personas_idPersonas = personas.idPersonas INNER JOIN perfiles on usuarios.perfiles_idperfiles = perfiles.idperfiles WHERE perfiles.descripcion = 'Cliente' AND activo_usuario = 1 limit $inicio,$cantidad";
        return $conexion->consultar($query);
}

public function traer_empleados($inicio,$cantidad){
        $conexion = new Conexion();
        $query = "SELECT * FROM usuarios INNER JOIN personas on usuarios.Personas_idPersonas = personas.idPersonas INNER JOIN perfiles on usuarios.perfiles_idperfiles = perfiles.idperfiles WHERE perfiles.descripcion = 'Empleado' AND activo_usuario = 1 limit $inicio,$cantidad";
        return $conexion->consultar($query);
}

public function consultar_usuario($busqueda){
        $conexion = new Conexion();
        $query = "SELECT * FROM usuarios INNER JOIN personas on usuarios.personas_idpersonas = personas.idpersonas INNER JOIN perfiles on usuarios.perfiles_idperfiles = perfiles.idperfiles WHERE (nombre LIKE '%$busqueda%' OR apellido LIKE '%$busqueda%' OR username LIKE '%$busqueda%' OR descripcion LIKE '%$busqueda%') AND activo_usuario = 1";
        return $conexion->consultar($query);
}

public function nuevos_usuarios_registrados($fecha_actual){
        $conexion = new Conexion();
        $query = "SELECT * FROM usuarios WHERE fecha_alta = '$fecha_actual' ORDER BY idusuarios ASC";
        return $conexion->consultar($query);
}

public function contar_nuevos_usuarios($fecha_actual){
        $conexion = new Conexion();
        $query = "SELECT COUNT(idusuarios)  as total_registrados FROM usuarios WHERE fecha_alta = '$fecha_actual' AND activo_usuario = 1";
        return $conexion->consultar($query);
}

public function guardar_token($token, $fecha_expiracion, $id_usuario){
        $conexion = new Conexion();
        $query = "INSERT INTO tokens_recuperacion (token, fecha_expiracion, Usuarios_idusuarios) VALUES ('$token', '$fecha_expiracion', '$id_usuario')";
        return $conexion->insertar($query);
}

public function validar_email_para_contraseña($email) {
        $conexion = new Conexion();
        $query = "SELECT idusuarios FROM usuarios WHERE email = '$email'";
        $resultado = $conexion->consultar($query);

        // Verificar si se encontró el email
        if ($resultado && $resultado->num_rows > 0) {
            // Retornar el resultado como array asociativo
        return $resultado->fetch_assoc();
        }
        return false;  // Retornar false si no se encontró el email
}

public function obtener_id_por_username($username) {
        $conexion = new Conexion();
        $query = "SELECT idusuarios FROM usuarios WHERE username = '$username'";
        $resultado = $conexion->consultar($query);

        if ($resultado && $resultado->num_rows > 0) {
                return $resultado->fetch_assoc()['idusuarios'];
        }
        return false;
}


/**
 * Get the value of id
 */ 
public function getIdUsuarios()
{
        return $this->idusuarios;
}

/**
 * Set the value of id
 *
 * @return  self
 */ 
public function setIdUsuarios($idusuarios)
{
        $this->idusuarios = $idusuarios;

        return $this;
}

/**
 * Get the value of username
 */ 
public function getUsername()
{
        return $this->username;
}

/**
 * Set the value of username
 *
 * @return  self
 */ 
public function setUsername($username)
{
        $this->username = $username;

        return $this;
}

/**
 * Get the value of email
 */ 
public function getEmail()
{
        return $this->email;
}

/**
 * Set the value of email
 *
 * @return  self
 */ 
public function setEmail($email)
{
        $this->email = $email;

        return $this;
}

/**
 * Get the value of password
 */ 
public function getPassword()
{
        return $this->password;
}

/**
 * Set the value of password
 *
 * @return  self
 */ 
public function setPassword($password)
{
        $this->password = $password;

        return $this;
}

/**
 * Get the value of perfiles_id
 */ 
public function getPerfiles_id()
{
        return $this->perfiles_idperfiles;
}

/**
 * Set the value of perfiles_id
 *
 * @return  self
 */ 
public function setPerfiles_id($perfiles_idperfiles)
{
        $this->perfiles_idperfiles = $perfiles_idperfiles;

        return $this;
}

/**
 * Get the value of personas_idpersonas
 */ 
public function getPersonas_idpersonas()
{
return $this->personas_idpersonas;
}

/**
 * Set the value of personas_idpersonas
 *
 * @return  self
 */ 
public function setPersonas_idpersonas($personas_idpersonas)
{
$this->personas_idpersonas = $personas_idpersonas;

return $this;
}

}

#$usuarios = new Usuario('','proyecto','proyecto@gmail.com','proyecto','1');
#$resultado = $usuarios->guardar();
#echo $resultado;
?>