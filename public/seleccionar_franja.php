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

$id_mesa = isset($_GET['id_mesa']) ? $_GET['id_mesa'] : '';
$sala_nombre = isset($_GET['sala']) ? $_GET['sala'] : '';

// Consultamos las franjas horarias disponibles para la mesa seleccionada
$sql_franjas = "SELECT * FROM tbl_franjas_horarias WHERE id_mesa = :id_mesa";
$stmt_franjas = $conn->prepare($sql_franjas);
$stmt_franjas->bindParam(':id_mesa', $id_mesa, PDO::PARAM_INT);
$stmt_franjas->execute();
$franjas = $stmt_franjas->fetchAll(PDO::FETCH_ASSOC);

// Consultamos el nombre de la sala
$sql_sala = "SELECT nombre_sala FROM tbl_sala WHERE nombre_sala = :sala_nombre";
$stmt_sala = $conn->prepare($sql_sala);
$stmt_sala->bindParam(':sala_nombre', $sala_nombre, PDO::PARAM_STR);
$stmt_sala->execute();
$sala_data = $stmt_sala->fetch(PDO::FETCH_ASSOC);
$sala_nombre_completo = $sala_data['nombre_sala'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Franja Horaria - Sala <?php echo $sala_nombre_completo; ?></title>
    <link rel="stylesheet" href="../css/detalle_sala.css">
</head>
<body>
    <h1>Seleccionar Franja Horaria - Mesa #<?php echo $id_mesa; ?> (Sala: <?php echo $sala_nombre_completo; ?>)</h1>
    <form action="../private/process_gestion_mesas.php" method="POST">
        <input type="hidden" name="id_mesa" value="<?php echo $id_mesa; ?>">
        <input type="hidden" name="sala" value="<?php echo $sala_nombre; ?>">

        <label for="franja_horaria">Seleccionar franja horaria:</label>
        <select name="franja_horaria" id="franja_horaria" required>
            <?php foreach ($franjas as $franja): ?>
                <option value="<?php echo $franja['id_franja']; ?>">
                    <?php echo $franja['hora_inicio'] . ' - ' . $franja['hora_fin']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="hora_reserva">Seleccionar hora de reserva:</label>
        <input type="datetime-local" name="hora_reserva" id="hora_reserva" required>

        <button type="submit" name="action" value="reservar">Confirmar Reserva</button>
    </form>

    <div class="action-buttons">
        <a href="detalle_sala.php?sala=<?php echo $sala_nombre; ?>" class="action-button back">Volver a las mesas</a>
    </div>
</body>
</html>
