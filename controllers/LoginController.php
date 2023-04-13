<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

    public static function login(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $auth = new Usuario($_POST);
            
            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                //
                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario) {
                    //Verificar password
                    if($usuario->comprobarPasswordVerificado($auth->password)) {
                        
                        //Iniciar session y guardar valores de sesion
                        isSession();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Rediccionamiento
                        if($usuario->admin === '1') {

                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');

                        } else {

                            header('Location: /cita');
                        }
                    }

                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [

            'alertas' => $alertas
        ]);

    }

    public static function logout() {
        
        isSession();
        $_SESSION = [];
        header('Location: /');

    }

    public static function olvide(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario && $usuario->confirmado === '1') {

                    //Generar token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //Enciar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    //Alertas
                    Usuario::setAlerta('exito', 'Revisa tu email');
                    

                } else {

                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');

                }
            }
        }
        $alertas = Usuario::getAlertas();
        
        $router->render('auth/olvide-password', [
            
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {

        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        
        //Buscar por token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Valido!');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Leer nuevo password y guardar
            $passord = new Usuario($_POST);
            $alertas = $passord->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $passord->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
            }

        }
        
        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperar-password', [

            'alertas' => $alertas,
            'error' => $error

        ]);
        
    }

    public static function crear(Router $router) {

        $usuario = new Usuario;

        //Alertas vacias
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //Revisar alertas
            if(empty($alertas)) {
               $resultado = $usuario->existeUsuario();

               if($resultado->num_rows) {

                $alertas = Usuario::getAlertas();

               } else {
                    //Hash password
                    $usuario->hashPassword();

                    //Generar token unico
                    $usuario->crearToken();

                    //Enviar email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    
                    $email->enviarConfirmacion();

                    //Crear usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {

                        header('Location: /mensaje');
                    }
               }
            }

        }

        $router->render('auth/crear-cuenta', [

            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje (Router $router) {

        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router) {

        $alertas = [];
        //Espacio ?
        $token = s($_GET['token'] . " ");
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            //Msj Error
            Usuario::setAlerta('error', 'Token No Valido');
            
        } else {
            //Usuario confirmado
            $usuario->confirmado = 1; 
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }

}