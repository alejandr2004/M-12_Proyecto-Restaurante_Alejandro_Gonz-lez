<?php
// Incluir el archivo de conexión
include('../db/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $id_usuario = $_POST['id_usuario'] ?? null;
    $nombre_usuario = $_POST['nombre_usuario'] ?? null;
    $nombre_real = $_POST['nombre_real'] ?? null;
    $apellidos = $_POST['apellidos'] ?? null;
    $rol_usuario = $_POST['rol_usuario'] ?? null;

    // Validar los datos recibidos
    if (!$id_usuario || !$nombre_usuario || !$nombre_real || !$apellidos || !$rol_usuario) {
        echo "Error: Todos los campos son obligatorios.";
        exit;
    }

    try {
        // Actualizar la información del usuario en la base de datos
        $query = "UPDATE tbl_usuario 
                  SET nombre_usuario = :nombre_usuario, 
                      nombre_real = :nombre_real, 
                      apellidos = :apellidos, 
                      rol_usuario = :rol_usuario 
                  WHERE id_usuario = :id_usuario";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':nombre_usuario', $nombre_usuario, PDO::PARAM_STR);
        $stmt->bindParam(':nombre_real', $nombre_real, PDO::PARAM_STR);
        $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
        $stmt->bindParam(':rol_usuario', $rol_usuario, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Usuario actualizado exitosamente.";
            header("Location: ../public/CRUD_usuarios.php"); // Redirigir a la página de lista de usuarios
            exit;
        } else {
            echo "Error al actualizar el usuario.";
        }
    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
} else {
    echo "Método no permitido.";
}
?>
