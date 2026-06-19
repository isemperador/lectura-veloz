<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 7);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 7"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        7
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 7"
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

<title>Lectium - Ejercicio 7</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{

background:
linear-gradient(135deg,#4e54c8,#8f94fb);

min-height:100vh;
padding:30px;
overflow-x:hidden;

}

.exercise-container{

background:white;
border-radius:30px;
padding:40px;
box-shadow:0 15px 40px rgba(0,0,0,.2);

}

.title{

text-align:center;
font-size:50px;
font-weight:bold;
color:#4e54c8;

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

.timer-circle{

width:140px;
height:140px;
background:
linear-gradient(135deg,#ff512f,#dd2476);

border-radius:50%;
margin:auto;

display:flex;
align-items:center;
justify-content:center;

font-size:35px;
font-weight:bold;
color:white;

box-shadow:0 0 25px rgba(255,255,255,.6);

animation:pulse 1s infinite;

}

@keyframes pulse{

0%{transform:scale(1);}
50%{transform:scale(1.08);}
100%{transform:scale(1);}

}

.letters-grid{

display:grid;
grid-template-columns:repeat(6,1fr);
gap:15px;
margin-bottom:40px;

}

.letter-card{

background:
linear-gradient(135deg,#667eea,#764ba2);

padding:20px;
border-radius:20px;
text-align:center;
color:white;
font-weight:bold;
font-size:28px;

transition:.3s;

box-shadow:0 8px 20px rgba(0,0,0,.15);

}

.letter-card:hover{

transform:translateY(-5px) scale(1.05);

}

.number{

display:block;
font-size:20px;
margin-top:10px;
color:#ffe082;

}

.word-section{

margin-top:40px;

}

.word-card{

background:#f8f9ff;
padding:25px;
border-radius:25px;
margin-bottom:20px;

box-shadow:0 5px 15px rgba(0,0,0,.08);

}

.word-title{

font-size:28px;
font-weight:bold;
color:#4e54c8;

}

.correct{

border:3px solid #28a745 !important;
background:#d4edda !important;

}

.wrong{

border:3px solid #dc3545 !important;
background:#f8d7da !important;

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
border-radius:15px;
font-size:16px;
font-weight:bold;
box-shadow:0 5px 15px rgba(0,0,0,.2);

}

.star{

position:fixed;
font-size:30px;
animation:fall 3s linear forwards;

}

@keyframes fall{

0%{

transform:translateY(-100px) rotate(0deg);
opacity:1;

}

100%{

transform:translateY(100vh) rotate(360deg);
opacity:0;

}

}

</style>
</head>

<body>

<div class="dashboard-float">

<a href="../dashboard.php"
class="btn btn-warning dashboard-btn">

🏠 Dashboard

</a>

</div>

<div class="container">

<div class="exercise-container">

<h1 class="title">
🧠 EJERCICIO 7
</h1>

<p class="subtitle">
Convierte las palabras en números y suma sus valores
</p>

<div class="timer-box">

<div class="timer-circle" id="timer">
01:30
</div>

</div>

<div class="letters-grid">

</div>

<div class="word-section" id="wordsContainer">

</div>

<div class="text-center mt-4">

<button
class="btn btn-success btn-lg px-5"
onclick="verificar()">

Finalizar Ejercicio

</button>

</div>

</div>

</div>

<form method="POST"
id="formCompletar"
class="d-none">

<input
type="hidden"
name="completar"
value="1">

</form>

<script>

const letras={

A:1,
B:2,
C:3,
D:4,
E:5,
F:6,
G:7,
H:8,
I:9,
J:10,
K:11,
L:12,
M:13,
N:14,
Ñ:15,
O:16,
P:17,
Q:18,
R:19,
S:20,
T:21,
U:22,
V:23,
W:24

};

const palabras=[

"SOL",
"CASA",
"LUNA",
"GATO"


];

const grid=document.querySelector(".letters-grid");

Object.entries(letras).forEach(([letra,numero])=>{

grid.innerHTML+=`

<div class="letter-card">

${letra}

<span class="number">

${numero}

</span>

</div>

`;

});

const container=document.getElementById("wordsContainer");

palabras.forEach((palabra,index)=>{

container.innerHTML+=`

<div class="word-card">

<div class="word-title">

${palabra}

</div>

<div class="mt-3">

<input
type="number"
class="form-control form-control-lg answer"
data-word="${palabra}"
placeholder="Escribe la suma">

</div>

</div>

`;

});

function calcularSuma(word){

let total=0;

word.split("").forEach(letra=>{

total+=letras[letra];

});

return total;

}

function estrellas(){

for(let i=0;i<20;i++){

let star=document.createElement("div");

star.innerHTML="⭐";

star.classList.add("star");

star.style.left=Math.random()*100+"vw";

star.style.top="-50px";

document.body.appendChild(star);

setTimeout(()=>{

star.remove();

},3000);

}

}

function verificar(){

let correctas=0;

document.querySelectorAll(".answer").forEach(input=>{

let palabra=input.dataset.word;

let correcta=calcularSuma(palabra);

let usuario=parseInt(input.value);

input.classList.remove("correct","wrong");

if(usuario===correcta){

input.classList.add("correct");

correctas++;

}else{

input.classList.add("wrong");

}

});

if(correctas===palabras.length){

estrellas();

setTimeout(()=>{

alert("🎉 Ejercicio 7 completado");

document
.getElementById("formCompletar")
.submit();

},1000);

}else{

alert("💪 Sigue esforzándote, la próxima lo harás mejor.");

}

}

let tiempo=90;

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

</script>

</body>
</html>