<?php 

namespace Model;

class Servicio extends ActiveRecord {

    //BASE DE DATOS
    protected static $tabla = 'servicios';
    protected static $columnasDB = ['id', 'nombre', 'precio'];

    public $id;
    public $nombre;
    public $precio;

    public function __construct($args= [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? '';
    }

    public function validar()
    {
        if(!$this->nombre) {
            self::$alertas['error'][] = "Nombre del Servicio Obligatorio";
        }

        if(!$this->precio) {
            self::$alertas['error'][] = "Precio del Servicio Obligatorio";
        }

        if( !is_numeric($this->precio)) {
            self::$alertas['error'][] = "No es un formato valido *Precio";
        }
        return self::$alertas;
    }
}
?>