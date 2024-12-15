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

// Obtener el nombre de la sala desde la URL
// $sala_nombre = isset($_POST['sala']) ? $_POST['sala'] : '';
if(isset($_POST["sala"])){
    $sala_nombre=$_POST["sala"];
}else if(isset($_GET["sala"])){
    $sala_nombre=$_GET["sala"];
}else{
    $sala_nombre="";
}

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

if ($sala_data) {
    $sala_nombre_completo = $sala_data['nombre_sala'];
} else {
    echo "Sala no encontrada.";
    exit();
}
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
    <div class="action-buttons">
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
                <form action="../private/process_gestion_mesas.php" method="POST">
                    <input type="hidden" name="id_mesa" value="<?php echo $mesa['id_mesa']; ?>">
                    <input type="hidden" name="sala" value="<?php echo $sala_nombre; ?>">
                    <?php if ($mesa['estado_mesa'] == 'libre'): ?>
                        <button type="submit" name="action" value="ocupar">Ocupar</button>
                        <button type="submit" name="action" value="reservar">Reservar</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="desocupar">Desocupar</button>
                        <button type="submit" name="action" value="reservar">Reservar</button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>



