<?php
session_start();
require_once '../config.php';

if(
    !isset($_SESSION['usuario_id']) ||
    $_SESSION['username'] != 'admin'
){
    header("Location: ../index.php");
    exit();
}

/*==========================
CREAR USUARIO
===========================*/

if(isset($_POST['crear'])){

    $stmt = $pdo->prepare("
    INSERT INTO usuarios
    (username,password,nombre,rol)
    VALUES(?,?,?,'usuario')
    ");

    $stmt->execute([
        $_POST['username'],
        $_POST['password'],
        $_POST['nombre']
    ]);

    header("Location: usuarios.php");
    exit();
}

/*==========================
LISTA DE USUARIOS
===========================*/

$stmt = $pdo->query("

SELECT
u.id,
u.username,
u.nombre,
COUNT(DISTINCT p.ejercicio) AS completados,
COALESCE(SUM(i.intentos),0) AS total_intentos
FROM usuarios u
LEFT JOIN progreso p ON u.id=p.usuario_id AND p.completado=1
LEFT JOIN intentos i ON u.id=i.usuario_id
WHERE u.username<>'admin'
GROUP BY u.id
ORDER BY completados DESC,u.nombre

");

$usuarios = $stmt->fetchAll();

/*==========================
ESTADISTICAS
===========================*/

$totalUsuarios = count($usuarios);
$totalCompletados = array_sum(array_column($usuarios,'completados'));
$totalIntentos = array_sum(array_column($usuarios,'total_intentos'));

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
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

        /* === TEXTO BLANCO FORZADO === */
        .card-panel h6,
        .card-panel h2,
        .card-panel h4,
        .card-body h6,
        .card-body h2,
        .card-body h4 {
            color: white !important;
        }

        h1, h2, h3, h4, h5, h6 {
            color: white !important;
        }

        .table-dark th,
        .table-dark td,
        .table-dark {
            color: white !important;
        }

        .table-dark thead th {
            background: #334155;
            color: white !important;
        }

        .badge {
            color: white !important;
        }

        .badge.bg-warning {
            color: #000 !important; /* este sí se mantiene oscuro */
        }

        input.form-control {
            background: #334155;
            color: white;
            border: none;
        }

        input.form-control::placeholder {
            color: #cbd5e1;
        }

        .btn {
            border-radius: 10px;
        }

        .progress {
            height: 22px;
        }
    </style>
</head>

<body>
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>👥 Gestión de Usuarios</h1>
        <a href="dashboard1.php" class="btn btn-warning">🏠 Panel Principal</a>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-panel">
                <div class="card-body text-center">
                    <h6>Usuarios</h6>
                    <h2><?= $totalUsuarios ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-panel">
                <div class="card-body text-center">
                    <h6>Ejercicios</h6>
                    <h2>12</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-panel">
                <div class="card-body text-center">
                    <h6>Completados</h6>
                    <h2><?= $totalCompletados ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-panel">
                <div class="card-body text-center">
                    <h6>Intentos</h6>
                    <h2><?= $totalIntentos ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Crear Nuevo Usuario -->
    <div class="card card-panel mb-4">
        <div class="card-body">
            <h4 class="mb-4">➕ Crear Nuevo Usuario</h4>
            <form method="POST">
                <div class="row">
                    <div class="col-md-3">
                        <input name="username" class="form-control" placeholder="Usuario" required>
                    </div>
                    <div class="col-md-3">
                        <input name="password" class="form-control" placeholder="Contraseña" required>
                    </div>
                    <div class="col-md-4">
                        <input name="nombre" class="form-control" placeholder="Nombre Completo" required>
                    </div>
                    <div class="col-md-2">
                        <button name="crear" class="btn btn-success w-100">Crear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Usuarios -->
    <div class="mb-4">
        <input type="text" id="buscar" class="form-control" placeholder="🔍 Buscar usuario...">
    </div>

    <div class="card card-panel">
        <div class="card-body">
            <h4 class="mb-4">📋 Lista de Usuarios</h4>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th width="220">Progreso</th>
                            <th>Ejercicio Actual</th>
                            <th>Intentos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $u):
                            $porcentaje = round(($u['completados']/12)*100);
                            $actual = min($u['completados'] + 1, 12);
                        ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                            <td><?= htmlspecialchars($u['nombre']) ?></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                         style="width:<?= $porcentaje ?>%">
                                        <?= $u['completados'] ?>/12
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if($u['completados'] >= 12): ?>
                                    <span class="badge bg-success">🏆 Finalizado</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">📚 Ejercicio <?= $actual ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark"><?= $u['total_intentos'] ?></span>
                            </td>
                            <td>
                                <a href="detalle_usuario.php?id=<?= $u['id'] ?>" class="btn btn-primary btn-sm">👁 Ver</a>
                                <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">✏ Editar</a>
                                <a href="eliminar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('¿Eliminar este usuario?')">🗑</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById("buscar").addEventListener("keyup", function(){
    let texto = this.value.toLowerCase();
    let filas = document.querySelectorAll("tbody tr");
    filas.forEach(function(fila){
        let contenido = fila.innerText.toLowerCase();
        fila.style.display = contenido.includes(texto) ? "" : "none";
    });
});
</script>

</body>
</html>