<?php
session_start();
include_once "../db/conexion.php";

$mensaje = "";

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
    } else {
        echo "No se encontró la sala especificada.<br>";
        exit;
    }
} else {
    echo "No se especificó una sala.<br>";
    exit;
}

// Procesar la automatización de mesas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['num_sillas'], $_POST['num_mesas'])) {
    $nuevo_num_sillas = (int)$_POST['num_sillas'];
    $nuevo_num_mesas = (int)$_POST['num_mesas'];

    // Validaciones
    if ($nuevo_num_sillas > 10) {
        $mensaje = "El número máximo de sillas por mesa es 10.";
    } elseif ($nuevo_num_mesas < 1) {
        $mensaje = "Debe crear al menos una mesa.";
    } else {
        try {
            // Iniciar una transacción
            $conn->beginTransaction();

            for ($i = 0; $i < $nuevo_num_mesas; $i++) {
                // Crear nueva mesa en la base de datos
                $queryCrearMesa = "INSERT INTO tbl_mesa (id_sala, num_sillas_mesa) VALUES (:id_sala, :num_sillas_mesa);";
                $stmtCrearMesa = $conn->prepare($queryCrearMesa);
                $stmtCrearMesa->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
                $stmtCrearMesa->bindParam(':num_sillas_mesa', $nuevo_num_sillas, PDO::PARAM_INT);
                $stmtCrearMesa->execute();
            }

            // Actualizar el stock de mesas en la sala
            $queryUpdateStockMesas = "UPDATE tbl_stock SET cantidad_mesas = cantidad_mesas - :num_mesas WHERE id_sala = :id_sala;";
            $stmtUpdateStockMesas = $conn->prepare($queryUpdateStockMesas);
            $stmtUpdateStockMesas->bindParam(':num_mesas', $nuevo_num_mesas, PDO::PARAM_INT);
            $stmtUpdateStockMesas->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
            $stmtUpdateStockMesas->execute();

            // Actualizar el stock de sillas en la sala
            $queryUpdateStockSillas = "UPDATE tbl_stock SET cantidad_sillas = cantidad_sillas - :total_sillas WHERE id_sala = :id_sala;";
            $stmtUpdateStockSillas = $conn->prepare($queryUpdateStockSillas);
            $total_sillas = $nuevo_num_sillas * $nuevo_num_mesas;
            $stmtUpdateStockSillas->bindParam(':total_sillas', $total_sillas, PDO::PARAM_INT);
            $stmtUpdateStockSillas->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
            $stmtUpdateStockSillas->execute();

            // Confirmar la transacción
            $conn->commit();
            $mensaje = "Se crearon $nuevo_num_mesas mesas correctamente, stock de mesas y sillas actualizado.";
        } catch (Exception $e) {
            $conn->rollBack();
            $mensaje = "Error al automatizar la creación de mesas: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automatizar Mesas</title>
    <link rel="stylesheet" href="../css/modificar-sala.css">
</head>
<body>
    <div class="container">
        <h1>Automatizar Creación de Mesas</h1>
        <?php if (!empty($mensaje)): ?>
            <p class="message"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>
        <form action="" method="POST" class="form-container">
            <label for="num_mesas">Número de Mesas a Crear:</label>
            <input type="number" name="num_mesas" id="num_mesas" min="1" required class="input-field">
            <label for="num_sillas">Número de Sillas por Mesa (máximo 10):</label>
            <input type="number" name="num_sillas" id="num_sillas" min="1" max="10" required class="input-field">
            <button type="submit" class="submit-button">Automatizar</button>
        </form>
        <form action="detalle_sala.php" method="GET">
            <input type="hidden" name="sala" value="<?= htmlspecialchars($nombre_sala) ?>">
            <button type="submit" class="back-button">Volver</button>
        </form>
    </div>
</body>
</html>
