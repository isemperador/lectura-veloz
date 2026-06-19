<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['username'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$usuario_id = (int)$_GET['id'];

/*==========================
DATOS DEL USUARIO
===========================*/
$stmt = $pdo->prepare("SELECT id, username, nombre FROM usuarios WHERE id = ? AND username <> 'admin'");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: usuarios.php");
    exit();
}

/*==========================
PROGRESO Y EJERCICIOS
===========================*/
$stmt = $pdo->prepare("
    SELECT ejercicio, completado, fecha_completado 
    FROM progreso 
    WHERE usuario_id = ? 
    ORDER BY ejercicio
");
$stmt->execute([$usuario_id]);
$progreso = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear array con los 12 ejercicios
$ejercicios = [];
$completados = 0;
$ultima_actividad = null;

for ($i = 1; $i <= 12; $i++) {
    $ej = array_filter($progreso, fn($p) => $p['ejercicio'] == $i);
    $ej = reset($ej);
    
    $ejercicios[$i] = [
        'completado' => !empty($ej) && $ej['completado'] == 1,
        'fecha' => $ej['fecha_completado'] ?? null
    ];
    
    if ($ejercicios[$i]['completado']) {
        $completados++;
        if ($ej['fecha_completado'] && (!$ultima_actividad || $ej['fecha_completado'] > $ultima_actividad)) {
            $ultima_actividad = $ej['fecha_completado'];
        }
    }
}

/*==========================
INTENTOS POR EJERCICIO
===========================*/
$stmt = $pdo->prepare("
    SELECT ejercicio, SUM(intentos) as total_intentos 
    FROM intentos 
    WHERE usuario_id = ? 
    GROUP BY ejercicio 
    ORDER BY ejercicio
");
$stmt->execute([$usuario_id]);
$intentos_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle - <?= htmlspecialchars($usuario['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #0f172a;
            color: white;
        }

        .card-panel {
            background: #1e293b;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,.3);
        }

        /* === TEXTO BLANCO FORZADO EN TODO EL SITIO === */
        h1, h2, h3, h4, h5, h6,
        .card-body, .card-title, .card-text,
        label, .form-label, strong, p, small,
        .text-muted, .text-secondary {
            color: white !important;
        }

        /* Textos que deben ser un poco más suaves pero aún visibles */
        .text-muted {
            color: #cbd5e1 !important;   /* Gris claro bonito */
        }

        /* Tablas */
        .table-dark th, 
        .table-dark td {
            color: white !important;
        }

        .table-dark thead th {
            background: #334155;
            color: white !important;
        }

        /* Inputs */
        input.form-control {
            background: #334155;
            color: white !important;
            border: none;
        }

        input.form-control::placeholder {
            color: #94a3b8;
        }

        /* Progress */
        .progress {
            height: 25px;
        }

        /* Ejercicios */
        .ejercicio-completado {
            background: rgba(34, 197, 151, 0.2);
            border-left: 4px solid #22c55e;
            color: white !important;
        }

        .ejercicio-pendiente {
            background: rgba(148, 163, 184, 0.1);
            border-left: 4px solid #64748b;
            color: white !important;
        }

        /* Badges y Alertas */
        .badge {
            color: white !important;
        }

        .badge.bg-warning {
            color: #000 !important; /* Este se mantiene oscuro porque es amarillo */
        }

        .alert {
            color: white !important;
        }

        .btn-light {
            color: #000 !important;
        }
    </style>
</head>
<body>
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>👤 Detalle del Alumno</h1>
        <a href="usuarios.php" class="btn btn-warning">← Volver a Lista</a>
    </div>

    <!-- Información del Usuario -->
    <div class="card card-panel mb-4">
        <div class="card-body">
            <h4>👤 Información del Usuario</h4>
            <div class="row">
                <div class="col-md-4">
                    <strong>Nombre:</strong><br>
                    <h5><?= htmlspecialchars($usuario['nombre']) ?></h5>
                </div>
                <div class="col-md-4">
                    <strong>Usuario:</strong><br>
                    <h5>@<?= htmlspecialchars($usuario['username']) ?></h5>
                </div>
                <div class="col-md-4">
                    <strong>Progreso General:</strong><br>
                    <h5><?= $completados ?>/12 ejercicios</h5>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success progress-bar-striped" 
                             style="width: <?= round(($completados/12)*100) ?>%">
                            <?= round(($completados/12)*100) ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Estado de Ejercicios -->
        <div class="col-lg-8">
            <div class="card card-panel mb-4">
                <div class="card-body">
                    <h4 class="mb-4">📚 Estado de los 12 Ejercicios</h4>
                    <div class="row g-3">
                        <?php for($i=1; $i<=12; $i++): 
                            $estado = $ejercicios[$i]['completado'] ? 'completado' : 'pendiente';
                            $intentos = $intentos_data[$i] ?? 0;
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-panel p-3 ejercicio-<?= $estado ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Ejercicio <?= $i ?></h5>
                                    <?php if($ejercicios[$i]['completado']): ?>
                                        <span class="badge bg-success">✓ Completado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">⏳ Pendiente</span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">
                                    Intentos: <strong><?= $intentos ?></strong>
                                </small>
                                <?php if($ejercicios[$i]['fecha']): ?>
                                    <small class="text-success">
                                        Completado: <?= date('d/m/Y H:i', strtotime($ejercicios[$i]['fecha'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card card-panel mb-4">
                <div class="card-body">
                    <h5>📊 Resumen</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>Completados:</strong> <?= $completados ?>/12</li>
                        <li class="mb-2"><strong>Intentos Totales:</strong> 
                            <?= array_sum($intentos_data ?? []) ?>
                        </li>
                        <li class="mb-2">
                            <strong>Última Actividad:</strong><br>
                            <?= $ultima_actividad ? date('d/m/Y H:i', strtotime($ultima_actividad)) : 'Sin actividad' ?>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Logros (ejemplo) -->
            <div class="card card-panel">
                <div class="card-body">
                    <h5>🏆 Logros Alcanzados</h5>
                    <?php if($completados >= 6): ?>
                        <div class="badge bg-warning text-dark p-2 mb-2">🏅 Medio Camino</div><br>
                    <?php endif; ?>
                    <?php if($completados == 12): ?>
                        <div class="badge bg-success p-2">🏆 Curso Completo</div>
                    <?php endif; ?>
                    <?php if($completados < 6): ?>
                        <p class="text-muted">Sigue avanzando para desbloquear logros.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>