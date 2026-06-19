<?php
session_start();
require_once '../config.php';

// Solo el administrador puede entrar (usuario "admin")
if (!isset($_SESSION['usuario_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Crear nuevo usuario
if (isset($_POST['crear_usuario'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $nombre   = $_POST['nombre'];

    $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nombre) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $nombre])) {
        $mensaje = "Usuario '$username' creado correctamente";
    } else {
        $error = "Error al crear usuario (puede que ya exista)";
    }
}

// Ver progreso de todos los usuarios
$stmt = $pdo->query("SELECT u.username, u.nombre, 
                    COUNT(CASE WHEN p.completado = 1 THEN 1 END) as completados 
                    FROM usuarios u 
                    LEFT JOIN progreso p ON u.id = p.usuario_id 
                    GROUP BY u.id");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container py-5">
        <h1 class="text-center mb-4">🛠 Panel de Administrador</h1>
        <a href="../dashboard.php" class="btn btn-secondary mb-4">← Volver al Dashboard</a>

        <div class="row">
            <!-- Crear Usuario -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5>Crear Nuevo Usuario</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($mensaje)): ?>
                            <div class="alert alert-success"><?= $mensaje ?></div>
                        <?php endif; ?>
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Usuario (login)</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Contraseña</label>
                                <input type="text" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Nombre completo</label>
                                <input type="text" name="nombre" class="form-control">
                            </div>
                            <button type="submit" name="crear_usuario" class="btn btn-primary w-100">
                                Crear Usuario
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Usuarios y Progreso -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>Usuarios y Progreso</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Nombre</th>
                                    <th>Ejercicios Completados</th>
                                    <th>Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($usuarios as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['username']) ?></td>
                                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                                    <td><strong><?= $u['completados'] ?>/6</strong></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?= ($u['completados']/6)*100 ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>