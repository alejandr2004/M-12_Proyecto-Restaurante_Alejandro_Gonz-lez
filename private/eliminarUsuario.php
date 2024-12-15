<?php
include('../db/conexion.php');

// Verificar si se recibió el ID del usuario a eliminar
if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    try {
        // Consultar si el usuario existe
        $queryCheck = "SELECT COUNT(*) FROM tbl_usuario WHERE id_usuario = :id_usuario";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->bindParam(':id_usuario', $id_usuario);
        $stmtCheck->execute();
        $exists = $stmtCheck->fetchColumn();

        if ($exists > 0) {
            // Eliminar usuario de la base de datos
            $query = "DELETE FROM tbl_usuario WHERE id_usuario = :id_usuario";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario);

            if ($stmt->execute()) {
                // Redirigir a la lista de usuarios
                header("Location: ../public/CRUD_usuarios.php"); // Redirigir a la página de lista de usuarios
                exit;
            } else {
                echo "Error al eliminar el usuario.";
            }
        } else {
            echo "El usuario no existe.";
        }
    } catch (PDOException $e) {
        echo "Error al procesar la eliminación: " . $e->getMessage();
    }
} else {
    echo "No se recibió el ID del usuario.";
}
?>
