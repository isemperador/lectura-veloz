<?php
session_start();
require_once '../config.php';

if (
    !isset($_SESSION['usuario_id']) ||
    $_SESSION['username'] !== 'admin'
){
    header("Location: ../index.php");
    exit();
}

/* ESTADISTICAS */

$totalUsuarios = $pdo->query("
SELECT COUNT(*) 
FROM usuarios
WHERE username <> 'admin'
")->fetchColumn();

$totalCompletados = $pdo->query("
SELECT COUNT(*)
FROM progreso
WHERE completado=1
")->fetchColumn();

$totalIntentos = $pdo->query("
SELECT IFNULL(SUM(intentos),0)
FROM intentos
")->fetchColumn();

$usuarios = $pdo->query("
SELECT
u.id,
u.nombre,
u.username,
COUNT(p.ejercicio) completados
FROM usuarios u
LEFT JOIN progreso p
ON u.id=p.usuario_id
AND p.completado=1
WHERE u.username<>'admin'
GROUP BY u.id
ORDER BY completados DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Panel Administrativo</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#0f172a;
color:white;
}

.sidebar{

position:fixed;

left:0;
top:0;

width:260px;
height:100vh;

background:#111827;

padding:25px;

}

.logo{

font-size:28px;
font-weight:bold;

margin-bottom:30px;

color:#38bdf8;

}

.menu a{

display:block;

padding:14px;

margin-bottom:10px;

text-decoration:none;

color:white;

border-radius:12px;

transition:.3s;

}

.menu a:hover{

background:#1e293b;

}

.content{

margin-left:280px;
padding:30px;

}

.card-admin{

background:#1e293b;

border:none;

border-radius:20px;

padding:20px;

box-shadow:0 0 20px rgba(0,0,0,.3);

}

.big{

font-size:38px;
font-weight:bold;

}

.table-dark{
border-radius:20px;
overflow:hidden;
}

</style>

</head>

<body>

<div class="sidebar">

<div class="logo">
🧠 LECTIUM
</div>

<div class="menu">
    <a href="dashboard1.php">📊 Dashboard</a>
    <a href="usuarios.php">👥 Usuarios</a>
    <a href="actividad.php">📋 Actividad</a>
    <a href="seguimiento.php">🔍 Seguimiento</a>
    <a href="ejercicios.php">📚 Ejercicios</a>
    <a href="estadisticas.php">📈 Estadísticas</a>
    <a href="../logout.php">🚪 Cerrar Sesión</a>
</div>

</div>

<div class="content">

<h2 class="mb-4">
Panel Administrativo
</h2>

<div class="row">

<div class="col-md-3">

<div class="card-admin">

<h6>
Usuarios
</h6>

<div class="big">
<?= $totalUsuarios ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card-admin">

<h6>
Completados
</h6>

<div class="big">
<?= $totalCompletados ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card-admin">

<h6>
Intentos
</h6>

<div class="big">
<?= $totalIntentos ?>
</div>

</div>

</div>

<div class="col-md-3">

<div class="card-admin">

<h6>
Ejercicios
</h6>

<div class="big">
12
</div>

</div>

</div>

</div>

<hr class="my-5">

<h3>
🏆 Ranking de Usuarios
</h3>

<table class="table table-dark mt-3">

<thead>

<tr>

<th>Usuario</th>
<th>Nombre</th>
<th>Completados</th>

</tr>

</thead>

<tbody>
<?php foreach($usuarios as $u): ?>

<tr>

<td>
<?= htmlspecialchars($u['username']) ?>
</td>

<td>
<?= htmlspecialchars($u['nombre']) ?>
</td>

<td>

<div class="d-flex align-items-center">

<div
class="progress flex-grow-1 me-2"
style="height:25px;">

<div
class="progress-bar bg-success"
style="width:<?= ($u['completados']/12)*100 ?>%">

<?= $u['completados'] ?>/12

</div>

</div>

</div>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</body>
</html>