<?php
session_start();

require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento(
    $pdo,
    $_SESSION['usuario_id'],
    1
);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 1"
);

// Marcar como completado
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        1
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 1"
    );

    header("Location: ../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>LECTIUM - Ejercicio 1</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

<style>

body{

background:
linear-gradient(135deg,#4e54c8,#8f94fb);

min-height:100vh;
padding:30px;

}

.exercise-card{

background:white;

max-width:900px;
margin:auto;

padding:40px;

border-radius:30px;

box-shadow:
0 15px 40px rgba(0,0,0,.25);

}

.title{

font-size:50px;
font-weight:bold;

text-align:center;

color:#4e54c8;

}

.subtitle{

text-align:center;
font-size:22px;

margin-bottom:30px;

color:#666;

}

.timer-container{

display:flex;
justify-content:center;

margin-bottom:30px;

}

.timer-circle{

width:150px;
height:150px;

border-radius:50%;

background:
linear-gradient(
135deg,
#ff512f,
#dd2476
);

display:flex;
justify-content:center;
align-items:center;

font-size:38px;
font-weight:bold;

color:white;

box-shadow:
0 0 30px rgba(255,255,255,.5);

animation:pulse 1s infinite;

}

@keyframes pulse{

0%{transform:scale(1);}
50%{transform:scale(1.08);}
100%{transform:scale(1);}

}

.exercise-area{

background:#f8f9ff;

padding:30px;

border-radius:25px;

text-align:center;

}

#mainImage{

max-height:500px;

transition:.5s;

animation:zoomImage 4s infinite alternate;

}

@keyframes zoomImage{

from{

transform:scale(1);

}

to{

transform:scale(1.03);

}

}

.hidden{

display:none!important;

}

.dashboard-float{

position:fixed;
right:20px;
top:50%;

transform:translateY(-50%);

z-index:999;

}

.dashboard-btn{

padding:15px 20px;

font-size:16px;
font-weight:bold;

border-radius:15px;

box-shadow:
0 5px 15px rgba(0,0,0,.2);

}

.answer-card{

background:#f8f9ff;

padding:30px;

border-radius:25px;

}

.star{

position:fixed;

font-size:30px;

animation:fall 3s linear forwards;

z-index:9999;

}

@keyframes fall{

0%{

transform:
translateY(-100px)
rotate(0deg);

opacity:1;

}

100%{

transform:
translateY(100vh)
rotate(360deg);

opacity:0;

}

}

.badge-top{

background:#4e54c8;
font-size:18px;

padding:10px 20px;

border-radius:20px;

margin-bottom:20px;

}

</style>

</head>
<body>

<div class="dashboard-float">

<a
href="../dashboard.php"
class="btn btn-warning dashboard-btn">

🏠 Dashboard

</a>

</div>

<div class="exercise-card">

<div class="text-center">

<span class="badge badge-top">

📖 Ejercicio 1 de 12

</span>

</div>

<h1 class="title">

🧠 EJERCICIO 1

</h1>

<p class="subtitle">

Agiliza tu percepción observando la imagen

</p>

<div class="timer-container">

<div
id="timer"
class="timer-circle">

00:30

</div>

</div>

<div
id="exerciseArea"
class="exercise-area">

<h4 class="mb-4">

👀 Observa atentamente la imagen

</h4>

<img
src="../assets/images/lectura1.png"
id="mainImage"
class="img-fluid rounded">

</div>

<div
id="answerSection"
class="answer-card hidden">

<h3 class="text-center mb-4">

¿Cuántos círculos viste?

</h3>

<input
type="number"
id="respuesta"
class="form-control form-control-lg text-center"
placeholder="Escribe tu respuesta">

<div class="text-center mt-4">

<button
onclick="enviarRespuesta()"
class="btn btn-success btn-lg px-5">

Finalizar

</button>

</div>

</div>

</div>

<form
method="POST"
id="formCompletar"
class="d-none">

<input
type="hidden"
name="completar"
value="1">

</form>

<script>

let tiempo = 30;

const timer =
document.getElementById("timer");

const imageArea =
document.getElementById("exerciseArea");

const answerSection =
document.getElementById("answerSection");

const countdown =
setInterval(()=>{

tiempo--;

timer.innerHTML =
`00:${String(tiempo).padStart(2,"0")}`;

if(tiempo <= 10){

timer.style.background =
"linear-gradient(135deg,#ff0000,#ff5722)";

}

if(tiempo <= 0){

clearInterval(countdown);

imageArea.classList.add("hidden");

answerSection.classList.remove("hidden");

timer.innerHTML =
"⏰";

}

},1000);

function estrellas(){

for(let i=0;i<35;i++){

let star =
document.createElement("div");

star.innerHTML="⭐";

star.classList.add("star");

star.style.left =
Math.random()*100+"vw";

document.body.appendChild(star);

setTimeout(()=>{

star.remove();

},3000);

}

}

function enviarRespuesta(){

const respuesta =
parseInt(
document.getElementById("respuesta").value
);

if(isNaN(respuesta)){

alert(
"⚠️ Escribe un número"
);

return;

}

if(respuesta === 16){

confetti({

particleCount:250,
spread:180,
origin:{y:.6}

});

estrellas();

setTimeout(()=>{

alert(
"🎉 ¡Excelente percepción! Has completado el ejercicio."
);

document
.getElementById("formCompletar")
.submit();

},1500);

}else{

alert(
"👏 Buen intento. Continúa practicando para mejorar tu percepción."
);

if(confirm(
"¿Deseas marcar el ejercicio como completado?"
)){

document
.getElementById("formCompletar")
.submit();

}

}

}

</script>

</body>
</html>