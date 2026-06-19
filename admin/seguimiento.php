<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$ejercicio_seleccionado = isset($_GET['ejercicio']) ? (int)$_GET['ejercicio'] : 1;
if($ejercicio_seleccionado < 1 || $ejercicio_seleccionado > 12) $ejercicio_seleccionado = 1;

// Obtener usuarios con datos de ese ejercicio
$stmt = $pdo->prepare("
    SELECT 
        u.id,
        u.username,
        u.nombre,
        COALESCE(i.intentos, 0) as intentos,
        COALESCE(p.completado, 0) as completado,
        p.fecha_completado
    FROM usuarios u
    LEFT JOIN intentos i ON u.id = i.usuario_id AND i.ejercicio = ?
    LEFT JOIN progreso p ON u.id = p.usuario_id AND p.ejercicio = ?
    WHERE u.username <> 'admin'
    ORDER BY completado DESC, intentos DESC, u.nombre
");
$stmt->execute([$ejercicio_seleccionado, $ejercicio_seleccionado]);
$datos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento - Ejercicio <?= $ejercicio_seleccionado ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#0f172a; color:white; }
        .sidebar { position:fixed; left:0; top:0; width:260px; height:100vh; background:#111827; padding:25px; }
        .content { margin-left:280px; padding:30px; }
        .card-admin { background:#1e293b; border:none; border-radius:20px; padding:20px; }
        .menu a { display:block; padding:14px; margin-bottom:10px; text-decoration:none; color:white; border-radius:12px; }
        .menu a:hover, .menu a.active { background:#3b82f6; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo" style="font-size:28px;font-weight:bold;margin-bottom:30px;color:#38bdf8;">🧠 LECTIUM</div>
    <div class="menu">
        <a href="dashboard1.php">📊 Dashboard</a>
        <a href="usuarios.php">👥 Usuarios</a>
        <a href="actividad.php">📋 Actividad</a>
        <a href="seguimiento.php" class="active">🔍 Seguimiento</a>
        <a href="ejercicios.php">📚 Ejercicios</a>
        <a href="estadisticas.php">📈 Estadísticas</a>
        <a href="../logout.php">🚪 Cerrar Sesión</a>
    </div>
</div>

<div class="content">
    <h2>🔍 Seguimiento por Ejercicio</h2>

    <div class="mb-4">
        <form method="GET">
            <label>Seleccionar Ejercicio:</label>
            <select name="ejercicio" class="form-select w-25 d-inline" onchange="this.form.submit()">
                <?php for($i=1; $i<=12; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $ejercicio_seleccionado ? 'selected' : '' ?>>
                        Ejercicio <?= $i ?>
                    </option>
                <?php endfor; ?>
            </select>
        </form>
    </div>

    <div class="card-admin">
        <h4>Ejercicio <?= $ejercicio_seleccionado ?></h4>
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Intentos</th>
                    <th>Estado</th>
                    <th>Fecha Completado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($datos as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['username']) ?></td>
                    <td><?= htmlspecialchars($d['nombre']) ?></td>
                    <td><strong><?= $d['intentos'] ?></strong></td>
                    <td>
                        <?php if($d['completado']): ?>
                            <span class="badge bg-success">✅ Completado</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">⏳ Pendiente</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $d['fecha_completado'] ? date('d/m/Y H:i', strtotime($d['fecha_completado'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>