
document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp(){
    buscarFecha();
}

function buscarFecha() {
    
    const fechaInput = document.querySelector('#fecha');
    fechaInput.addEventListener('input', function(e) {

        const fechaSeleccion = e.target.value;

        window.location = `?fecha=${fechaSeleccion}`;
    })
}