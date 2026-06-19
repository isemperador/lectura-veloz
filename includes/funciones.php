<?php

function registrarIntento($pdo, $usuario, $ejercicio)
{
    $stmt = $pdo->prepare("
        INSERT INTO intentos
        (usuario_id, ejercicio, intentos)
        VALUES (?, ?, 1)
        ON DUPLICATE KEY UPDATE
        intentos = intentos + 1,
        ultima_fecha = NOW()
    ");

    $stmt->execute([
        $usuario,
        $ejercicio
    ]);
}

function registrarActividad($pdo, $usuario, $accion)
{
    $stmt = $pdo->prepare("
        INSERT INTO actividad
        (usuario_id, accion)
        VALUES (?, ?)
    ");

    $stmt->execute([
        $usuario,
        $accion
    ]);
}

function completarEjercicio($pdo, $usuario, $ejercicio)
{
    $stmt = $pdo->prepare("
        INSERT INTO progreso
        (usuario_id, ejercicio, completado, fecha_completado)
        VALUES (?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE
        completado = 1,
        fecha_completado = NOW()
    ");

    $stmt->execute([
        $usuario,
        $ejercicio
    ]);
}