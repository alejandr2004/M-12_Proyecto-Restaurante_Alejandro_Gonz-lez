<?php
session_start();
include '../db/conexion.php';

// Verificamos que el usuario esté logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Obtener el nombre de la sala desde la URL
$sala_nombre = isset($_GET['sala']) ? $_GET['sala'] : '';

// Verificamos si la sala existe
$sql_sala = "SELECT * FROM tbl_sala WHERE nombre_sala = :sala_nombre";
$stmt_sala = $conn->prepare($sql_sala);
$stmt_sala->bindParam(':sala_nombre', $sala_nombre, PDO::PARAM_STR);
$stmt_sala->execute();
$sala = $stmt_sala->fetch(PDO::FETCH_ASSOC);

// Si no existe la sala, redirigimos al CRUD de salas
if (!$sala) {
    header("Location: crud_salas.php");
    exit();
}

// Si el usuario ha confirmado la eliminación
if (isset($_POST['confirmar_eliminacion'])) {
    // Primero eliminamos las mesas asociadas a la sala
    $sql_eliminar_mesas = "DELETE FROM tbl_mesa WHERE id_sala = (SELECT id_sala FROM tbl_sala WHERE nombre_sala = :sala_nombre)";
    $stmt_eliminar_mesas = $conn->prepare($sql_eliminar_mesas);
    $stmt_eliminar_mesas->bindParam(':sala_nombre', $sala_nombre, PDO::PARAM_STR);
    $stmt_eliminar_mesas->execute();

    // Luego eliminamos la sala
    $sql_eliminar_sala = "DELETE FROM tbl_sala WHERE nombre_sala = :sala_nombre";
    $stmt_eliminar_sala = $conn->prepare($sql_eliminar_sala);
    $stmt_eliminar_sala->bindParam(':sala_nombre', $sala_nombre, PDO::PARAM_STR);
    $stmt_eliminar_sala->execute();

    // Redirigimos al CRUD de salas
    header("Location: crud_salas.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Sala</title>
    <link rel="stylesheet" href="../css/detalle_sala.css">
</head>
<body>
    <h1>Eliminar Sala: <?php echo $sala['nombre_sala']; ?></h1>

    <div class="confirmation">
        <p>¿Estás seguro de que deseas eliminar la sala <?php echo $sala['nombre_sala']; ?> y todas las mesas asociadas?</p>
        <form method="POST">
            <button type="submit" name="confirmar_eliminacion">Sí, eliminar</button>
            <a href="crud_salas.php" class="cancel-button">Cancelar</a>
        </form>
    </div>

</body>
</html>
