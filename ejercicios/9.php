<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 9);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 9"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        9  
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 9"
    );

    header("Location: ../dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ejercicio 9 - Laberinto</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:linear-gradient(135deg,#5f2c82,#49a09d);
    min-height:100vh;
    padding:30px;
}

.container-box{
    background:white;
    border-radius:25px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}

h1{
    text-align:center;
    margin-bottom:30px;
    font-weight:bold;
}

.laberinto-container{
    position:relative;
    width:800px;
    margin:auto;
}

#laberinto{
    width:100%;
    border-radius:15px;
    border:4px solid #444;
}

#calabaza{
    position:absolute;
    width:25px;
    height:25px;
    left:10px;
    bottom:20px;
    cursor:grab;
    z-index:100;
}

#salida{

    position:absolute;

    right:25px;
    top:500px;

    width:80px;
    height:80px;

    z-index:10;

    border:3px dashed lime;

    opacity:.3;
}

.dashboard{

    position:fixed;
    right:20px;
    top:50%;
    transform:translateY(-50%);
}

.win{
    animation:zoom .6s infinite alternate;
}

@keyframes zoom{

from{transform:scale(1);}
to{transform:scale(1.1);}

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

<div class="container-box">

<h1>
🎃 EJERCICIO 9
<br>
Saca la calabaza del laberinto
</h1>

<p class="text-center">
Arrastra la calabaza hasta la salida.
</p>

<div class="text-center mb-3">
    <h3 id="vidas">❤️❤️❤️</h3>
</div>

<div class="laberinto-container">

<img
src="../assets/images/laberinto5.png"
id="laberinto">

<canvas
id="mazeCanvas"
style="display:none;">
</canvas>

<img
src="../assets/images/calabaza.png"
id="calabaza">

<div id="salida"></div>

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

const pumpkin = document.getElementById("calabaza");
const salida = document.getElementById("salida");
const laberinto = document.getElementById("laberinto");

const canvas = document.getElementById("mazeCanvas");
const ctx = canvas.getContext("2d");

let dragging = false;
let vidas = 3;

const startX = 20;
const startY = 140;

laberinto.onload = () => {

    canvas.width = laberinto.naturalWidth;
    canvas.height = laberinto.naturalHeight;

    ctx.drawImage(
        laberinto,
        0,
        0
    );

};

pumpkin.style.left = startX + "px";
pumpkin.style.top = startY + "px";

pumpkin.addEventListener("mousedown",()=>{

    dragging = true;

});

document.addEventListener("mouseup",()=>{

    dragging = false;

});

document.addEventListener("mousemove",(e)=>{

    if(!dragging) return;

    const rect =
    document
    .querySelector(".laberinto-container")
    .getBoundingClientRect();

    let x = e.clientX - rect.left - 25;
    let y = e.clientY - rect.top - 25;

    pumpkin.style.left = x + "px";
    pumpkin.style.top = y + "px";

    verificarPared();
    verificarSalida();

});

function actualizarVidas(){

    let corazones = "";

    for(let i=0;i<vidas;i++){

        corazones += "❤️";

    }

    document
    .getElementById("vidas")
    .innerHTML = corazones;

}

function perderVida(){

    dragging = false;

    vidas--;

    actualizarVidas();

    if(vidas <= 0){

        alert(
        "☠️ Juego terminado. Te quedaste sin vidas."
        );

        location.reload();

        return;

    }

    alert(
    "💥 Tocaste una pared. Regresas al inicio."
    );

    pumpkin.style.left = startX + "px";
    pumpkin.style.top = startY + "px";

}

function verificarPared(){

    const imgRect =
    laberinto.getBoundingClientRect();

    const scaleX =
    canvas.width / imgRect.width;

    const scaleY =
    canvas.height / imgRect.height;

    const x =
    (parseInt(pumpkin.style.left)+12)
    * scaleX;

    const y =
    (parseInt(pumpkin.style.top)+12)
    * scaleY;

    if(
        x < 0 ||
        y < 0 ||
        x >= canvas.width ||
        y >= canvas.height
    ){
        return;
    }

    const pixel =
    ctx.getImageData(
        Math.floor(x),
        Math.floor(y),
        1,
        1
    ).data;

    const r = pixel[0];
    const g = pixel[1];
    const b = pixel[2];

    if(
        r < 30 &&
        g < 30 &&
        b < 30
    ){

        perderVida();

    }

}

function verificarSalida(){

    const p =
    pumpkin.getBoundingClientRect();

    const s =
    salida.getBoundingClientRect();

    if(

        p.right > s.left &&
        p.left < s.right &&
        p.bottom > s.top &&
        p.top < s.bottom

    ){

        pumpkin.classList.add("win");

        setTimeout(()=>{

            alert(
            "🎉 ¡Felicidades! La calabaza ha salido del laberinto."
            );

            document
            .getElementById("formCompletar")
            .submit();

        },500);

    }

}

actualizarVidas();

</script>

</body>
</html>