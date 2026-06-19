<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

/*==========================
DATOS PARA GRÁFICOS
===========================*/

// Intentos por ejercicio
$stmt = $pdo->query("
    SELECT ejercicio, SUM(intentos) as total 
    FROM intentos 
    GROUP BY ejercicio 
    ORDER BY ejercicio
");
$intentos_por_ej = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Completados por ejercicio
$stmt = $pdo->query("
    SELECT ejercicio, COUNT(*) as completados 
    FROM progreso 
    WHERE completado = 1 
    GROUP BY ejercicio 
    ORDER BY ejercicio
");
$completados_por_ej = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Usuarios más activos
$stmt = $pdo->query("
    SELECT u.username, SUM(i.intentos) as total_intentos
    FROM usuarios u
    JOIN intentos i ON u.id = i.usuario_id
    WHERE u.username <> 'admin'
    GROUP BY u.id
    ORDER BY total_intentos DESC
    LIMIT 5
");
$usuarios_activos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - LECTIUM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        body{ background:#0f172a; color:white; }
        .sidebar{
            position:fixed; left:0; top:0;
            width:260px; height:100vh;
            background:#111827; padding:25px;
        }
        .logo{
            font-size:28px; font-weight:bold;
            margin-bottom:30px; color:#38bdf8;
        }
        .menu a{
            display:block; padding:14px; margin-bottom:10px;
            text-decoration:none; color:white; border-radius:12px;
        }
        .menu a:hover, .menu a.active{ background:#3b82f6; }
        .content{ margin-left:280px; padding:30px; }
        .card-admin{
            background:#1e293b; border:none; border-radius:20px;
            padding:20px; box-shadow:0 0 20px rgba(0,0,0,.3);
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
        <a href="estadisticas.php" class="active">📈 Estadísticas</a>
        <a href="../logout.php">🚪 Cerrar Sesión</a>
    </div>
</div>

<div class="content">
    <h2 class="mb-4">📈 Estadísticas Generales</h2>

    <div class="row">
        <!-- Gráfico 1: Intentos por Ejercicio -->
        <div class="col-lg-8">
            <div class="card-admin mb-4">
                <h4>📊 Intentos por Ejercicio</h4>
                <canvas id="chartIntentos" height="100"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Usuarios más activos -->
        <div class="col-lg-4">
            <div class="card-admin mb-4">
                <h4>🏆 Usuarios Más Activos</h4>
                <canvas id="chartUsuarios" height="180"></canvas>
            </div>
        </div>
    </div>

    <div class="card-admin">
        <h4>📋 Resumen General</h4>
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Ejercicio</th>
                    <th>Intentos Totales</th>
                    <th>Completados</th>
                    <th>% Completado</th>
                </tr>
            </thead>
            <tbody>
                <?php for($i=1; $i<=12; $i++): 
                    $intentos = $intentos_por_ej[$i] ?? 0;
                    $completados = $completados_por_ej[$i] ?? 0;
                    $porc = $intentos > 0 ? round(($completados / max($completados,1)) * 100) : 0; // simple
                ?>
                <tr>
                    <td><strong><?= $i ?></strong></td>
                    <td><?= $intentos ?></td>
                    <td><?= $completados ?></td>
                    <td><?= $porc ?>%</td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Gráfico de Intentos por Ejercicio
new Chart(document.getElementById('chartIntentos'), {
    type: 'bar',
    data: {
        labels: Array.from({length:12}, (_,i) => "Ej " + (i+1)),
        datasets: [{
            label: 'Intentos',
            data: <?= json_encode(array_values(array_replace(array_fill(1,12,0), $intentos_por_ej))) ?>,
            backgroundColor: '#60a5fa',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, grid: { color: '#334155' }, ticks: { color: '#cbd5e1' }},
            x: { grid: { color: '#334155' }, ticks: { color: '#cbd5e1' }}
        }
    }
});

// Gráfico de Usuarios Más Activos
new Chart(document.getElementById('chartUsuarios'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($usuarios_activos, 'username')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($usuarios_activos, 'total_intentos')) ?>,
            backgroundColor: ['#60a5fa', '#34d399', '#fbbf24', '#f87171', '#a78bfa']
        }]
    },
    options: { responsive: true }
});
</script>
</body>
</html>