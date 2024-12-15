<?php
include('../db/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_rol = $_POST['nombre_rol'];

    try {
        // Verificamos si el rol ya existe para evitar duplicados
        $queryCheck = "SELECT COUNT(*) FROM tbl_rol WHERE rol = :rol";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->bindParam(':rol', $nombre_rol);
        $stmtCheck->execute();
        $exists = $stmtCheck->fetchColumn();

        if ($exists > 0) {
            echo "El rol ya existe.";
        } else {
            // Insertamos el nuevo rol
            $query = "INSERT INTO tbl_rol (rol) VALUES (:rol)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':rol', $nombre_rol);

            if ($stmt->execute()) {
                header("Location: crearUsuario.php");
                exit; // Aseguramos que el script termine después de redirigir
            } else {
                echo "Error al crear el rol.";
            }
        }
    } catch (PDOException $e) {
        echo "Error al crear rol: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Rol</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1>Crear Rol</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="nombre_rol" class="form-label">Nombre del Rol</label>
            <input type="text" class="form-control" id="nombre_rol" name="nombre_rol" required>
        </div>
        <button type="submit" class="btn btn-primary">Crear Rol</button>
    </form>
</div>
</body>
</html>
