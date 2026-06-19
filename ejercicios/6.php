<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 6);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 6"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        6
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 6"
    );

    header("Location: ../dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ejercicio 6</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{

background:linear-gradient(135deg,#432371,#f5f7fa);
min-height:100vh;
padding:30px;

}

.exercise{

background:white;
padding:30px;
border-radius:25px;
box-shadow:0 10px 30px rgba(0,0,0,.2);

}

h1{

text-align:center;
font-weight:bold;
margin-bottom:20px;

}

.bomb-container{

text-align:center;
margin-bottom:30px;

}

.bomb{

font-size:70px;
animation:pulse 1s infinite;

}

.time{

font-size:35px;
font-weight:bold;

}

@keyframes pulse{

0%{transform:scale(1);}
50%{transform:scale(1.15);}
100%{transform:scale(1);}

}

@keyframes shake{

0%{transform:translateX(-5px);}
50%{transform:translateX(5px);}
100%{transform:translateX(-5px);}
}

.danger{

animation:shake .3s infinite;
color:red;

}

.words{

display:flex;
flex-wrap:wrap;
gap:10px;
justify-content:center;
margin-bottom:30px;

}

.word{

padding:10px 20px;
background:#6f42c1;
color:white;
border-radius:15px;
cursor:grab;
font-weight:bold;

}

.columns{

display:grid;
grid-template-columns:repeat(4,1fr);
gap:15px;

}

.column{

background:#f8f9fa;
padding:15px;
border-radius:20px;
min-height:350px;

}

.dropzone{

border:2px dashed #bbb;
height:45px;
margin-bottom:10px;
border-radius:10px;

}

.correct{

background:#b8f5c4 !important;

}

.wrong{

background:#ffb3b3 !important;

}

.hidden{

display:none;

}

.dashboard{

position:fixed;
right:20px;
top:50%;
transform:translateY(-50%);

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

<h1>
EJERCICIO 6
<br>
¡Completa el cuadro!
</h1>

<div class="bomb-container">

<div id="bomb" class="bomb">
💣
</div>

<div class="time" id="timer">
02:00
</div>

</div>

<div class="words" id="wordContainer">

</div>

<div class="columns" id="columns">

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

let tiempo=120;

const timer=document.getElementById("timer");
const bomb=document.getElementById("bomb");

const categories=[

{
title:"Palabras que empiecen por PE",
match:(w)=>w.startsWith("PE")
},

{
title:"Palabras que contengan -RI-",
match:(w)=>w.includes("RI")
},

{
title:"Palabras que terminen por -RA",
match:(w)=>w.endsWith("RA")
},

{
title:"Palabras que empiecen por V-",
match:(w)=>w.startsWith("V")
}

];

const words=[

"PERRO",
"PERA",
"PELOTA",

"RISA",
"CARICIA",
"PRINCESA",

"PINTURA",
"TIERRA",
"PRIMAVERA",

"VIDA",
"VELA",
"VENTANA"

];

categories.sort(()=>Math.random()-0.5);

let columns=document.getElementById("columns");

categories.forEach(cat=>{

let html=`

<div class='column'>

<h5>${cat.title}</h5>

<div class='dropzone'
data-category='${cat.title}'></div>

<div class='dropzone'
data-category='${cat.title}'></div>

<div class='dropzone'
data-category='${cat.title}'></div>

</div>

`;

columns.innerHTML+=html;

});


words.sort(()=>Math.random()-0.5);

let container=document.getElementById("wordContainer");

words.forEach((word,i)=>{

container.innerHTML+=`

<div
class='word'
draggable='true'
id='word${i}'>

${word}

</div>

`;

});

let dragged=null;

document.querySelectorAll(".word").forEach(el=>{

el.addEventListener("dragstart",()=>{

dragged=el;

});

});

document.querySelectorAll(".dropzone").forEach(zone=>{

zone.addEventListener("dragover",(e)=>{

e.preventDefault();

});

zone.addEventListener("drop",()=>{

if(zone.innerHTML==""){

zone.appendChild(dragged);

}

});

});

const interval=setInterval(()=>{

tiempo--;

let min=Math.floor(tiempo/60);
let sec=tiempo%60;

timer.innerHTML=
`${String(min).padStart(2,"0")}:${String(sec).padStart(2,"0")}`;

if(tiempo<=30){

bomb.classList.add("danger");

}

if(tiempo<=0){

clearInterval(interval);

document.body.innerHTML=`

<div class='container text-center mt-5'>

<h1 style='font-size:100px'>
💥
</h1>

<h2>

La bomba explotó

</h2>

<p>

💪 Sigue esforzándote,
la próxima lo harás mejor.

</p>

<button
onclick='location.reload()'
class='btn btn-primary'>

Intentar nuevamente

</button>

</div>

`;

}

},1000);

function verificar(){

let ok=true;

document
.querySelectorAll(".dropzone")
.forEach(zone=>{

let word=zone.innerText.trim();

if(word==""){

ok=false;

return;

}

let cat=categories.find(
x=>x.title==
zone.dataset.category
);

if(cat.match(word)){

zone.classList.add("correct");

}else{

zone.classList.add("wrong");

ok=false;

}

});

if(ok){

alert("🎉 Ejercicio completado");

document
.getElementById(
"formCompletar"
)
.submit();

}else{

alert(
"💪 Sigue esforzándote, la próxima lo harás mejor."
);

}

}

let btn=document.createElement("button");

btn.innerHTML="Finalizar";

btn.className=
"btn btn-success mt-4";

btn.onclick=verificar;

document
.querySelector(".exercise")
.appendChild(btn);

</script>

</body>
</html>