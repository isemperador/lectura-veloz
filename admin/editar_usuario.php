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
CARGAR DATOS DEL USUARIO
===========================*/
$stmt = $pdo->prepare("SELECT id, username, nombre FROM usuarios WHERE id = ? AND username <> 'admin'");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: usuarios.php");
    exit();
}

/*==========================
ACTUALIZAR USUARIO
===========================*/
$mensaje = '';

if (isset($_POST['guardar'])) {
    $nuevo_username = trim($_POST['username']);
    $nuevo_nombre   = trim($_POST['nombre']);
    $nueva_password = trim($_POST['password']);

    if (empty($nuevo_username) || empty($nuevo_nombre)) {
        $mensaje = '<div class="alert alert-danger">Usuario y Nombre son obligatorios.</div>';
    } else {
        if (!empty($nueva_password)) {
            $stmt = $pdo->prepare("UPDATE usuarios SET username = ?, nombre = ?, password = ? WHERE id = ?");
            $stmt->execute([$nuevo_username, $nuevo_nombre, $nueva_password, $usuario_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET username = ?, nombre = ? WHERE id = ?");
            $stmt->execute([$nuevo_username, $nuevo_nombre, $usuario_id]);
        }
        
        $mensaje = '<div class="alert alert-success">✅ Usuario actualizado correctamente.</div>';
        
        $stmt = $pdo->prepare("SELECT id, username, nombre FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar - <?= htmlspecialchars($usuario['nombre']) ?></title>
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
        h1, h2, h3, h4, h5, h6, label, strong, p, small {
            color: white !important;
        }
        .form-label {
            color: white !important;
        }
        input.form-control {
            background: #334155;
            color: white !important;
            border: none;
        }
        input.form-control::placeholder {
            color: #cbd5e1;
        }
        .text-muted {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body>
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>✏ Editar Usuario</h1>
        <a href="detalle_usuario.php?id=<?= $usuario_id ?>" class="btn btn-warning">
            ← Volver al Detalle
        </a>
    </div>

    <div class="card card-panel">
        <div class="card-body">
            <?= $mensaje ?>

            <form method="POST">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Usuario (Username)</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?= htmlspecialchars($usuario['username']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" 
                               value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Nueva Contraseña <small>(Dejar en blanco si no quieres cambiarla)</small></label>
                    <input type="text" name="password" class="form-control" 
                           placeholder="Nueva contraseña (opcional)">
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" name="guardar" class="btn btn-success btn-lg">
                        💾 Guardar Cambios
                    </button>
                    <a href="detalle_usuario.php?id=<?= $usuario_id ?>" class="btn btn-secondary btn-lg">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
</body>
</html>