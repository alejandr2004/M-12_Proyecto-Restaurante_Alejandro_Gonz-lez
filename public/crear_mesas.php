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

// Procesar la creación de la nueva mesa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['num_sillas'])) {
    $nuevo_num_sillas = (int)$_POST['num_sillas'];

    // Validar que el número de sillas no exceda el máximo permitido
    if ($nuevo_num_sillas > 10) {
        $mensaje = "El número máximo de sillas por mesa es 10.";
    } else {
        // Crear nueva mesa en la base de datos
        $queryCrearMesa = "INSERT INTO tbl_mesa (id_sala, num_sillas_mesa) VALUES (:id_sala, :num_sillas_mesa);";
        $stmtCrearMesa = $conn->prepare($queryCrearMesa);
        $stmtCrearMesa->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
        $stmtCrearMesa->bindParam(':num_sillas_mesa', $nuevo_num_sillas, PDO::PARAM_INT);
        $var=1;
        // Actualizar el stock de mesas en la sala
        $queryUpdateStockMesas = "UPDATE tbl_stock SET cantidad_mesas = cantidad_mesas - 1 WHERE id_sala = :id_sala;";
        $stmtUpdateStockMesas = $conn->prepare($queryUpdateStockMesas);
        $stmtUpdateStockMesas->bindParam(':id_sala', $var, PDO::PARAM_INT);

        // Actualizar el stock de sillas en la sala
        $queryUpdateStockSillas = "UPDATE tbl_stock SET cantidad_sillas = cantidad_sillas - :num_sillas WHERE id_sala = :id_sala;";
        $stmtUpdateStockSillas = $conn->prepare($queryUpdateStockSillas);
        $stmtUpdateStockSillas->bindValue(':num_sillas', $nuevo_num_sillas, PDO::PARAM_INT);
        $stmtUpdateStockSillas->bindValue(':id_sala', $var, PDO::PARAM_INT);

        // Ejecutar las actualizaciones
        if ($stmtCrearMesa->execute() && $stmtUpdateStockMesas->execute() && $stmtUpdateStockSillas->execute()) {
            $mensaje = "Mesa creada correctamente, stock de mesas y sillas actualizado.<br>";
        } else {
            $mensaje = "Error al crear la mesa o actualizar el stock de mesas o sillas.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Mesa</title>
    <link rel="stylesheet" href="../css/modificar-sala.css">
</head>
<body>
    <div class="container">
        <h1>Crear Nueva Mesa</h1>
        <?php if (!empty($mensaje)): ?>
            <p class="message"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>
        <form action="" method="POST" class="form-container">
            <label for="num_sillas">Número de Sillas para la Nueva Mesa (máximo 10):</label>
            <input type="number" name="num_sillas" id="num_sillas" min="1" max="10" required class="input-field">
            <button type="submit" class="submit-button">Crear Mesa</button>
        </form>
        <form action="detalle_sala.php" method="GET">
            <input type="hidden" name="sala" value="<?= htmlspecialchars($nombre_sala) ?>">
            <button type="submit" class="back-button">Volver</button>
        </form>
        <form action="automatizar_mesas.php" method="GET">
            <input type="hidden" name="sala" value="<?= htmlspecialchars($nombre_sala) ?>">
            <button type="submit" class="automate-button">Automatizar Mesas</button>
        </form>
    </div>
</body>
</html>

