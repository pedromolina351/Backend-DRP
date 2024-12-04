<?php
use Illuminate\Support\Facades\DB;
// Mostrar errores en pantalla
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Credenciales de conexión (usa las variables del entorno de Laravel)
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');

    // Crear la conexión PDO
    $dsn = "sqlsrv:Server=$host,$port;Database=$database;Encrypt=true;TrustServerCertificate=true;";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexión a la base de datos exitosa.";
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}
