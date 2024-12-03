<?php
include_once '../db/conexion.php';
include_once '../private/header.php';
session_start();
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ocupar'])){ 
    $mesa_id = $_POST['ocupar'];
    $camarero_id = $_SESSION['usuario_id'];
    try {
        mysqli_autocommit($conn, false);
        mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
        
        $queryReserva = "INSERT INTO tbl_ocupacion (id_mesa, id_camarero, fecha_hora_ocupacion) VALUES (?,?,NOW())";
        $stmtReserva = mysqli_prepare($conn, $queryReserva);
        mysqli_stmt_bind_param($stmtReserva, "ii", $mesa_id, $camarero_id);

        if (mysqli_stmt_execute($stmtReserva)) {
            $updateQuery = "UPDATE tbl_mesa SET estado_mesa = 'ocupada' WHERE id_mesa = ?";
            $stmtUpdate = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmtUpdate, "i", $mesa_id);
            mysqli_stmt_execute($stmtUpdate);

            $success = "Mesa ocupada con éxito.";
        } else {
            $error = "Hubo un error al hacer la reserva.";
        }

        mysqli_commit($conn);
        mysqli_stmt_close($stmtReserva);
        mysqli_stmt_close($stmtUpdate);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }

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
    
    // Iniciar la transacción
    try {
        mysqli_autocommit($conn, false);
        mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);

        // Actualizar la ocupación más reciente de la mesa para establecer la hora de desocupación
        $updateOcupacion = "UPDATE tbl_ocupacion 
                            SET fecha_hora_desocupacion = NOW() 
                            WHERE id_mesa = ? AND id_camarero = ? AND fecha_hora_desocupacion IS NULL";
        $stmtOcupacion = mysqli_prepare($conn, $updateOcupacion);
        mysqli_stmt_bind_param($stmtOcupacion, "ii", $mesa_id, $camarero_id);

        if (mysqli_stmt_execute($stmtOcupacion)) {
            // Cambiar el estado de la mesa a 'libre'
            $updateMesa = "UPDATE tbl_mesa SET estado_mesa = 'libre' WHERE id_mesa = ?";
            $stmtUpdateMesa = mysqli_prepare($conn, $updateMesa);
            mysqli_stmt_bind_param($stmtUpdateMesa, "i", $mesa_id);

            if (mysqli_stmt_execute($stmtUpdateMesa)) {
                $success = "Mesa desocupada con éxito.";
                mysqli_commit($conn);
            } else {
                $error = "Hubo un error al actualizar el estado de la mesa.";
                mysqli_rollback($conn);
            }

            mysqli_stmt_close($stmtUpdateMesa);
        } else {
            $error = "Hubo un error al registrar la desocupación en la ocupación.";
            mysqli_rollback($conn);
        }

        mysqli_stmt_close($stmtOcupacion);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }

    // Mostrar el mensaje adecuado
    if (!$error) {
        echo "<script>showSweetAlert('success', 'Éxito', '$success');</script>";
    } else {
        echo "<script>showSweetAlert('error', 'Error', '$error');</script>";
    }
    exit();
}

?>