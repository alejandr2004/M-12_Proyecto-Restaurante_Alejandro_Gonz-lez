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

// Consultamos las mesas de la sala seleccionada
$sql = "SELECT m.id_mesa, m.num_sillas_mesa, m.estado_mesa
        FROM tbl_mesa m
        JOIN tbl_sala s ON m.id_sala = s.id_sala
        WHERE s.nombre_sala = :sala_nombre";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':sala_nombre', $sala_nombre, PDO::PARAM_STR);
$stmt->execute();
$mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener el nombre completo de la sala
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
    <title>Mesas de la Sala <?php echo $sala_nombre_completo; ?></title>
    <link rel="stylesheet" href="../css/detalle_sala.css">
</head>
<body>
    <h1>Mesas de la Sala: <?php echo $sala_nombre_completo; ?></h1>
        <!-- Botones para acciones -->
    <!-- Botones para eliminar o modificar la sala -->
<div class="action-buttons">
    <a href="eliminar_sala.php?sala=<?php echo $sala_nombre; ?>" class="action-button delete">Eliminar Sala</a>
    <a href="modificar_sala.php?sala=<?php echo $sala_nombre; ?>" class="action-button modify">Modificar Sala</a>
    <a href="crud_salas.php" class="action-button back">Volver al CRUD de Salas</a>
</div>


    <div class="mesas-container">
        <?php foreach ($mesas as $mesa): ?>
            <div class="mesa-card <?php echo $mesa['estado_mesa'] == 'libre' ? 'libre' : 'ocupada'; ?>">
                <div class="mesa-info">
                    <p>Mesa #<?php echo $mesa['id_mesa']; ?></p>
                    <p>Sillas: <?php echo $mesa['num_sillas_mesa']; ?></p>
                    <p>Estado: <?php echo ucfirst($mesa['estado_mesa']); ?></p>
                </div>
                <form method="POST">
                    <input type="hidden" name="id_mesa" value="<?php echo $mesa['id_mesa']; ?>">
                    <?php if ($mesa['estado_mesa'] == 'libre'): ?>
                        <button type="submit" name="ocupar">Ocupar</button>
                    <?php else: ?>
                        <button type="submit" name="desocupar">Desocupar</button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>
    </div>



    <?php
    // Acción de ocupar la mesa
    if (isset($_POST['ocupar'])) {
        $id_mesa = $_POST['id_mesa'];
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

        // Redirigir para recargar la página con la mesa ocupada
        header("Location: detalle_sala.php?sala=" . $sala_nombre);
        exit();
    }

    // Acción de desocupar la mesa
    if (isset($_POST['desocupar'])) {
        $id_mesa = $_POST['id_mesa'];
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

        // Redirigir para recargar la página con la mesa desocupada
        header("Location: detalle_sala.php?sala=" . $sala_nombre);
        exit();
    }

    // Acción de crear una mesa
    if (isset($_POST['crear_mesa'])) {
        // Aquí va la lógica para crear una nueva mesa (por ejemplo, pedir el número de sillas)
        $sql_crear_mesa = "INSERT INTO tbl_mesa (id_sala, num_sillas_mesa, estado_mesa) 
                           VALUES ((SELECT id_sala FROM tbl_sala WHERE nombre_sala = :sala_nombre), 4, 'libre')";
        $stmt_crear_mesa = $conn->prepare($sql_crear_mesa);
        $stmt_crear_mesa->bindParam(':sala_nombre', $sala_nombre, PDO::PARAM_STR);
        $stmt_crear_mesa->execute();

        // Redirigir para recargar la página con la nueva mesa
        header("Location: detalle_sala.php?sala=" . $sala_nombre);
        exit();
    }

    // Acción de eliminar la sala
    if (isset($_POST['eliminar_sala'])) {
        // Eliminar las mesas asociadas a la sala
        $sql_eliminar_mesas = "DELETE FROM tbl_mesa WHERE id_sala = (SELECT id_sala FROM tbl_sala WHERE nombre_sala = :sala_nombre)";
        $stmt_eliminar_mesas = $conn->prepare($sql_eliminar_mesas);
        $stmt_eliminar_mesas->bindParam(':sala_nombre', $sala_nombre, PDO::PARAM_STR);
        $stmt_eliminar_mesas->execute();

        // Eliminar la sala
        $sql_eliminar_sala = "DELETE FROM tbl_sala WHERE nombre_sala = :sala_nombre";
        $stmt_eliminar_sala = $conn->prepare($sql_eliminar_sala);
        $stmt_eliminar_sala->bindParam(':sala_nombre', $sala_nombre, PDO::PARAM_STR);
        $stmt_eliminar_sala->execute();

        // Redirigir al CRUD de salas
        header("Location: CRUD_salas.php");
        exit();
    }
    ?>
</body>
</html>
