<?php
session_start();
include '../db/conexion.php';

// Verificamos que el usuario esté logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Obtenemos los datos del usuario desde la base de datos
$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT nombre_usuario, rol_usuario FROM tbl_usuario WHERE id_usuario = :usuario_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$nombre_usuario = $user_data['nombre_usuario'];
$rol_usuario = $user_data['rol_usuario'];

// Verificamos si el usuario tiene una jornada abierta
$sql_jornada = "SELECT * FROM tbl_jornada WHERE id_usuario = :usuario_id AND hora_fin IS NULL";
$stmt_jornada = $conn->prepare($sql_jornada);
$stmt_jornada->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_jornada->execute();
$jornada = $stmt_jornada->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fichar</title>
    <link rel="stylesheet" href="../css/fichar.css">
</head>
<body>
    <h1>Bienvenido, <?php echo $nombre_usuario; ?> (<?php echo $rol_usuario; ?>)</h1>
    
    <form method="POST">
        <?php if (!$jornada): ?>
            <button type="submit" name="fichar" id="ficharBtn">Fichar</button>
        <?php else: ?>
            <button type="submit" name="finalizar_jornada" id="ficharBtn">Finalizar Jornada</button>
        <?php endif; ?>
        <!-- Botón para salir -->
        <button type="submit" name="salir" id="salirBtn">Salir</button>
    </form>

    <div id="mensaje"></div>
    
    <?php
    // Acción de fichar
    if (isset($_POST['fichar'])) {
        // Registrar la hora de inicio de la jornada
        $hora_inicio = date('Y-m-d H:i:s');
        $sql_fichar = "INSERT INTO tbl_jornada (id_usuario, hora_inicio) VALUES (:usuario_id, :hora_inicio)";
        $stmt_fichar = $conn->prepare($sql_fichar);
        $stmt_fichar->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_fichar->bindParam(':hora_inicio', $hora_inicio, PDO::PARAM_STR);
        $stmt_fichar->execute();

        echo "<script>document.getElementById('mensaje').innerHTML = 'Hora de inicio registrada: $hora_inicio. ¡Suerte en tu jornada!';</script>";
    }

    // Acción de finalizar jornada
    if (isset($_POST['finalizar_jornada'])) {
        // Registrar la hora de fin de la jornada
        $hora_fin = date('Y-m-d H:i:s');
        $sql_finalizar = "UPDATE tbl_jornada SET hora_fin = :hora_fin WHERE id_usuario = :usuario_id AND hora_fin IS NULL";
        $stmt_finalizar = $conn->prepare($sql_finalizar);
        $stmt_finalizar->bindParam(':hora_fin', $hora_fin, PDO::PARAM_STR);
        $stmt_finalizar->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_finalizar->execute();

        // Obtener las horas trabajadas
        $time_start = strtotime($jornada['hora_inicio']);
        $time_end = strtotime($hora_fin);
        $time_worked = $time_end - $time_start; // tiempo trabajado en segundos
        $hours = floor($time_worked / 3600);
        $minutes = floor(($time_worked % 3600) / 60);
        $seconds = $time_worked % 60;

        echo "<script>document.getElementById('mensaje').innerHTML = 'Hora de fin registrada: $hora_fin. Has trabajado $hours horas, $minutes minutos y $seconds segundos. ¡Gracias por tu trabajo!';</script>";

        // Desloguear después de 5 segundos
        echo "<script>
                setTimeout(function() {
                    window.location.href = '../index.php';
                }, 5000); // 5 segundos
              </script>";
    }

    // Acción de salir
    if (isset($_POST['salir'])) {
        // Limpiar la sesión y redirigir al login
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit();
    }
    ?>
</body>
</html>
