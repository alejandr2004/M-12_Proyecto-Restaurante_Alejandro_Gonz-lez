<?php
session_start();
include_once "../db/conexion.php";

if ($_SESSION['rol_usuario'] != 'Administrador') {
    header("Location: ../index.php");
    exit();
}

// Verificar si se proporcionó una sala
if (isset($_GET['sala'])) {
    $nombre_sala = $_GET['sala'];
    echo "Nombre de la sala: " . htmlspecialchars($nombre_sala) . "<br>";

    // Obtener el ID de la sala
    $querySala = "SELECT id_sala FROM tbl_sala WHERE nombre_sala = :nombre_sala;";
    $stmtSala = $conn->prepare($querySala);
    $stmtSala->bindParam(':nombre_sala', $nombre_sala, PDO::PARAM_STR);
    $stmtSala->execute();
    $sala = $stmtSala->fetch(PDO::FETCH_ASSOC);

    if ($sala) {
        $id_sala = $sala['id_sala'];

        // Obtener las mesas asociadas a la sala
        $queryMesas = "SELECT id_mesa, num_sillas_mesa FROM tbl_mesa WHERE id_sala = :id_sala;";
        $stmtMesas = $conn->prepare($queryMesas);
        $stmtMesas->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
        $stmtMesas->execute();
        $mesas = $stmtMesas->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "No se encontró la sala especificada.<br>";
        $mesas = [];
    }
} else {
    echo "No se especificó una sala.<br>";
    $mesas = [];
}

// Procesar la actualización del número de sillas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mesa']) && isset($_POST['num_sillas'])) {
    $id_mesa = $_POST['mesa'];
    $num_sillas = $_POST['num_sillas'];

    // Obtener el número actual de sillas de la mesa
    $queryGetCurrentSillas = "SELECT num_sillas_mesa FROM tbl_mesa WHERE id_mesa = :id_mesa;";
    $stmtGetCurrentSillas = $conn->prepare($queryGetCurrentSillas);
    $stmtGetCurrentSillas->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
    $stmtGetCurrentSillas->execute();
    $currentSillas = $stmtGetCurrentSillas->fetch(PDO::FETCH_ASSOC);

    if ($currentSillas) {
        $currentNumSillas = $currentSillas['num_sillas_mesa'];

        // Calcular la diferencia de sillas
        $diferenciaSillas = $num_sillas - $currentNumSillas;

        // Actualizar el número de sillas en la mesa
        $queryUpdate = "UPDATE tbl_mesa SET num_sillas_mesa = :num_sillas WHERE id_mesa = :id_mesa;";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->bindParam(':num_sillas', $num_sillas, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);

        // Actualizar el stock de sillas en la sala según la diferencia
        if ($diferenciaSillas > 0) {
            // Si el número de sillas aumenta, reducir el stock
            $queryUpdateStock = "UPDATE tbl_stock SET cantidad_sillas = cantidad_sillas - :diferencia WHERE id_sala = :id_sala;";
        } else {
            // Si el número de sillas disminuye, aumentar el stock
            $queryUpdateStock = "UPDATE tbl_stock SET cantidad_sillas = cantidad_sillas + :diferencia WHERE id_sala = :id_sala;";
        }

        $stmtUpdateStock = $conn->prepare($queryUpdateStock);
        $stmtUpdateStock->bindParam(':diferencia', abs($diferenciaSillas), PDO::PARAM_INT);
        $stmtUpdateStock->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);

        // Ejecutar las actualizaciones
        if ($stmtUpdate->execute() && $stmtUpdateStock->execute()) {
            echo "<p>El número de sillas de la mesa {$id_mesa} ha sido actualizado a {$num_sillas} y el stock de sillas ha sido ajustado.</p>";
        } else {
            echo "<p>Error al actualizar el número de sillas o el stock.</p>";
        }

        // Recargar las mesas después de la actualización
        header("Location: " . $_SERVER['PHP_SELF'] . "?sala=" . urlencode($nombre_sala));
        exit;
    } else {
        echo "<p>No se encontró la mesa especificada.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas de la Sala</title>
    <link rel="stylesheet" href="../css/modificar-sala.css">
</head>
<body>
    <div class="container">
        <?php if (!empty($mesas)): ?>
            <form action="" method="POST" class="form-container">
                <label for="mesa">Seleccione una mesa:</label>
                <select name="mesa" id="mesa" required class="select-box">
                    <option value="" disabled selected>Seleccione una mesa</option>
                    <?php foreach ($mesas as $mesa): ?>
                        <option value="<?= htmlspecialchars($mesa['id_mesa']) ?>">
                            Mesa <?= htmlspecialchars($mesa['id_mesa']) ?> (<?= htmlspecialchars($mesa['num_sillas_mesa']) ?> sillas)
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="num_sillas">Nuevo número de sillas:</label>
                <input type="number" name="num_sillas" id="num_sillas" min="1" required class="input-field">
                <button type="submit" class="submit-button">Actualizar</button>
            </form>
        <?php else: ?>
            <p>No hay mesas disponibles para esta sala.</p>
        <?php endif; ?>

        <!-- Botón para volver -->
        <form action="detalle_sala.php" method="get">
            <input type="hidden" name="sala" value="<?= htmlspecialchars($nombre_sala) ?>">
            <button type="submit" class="back-button">Volver</button>
        </form>
    </div>
</body>
</html>



