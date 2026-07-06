
//Funcion para llamar imagen 1 - Residencia permanente
let contenedorResidencia = document.getElementById("contenedor-imagen-residencia");

let imagen = document.createElement("img");
imagen.src = "../imagen/imagenServicios1.jpg";
imagen.alt = "Foto del centro diurno Vida Activa";
imagen.className = "imagenServicio1";
contenedorResidencia.appendChild(imagen);


//Funcion para llamar imagen 2 - Centros de cuido diurno

let contenedorDiurno = document.getElementById("contenedor-imagen-diurno");

let imagen2 = document.createElement("img");
imagen2.src = "../imagen/imagenServicios2.jpg";
imagen2.alt = "Foto de las actividades del cuidado diurno";
imagen2.className = "imagenServicio2";
contenedorDiurno.appendChild(imagen2);


//Funcion para llamar imagen 3 - Terapias
let contenedorimagenterapias = document.getElementById("contenedor-imagen-terapias");
let imagen3 = document.createElement("img");
imagen3.src = "../imagen/imagenServicios3.jpg";
imagen3.alt = "Foto de las terapias del centro";
imagen3.className = "imagenServicio3";
contenedorimagenterapias.appendChild(imagen3);


//Funcion para llamar imagen 4 - Valoración incial
let contenedorImagenValoracion = document.getElementById("contenedor-imagen-valoracion");
let imagen4 = document.createElement("img");
imagen4.src = "../imagen/imagenServicios4.jpg"; 
imagen4.alt = "Profesional realizando una valoración de salud a un adulto mayor";
imagen4.className = "imagenServicio4";
contenedorImagenValoracion.appendChild(imagen4);