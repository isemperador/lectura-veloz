<?php
session_start();
require_once 'config.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("
        SELECT id, username, password, rol
        FROM usuarios
        WHERE username = ?
        ");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $password === $user['password']) {  // Comparación simple por ahora
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['rol'] = $user['rol'];

        if($user['rol'] == 'admin'){

            header("Location: admin/dashboard1.php");

        }else{

            header("Location: usuario/inicio.php");

        }

        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LECTIUM - Lectura Veloz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #6a11cb, #2575fc); height: 100vh; }
        .login-card { max-width: 420px; margin: 100px auto; }
    </style>
</head>
<body class="text-white">
    <div class="container">
        <div class="login-card card shadow">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">LECTIUM</h2>
                    <p class="text-muted">Lectura Evolucionada</p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="username" class="form-control" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3">Ingresar</button>
                </form>

                <div class="text-center mt-4">
                    <small>¿No tienes cuenta? Contacta al administrador</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>