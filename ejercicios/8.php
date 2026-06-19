<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 8);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 8"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        8
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 8"
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

<title>Lectium - Ejercicio 8</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:linear-gradient(135deg,#667eea,#764ba2);
    min-height:100vh;
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
}

.exercise-card{
    background:white;
    border-radius:25px;
    box-shadow:0 10px 30px rgba(0,0,0,.2);
    overflow:hidden;
}

.canvas-box{
    border:3px dashed #6c63ff;
    border-radius:15px;
    padding:10px;
    background:#fafafa;
}

canvas{
    background:white;
    cursor:crosshair;
    max-width:100%;
}

.title{
    color:white;
    font-weight:bold;
    text-align:center;
    margin-bottom:40px;
}

.success-box{
    position:fixed;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    background:white;
    padding:40px;
    border-radius:20px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,.3);
    display:none;
    z-index:9999;
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

<div class="container py-5">

<h1 class="title">

✏️ EJERCICIO 8
<br>
Copia los dibujos sin salirte del recuadro

</h1>

<!-- DIBUJO 1 -->

<div class="exercise-card mb-5">

<div class="row g-0">

<div class="col-md-4">

<div class="p-4 text-center">

<h4>DIBUJO 1</h4>

<img
src="../assets/images/casa.png"
class="img-fluid rounded shadow">

</div>

</div>

<div class="col-md-8">

<div class="p-4">

<h4 class="text-center mb-3">

COPIA EL DIBUJO

</h4>

<div class="canvas-box text-center">

<canvas
id="canvas1"
width="700"
height="300">
</canvas>

</div>

<div class="text-center mt-3">

<button
class="btn btn-warning"
onclick="limpiarCanvas('canvas1')">

🧽 Limpiar dibujo

</button>

</div>

</div>

</div>

</div>

</div>

<!-- DIBUJO 2 -->

<div class="exercise-card mb-5">

<div class="row g-0">

<div class="col-md-4">

<div class="p-4 text-center">

<h4>DIBUJO 2</h4>

<img
src="../assets/images/clavera.png"
class="img-fluid rounded shadow">

</div>

</div>

<div class="col-md-8">

<div class="p-4">

<h4 class="text-center mb-3">

COPIA EL DIBUJO

</h4>

<div class="canvas-box text-center">

<canvas
id="canvas2"
width="700"
height="300">
</canvas>

</div>

<div class="text-center mt-3">

<button
class="btn btn-warning"
onclick="limpiarCanvas('canvas2')">

🧽 Limpiar dibujo

</button>

</div>

</div>

</div>

</div>

</div>

<div class="text-center">

<button
class="btn btn-success btn-lg px-5"
onclick="finalizarEjercicio()">

✅ Finalizar Ejercicio

</button>

</div>

</div>

<div class="success-box" id="successBox">

<h2>🎉 ¡Perfecto!</h2>

<p>

Has terminado el Ejercicio 8.

<br><br>

Tu coordinación visual y atención siguen mejorando.

</p>

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

let canvas1Dibujado = false;
let canvas2Dibujado = false;

function activarCanvas(id){

    const canvas=document.getElementById(id);
    const ctx=canvas.getContext("2d");

    let dibujando=false;

    canvas.addEventListener("mousedown",()=>{

        dibujando=true;

    });

    canvas.addEventListener("mouseup",()=>{

        dibujando=false;
        ctx.beginPath();

    });

    canvas.addEventListener("mouseleave",()=>{

        dibujando=false;
        ctx.beginPath();

    });

    canvas.addEventListener("mousemove",(e)=>{

        if(!dibujando) return;

        if(id==="canvas1"){
            canvas1Dibujado=true;
        }

        if(id==="canvas2"){
            canvas2Dibujado=true;
        }

        const rect=canvas.getBoundingClientRect();

        ctx.lineWidth=3;
        ctx.lineCap="round";

        ctx.lineTo(
            e.clientX-rect.left,
            e.clientY-rect.top
        );

        ctx.stroke();

        ctx.beginPath();

        ctx.moveTo(
            e.clientX-rect.left,
            e.clientY-rect.top
        );

    });

}

activarCanvas("canvas1");
activarCanvas("canvas2");

function limpiarCanvas(id){

    const canvas=document.getElementById(id);

    const ctx=canvas.getContext("2d");

    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    );

    if(id==="canvas1"){
        canvas1Dibujado=false;
    }

    if(id==="canvas2"){
        canvas2Dibujado=false;
    }

}

function finalizarEjercicio(){

    if(!canvas1Dibujado && !canvas2Dibujado){

        alert(
        "✏️ Debes copiar ambos dibujos para terminar el ejercicio."
        );

        return;
    }

    if(!canvas1Dibujado){

        alert(
        "🏠 Aún no has copiado el dibujo de la casa."
        );

        return;
    }

    if(!canvas2Dibujado){

        alert(
        "💀 Aún no has copiado el dibujo de la calavera."
        );

        return;
    }

    document.getElementById(
        "successBox"
    ).style.display="block";

    setTimeout(()=>{

        document.getElementById(
            "formCompletar"
        ).submit();

    },2500);

}

</script>

</body>
</html>