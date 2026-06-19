<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 3);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 3"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        3
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 3"
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

<title>Ejercicio 3</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

<style>

body{

background:
linear-gradient(
135deg,
#0f172a,
#1e3a8a,
#7c3aed
);

min-height:100vh;
padding:30px;

overflow-x:hidden;

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

font-weight:bold;

box-shadow:0 5px 15px rgba(0,0,0,.2);

}

.title{

text-align:center;

color:white;

font-size:45px;
font-weight:bold;

margin-bottom:10px;

}

.subtitle{

text-align:center;

color:#dbeafe;

margin-bottom:25px;

font-size:18px;

}

.timer-card{

width:180px;

margin:auto;

background:
rgba(255,255,255,.15);

backdrop-filter:blur(10px);

border-radius:20px;

padding:15px;

text-align:center;

color:white;

font-size:35px;
font-weight:bold;

box-shadow:
0 10px 25px rgba(0,0,0,.25);

}

.passes{

text-align:center;

color:white;

font-size:24px;
font-weight:bold;

margin-top:15px;

}

.exercise-container{

position:relative;

width:950px;
height:600px;

margin:auto;
margin-top:25px;

border-radius:30px;

background:
rgba(255,255,255,.12);

backdrop-filter:blur(15px);

border:
1px solid rgba(255,255,255,.2);

box-shadow:
0 20px 40px rgba(0,0,0,.3);

overflow:hidden;

}

.center-line{

position:absolute;

left:50%;
top:0;

transform:translateX(-50%);

width:6px;
height:100%;

background:
linear-gradient(
to bottom,
transparent,
white,
transparent
);

box-shadow:
0 0 20px white;

}

.bar{

position:absolute;

height:20px;

left:50%;

transform:translateX(-50%);

border-radius:30px;

background:
linear-gradient(
90deg,
#38bdf8,
#818cf8
);

transition:.25s;

}

.active{

background:
linear-gradient(
90deg,
#facc15,
#f97316
) !important;

transform:
translateX(-50%)
scale(1.08);

box-shadow:
0 0 25px #facc15;

}

.progress{

height:25px;

border-radius:20px;

max-width:950px;

margin:auto;

}

.hidden{

display:none;

}

#questionSection{

max-width:650px;
margin:auto;

}

.card{

border:none;
border-radius:30px;

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

<h1 class="title">

👀 EJERCICIO 3

</h1>

<p class="subtitle">

Expande tu campo de visión siguiendo las barras luminosas

</p>

<div class="timer-card">

<div id="timer">

02:00

</div>

</div>


<div class="progress mt-3 mb-4">

<div
id="barraTiempo"
class="progress-bar progress-bar-striped progress-bar-animated"
style="width:100%">

</div>

</div>

<div
class="exercise-container"
id="exerciseBox">

<div class="center-line"></div>

<div class="bar" style="top:40px;width:120px;"></div>
<div class="bar" style="top:90px;width:160px;"></div>
<div class="bar" style="top:140px;width:210px;"></div>
<div class="bar" style="top:190px;width:260px;"></div>
<div class="bar" style="top:240px;width:320px;"></div>
<div class="bar" style="top:290px;width:380px;"></div>
<div class="bar" style="top:360px;width:120px;"></div>
<div class="bar" style="top:410px;width:160px;"></div>
<div class="bar" style="top:460px;width:210px;"></div>

</div>

<div
id="questionSection"
class="hidden mt-5">

<div class="card shadow-lg">

<div class="card-body p-5">

<h2 class="text-center mb-4">

🧠 ¿Cuántas pasadas hiciste?

</h2>

<input
type="number"
id="respuesta"
class="form-control form-control-lg text-center"
placeholder="Escribe un número">

<div class="text-center mt-4">

<button
onclick="finalizarEjercicio()"
class="btn btn-success btn-lg px-5">

Finalizar

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

const bars =
document.querySelectorAll(".bar");

let current = 0;

let tiempo = 120;

const timerEl =
document.getElementById("timer");

function animateBars(){

    bars.forEach(bar=>{

        bar.classList.remove("active");

    });

    bars[current]
    .classList.add("active");

    current++;

    if(current >= bars.length){

        current = 0;

    }

}

let animationInterval =
setInterval(
animateBars,
350
);

let timerInterval =
setInterval(()=>{

    tiempo--;

    let min =
    Math.floor(tiempo/60);

    let sec =
    tiempo%60;

    timerEl.innerHTML =
    `${String(min).padStart(2,"0")}:${String(sec).padStart(2,"0")}`;

    let porcentaje =
    (tiempo/120)*100;

    document
    .getElementById("barraTiempo")
    .style.width =
    porcentaje + "%";

    if(tiempo <= 20){

        timerEl.style.color =
        "#ff6b6b";

    }

    if(tiempo <= 0){

        clearInterval(
        timerInterval
        );

        clearInterval(
        animationInterval
        );

        finishExercise();

    }

},1000);

function finishExercise(){

    document
    .getElementById("exerciseBox")
    .classList.add("hidden");

    document
    .getElementById("questionSection")
    .classList.remove("hidden");

}

function finalizarEjercicio(){

    const respuesta =
    document
    .getElementById("respuesta")
    .value
    .trim();

    if(respuesta === ""){

        alert(
        "⚠️ Escribe cuántas pasadas realizaste."
        );

        return;

    }

    confetti({

        particleCount:300,
        spread:180,
        origin:{y:0.6}

    });

    setTimeout(()=>{

        alert(
        "🏆 ¡Excelente trabajo! Has completado el Ejercicio 3."
        );

        document
        .getElementById("formCompletar")
        .submit();

    },1200);

}

</script>

</body>
</html>