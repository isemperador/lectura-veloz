<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 10);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 10"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        10  
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 10"
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

<title>Ejercicio 10 - Mensaje Oculto</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

<style>

body{

background:linear-gradient(135deg,#5f2c82,#49a09d);
min-height:100vh;
padding:30px;

}

.exercise{

background:white;
border-radius:30px;
padding:40px;
box-shadow:0 10px 30px rgba(0,0,0,.2);

}

.title{

font-size:50px;
font-weight:bold;
text-align:center;
color:#5f2c82;

}

.subtitle{

text-align:center;
font-size:22px;
margin-bottom:30px;
color:#666;

}

.timer-box{

text-align:center;
margin-bottom:30px;

}

.timer{

width:140px;
height:140px;

border-radius:50%;

background:
linear-gradient(135deg,#ff512f,#dd2476);

display:flex;
align-items:center;
justify-content:center;

margin:auto;

font-size:35px;
font-weight:bold;
color:white;

animation:pulse 1s infinite;

}

@keyframes pulse{

0%{transform:scale(1);}
50%{transform:scale(1.08);}
100%{transform:scale(1);}

}

.word-card{

background:#f8f9ff;
border-radius:25px;
padding:25px;
margin-bottom:25px;

box-shadow:0 5px 15px rgba(0,0,0,.08);

}

.hidden-word{

font-size:35px;
font-weight:bold;
text-align:center;
letter-spacing:2px;
color:#5f2c82;

margin-bottom:15px;

}

.correct{

border:3px solid #28a745 !important;
background:#d4edda !important;

}

.wrong{

border:3px solid #dc3545 !important;
background:#f8d7da !important;

}

.progress{

height:35px;
border-radius:20px;

}

.dashboard{

position:fixed;
right:20px;
top:50%;
transform:translateY(-50%);
z-index:999;

}

</style>

</head>

<body>

<div class="dashboard">

<a href="../dashboard.php"
class="btn btn-warning">

🏠 Dashboard

</a>

</div>

<div class="container">

<div class="exercise">

<h1 class="title">
🔎 EJERCICIO 10
</h1>

<p class="subtitle">
Quita los números y ordena las letras para descubrir el mensaje oculto
</p>

<div class="timer-box">

<div class="timer" id="timer">
02:00
</div>

</div>

<div class="progress mb-4">

<div
id="barra"
class="progress-bar progress-bar-striped progress-bar-animated"
style="width:0%">

0%

</div>

</div>

<div class="word-card">

<div class="hidden-word">
A23R56I9S2U7U6SO
</div>

<input
type="text"
id="r1"
class="form-control form-control-lg"
placeholder="Escribe la palabra">

</div>

<div class="word-card">

<div class="hidden-word">
S45T986M902O4RE5NS21I1O
</div>

<input
type="text"
id="r2"
class="form-control form-control-lg"
placeholder="Escribe la palabra">

</div>

<div class="word-card">

<div class="hidden-word">
L34TE45M89I20U3C7
</div>

<input
type="text"
id="r3"
class="form-control form-control-lg"
placeholder="Escribe la palabra">

</div>

<div class="text-center">

<button
id="btnFinalizar"
class="btn btn-success btn-lg px-5"
disabled
onclick="finalizarEjercicio()">

Finalizar Ejercicio

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

let tiempo=120;

const timer=document.getElementById("timer");

const interval=setInterval(()=>{

tiempo--;

let min=Math.floor(tiempo/60);
let sec=tiempo%60;

timer.innerHTML=
`${String(min).padStart(2,"0")}:${String(sec).padStart(2,"0")}`;

if(tiempo<=20){

timer.style.background=
"linear-gradient(135deg,#ff0000,#ff5722)";

}

if(tiempo<=0){

clearInterval(interval);

alert("⏰ Tiempo terminado");

location.reload();

}

},1000);

const respuestas = {

r1:"USUARIOS",
r2:"MONITORES",
r3:"LECTIUM"

};

let correctas = 0;

Object.keys(respuestas).forEach(id=>{

const campo =
document.getElementById(id);

campo.addEventListener("input",()=>{

if(campo.readOnly) return;

const valor =
campo.value
.toUpperCase()
.trim();

if(valor === respuestas[id]){

campo.classList.remove("wrong");

campo.classList.add("correct");

campo.readOnly = true;

correctas++;

actualizarBarra();

}else{

campo.classList.remove("correct");

if(valor.length > 0){

campo.classList.add("wrong");

}

}

});

});

function actualizarBarra(){

const porcentaje =
(correctas / 3) * 100;

const barra =
document.getElementById("barra");

barra.style.width =
porcentaje + "%";

barra.innerHTML =
Math.round(porcentaje) + "%";

if(correctas === 3){

barra.classList.remove(
"progress-bar-animated"
);

document
.getElementById("btnFinalizar")
.disabled = false;

confetti({
particleCount:120,
spread:120
});

setTimeout(()=>{

alert(
"🎉 Excelente, resolviste todas las palabras. Ahora presiona 'Finalizar Ejercicio'."
);

},300);

}

}

function finalizarEjercicio(){

confetti({

particleCount:250,
spread:180,
origin:{y:.6}

});

setTimeout(()=>{

alert(
"🏆 Felicitaciones, completaste el Ejercicio 10."
);

document
.getElementById("formCompletar")
.submit();

},1000);

}

</script>

</body>
</html>