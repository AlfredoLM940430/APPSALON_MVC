<h2 class="nombre-pagina">Olvide Contraseña</h2>

<p class="descripcion-pagina">Reestablecer Contraseña</p>

<?php include_once __DIR__ . "/../templates/alertas.php"?>

<form action="/olvide" class="formulario" method="POST">
    <div class="campo">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="Tu E-Mail">
    </div>

    <input type="submit" class="boton" value="Enviar Instrucciones"> 
</form>

<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Iniciar sesión</a>
    <a href="/crear-cuenta">¿Aun no tienes una cuenta? Crea una!</a>
</div>