<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 5);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 5"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        5
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 5"
    );

    header("Location: ../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ejercicio 5 - Atención Visual</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

<style>

body{

background:linear-gradient(135deg,#4f46e5,#7c3aed);
min-height:100vh;
padding:30px;

}

.main-card{

background:white;
border-radius:30px;
padding:35px;
box-shadow:0 15px 40px rgba(0,0,0,.25);

}

.title{

font-size:50px;
font-weight:bold;
text-align:center;
color:#4f46e5;

}

.subtitle{

text-align:center;
font-size:20px;
color:#555;
margin-bottom:25px;

}

.timer-box{

display:flex;
justify-content:center;
margin-bottom:25px;

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

.letter-grid{

display:grid;
grid-template-columns:repeat(14,50px);
gap:8px;
justify-content:center;
margin-top:30px;

}

.cell{

width:50px;
height:50px;

display:flex;
align-items:center;
justify-content:center;

font-size:22px;
font-weight:bold;

cursor:pointer;
user-select:none;

border-radius:12px;

background:white;
border:2px solid #e5e7eb;

transition:.3s;

}

.cell:hover{

transform:scale(1.1);

}

.circle-blue{

background:#93c5fd;
border-radius:50%;

}

.circle-red{

background:#fca5a5;
border-radius:50%;

}

.square-yellow{

background:#fde68a;

}

.square-green{

background:#86efac;

}

.correct{

background:#bbf7d0!important;
border:3px solid #16a34a!important;

}

.wrong{

background:#fecaca!important;
border:3px solid #dc2626!important;

}

.hidden{

display:none;

}

.dashboard{

position:fixed;
right:20px;
top:50%;
transform:translateY(-50%);
z-index:999;

}

.instructions{

background:#eef2ff;
padding:20px;
border-radius:20px;
margin-bottom:25px;

}

.legend{

display:flex;
justify-content:center;
gap:20px;
flex-wrap:wrap;

}

.legend span{

padding:8px 15px;
border-radius:10px;
font-weight:bold;

}

.result-card{

background:#f8fafc;
border-radius:25px;
padding:30px;

}

input.form-control{

font-size:22px;
text-align:center;

}

</style>
</head>

<body>

<div class="dashboard">

<a href="../dashboard.php"
class="btn btn-warning btn-lg">

🏠 Dashboard

</a>

</div>

<div class="container">

<div class="main-card">

<h1 class="title">
🎯 EJERCICIO 5
</h1>

<p class="subtitle">
Encuentra las letras especiales y cuenta cuántas hay de cada una.
</p>

<div class="timer-box">

<div id="timer" class="timer">
02:00
</div>

</div>

<div class="instructions">

<div class="legend">

<span style="background:#93c5fd;">
D = Círculo Celeste
</span>

<span style="background:#fca5a5;">
I = Círculo Rojo
</span>

<span style="background:#fde68a;">
N = Cuadrado Amarillo
</span>

<span style="background:#86efac;">
U = Cuadrado Verde
</span>

</div>

</div>

<div id="grid" class="letter-grid"></div>

<div id="resultSection" class="hidden mt-5">

<div class="result-card">

<h3 class="text-center mb-4">
📝 ¿Cuántas encontraste?
</h3>

<div class="row text-center">

<div class="col-md-3">
<label>D</label>
<input id="dCount" class="form-control">
</div>

<div class="col-md-3">
<label>I</label>
<input id="iCount" class="form-control">
</div>

<div class="col-md-3">
<label>N</label>
<input id="nCount" class="form-control">
</div>

<div class="col-md-3">
<label>U</label>
<input id="uCount" class="form-control">
</div>

</div>

<div class="text-center mt-4">

<button
onclick="validar()"
class="btn btn-success btn-lg px-5">

Finalizar Ejercicio

</button>

</div>

</div>

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

const grid = document.getElementById("grid");

const letters = "ABCEFGHJKLMOPQRSTVWXYZ";

let matrix = [];

let real = {
    D:8,
    I:7,
    N:6,
    U:5
};

function randomLetter(){
    return letters[
        Math.floor(Math.random()*letters.length)
    ];
}

/* Generar tablero */

for(let i=0;i<196;i++){
    matrix.push(randomLetter());
}

/* Insertar letras objetivo */

function placeLetter(letter,count){

    for(let i=0;i<count;i++){

        let pos;

        do{

            pos = Math.floor(
                Math.random()*196
            );

        }while(
            ["D","I","N","U"]
            .includes(matrix[pos])
        );

        matrix[pos] = letter;
    }
}

placeLetter("D",8);
placeLetter("I",7);
placeLetter("N",6);
placeLetter("U",5);

/* Dibujar tablero */

matrix.forEach(letter=>{

    let div = document.createElement("div");

    div.className = "cell";

    div.innerHTML = letter;

    div.onclick = ()=>{

        if(letter==="D")
            div.classList.toggle("circle-blue");

        if(letter==="I")
            div.classList.toggle("circle-red");

        if(letter==="N")
            div.classList.toggle("square-yellow");

        if(letter==="U")
            div.classList.toggle("square-green");

    };

    grid.appendChild(div);

});

/* Temporizador */
let time = 120;

const timer = document.getElementById("timer");

const interval = setInterval(() => {

    time--;

    let min = Math.floor(time / 60);
    let sec = time % 60;

    timer.innerHTML =
    `${String(min).padStart(2,"0")}:${String(sec).padStart(2,"0")}`;

    if(time <= 20){

        timer.classList.remove("bg-danger");
        timer.classList.add("bg-warning","text-dark");

    }

    if(time <= 0){

        clearInterval(interval);

        timer.innerHTML = "00:00";

        document.getElementById("grid").style.display = "none";

        document.getElementById("resultSection").classList.remove("hidden");

    }

},1000);
/* Validación */

function validar(){

    let dInput =
    document.getElementById("dCount");

    let iInput =
    document.getElementById("iCount");

    let nInput =
    document.getElementById("nCount");

    let uInput =
    document.getElementById("uCount");

    let d =
    parseInt(dInput.value)||0;

    let i =
    parseInt(iInput.value)||0;

    let n =
    parseInt(nInput.value)||0;

    let u =
    parseInt(uInput.value)||0;

    [dInput,iInput,nInput,uInput]
    .forEach(input=>{

        input.classList.remove(
            "correct",
            "wrong"
        );

    });

    if(d===real.D)
        dInput.classList.add("correct");
    else
        dInput.classList.add("wrong");

    if(i===real.I)
        iInput.classList.add("correct");
    else
        iInput.classList.add("wrong");

    if(n===real.N)
        nInput.classList.add("correct");
    else
        nInput.classList.add("wrong");

    if(u===real.U)
        uInput.classList.add("correct");
    else
        uInput.classList.add("wrong");

    if(
        d===real.D &&
        i===real.I &&
        n===real.N &&
        u===real.U
    ){

        confetti({
            particleCount:250,
            spread:180,
            origin:{y:.6}
        });

        setTimeout(()=>{

            alert(
            "🎉 ¡Excelente trabajo!\nHas completado el Ejercicio 5."
            );

            document
            .getElementById("formCompletar")
            .submit();

        },1000);

    }else{

        setTimeout(()=>{

            alert(
            "💪 Sigue esforzándote.\nCada intento mejora tu concentración y velocidad visual."
            );

        },300);

    }

}

/* Enter para validar */

document.addEventListener(
"keypress",
function(e){

    if(e.key==="Enter"){

        e.preventDefault();

        if(
        !document
        .getElementById("resultSection")
        .classList.contains("hidden")
        ){

            validar();

        }

    }

});

</script>

</body>
</html>