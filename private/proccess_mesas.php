<?php
include_once '../db/conexion.php';
include_once '../private/header.php';
session_start();

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ocupar'])) {
    $mesa_id = $_POST['ocupar'];
    $camarero_id = $_SESSION['usuario_id'];

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Insertar nueva ocupación
        $queryReserva = "INSERT INTO tbl_ocupacion (id_mesa, id_camarero, fecha_hora_ocupacion) VALUES (:id_mesa, :id_camarero, NOW())";
        $stmtReserva = $conn->prepare($queryReserva);
        $stmtReserva->bindParam(':id_mesa', $mesa_id, PDO::PARAM_INT);
        $stmtReserva->bindParam(':id_camarero', $camarero_id, PDO::PARAM_INT);

        if ($stmtReserva->execute()) {
            // Actualizar estado de la mesa a 'ocupada'
            $updateQuery = "UPDATE tbl_mesa SET estado_mesa = 'ocupada' WHERE id_mesa = :id_mesa";
            $stmtUpdate = $conn->prepare($updateQuery);
            $stmtUpdate->bindParam(':id_mesa', $mesa_id, PDO::PARAM_INT);
            $stmtUpdate->execute();

            $success = "Mesa ocupada con éxito.";
        } else {
            throw new Exception("Error al hacer la reserva.");
        }

        // Confirmar transacción
        $conn->commit();
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollBack();
        $error = $e->getMessage();
    }

    // Mostrar mensaje al usuario
    if (!$error) {
        echo "<script>showSweetAlert('success', 'Éxito', '$success');</script>";
    } else {
        echo "<script>showSweetAlert('error', 'Error', '$error');</script>";
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['desocupar'])) {
    $mesa_id = $_POST['desocupar'];
    $camarero_id = $_SESSION['usuario_id'];

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Actualizar ocupación para establecer la hora de desocupación
        $updateOcupacion = "UPDATE tbl_ocupacion 
                            SET fecha_hora_desocupacion = NOW() 
                            WHERE id_mesa = :id_mesa AND id_camarero = :id_camarero AND fecha_hora_desocupacion IS NULL";
        $stmtOcupacion = $conn->prepare($updateOcupacion);
        $stmtOcupacion->bindParam(':id_mesa', $mesa_id, PDO::PARAM_INT);
        $stmtOcupacion->bindParam(':id_camarero', $camarero_id, PDO::PARAM_INT);

        if ($stmtOcupacion->execute()) {
            // Cambiar el estado de la mesa a 'libre'
            $updateMesa = "UPDATE tbl_mesa SET estado_mesa = 'libre' WHERE id_mesa = :id_mesa";
            $stmtUpdateMesa = $conn->prepare($updateMesa);
            $stmtUpdateMesa->bindParam(':id_mesa', $mesa_id, PDO::PARAM_INT);
            $stmtUpdateMesa->execute();

            $success = "Mesa desocupada con éxito.";
        } else {
            throw new Exception("Error al registrar la desocupación en la ocupación.");
        }

        // Confirmar transacción
        $conn->commit();
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollBack();
        $error = $e->getMessage();
    }

    // Mostrar mensaje al usuario
    if (!$error) {
        echo "<script>showSweetAlert('success', 'Éxito', '$success');</script>";
    } else {
        echo "<script>showSweetAlert('error', 'Error', '$error');</script>";
    }
    exit();
}
?>
