<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM progreso
WHERE usuario_id=?
AND completado=1
");

$stmt->execute([$_SESSION['usuario_id']]);

$completados = $stmt->fetchColumn();

$pendientes = 12 - $completados;

$porcentaje = round(($completados/12)*100);

$actual = min($completados + 1,12);

$usuario = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Inicio - LECTIUM</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

background:
linear-gradient(135deg,#0f172a,#1e293b);

min-height:100vh;

color:white;

overflow-x:hidden;

}

.navbar{

background:rgba(255,255,255,.05);

backdrop-filter:blur(12px);

}

.logo{

font-size:28px;
font-weight:bold;
color:#38bdf8;

}

.hero{

padding:60px 20px;

text-align:center;

}

.hero h1{

font-size:55px;
font-weight:bold;

}

.hero p{

font-size:22px;
color:#cbd5e1;

}

.card-modern{

background:#1e293b;

border:none;

border-radius:25px;

padding:20px;

box-shadow:
0 10px 30px rgba(0,0,0,.3);

transition:.3s;

height:100%;

}

.card-modern:hover{

transform:translateY(-5px);

}

.big-number{

font-size:50px;

font-weight:bold;

color:#38bdf8;

}

.progress{

height:35px;

border-radius:20px;

background:#334155;

}

.progress-bar{

font-size:18px;
font-weight:bold;

}

.logro{

padding:12px;
margin-bottom:10px;

border-radius:12px;

background:#334155;

}

.lock{

opacity:.5;

}

.btn-start{

padding:18px 50px;

font-size:22px;

border-radius:50px;

}

.footer-card{

background:#1e293b;

border:none;

border-radius:25px;

box-shadow:
0 10px 30px rgba(0,0,0,.3);

}

.glow{

animation:glow 2s infinite;

}

@keyframes glow{

0%{
box-shadow:0 0 10px #38bdf8;
}

50%{
box-shadow:0 0 30px #38bdf8;
}

100%{
box-shadow:0 0 10px #38bdf8;
}

}

</style>

</head>

<body>

<nav class="navbar navbar-expand-lg">

<div class="container">

<span class="logo">

📚 LECTIUM

</span>

<div>

<span class="me-4">

👤 <?= htmlspecialchars($usuario) ?>

</span>

<a href="../logout.php"
class="btn btn-outline-light">

Cerrar Sesión

</a>

</div>

</div>

</nav>

<div class="hero">

<h1>

🚀 Bienvenido a LECTIUM

</h1>

<p>

Entrena tu lectura veloz y mejora tu comprensión

</p>

</div>

<div class="container">

<div class="row g-4">

<div class="col-md-4">

<div class="card-modern text-center">

<div class="big-number">

<?= $completados ?>

</div>

<h4>

Ejercicios Completados

</h4>

</div>

</div>

<div class="col-md-4">

<div class="card-modern text-center">

<div class="big-number">

<?= $pendientes ?>

</div>

<h4>

Pendientes

</h4>

</div>

</div>

<div class="col-md-4">

<div class="card-modern text-center">

<div class="big-number">

<?= $actual ?>

</div>

<h4>

Ejercicio Actual

</h4>

</div>

</div>

</div>

<div class="card footer-card p-4 mt-5">

<h3 class="text-info fw-bold">

📊 Progreso General

</h3>

<div class="progress mt-3">

<div
class="progress-bar progress-bar-striped progress-bar-animated bg-success"
style="width:<?= $porcentaje ?>%">

<?= $porcentaje ?>%

</div>

</div>

</div>
<div class="row mt-5">

<div class="col-lg-6">

<div class="footer-card p-4">

<h3>

🏆 Logros

</h3>

<div class="logro <?= $completados >= 1 ? '' : 'lock' ?>">

<?= $completados >= 1 ? '✅' : '🔒' ?>

Primer ejercicio completado

</div>

<div class="logro <?= $completados >= 5 ? '' : 'lock' ?>">

<?= $completados >= 5 ? '✅' : '🔒' ?>

5 ejercicios completados

</div>

<div class="logro <?= $completados >= 10 ? '' : 'lock' ?>">

<?= $completados >= 10 ? '✅' : '🔒' ?>

Lector avanzado

</div>

<div class="logro <?= $completados >= 12 ? '' : 'lock' ?>">

<?= $completados >= 12 ? '✅' : '🔒' ?>

Maestro LECTIUM

</div>

</div>

</div>

<div class="col-lg-6">

<div class="footer-card p-4">

<h3>

📈 Resumen

</h3>

<p>

✅ Completados:
<strong><?= $completados ?></strong>

</p>

<p>

📚 Pendientes:
<strong><?= $pendientes ?></strong>

</p>

<p>

🎯 Siguiente ejercicio:
<strong><?= $actual ?></strong>

</p>

<p>

📊 Avance:
<strong><?= $porcentaje ?>%</strong>

</p>

</div>

</div>

</div>

<div class="text-center mt-5 mb-5">

<a
href="../dashboard.php"
class="btn btn-primary btn-start glow">

🚀 Continuar Entrenamiento

</a>

</div>

</div>

</body>
</html>