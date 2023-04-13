<?php 

namespace Model;

class Usuario extends ActiveRecord {

    //Base de datos
    protected static $tabla = 'usuarios';
    //Estructura tiene que ser espejo, igual a la base de datos
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    //Crea la estructura/objeto de usuario **Envios con $_POST
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    // Validaciones Crear Cuenta
    public function validarNuevaCuenta() {

        if(!$this->nombre) {
            self::$alertas ['error'][] = 'El Nombre es Obligatorio';
        }

        if(!$this->apellido) {
            self::$alertas ['error'][] = 'El Apellido es Obligatorio';
        }

        if(!$this->email) {
            self::$alertas ['error'][] = 'El Email es Obligatorio';
        }

        if(!$this->password) {
            self::$alertas ['error'][] = 'La Contraseña es Obligatoria';
        }

        if(strlen($this->password) < 6) {
            self::$alertas ['error'][] = 'La Contraseña Debe Contar Por Lo Menos Con 6 Caracteres';
        }
        
        return self::$alertas;
    }

    //Usuario replicado
    public function existeUsuario() {

        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);
        
        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El Email Ya Esta Registrado';
        }

        return $resultado;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        //Generacion de ID, no password
        $this->token = uniqid();
    }

    public function validarLogin() {

        if(!$this->email) {
            self::$alertas['error'][] = 'Email Obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'Contraseña Obligatoria';
        }

        return self::$alertas;
    }

    public function validarEmail() {
        
        if(!$this->email) {
            self::$alertas['error'][] = 'Email Obligatorio';
        }
        return self::$alertas;
    }

    public function validarPassword() {

        if(!$this->password) {
            self::$alertas['error'][] = 'Contraseña Obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener almeos 6 caracteres';
        }
        return self::$alertas;
    }

    public function comprobarPasswordVerificado($password) {

        $resultado= password_verify($password, $this->password);

        if(!$resultado || !$this->confirmado) {

            self::$alertas['error'][] = 'Contraseña incorrecta o no has confirmado tu cuenta';

        } else {

            return true;
        }
    }

}