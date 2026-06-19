<?php
session_start();
require_once '../config.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Registrar intento al abrir el ejercicio
registrarIntento($pdo, $_SESSION['usuario_id'], 11);

// Registrar actividad
registrarActividad(
    $pdo,
    $_SESSION['usuario_id'],
    "Ingresó al ejercicio 11"
);

// Cuando termine el ejercicio
if (isset($_POST['completar'])) {

    completarEjercicio(
        $pdo,
        $_SESSION['usuario_id'],
        11  
    );

    registrarActividad(
        $pdo,
        $_SESSION['usuario_id'],
        "Completó el ejercicio 11"
    );

    header("Location: ../dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ejercicio 11</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

<style>

body{
background:linear-gradient(135deg,#4e54c8,#8f94fb);
min-height:100vh;
padding:30px;
}

.container-box{
background:white;
padding:30px;
border-radius:25px;
box-shadow:0 10px 25px rgba(0,0,0,.2);
max-width:900px;
margin:auto;
}

h1{
text-align:center;
font-weight:bold;
margin-bottom:20px;
}

.grid{
display:grid;
grid-template-rows:repeat(6,70px);
gap:8px;
max-width:400px;
margin:auto;
}

.row-word{
display:grid;
grid-template-columns:repeat(5,70px);
gap:8px;
}

.cell{
width:70px;
height:70px;
border:2px solid #ccc;
display:flex;
align-items:center;
justify-content:center;
font-size:30px;
font-weight:bold;
text-transform:uppercase;
background:white;
}

.green{
background:#28a745;
color:white;
}

.yellow{
background:#ffc107;
color:black;
}

.gray{
background:#6c757d;
color:white;
}

.input-area{
max-width:400px;
margin:30px auto;
}

#significado{
display:none;
background:#d4edda;
padding:20px;
border-radius:15px;
margin-top:20px;
font-size:18px;
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

<div class="container-box">

<h1>
🧠 EJERCICIO 11
</h1>

<p class="text-center">
Descubre la palabra secreta
</p>

<div class="grid" id="grid">

</div>

<div class="input-area">

<input
type="text"
maxlength="5"
id="entrada"
class="form-control form-control-lg text-center"
placeholder="Escribe una palabra">

<button
class="btn btn-primary w-100 mt-3"
onclick="intentar()">

Intentar

</button>

</div>

<div id="significado"></div>

<div class="text-center mt-4">

<button
id="btnFinalizar"
class="btn btn-success btn-lg"
style="display:none"
onclick="finalizarEjercicio()">

Finalizar Ejercicio

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

const palabras=[

{
palabra:"MUNDO",
significado:"Conjunto de todo lo existente."
},

{
palabra:"LIBRO",
significado:"Conjunto de hojas escritas o impresas."
},

{
palabra:"FLORA",
significado:"Conjunto de plantas de una región."
},

{
palabra:"PLAYA",
significado:"Zona de arena junto al mar."
},

{
palabra:"NUBES",
significado:"Masas visibles de vapor de agua."
},

{
palabra:"SOLAR",
significado:"Relativo al Sol."
},

{
palabra:"CASAS",
significado:"Construcciones destinadas a vivienda."
}

];

const seleccion=
palabras[
Math.floor(Math.random()*palabras.length)
];

const palabra=
seleccion.palabra;

let fila=0;

const grid=
document.getElementById("grid");

for(let i=0;i<6;i++){

let row=document.createElement("div");

row.className="row-word";

for(let j=0;j<5;j++){

let cell=document.createElement("div");

cell.className="cell";

row.appendChild(cell);

}

grid.appendChild(row);

}

function intentar(){

if(fila>=6) return;

const intento=
document
.getElementById("entrada")
.value
.toUpperCase()
.trim();

if(intento.length!==5){

alert("Debe tener 5 letras");

return;

}

const rows=
document.querySelectorAll(".row-word");

const cells=
rows[fila]
.querySelectorAll(".cell");

let letrasDisponibles =
palabra.split("");

for(let i=0;i<5;i++){

    if(intento[i]===palabra[i]){

        letrasDisponibles[i] = null;

    }

}

for(let i=0;i<5;i++){

    cells[i].innerHTML =
    intento[i];

    if(intento[i]===palabra[i]){

        cells[i].classList.add("green");

    }else{

        const index =
        letrasDisponibles.indexOf(
            intento[i]
        );

        if(index !== -1){

            cells[i].classList.add("yellow");

            letrasDisponibles[index] = null;

        }else{

            cells[i].classList.add("gray");

        }

    }

}

if(intento===palabra){

confetti({

particleCount:250,
spread:180

});

document
.getElementById("significado")
.style.display="block";

document
.getElementById("significado")
.innerHTML=

`<h4>🎉 ¡Correcto!</h4>

<b>${palabra}</b>

<br><br>

${seleccion.significado}`;

document
.getElementById("btnFinalizar")
.style.display="inline-block";

document
.getElementById("entrada")
.disabled=true;

return;

}

fila++;

document
.getElementById("entrada")
.value="";

if(fila===6){

alert(
"😢 Se acabaron los intentos.\nLa palabra era: "
+ palabra
);

}

}

function finalizarEjercicio(){

confetti({

particleCount:300,
spread:200

});

setTimeout(()=>{

alert(
"🏆 Felicitaciones, completaste el Ejercicio 11."
);

document
.getElementById("formCompletar")
.submit();

},1000);

}

</script>

</body>
</html>