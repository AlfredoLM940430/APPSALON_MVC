<?php 

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($nombre, $email, $token)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token; 
    }

    public function enviarConfirmacion() {

        //Objeto email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '16323b864a8056';
        $mail->Password = '61c257944c7da2';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@addsalon.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        //setHTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p>Hola <strong>" . $this->nombre . "!</strong>, has creado tu cuenta en Appsalon, confirma presionando el siguiente enlace </p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/confirmar-cuenta?token=" . $this->token . "'> Confirmar Cuenta </a> </p>";
        $contenido .= "<p>Si no creaste una cuenta con nosotros ignorar este mensaje";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //Enviar
        $mail->send();
    }

    public function enviarInstrucciones() {
        
                //Objeto email
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'sandbox.smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Port = 2525;
                $mail->Username = '16323b864a8056';
                $mail->Password = '61c257944c7da2';
        
                $mail->setFrom('cuentas@appsalon.com');
                $mail->addAddress('cuentas@addsalon.com', 'AppSalon.com');
                $mail->Subject = 'Restablece tu Contraseña';
        
                //setHTML
                $mail->isHTML(TRUE);
                $mail->CharSet = 'UTF-8';
        
                $contenido = "<html>";
                $contenido .= "<p>Hola <strong>" . $this->nombre . "!</strong> Has solicitado restablecer tu password, sigue el siguiente enlace </p>";
                $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/recuperar?token=" . $this->token . "'> Recuperar Cuenta </a> </p>";
                $contenido .= "<p>Si no solicitaste un cambio, ignorar este mensaje";
                $contenido .= "</html>";
        
                $mail->Body = $contenido;
        
                //Enviar
                $mail->send();

    }

}