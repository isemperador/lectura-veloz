<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Obtener progreso del usuario
$stmt = $pdo->prepare("SELECT ejercicio, completado FROM progreso WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$progreso = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // ejercicio => completado
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LECTIUM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card-hover:hover { transform: translateY(-5px); transition: all 0.3s; }
        .completado { background-color: #d4edda !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div>

            <a href="usuario/inicio.php"
            class="btn btn-info btn-sm me-2">

            🏠 Inicio

            </a>

            <span class="text-light me-3">
                👤 <?= htmlspecialchars($_SESSION['username']) ?>
            </span>

            <a href="logout.php"
            class="btn btn-outline-light btn-sm">

            🚪 Cerrar Sesión

            </a>

        </div>
    </nav>

    <div class="container py-5">
        <h1 class="text-center mb-5">📖 Programa de Lectura Veloz</h1>
        
        <div class="row g-4">
            <?php for($i = 1; $i <= 12; $i++): 
                $completado = isset($progreso[$i]) && $progreso[$i] == 1;
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-hover h-100 <?= $completado ? 'completado border-success' : '' ?>">
                    <div class="card-body">
                        <h5 class="card-title">Ejercicio <?= $i ?></h5>
                        <p class="card-text">
                            <?php
                            $titulos = [
                                1 => "Agiliza tu percepción",
                                2 => "Enfoca tu percepción",
                                3 => "Expande tu campo de visión (Parte 1)",
                                4 => "Expande tu campo de visión (Parte 2)",
                                5 => "Reduce las fijaciones",
                                6 => "Desliza tu mirada en la lectura",

                                7 => "Asociación de letras y números",
                                8 => "Dibujo y coordinación visual",
                                9 => "Laberinto visual",
                                10 => "Mensaje oculto",
                                11 => "Velocidad de lectura",
                                12 => "Comprensión lectora"
                            ];
                            echo $titulos[$i];
                            ?>
                        </p>
                        <?php if($completado): ?>
                            <span class="badge bg-success">✅ Completado</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="ejercicios/<?= $i ?>.php" class="btn btn-primary w-100">
                            <?= $completado ? 'Repasar Ejercicio' : 'Iniciar Ejercicio' ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>