
//Imagen inicial de la pagina
let contenido = document.getElementById("contenido");
let imagen = document.createElement("img");
imagen.src = "../imagen/imagenIndex1.jpg";
imagen.alt = "Foto del centro diurno Vida Activa";
imagen.className = "hero__photo";
contenido.appendChild(imagen);