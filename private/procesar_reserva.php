<?php
session_start();
include_once '../db/conexion.php';

// Verificar que el usuario esté logueado y sea un camarero
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['rol_usuario'] != 'Camarero') {
    header("Location: ../index.php");
    exit();
}

$mesaId = isset($_POST['id_mesa']) ? $_POST['id_mesa'] : null;
$fechaInicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
$franjaId = isset($_POST['franja']) ? $_POST['franja'] : null;
$idCamarero = $_SESSION['usuario_id']; // Obtener el ID del camarero desde la sesión

// Verificar que los datos necesarios estén presentes
if (!$mesaId || !$fechaInicio || !$franjaId) {
    echo "Datos incompletos para realizar la reserva.";
    exit();
}

try {
    // Obtener la información de la franja horaria
    $sqlFranja = "SELECT hora_inicio, hora_fin FROM tbl_franjas WHERE id_franja = :id_franja";
    $stmtFranja = $conn->prepare($sqlFranja);
    $stmtFranja->bindParam(':id_franja', $franjaId);
    $stmtFranja->execute();
    $franja = $stmtFranja->fetch(PDO::FETCH_ASSOC);

    if (!$franja) {
        echo "Franja horaria no encontrada.";
        exit();
    }

    $horaInicio = $franja['hora_inicio'];
    $horaFin = $franja['hora_fin'];

    // Concatenar fecha con hora para generar fecha_hora_ocupacion y fecha_hora_desocupacion
    $fechaHoraOcupacion = $fechaInicio . ' ' . $horaInicio;
    $fechaHoraDesocupacion = $fechaInicio . ' ' . $horaFin;

    // Iniciar la transacción
    $conn->beginTransaction();

    // Verificar si la mesa ya está ocupada en ese horario
    $sqlOcupacion = "SELECT * FROM tbl_ocupacion 
                     WHERE id_mesa = :id_mesa 
                     AND ((:fecha_hora_ocupacion < fecha_hora_desocupacion AND :fecha_hora_desocupacion > fecha_hora_ocupacion))";
    $stmtOcupacion = $conn->prepare($sqlOcupacion);
    $stmtOcupacion->bindParam(':id_mesa', $mesaId);
    $stmtOcupacion->bindParam(':fecha_hora_ocupacion', $fechaHoraOcupacion);
    $stmtOcupacion->bindParam(':fecha_hora_desocupacion', $fechaHoraDesocupacion);
    $stmtOcupacion->execute();

    if ($stmtOcupacion->rowCount() > 0) {
        echo "La mesa ya está ocupada en esta franja horaria.";
        exit();
    }

    // Insertar la nueva ocupación
    $sqlInsert = "INSERT INTO tbl_ocupacion (id_mesa, id_camarero, fecha_hora_ocupacion, fecha_hora_desocupacion) 
                  VALUES (:id_mesa, :id_camarero, :fecha_hora_ocupacion, :fecha_hora_desocupacion)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bindParam(':id_mesa', $mesaId);
    $stmtInsert->bindParam(':id_camarero', $idCamarero);
    $stmtInsert->bindParam(':fecha_hora_ocupacion', $fechaHoraOcupacion);
    $stmtInsert->bindParam(':fecha_hora_desocupacion', $fechaHoraDesocupacion);
    $stmtInsert->execute();

    // Confirmar la transacción
    $conn->commit();

    // Redirigir a una página de éxito o al historial de reservas
    header("Location: ../public/dashboard.php");
    exit();

} catch (Exception $e) {
    // Si ocurre un error, revertir la transacción
    $conn->rollBack();
    echo "Error al procesar la reserva: " . $e->getMessage();
    exit();
}
?>



