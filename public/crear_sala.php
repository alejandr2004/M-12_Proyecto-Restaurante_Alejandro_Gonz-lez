<?php
session_start();
include '../db/conexion.php';

// Verificamos que el usuario esté logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Procesar el formulario de creación de sala
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_sala'], $_POST['tipo_sala'], $_FILES['imagen_sala'])) {
    $nombre_sala = $_POST['nombre_sala'];
    $tipo_sala = $_POST['tipo_sala'];

    // Validar tipo de sala
    $tipos_validos = ['terraza', 'comedor', 'privada'];
    if (!in_array($tipo_sala, $tipos_validos)) {
        echo "Tipo de sala no válido.";
        exit();
    }

    // Subir imagen
    $imagen_sala = $_FILES['imagen_sala']['name'];
    $target_dir = "../img/salas/";
    $target_file = $target_dir . basename($imagen_sala);
    move_uploaded_file($_FILES['imagen_sala']['tmp_name'], $target_file);

    // Insertar la nueva sala en la base de datos
    $sql = "INSERT INTO tbl_sala (nombre_sala, tipo_sala, imagen_sala) VALUES (:nombre_sala, :tipo_sala, :imagen_sala)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre_sala', $nombre_sala, PDO::PARAM_STR);
    $stmt->bindParam(':tipo_sala', $tipo_sala, PDO::PARAM_STR);
    $stmt->bindParam(':imagen_sala', $imagen_sala, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "Sala creada exitosamente.";
        header("Location: CRUD_salas.php");
        exit();
    } else {
        echo "Error al crear la sala.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nueva Sala</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Crear Nueva Sala</h1>
    <form action="crear_sala.php" method="POST" enctype="multipart/form-data">
        <label for="nombre_sala">Nombre de la Sala:</label>
        <input type="text" name="nombre_sala" id="nombre_sala" required><br><br>

        <label for="tipo_sala">Tipo de Sala:</label>
        <select name="tipo_sala" id="tipo_sala" required>
            <option value="terraza">Terraza</option>
            <option value="comedor">Comedor</option>
            <option value="privada">Privada</option>
        </select><br><br>

        <label for="imagen_sala">Imagen de la Sala:</label>
        <input type="file" name="imagen_sala" id="imagen_sala" accept="image/*" required><br><br>

        <img id="imagen_previa" src="#" alt="Vista previa" style="max-width: 200px; max-height: 200px; display: none;"><br><br>

        <button type="submit">Crear Sala</button>
    </form>

    <script>
        // Función para mostrar una vista previa de la imagen seleccionada
        document.getElementById('imagen_sala').addEventListener('change', function(event) {
            const archivo = event.target.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const imagenPrevia = document.getElementById('imagen_previa');
                imagenPrevia.src = e.target.result;
                imagenPrevia.style.display = 'block';
            };

            if (archivo) {
                reader.readAsDataURL(archivo);
            }
        });
    </script>
</body>
</html>


