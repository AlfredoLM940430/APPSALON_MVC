let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '', 
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});
    
function iniciarApp() {

    mostrarSeccion();  //Muestra y oculta las secciones  
    tabs(); //Cambia la seccion al precionar
    botonesPaginador() //Agrega o quita botones paginador
    paginaSiguiente();
    paginaAnterior();

    consultarAPI();

    idCliente();
    nombreCliente();
    seleccionarFecha();
    seleccionarHora();

    mostrarResumen();
}

function mostrarSeccion() {

    //Ocultar seccion **clase mostrar
    const seccionAnterior = document.querySelector('.mostrar');

    if(seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }
    

    ///Seleccionar secicon
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    //Quita la clase tab actual
    const tabAnterior = document.querySelector('.actual');

    if(tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    //Resalta tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {

    const botones = document.querySelectorAll('.tabs button');

    botones.forEach (boton => {

        boton.addEventListener('click', function(e) {
            paso = (parseInt(e.target.dataset.paso));

            mostrarSeccion();
            botonesPaginador();

        });
    });
}

function botonesPaginador() {

    const paginaSiguiente = document.querySelector('#siguiente');
    const paginaAnterior = document.querySelector('#anterior');

    if(paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if(paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
    mostrarSeccion();
}

function paginaAnterior() {

    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function() {

        if(paso <= pasoInicial) return;
        paso--;

        botonesPaginador();
    });
}

function paginaSiguiente() {

    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function() {

        if(paso >= pasoFinal) return;
        paso++;

        botonesPaginador();
    });
}

//No hace esperar a las siguientes lineas de codigo, brinca a la siguiente funcion *se ejecuta en cascada*
async function consultarAPI() {

    try {
        const url = `${location.origin}/api/servicios`;

        //Detiene ejecucion hasta finalizar await  
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios) {
    
    servicios.forEach(servicio => {

        const {id, nombre, precio} = servicio;
        
        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$ ${precio}`;

        const servicioDIV = document.createElement('DIV');
        servicioDIV.classList.add('servicio');
        servicioDIV.dataset.idServicio = id;
        servicioDIV.onclick = function() {
            seleccionarServicio(servicio);
        }

        servicioDIV.appendChild(nombreServicio);
        servicioDIV.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDIV);
    });
}

function seleccionarServicio(servicio) {

    const {id} = servicio;

    //Extrae arreglo de servicios
    const {servicios} = cita;

    //Identificar elemento click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    //Coprobar servicio agregado
    if(servicios.some(agregado => agregado.id === id)) {

        //Eliminar
        cita.servicios = servicios.filter(agregado => agregado.id !== id);
        divServicio.classList.remove('seleccionado');
        
    } else {

        //Agregar
        //Copia de servicios, agrega nuevo servicio de la funcion
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }

    // console.log(cita);
}

function idCliente() {
    cita.id = document.querySelector('#id').value; 
}

function nombreCliente() {
    
    cita.nombre = document.querySelector('#nombre').value;   
}

function seleccionarFecha() {

    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e) {

        const dias = new Date(e.target.value).getUTCDay();

        if([6, 0]. includes(dias)){
            e.target.value = '';
            mostrarAlerta('No Hay Citas Los Fines De Semena', 'error', '.formulario' );
        } else {
            cita.fecha = e.target.value;

        }
 
    });
}

function seleccionarHora() {

    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {
        console.log(e.target.value);

        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];
        if(hora < 10 || hora > 18) {
            e.target.value = '';
            mostrarAlerta('Hora no valida', 'error', '.formulario');
        } else {
            cita.hora = e.target.value;

            // console.log(cita)
        }
    });
}

function mostrarAlerta (mensaje, tipo, elemento, desaparece = true) {

    //Previene generacion de multiples alertas
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    }

    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);
    
    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece) {

        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }


}

function mostrarResumen() {

    const resumen = document.querySelector('.contenido-resumen');

    //Limpiar contenido de resumen
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    if(Object.values(cita).includes('') || cita.servicios.length === 0) {

        mostrarAlerta('Faltan datos de servicio, fecha u hora', 'error', '.contenido-resumen', false);
        return;
    }

    //Formateaar div resumen
    const {nombre, fecha, hora, servicios} = cita;

    //Heading servivios en resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    //Iternado y mostrando servicios
    servicios.forEach(servicios => {

        const {id, precio, nombre} = servicios;

        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicios');
        
        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    //Heading cita en resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);


    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    //Formatear fecha
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const opciones = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
    const fechaUTC = new Date(Date.UTC(year, mes, dia));
    const fechaFromato = fechaUTC.toLocaleDateString('es-MX', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFromato}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora}`;

    //Boton cita
    const botnReservar = document.createElement('BUTTON');
    botnReservar.classList.add('boton');
    botnReservar.textContent = 'Reservar Cita';
    botnReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    // console.log(nombreCliente);

    resumen.appendChild(botnReservar);

}

async function reservarCita() {

    const {nombre, fecha, hora, servicios, id} = cita;
    
    const datos = new FormData();

    const idServicios = servicios.map(servicio => servicio.id);

    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioid', id);
    datos.append('servicios', idServicios);

    try {
      //Peticion a API
        const url = `${location.origin}/api/citas`;

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();
        console.log(resultado.resultado);

        if(resultado.resultado) {
            Swal.fire({
                icon: 'success',
                title: 'Cita Creada',
                text: 'Cita Creada Correctamente',
                button: 'OK'
            }).then(() => {
                 window.location.reload();
            });
        }  
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al guardar la cita'
          })
    }

    


    // Leer datod FormData
    // console.log([...datos]);
}