<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 4);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 4"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        4
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 4"
    );

    header("Location: ../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Ejercicio 4</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

<style>

body{

background:
linear-gradient(135deg,#4e54c8,#8f94fb);

min-height:100vh;
padding:30px;

}

.main-card{

background:white;
border-radius:30px;
padding:40px;

box-shadow:
0 15px 40px rgba(0,0,0,.2);

}

.title{

text-align:center;
font-size:48px;
font-weight:bold;
color:#4e54c8;

}

.subtitle{

text-align:center;
font-size:20px;
color:#666;
margin-bottom:30px;

}

.timer-box{

display:flex;
justify-content:center;
margin-bottom:30px;

}

.timer-circle{

width:140px;
height:140px;

border-radius:50%;

background:
linear-gradient(135deg,#ff512f,#dd2476);

display:flex;
justify-content:center;
align-items:center;

font-size:34px;
font-weight:bold;
color:white;

animation:pulse 1s infinite;

box-shadow:
0 0 25px rgba(255,255,255,.5);

}

@keyframes pulse{

0%{transform:scale(1);}
50%{transform:scale(1.08);}
100%{transform:scale(1);}

}

.card-box{

background:
linear-gradient(135deg,#ffffff,#f8f9ff);

padding:30px;

border-radius:25px;

margin-bottom:35px;

box-shadow:
0 8px 25px rgba(0,0,0,.1);

}

.card-title{

font-size:28px;
font-weight:bold;
color:#4e54c8;
margin-bottom:20px;

}

.number-box{

display:inline-flex;

align-items:center;
justify-content:center;

width:75px;
height:75px;

margin:6px;

font-size:26px;
font-weight:bold;

background:white;

border-radius:15px;

box-shadow:
0 5px 15px rgba(0,0,0,.1);

transition:.3s;

}

.number-box:hover{

transform:translateY(-5px);

background:#eef2ff;

}

.answer{

width:75px;
height:65px;

text-align:center;

font-size:24px;
font-weight:bold;

margin:6px;

border-radius:15px;

border:2px solid #ddd;

transition:.3s;

}

.answer:focus{

border-color:#4e54c8;
box-shadow:0 0 15px rgba(78,84,200,.4);

}

.correct{

background:#d4edda !important;
border-color:#28a745 !important;

}

.wrong{

background:#f8d7da !important;
border-color:#dc3545 !important;

}

.message{

font-size:20px;
font-weight:bold;
margin-top:15px;

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

box-shadow:
0 5px 15px rgba(0,0,0,.2);

}

.hidden{

display:none;

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

<div class="container">

<div class="main-card">

<h1 class="title">
🔢 EJERCICIO 4
</h1>

<p class="subtitle">
Ordena correctamente los números para entrenar tu velocidad mental
</p>

<div class="timer-box">

<div
class="timer-circle"
id="timer">

02:00

</div>

</div>

<div class="card-box">

<h3 class="card-title">
⬇️ Ordena de Mayor a Menor
</h3>

<div id="numbers1"></div>

<hr>

<div id="inputs1"></div>

<div id="msg1" class="message"></div>

</div>

<div class="card-box">

<h3 class="card-title">
⬆️ Ordena de Menor a Mayor
</h3>

<div id="numbers2"></div>

<hr>

<div id="inputs2"></div>

<div id="msg2" class="message"></div>

</div>

</div>

</div>

<form method="POST" id="formCompletar">

<input
type="hidden"
name="completar"
value="1">

</form>
<script>

let nums1=[
1000,50,900,700,300,
800,500,250,450,150,
200,400,950,550,100,
650,600,750,350,1
];

let nums2=[
250,50,175,375,325,
125,200,75,400,300,
425,450,475,100,350,
500,225,1,150,25
];

let correct1=[...nums1].sort((a,b)=>b-a);
let correct2=[...nums2].sort((a,b)=>a-b);

loadNumbers();
createInputs();

function loadNumbers(){

    nums1.forEach(n=>{

        document.getElementById("numbers1").innerHTML+=`

        <div class="number-box">

            ${n}

        </div>

        `;

    });

    nums2.forEach(n=>{

        document.getElementById("numbers2").innerHTML+=`

        <div class="number-box">

            ${n}

        </div>

        `;

    });

}

function createInputs(){

    for(let i=0;i<20;i++){

        document.getElementById("inputs1").innerHTML+=`

        <input
        class="answer"
        id="e1_${i}"
        onkeyup="check1(${i})">

        `;

        document.getElementById("inputs2").innerHTML+=`

        <input
        class="answer"
        id="e2_${i}"
        onkeyup="check2(${i})">

        `;

    }

}

function mostrarMensaje(id,texto,color){

    const msg=document.getElementById(id);

    msg.innerHTML=texto;
    msg.style.color=color;

    setTimeout(()=>{

        msg.innerHTML="";

    },1200);

}

function check1(i){

    let input=
    document.getElementById(`e1_${i}`);

    let value=
    parseInt(input.value);

    input.classList.remove(
    "correct",
    "wrong"
    );

    if(value===correct1[i]){

        input.classList.add("correct");

        mostrarMensaje(
        "msg1",
        "✔ Correcto",
        "#28a745"
        );

        input.nextElementSibling?.focus();

    }else if(input.value!=""){

        input.classList.add("wrong");

        mostrarMensaje(
        "msg1",
        "❌ Busca bien otro número",
        "#dc3545"
        );

    }

    checkFinish();

}

function check2(i){

    let input=
    document.getElementById(`e2_${i}`);

    let value=
    parseInt(input.value);

    input.classList.remove(
    "correct",
    "wrong"
    );

    if(value===correct2[i]){

        input.classList.add("correct");

        mostrarMensaje(
        "msg2",
        "✔ Correcto",
        "#28a745"
        );

        input.nextElementSibling?.focus();

    }else if(input.value!=""){

        input.classList.add("wrong");

        mostrarMensaje(
        "msg2",
        "❌ Busca bien otro número",
        "#dc3545"
        );

    }

    checkFinish();

}

function checkFinish(){

    let ok1=true;
    let ok2=true;

    for(let i=0;i<20;i++){

        if(
        parseInt(
        document.getElementById(`e1_${i}`).value
        )!==correct1[i]
        ){
            ok1=false;
        }

        if(
        parseInt(
        document.getElementById(`e2_${i}`).value
        )!==correct2[i]
        ){
            ok2=false;
        }

    }

    if(ok1 && ok2){

        clearInterval(timer);

        confetti({

            particleCount:300,
            spread:180,
            origin:{y:0.6}

        });

        document.querySelector(".container").innerHTML=`

        <div class="main-card text-center">

            <h1 style="font-size:80px;">
            🏆
            </h1>

            <h2 class="mb-4">
            ¡Excelente trabajo!
            </h2>

            <p class="fs-4">

            Has completado correctamente
            el Ejercicio 4.

            </p>

            <button
            onclick="document.getElementById('formCompletar').submit()"
            class="btn btn-success btn-lg mt-3">

            Continuar

            </button>

        </div>

        `;

    }

}

const timerEl=
document.getElementById("timer");

let time=120;

let timer=setInterval(()=>{

    time--;

    let min=
    Math.floor(time/60);

    let sec=
    time%60;

    timerEl.innerHTML=
    `${String(min).padStart(2,"0")}:${String(sec).padStart(2,"0")}`;

    if(time<=20){

        timerEl.style.background=
        "linear-gradient(135deg,#ff0000,#ff5722)";

    }

    if(time<=0){

        clearInterval(timer);

        document.querySelector(".container").innerHTML=`

        <div class="main-card text-center">

            <h1>
            ⏰
            </h1>

            <h2>
            Tiempo agotado
            </h2>

            <p class="fs-5">

            Tu cerebro mejora cada vez
            que practicas.

            Inténtalo nuevamente.

            </p>

            <a
            href="../dashboard.php"
            class="btn btn-primary btn-lg">

            Volver al Dashboard

            </a>

        </div>

        `;

    }

},1000);

</script>

</body>
</html>