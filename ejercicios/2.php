<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 2);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 2"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        2
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 2"
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

<title>LECTIUM - Ejercicio 2</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

<style>

body{

background:
linear-gradient(
135deg,
#4e54c8,
#8f94fb
);

min-height:100vh;
padding:30px;

overflow-x:hidden;

}

.exercise-card{

background:white;

max-width:1000px;
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

font-size:22px;

text-align:center;

margin-bottom:30px;

color:#666;

}

.badge-top{

background:#4e54c8;

font-size:18px;

padding:10px 20px;

border-radius:20px;

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

font-size:35px;
font-weight:bold;

color:white;

box-shadow:
0 0 30px rgba(255,255,255,.5);

animation:pulse 1s infinite;

}

@keyframes pulse{

0%{
transform:scale(1);
}

50%{
transform:scale(1.08);
}

100%{
transform:scale(1);
}

}

.exercise-area{

background:
linear-gradient(
135deg,
#4e1e9e,
#6a11cb
);

border-radius:25px;

padding:60px 20px;

min-height:350px;

display:flex;
justify-content:center;
align-items:center;

}

.word{

font-size:70px;

font-weight:bold;

color:white;

text-shadow:
0 0 20px rgba(255,255,255,.8);

}

@keyframes aparecer{

0%{

opacity:0;

transform:
scale(.5);

}

100%{

opacity:1;

transform:
scale(1);

}

}

.controls{

text-align:center;
margin-top:30px;

}

.speed-box{

font-size:22px;
font-weight:bold;

margin-top:15px;

color:#4e54c8;

}

.answer-card{

background:#f8f9ff;

padding:35px;

border-radius:25px;

box-shadow:
0 5px 20px rgba(0,0,0,.1);

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

font-weight:bold;

border-radius:15px;

box-shadow:
0 5px 15px rgba(0,0,0,.2);

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

<div class="text-center mb-4">

<span class="badge badge-top">

📖 Ejercicio 2 de 12

</span>

</div>

<h1 class="title">

👀 EJERCICIO 2

</h1>

<p class="subtitle">

Mantén tu mirada en el centro y memoriza las palabras

</p>

<div class="timer-container">

<div
id="timer"
class="timer-circle">

02:00

</div>

</div>

<div
class="exercise-area"
id="exerciseArea">

<div
class="word"
id="wordDisplay">

Presiona INICIAR

</div>

</div>

<div class="controls">

<button
onclick="startExercise()"
class="btn btn-success btn-lg mx-2"
id="startBtn">

▶ INICIAR

</button>

<button
onclick="changeSpeed(100)"
class="btn btn-primary">

🐢 Más Lento

</button>

<button
onclick="changeSpeed(-100)"
class="btn btn-primary">

🚀 Más Rápido

</button>

<div
class="speed-box"
id="speedDisplay">

⚡ Velocidad Media

</div>

</div>

<div
id="answerSection"
class="hidden mt-5">

<div class="answer-card">

<h3 class="text-center">

🏆 Excelente trabajo

</h3>

<p class="text-center text-muted">

Escribe las palabras que recuerdas haber visto.

</p>

<textarea
id="respuesta"
rows="6"
class="form-control form-control-lg"
placeholder="Escribe aquí las palabras...">

</textarea>

<div class="text-center mt-4">

<button
onclick="enviarRespuesta()"
class="btn btn-success btn-lg">

Finalizar Ejercicio

</button>

</div>

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

const words = [

"Ojos",
"Luz",
"Foco",
"Mente",
"Veloz",
"Leer",
"Texto",
"Palabra",
"Vision",
"Centro",
"Rapido",
"Atento",
"Claro",
"Imagen",
"Percepcion",
"Enfoque",
"Cerebro",
"Lectura",
"Velocidad",
"Entrena"

];

let timerInterval;
let wordInterval;

let tiempo = 120;
let speed = 800;

let isRunning = false;

const timerEl =
document.getElementById("timer");

const wordEl =
document.getElementById("wordDisplay");

const answerSection =
document.getElementById("answerSection");

const exerciseArea =
document.getElementById("exerciseArea");

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

function startExercise(){

if(isRunning) return;

isRunning = true;

document
.getElementById("startBtn")
.disabled = true;

showRandomWord();

timerInterval = setInterval(()=>{

tiempo--;

let min =
Math.floor(tiempo/60);

let sec =
tiempo%60;

timerEl.innerHTML =

`${String(min).padStart(2,"0")}:${String(sec).padStart(2,"0")}`;

if(tiempo <= 20){

timerEl.style.background =
"linear-gradient(135deg,#ff0000,#ff5722)";

}

if(tiempo <= 0){

finishExercise();

}

},1000);

wordInterval =
setInterval(showRandomWord,speed);

}

function showRandomWord(){

const randomWord =

words[
Math.floor(
Math.random()*words.length
)
];

const colores = [

"#ff4757",
"#3742fa",
"#2ed573",
"#ffa502",
"#e056fd",
"#00d2d3"

];

wordEl.style.color =

colores[
Math.floor(
Math.random()*colores.length
)
];

wordEl.style.animation="none";

void wordEl.offsetWidth;

wordEl.style.animation=
"aparecer .35s";

wordEl.innerHTML =
randomWord;

}

function changeSpeed(change){

speed = Math.max(
300,
Math.min(
2000,
speed + change
)
);

let texto = "";

if(speed >= 1200){

texto =
"🐢 Velocidad Lenta";

}
else if(speed >= 700){

texto =
"⚡ Velocidad Media";

}
else{

texto =
"🚀 Velocidad Rápida";

}

document
.getElementById("speedDisplay")
.innerHTML = texto;

if(isRunning){

clearInterval(wordInterval);

wordInterval =
setInterval(
showRandomWord,
speed
);

}

}

function finishExercise(){

clearInterval(timerInterval);

clearInterval(wordInterval);

isRunning = false;

exerciseArea.classList.add("hidden");

answerSection.classList.remove("hidden");

}

function enviarRespuesta(){

const respuesta =

document
.getElementById("respuesta")
.value
.trim();

if(respuesta === ""){

alert(
"⚠️ Escribe al menos algunas palabras."
);

return;

}

confetti({

particleCount:250,
spread:180,
origin:{y:.6}

});

estrellas();

setTimeout(()=>{

alert(
"🏆 Excelente trabajo. Has completado el Ejercicio 2."
);

document
.getElementById("formCompletar")
.submit();

},1200);

}

</script>

</body>
</html>