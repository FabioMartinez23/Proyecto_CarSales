<?php

require_once('conexion.php');
require_once('paginacion.php');

class Usuario extends Paginacion {
private $idusuarios;
private $username;
private $email;
private $password;
private $perfiles_idperfiles;

public function __construct($idusuarios='', $username='', $email='', $password='',$perfiles_idperfiles='') {
        $this->idusuarios = $idusuarios;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->perfiles_idperfiles = $perfiles_idperfiles;
}

public function guardar(){
        $conexion = new Conexion();
        $password = password_hash($this->password, PASSWORD_DEFAULT);
        $query = "INSERT INTO usuarios (username, email, password,perfiles_idperfiles) VALUES ('$this->username','$this->email','$password','$this->perfiles_idperfiles')";
        return $conexion->insertar($query);
}

public function actualizar(){
        $conexion = new Conexion();
        $password = password_hash($this->password, PASSWORD_DEFAULT);
        $query = "UPDATE usuarios SET username = '$this->username', email = '$this->email', password = '$password', perfiles_idperfiles = '$this->perfiles_idperfiles'";
        return $conexion->actualizar($query);
}

public function eliminar(){
        $conexion = new Conexion();
        $query = "UPDATE usuarios SET activo_usuario = 0 WHERE idusuarios = '$this->idusuarios'";
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
            return $resultado->fetch_assoc(); // Devuelve el array con la contraseña
        }
        return null; // Si no se encuentra el usuario
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
}

#$usuarios = new Usuario('','proyecto','proyecto@gmail.com','proyecto','1');
#$resultado = $usuarios->guardar();
#echo $resultado;
?>