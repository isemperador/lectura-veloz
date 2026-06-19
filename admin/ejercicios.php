<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

/*==========================
ESTADÍSTICAS POR EJERCICIO
===========================*/
$stmt = $pdo->query("
    SELECT 
        e.ejercicio,
        COUNT(DISTINCT e.usuario_id) as usuarios_ingresados,
        COUNT(CASE WHEN p.completado = 1 THEN 1 END) as usuarios_completados,
        COALESCE(AVG(i.intentos), 0) as promedio_intentos,
        COALESCE(SUM(i.intentos), 0) as total_intentos
    FROM (
        SELECT DISTINCT usuario_id, ejercicio FROM intentos
        UNION
        SELECT usuario_id, ejercicio FROM progreso
    ) e
    LEFT JOIN progreso p ON e.usuario_id = p.usuario_id AND e.ejercicio = p.ejercicio
    LEFT JOIN intentos i ON e.usuario_id = i.usuario_id AND e.ejercicio = i.ejercicio
    GROUP BY e.ejercicio
    ORDER BY e.ejercicio
");
$estadisticas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicios - LECTIUM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background:#0f172a;
            color:white;
        }
        .sidebar{
            position:fixed;
            left:0; top:0;
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
        .menu a:hover, .menu a.active{
            background:#3b82f6;
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
        .progress {
            height: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">🧠 LECTIUM</div>
    <div class="menu">
        <a href="dashboard1.php">📊 Dashboard</a>
        <a href="usuarios.php">👥 Usuarios</a>
        <a href="actividad.php">📋 Actividad</a>
        <a href="seguimiento.php">🔍 Seguimiento</a>
        <a href="ejercicios.php" class="active">📚 Ejercicios</a>
        <a href="estadisticas.php">📈 Estadísticas</a>
        <a href="../logout.php">🚪 Cerrar Sesión</a>
    </div>
</div>

<div class="content">
    <h2 class="mb-4">📚 Estadísticas por Ejercicio</h2>

    <div class="row">
        <?php foreach($estadisticas as $ej): 
            $porcentaje = $ej['usuarios_ingresados'] > 0 
                ? round(($ej['usuarios_completados'] / $ej['usuarios_ingresados']) * 100) 
                : 0;
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card-admin">
                <h4>Ejercicio <?= $ej['ejercicio'] ?></h4>
                
                <div class="mb-3">
                    <strong>Usuarios que ingresaron:</strong> <?= $ej['usuarios_ingresados'] ?>
                </div>
                <div class="mb-3">
                    <strong>Usuarios que completaron:</strong> <?= $ej['usuarios_completados'] ?>
                </div>
                <div class="mb-3">
                    <strong>Promedio de Intentos:</strong> <?= round($ej['promedio_intentos'], 1) ?>
                </div>

                <div class="progress mb-2">
                    <div class="progress-bar bg-success" style="width: <?= $porcentaje ?>%">
                        <?= $porcentaje ?>%
                    </div>
                </div>
                <small class="text-muted">Tasa de completado</small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>