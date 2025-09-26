<?php

require_once('conexion.php');

class Empleado{
    private $idempleados;
    private $legajo;
    private $tipo_de_puestos_idtipo_de_puestos;
    private $Usuarios_idUsuarios;

    public function __construct($idempleados='', $legajo='', $tipo_de_puestos_idtipo_de_puestos='', $Usuarios_idUsuarios='') {
        $this->idempleados = $idempleados;
        $this->legajo = $legajo;
        $this->tipo_de_puestos_idtipo_de_puestos = $tipo_de_puestos_idtipo_de_puestos;
        $this->Usuarios_idUsuarios = $Usuarios_idUsuarios;
    }

    public function traerEmpleadoPorUsuario($idusuario) {
        $conexion = new Conexion();
        $query = "SELECT idempleados FROM empleados WHERE Usuarios_idusuarios = $idusuario LIMIT 1";
        $resultado = $conexion->consultar($query);
        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            return $row['idempleados'];
        }
        return null;
    }

    public function obtenerUltimoLegajo() {
        $conexion = new Conexion();
        $query = "SELECT legajo FROM empleados WHERE legajo IS NOT NULL ORDER BY idempleados DESC LIMIT 1";
        $resultado = $conexion->consultar($query);
        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            return $row['legajo'];
        }
        return null;
    }

    public function asignarLegajoYPuesto($idusuario, $legajo, $idtipo_puesto) {
        $conexion = new Conexion();
        $query = "INSERT INTO empleados (legajo, tipo_de_puestos_idtipo_de_puestos, Usuarios_idusuarios)
                VALUES ('$legajo', $idtipo_puesto, $idusuario)";
        return $conexion->consultar($query);
    }

    public function actualizarLegajoYPuesto($idempleados, $legajo, $idtipo_puesto) {
        $conexion = new Conexion();
        $query = "UPDATE empleados 
                SET legajo = '$legajo', tipo_de_puestos_idtipo_de_puestos = $idtipo_puesto
                WHERE idempleados = $idempleados";
        return $conexion->consultar($query);
    }

    public function consultar_empleado_id($idempleados){
        $conexion = new Conexion();
        $query = "SELECT *, usuarios.*, personas.*,tipo_sexo.*, tipo_sexo.descripcion as nombre_tipo_sexo, documentos.*, documentos.valor as valor_documento, Tipo_documento.*, Tipo_documento.descripcion as nombre_tipo_documento, contactos.*, contactos.valor as valor_contacto, tipo_contacto.*, tipo_contacto.descripcion as nombre_tipo_contacto, tipo_domicilio.*, tipo_domicilio.descripcion as nombre_tipo_domicilio, domicilios.*, domicilios.descripcion as nombre_domicilio, barrios.*, barrios.descripcion as nombre_barrio, localidades.*, localidades.descripcion as nombre_localidad, provincias.*, provincias.descripcion as nombre_provincia, paises.*, paises.descripcion as nombre_pais FROM empleados INNER JOIN Usuarios on empleados.Usuarios_idUsuarios = Usuarios.idUsuarios INNER JOIN personas on usuarios.personas_idpersonas = personas.idpersonas INNER JOIN tipo_sexo on personas.tipo_sexo_idtipo_sexo = tipo_sexo.idtipo_sexo INNER JOIN documentos on documentos.Personas_idPersonas = personas.idpersonas INNER JOIN Tipo_documento on documentos.Tipo_documento_idTipo_documento = Tipo_documento.idTipo_documento INNER JOIN contactos on contactos.Personas_idPersonas = personas.idpersonas INNER JOIN tipo_contacto on contactos.tipo_contactos_idtipo_contactos = tipo_contacto.idtipo_contacto INNER JOIN domicilios on domicilios.Personas_idPersonas = personas.idpersonas INNER JOIN tipo_domicilio on domicilios.tipo_domicilio_idtipo_domicilio = tipo_domicilio.idtipo_domicilio INNER JOIN barrios on domicilios.barrios_idbarrios = barrios.idbarrios INNER JOIN localidades on barrios.localidades_idlocalidades = localidades.idlocalidades INNER JOIN provincias on localidades.provincias_idprovincias = provincias.idprovincias INNER JOIN paises on provincias.paises_idpaises = paises.idpaises WHERE idempleados = $idempleados";
        $resultado = $conexion->consultar($query);
        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc();
        }
        return null;
    }

    /**
     * Get the value of idempleados
     */ 
    public function getIdempleados()
    {
        return $this->idempleados;
    }

    /**
     * Set the value of idempleados
     *
     * @return  self
     */ 
    public function setIdempleados($idempleados)
    {
        $this->idempleados = $idempleados;

        return $this;
    }

    /**
     * Get the value of legajo
     */ 
    public function getLegajo()
    {
        return $this->legajo;
    }

    /**
     * Set the value of legajo
     *
     * @return  self
     */ 
    public function setLegajo($legajo)
    {
        $this->legajo = $legajo;

        return $this;
    }

    /**
     * Get the value of tipo_de_puestos_idtipo_de_puestos
     */ 
    public function getTipo_de_puestos_idtipo_de_puestos()
    {
        return $this->tipo_de_puestos_idtipo_de_puestos;
    }

    /**
     * Set the value of tipo_de_puestos_idtipo_de_puestos
     *
     * @return  self
     */ 
    public function setTipo_de_puestos_idtipo_de_puestos($tipo_de_puestos_idtipo_de_puestos)
    {
        $this->tipo_de_puestos_idtipo_de_puestos = $tipo_de_puestos_idtipo_de_puestos;

        return $this;
    }

    /**
     * Get the value of Usuarios_idUsuarios
     */ 
    public function getUsuarios_idUsuarios()
    {
        return $this->Usuarios_idUsuarios;
    }

    /**
     * Set the value of Usuarios_idUsuarios
     *
     * @return  self
     */ 
    public function setUsuarios_idUsuarios($Usuarios_idUsuarios)
    {
        $this->Usuarios_idUsuarios = $Usuarios_idUsuarios;

        return $this;
    }
}