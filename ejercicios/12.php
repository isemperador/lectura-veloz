<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 12);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 12"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        12  
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 12"
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

<title>LECTIUM - Ejercicio 12</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

<style>

body{

background:
linear-gradient(135deg,#0f172a,#1e293b);

min-height:100vh;
padding:30px;
color:white;

}

.exercise{

background:white;
color:black;

padding:40px;
border-radius:30px;

max-width:1200px;
margin:auto;

box-shadow:0 10px 30px rgba(0,0,0,.3);

}

.title{

font-size:50px;
font-weight:bold;
text-align:center;

color:#2563eb;

}

.subtitle{

text-align:center;
font-size:22px;
margin-bottom:30px;

}

.dashboard{

position:fixed;
right:20px;
top:50%;
transform:translateY(-50%);
z-index:999;

}

#frase{

display:flex;
flex-wrap:wrap;
justify-content:center;

gap:4px;

margin-bottom:40px;

}

.casilla{

width:45px;
height:45px;

border:2px solid #ccc;
border-radius:8px;

background:white;

}

.casilla.verde{

background:#22c55e;
color:white;

display:flex;
justify-content:center;
align-items:center;

font-weight:bold;
font-size:22px;

}

.espacio{

width:20px;

}

#historial{

margin-top:30px;

}

.fila{

display:flex;
justify-content:center;

gap:6px;
margin-bottom:8px;

}

.box{

width:55px;
height:55px;

border-radius:8px;

display:flex;
justify-content:center;
align-items:center;

font-weight:bold;
font-size:24px;

color:white;

}

.verde{

background:#22c55e;

}

.gris{

background:#6b7280;

}

.wordle-board{

display:flex;
flex-direction:column;
align-items:center;
margin-bottom:30px;

}

.wordle-row{

display:flex;
gap:6px;
margin-bottom:6px;

}

.wordle-empty{

width:55px;
height:55px;

border:2px solid #d1d5db;
border-radius:8px;

background:white;

}

.input-area{

text-align:center;
margin-top:30px;

}

.input-palabra{

width:250px;
margin:auto;

font-size:22px;
text-transform:uppercase;

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

<div class="exercise">

<h1 class="title">

🧩 EJERCICIO 12

</h1>

<p class="subtitle">

Descubre la frase escribiendo palabras de 5 letras

</p>

<div id="frase"></div>

<div class="wordle-board">

<div class="wordle-row">
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
</div>

<div class="wordle-row">
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
</div>

<div class="wordle-row">
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
</div>

<div class="wordle-row">
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
</div>

<div class="wordle-row">
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
</div>

<div class="wordle-row">
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
<div class="wordle-empty"></div>
</div>

</div>

<div id="historial"></div>

<div class="input-area">

<input
type="text"
id="palabra"
maxlength="5"
class="form-control form-control-lg input-palabra"
placeholder="PALABRA">

<button
class="btn btn-success btn-lg mt-3"
onclick="intentar()">

Intentar

</button>

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

const frases = [
    "LA LECTURA ES VIDA",
    "LEER ABRE NUEVAS PUERTAS",
    "NUNCA DEJES DE LEER",
    "TODOS PUEDEN APRENDER",
    "LEER HACE CRECER MAS",
    "VIVIR ES SEGUIR LEYENDO"
];

const frase = frases[Math.floor(Math.random() * frases.length)];
const fraseLimpia = frase.replace(/ /g, "");
let letrasDescubiertas = [];
let filaActual = 0;   // ← Nueva variable para controlar la fila actual

function dibujarFrase() {
    const contenedor = document.getElementById("frase");
    contenedor.innerHTML = "";

    frase.split("").forEach(caracter => {
        if (caracter === " ") {
            const espacio = document.createElement("div");
            espacio.className = "espacio";
            contenedor.appendChild(espacio);
        } else {
            const box = document.createElement("div");
            if (letrasDescubiertas.includes(caracter)) {
                box.className = "casilla verde";
                box.innerHTML = caracter;
            } else {
                box.className = "casilla";
            }
            contenedor.appendChild(box);
        }
    });
}

// Llena la siguiente fila del tablero principal
function llenarFilaTablero(palabra) {
    if (filaActual >= 6) return;

    const rows = document.querySelectorAll(".wordle-row");
    const row = rows[filaActual];

    // Limpiar fila actual (por si acaso)
    row.innerHTML = "";

    palabra.split("").forEach((letra, index) => {
        const box = document.createElement("div");
        box.className = "box";
        box.innerHTML = letra;

        if (fraseLimpia.includes(letra)) {
            box.classList.add("verde");
            if (!letrasDescubiertas.includes(letra)) {
                letrasDescubiertas.push(letra);
            }
        } else {
            box.classList.add("gris");
        }

        row.appendChild(box);
    });

    filaActual++;
}

function intentar() {
    const input = document.getElementById("palabra");
    let palabra = input.value.toUpperCase().trim();

    if (palabra.length !== 5) {
        alert("⚠️ Debes escribir exactamente 5 letras");
        return;
    }

    llenarFilaTablero(palabra);     // ← Aquí llenamos el tablero principal
    dibujarFrase();                 // Actualizamos las letras descubiertas arriba
    verificarCompleto();

    input.value = "";
}

function verificarCompleto() {
    let completo = true;
    fraseLimpia.split("").forEach(letra => {
        if (!letrasDescubiertas.includes(letra)) {
            completo = false;
        }
    });

    if (completo) {
        confetti({
            particleCount: 300,
            spread: 180,
            origin: { y: 0.6 }
        });

        setTimeout(() => {
            alert("🏆 ¡Felicitaciones! Descubriste toda la frase.");
            document.getElementById("formCompletar").submit();
        }, 1000);
    }
}

// Enter para enviar
document.getElementById("palabra").addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
        intentar();
    }
});

// Inicializar
dibujarFrase();

</script>

</body>
</html>