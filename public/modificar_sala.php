<?php
session_start();
include '../db/conexion.php';

// Verificamos que el usuario esté logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Obtener el id de la sala desde la URL
$id_sala = isset($_GET['sala']) ? $_GET['sala'] : 0;

// Verificar que el id_sala es válido
if ($id_sala <= 0) {
    echo "ID de sala inválido.";
    exit();
}

// Obtener la cantidad actual de mesas en la sala seleccionada
$sql_mesas = "SELECT COUNT(*) AS cantidad_mesas FROM tbl_mesa WHERE id_sala = :id_sala";
$stmt_mesas = $conn->prepare($sql_mesas);
$stmt_mesas->bindParam(':id_sala', $_SESSION['id_sala'], PDO::PARAM_INT);
$stmt_mesas->execute();
$mesas_actuales_data = $stmt_mesas->fetch(PDO::FETCH_ASSOC);

// Verificar si la consulta devolvió resultados
if ($mesas_actuales_data === false) {
    echo $_SESSION['id_sala'];
    echo "No se pudo obtener la cantidad de mesas.";
    exit();
}

$mesas_actuales = $mesas_actuales_data['cantidad_mesas'];

// Consultar el nombre de la sala
$sql_sala = "SELECT nombre_sala FROM tbl_sala WHERE id_sala = :id_sala";
$stmt_sala = $conn->prepare($sql_sala);
$stmt_sala->bindParam(':id_sala', $_SESSION['id_sala'], PDO::PARAM_INT);
$stmt_sala->execute();
$sala_data = $stmt_sala->fetch(PDO::FETCH_ASSOC);

// Verificar si la consulta devolvió resultados
if ($sala_data === false) {
    echo "No se pudo obtener la información de la sala.";
    exit();
}

$sala_nombre = $sala_data['nombre_sala'];

// Si el formulario ha sido enviado, actualizar el número de mesas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cantidad_mesas_nueva = $_POST['cantidad_mesas'];

    // Resumen de la modificación
    $mensaje_resumen = "MESAS " . $mesas_actuales . " => " . $cantidad_mesas_nueva;
    
    // Actualizar la cantidad de mesas en la base de datos (si es necesario)
    $sql_actualizar_mesas = "UPDATE tbl_sala SET capacidad_total = :cantidad_mesas WHERE id_sala = :id_sala";
    $stmt_actualizar = $conn->prepare($sql_actualizar_mesas);
    $stmt_actualizar->bindParam(':cantidad_mesas', $cantidad_mesas_nueva, PDO::PARAM_INT);
    $stmt_actualizar->bindParam(':id_sala', $_SESSION['id_sala'], PDO::PARAM_INT);
    $stmt_actualizar->execute();

    // Insertar un nuevo registro en la tabla de stock
    $sql_insert_stock = "INSERT INTO tbl_stock (id_sala, cantidad_mesas) VALUES (:id_sala, :cantidad_mesas)";
    $stmt_insert_stock = $conn->prepare($sql_insert_stock);
    $stmt_insert_stock->bindParam(':id_sala', $_SESSION['id_sala'], PDO::PARAM_INT);
    $stmt_insert_stock->bindParam(':cantidad_mesas', $cantidad_mesas_nueva, PDO::PARAM_INT);
    $stmt_insert_stock->execute();

    // Mostrar el resumen de la modificación
    echo "<p>Resumen de la modificación: " . $mensaje_resumen . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Sala - <?php echo $sala_nombre; ?></title>
    <link rel="stylesheet" href="../css/modificar_sala.css">
</head>
<body>
    <h1>Modificar Sala: <?php echo $sala_nombre; ?></h1>

    <form method="POST">
        <label for="cantidad_mesas">Cantidad de Mesas:</label>
        <input type="number" name="cantidad_mesas" value="<?php echo $mesas_actuales; ?>" required>

        <input type="submit" value="Actualizar">
    </form>

    <a href="crud_salas.php">Volver al CRUD de Salas</a>
</body>
</html>
