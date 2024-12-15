<?php
session_start();
include '../db/conexion.php';

// Verificamos que el usuario esté logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['rol_usuario'] != 'Camarero') {
    header("Location: ../index.php");
    exit();
}

// Obtener los datos del formulario
$id_mesa = $_POST['id_mesa'];
$sala_nombre = $_POST['sala'];
$action = $_POST['action'];

// Acción de ocupar la mesa
if ($action == 'ocupar') {
    $hora_ocupacion = date('Y-m-d H:i:s');
    $id_camarero = $_SESSION['usuario_id']; // Suponiendo que el camarero tiene la id de usuario en la sesión

    // Registrar la ocupación de la mesa
    $sql_ocupar = "INSERT INTO tbl_ocupacion (id_mesa, id_camarero, fecha_hora_ocupacion) 
                   VALUES (:id_mesa, :id_camarero, :hora_ocupacion)";
    $stmt_ocupar = $conn->prepare($sql_ocupar);
    $stmt_ocupar->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt_ocupar->bindParam(':id_camarero', $id_camarero, PDO::PARAM_INT);
    $stmt_ocupar->bindParam(':hora_ocupacion', $hora_ocupacion, PDO::PARAM_STR);
    $stmt_ocupar->execute();

    // Actualizar el estado de la mesa a 'ocupada'
    $sql_update = "UPDATE tbl_mesa SET estado_mesa = 'ocupada' WHERE id_mesa = :id_mesa";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt_update->execute();
}

// Acción de desocupar la mesa
if ($action == 'desocupar') {
    $hora_desocupacion = date('Y-m-d H:i:s');

    // Actualizar la hora de desocupación
    $sql_desocupar = "UPDATE tbl_ocupacion 
                      SET fecha_hora_desocupacion = :hora_desocupacion 
                      WHERE id_mesa = :id_mesa AND fecha_hora_desocupacion IS NULL";
    $stmt_desocupar = $conn->prepare($sql_desocupar);
    $stmt_desocupar->bindParam(':hora_desocupacion', $hora_desocupacion, PDO::PARAM_STR);
    $stmt_desocupar->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt_desocupar->execute();

    // Actualizar el estado de la mesa a 'libre'
    $sql_update = "UPDATE tbl_mesa SET estado_mesa = 'libre' WHERE id_mesa = :id_mesa";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmt_update->execute();
}

// Redirigir a la página de detalles de la sala
header("Location: ../public/gestion_mesas.php?sala=" . $sala_nombre);
exit();
?>
