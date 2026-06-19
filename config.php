<?php
$host = 'mysql.railway.internal';
$db   = 'railway';
$user = 'root';
$pass = 'lcpVbBNVqzqrBhgVNiSbuLjhuRygrecG';
$port = '3306'; // Suele ser 3306 o similar

try {
    // Añadimos el port a la cadena de conexión de PDO
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>