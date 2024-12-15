<?php
session_start();
include_once '../db/conexion.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['rol_usuario'] != 'Camarero') {
    header("Location: ../index.php");
    exit();
}

$mesaId = isset($_POST['id_mesa']) ? $_POST['id_mesa'] : (isset($_GET['id_mesa']) ? $_GET['id_mesa'] : null);
$sala = isset($_POST['sala']) ? $_POST['sala'] : null;

if ($mesaId === null) {
    echo "Mesa no especificada.";
    exit();
}

$sql = "SELECT DISTINCT tipo_franja FROM tbl_franjas_horarias WHERE id_mesa = :id_mesa ORDER BY tipo_franja";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_mesa', $mesaId);
$stmt->execute();
$turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Reserva</title>
    <link rel="stylesheet" href="../css/reserva.css">
    <script>
        function actualizarFranjas() {
            const turnoSelect = document.getElementById('turno');
            const franjaSelect = document.getElementById('franja');
            const horaInicioInput = document.getElementById('hora_inicio');
            const horaFinInput = document.getElementById('hora_fin');

            // Limpiar opciones actuales del select de franjas
            franjaSelect.innerHTML = '<option value="">Selecciona una franja</option>';

            if (turnoSelect.value) {
                // Obtener franjas desde dataset
                const franjas = JSON.parse(turnoSelect.selectedOptions[0].dataset.franjas || '[]');

                // Añadir opciones al select de franjas
                franjas.forEach(franja => {
                    const option = document.createElement('option');
                    option.value = franja.id_franja;
                    option.textContent = `${franja.hora_inicio} - ${franja.hora_fin}`;
                    option.dataset.horaInicio = franja.hora_inicio;
                    option.dataset.horaFin = franja.hora_fin;
                    franjaSelect.appendChild(option);
                });
            }

            // Actualizar campos de hora de inicio y fin
            franjaSelect.addEventListener('change', function () {
                const selectedOption = franjaSelect.selectedOptions[0];
                horaInicioInput.value = selectedOption ? selectedOption.dataset.horaInicio : '';
                horaFinInput.value = selectedOption ? selectedOption.dataset.horaFin : '';
            });
        }
    </script>
</head>
<body>
    <div class="navbar">
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></span>
        </div>
    </div>

    <div class="reservation-form">
        <h2>Realizar Reserva para la Mesa <?php echo htmlspecialchars($mesaId); ?></h2>
        <form method="POST" action="../private/procesar_reserva.php">
            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($mesaId); ?>">
            <input type="hidden" name="sala" value="<?php echo htmlspecialchars($sala); ?>">

            <label for="turno">Selecciona un turno:</label>
            <select name="turno" id="turno" onchange="actualizarFranjas()" required>
                <option value="">Selecciona un turno</option>
                <?php
                foreach ($turnos as $turno) {
                    // Obtener franjas de este turno
                    $sql = "SELECT id_franja, hora_inicio, hora_fin FROM tbl_franjas_horarias WHERE id_mesa = :id_mesa AND tipo_franja = :tipo_franja ORDER BY hora_inicio";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id_mesa', $mesaId);
                    $stmt->bindParam(':tipo_franja', $turno['tipo_franja']);
                    $stmt->execute();
                    $franjas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Serializar franjas en JSON para el dataset
                    $franjasJson = htmlspecialchars(json_encode($franjas));
                    echo "<option value='{$turno['tipo_franja']}' data-franjas='$franjasJson'>" . ucfirst($turno['tipo_franja']) . "</option>";
                }
                ?>
            </select><br><br>

            <label for="franja">Selecciona una franja horaria:</label>
            <select name="franja" id="franja" required>
                <option value="">Selecciona una franja</option>
            </select><br><br>

            <label for="hora_inicio">Hora de inicio:</label>
            <input type="text" name="hora_inicio" id="hora_inicio" readonly><br><br>

            <label for="hora_fin">Hora de fin:</label>
            <input type="text" name="hora_fin" id="hora_fin" readonly><br><br>

            <label for="fecha_inicio">Fecha de inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" required><br><br>

            <label for="fecha_fin">Fecha de fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" required><br><br>

            <button type="submit">Reservar</button>
        </form>
    </div>
</body>
</html>















