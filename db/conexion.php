<?php
$dbserver = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "elmanantial";

try {
    $conn = new PDO("mysql:host=$dbserver;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    // Configurar el modo de errores de PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    die();
}
