<?php
require_once('conexion.php');

class Vehiculos extends Paginacion{
    private $idvehiculos;
    private $patente;
    private $chasis;
    private $motor;
    private $año;
    private $modelos_idmodelos;
    private $colores_idcolores;

    public function __construct($idvehiculos='',$patente='',$chasis='',$motor='',$año='',$modelos_idmodelos='',$colores_idcolores='') {
        $this->idvehiculos = $idvehiculos;
        $this->patente = $patente;
        $this->chasis = $chasis;
        $this->motor = $motor;
        $this->año = $año;
        $this->modelos_idmodelos = $modelos_idmodelos;
        $this->colores_idcolores = $colores_idcolores;
    }

    public function agregar_vehiculo(){
        $conexion = new Conexion();
        $query = "INSERT INTO vehiculos (patente,chasis,motor,año,modelos_idmodelos,colores_idcolores) VALUES ('$this->patente','$this->chasis','$this->motor','$this->año','$this->modelos_idmodelos','$this->colores_idcolores')";
        return $conexion->insertar($query);
    }

    public function actualizar_vehiculo(){
        $conexion = new Conexion();
        $query = "UPDATE vehiculos SET patente = '$this->patente', chasis = '$this->chasis', motor = '$this->motor', año = '$this->año', modelos_idmodelos = '$this->modelos_idmodelos', colores_idcolores = '$this->colores_idcolores' WHERE idvehiculos = '$this->idvehiculos'";
        return $conexion->actualizar($query);
    }

    public function eliminar_vehiculo(){
        $conexion = new Conexion();
        $query = "UPDATE vehiculos SET activo = 0 WHERE idvehiculos = '$this->idvehiculos'";
        return $conexion->actualizar($query);
    }

    public function traer_cantidad_vehiculo(){
        $conexion = new Conexion();
        $query = "SELECT count(*) as total FROM vehiculos WHERE activo_vehiculo = 1";
        return $conexion->consultar($query);
    }

    public function traer_vehiculos(){
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*,colores.idcolores, colores.descripcion as nombre_color, marcas.idmarcas, marcas.nombre as nombre_marca,modelos.idmodelos,modelos.nombre as nombre_modelo,tipo_vehiculos.idtipo_vehiculos, tipo_vehiculos.nombre as nombre_tipo FROM vehiculos INNER JOIN modelos on vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on modelos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE activo_vehiculo = 1 LIMIT $this->pagina_actual,$this->paginacion";
        return $conexion->consultar($query);
    }

    public function traer_todos_vehiculos() {
        $conexion = new Conexion();
        $query = "SELECT vehiculos.*,colores.idcolores, colores.descripcion as nombre_color, marcas.idmarcas, marcas.nombre as nombre_marca,modelos.idmodelos, modelos.nombre as nombre_modelo,tipo_vehiculos.idtipo_vehiculos, tipo_vehiculos.nombre as nombre_tipo_vehiculo FROM vehiculos INNER JOIN modelos on vehiculos.modelos_idmodelos = modelos.idmodelos INNER JOIN colores on vehiculos.colores_idcolores = colores.idcolores INNER JOIN marcas on modelos.marcas_idmarcas = marcas.idmarcas INNER JOIN tipo_vehiculos on modelos.tipo_vehiculos_idtipo_vehiculos = tipo_vehiculos.idtipo_vehiculos WHERE activo_vehiculo = 1";
        return $conexion->consultar($query);
    }
}