<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

/*==========================
ESTADÍSTICAS GENERALES
===========================*/
$totalIntentos = $pdo->query("SELECT IFNULL(SUM(intentos),0) FROM intentos")->fetchColumn();

$totalUsuariosActivos = $pdo->query("
    SELECT COUNT(DISTINCT usuario_id) 
    FROM intentos
")->fetchColumn();

/* Usuarios más activos */
$stmt = $pdo->query("
    SELECT 
        u.username,
        u.nombre,
        SUM(i.intentos) as total_intentos,
        COUNT(DISTINCT i.ejercicio) as ejercicios_intentados
    FROM usuarios u
    LEFT JOIN intentos i ON u.id = i.usuario_id
    WHERE u.username <> 'admin'
    GROUP BY u.id
    ORDER BY total_intentos DESC
    LIMIT 8
");
$topUsuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividad - LECTIUM</title>
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
    <a href="ejercicios.php">📚 Ejercicios</a>
    <a href="estadisticas.php">📈 Estadísticas</a>
    <a href="../logout.php">🚪 Cerrar Sesión</a>
</div>
</div>

<div class="content">
    <h2 class="mb-4">📋 Panel de Actividad</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card-admin">
                <h6>Total de Intentos</h6>
                <h2 class="big"><?= number_format($totalIntentos) ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-admin">
                <h6>Usuarios Activos</h6>
                <h2 class="big"><?= $totalUsuariosActivos ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-admin">
                <h6>Ejercicios Totales</h6>
                <h2 class="big">12</h2>
            </div>
        </div>
    </div>

    <h4 class="mb-3">🏆 Usuarios Más Activos</h4>
    <div class="card-admin">
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Intentos</th>
                    <th>Ejercicios Probados</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($topUsuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><strong><?= $u['total_intentos'] ?></strong></td>
                    <td><?= $u['ejercicios_intentados'] ?>/12</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if(empty($topUsuarios)): ?>
        <div class="card-admin text-center py-5">
            <p>Aún no hay actividad registrada por los usuarios.</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>