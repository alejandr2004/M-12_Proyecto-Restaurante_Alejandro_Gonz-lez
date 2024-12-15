<?php
include('../db/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre_usuario = $_POST['nombre_usuario'];
    $nombre_real = $_POST['nombre_real'];
    $apellidos = $_POST['apellidos'];
    $contrasena_usuario = $_POST['contrasena_usuario'];
    $rol_usuario = $_POST['rol_usuario'];

    // Validación simple (puedes agregar más validaciones según sea necesario)
    if (empty($nombre_usuario) || empty($nombre_real) || empty($apellidos) || empty($contrasena_usuario) || empty($rol_usuario)) {
        echo "Por favor, complete todos los campos.";
        exit;
    }

    // Encriptar la contraseña
    $hashed_password = password_hash($contrasena_usuario, PASSWORD_BCRYPT);

    try {
        // Verificar si el nombre de usuario ya existe en la base de datos
        $queryCheck = "SELECT COUNT(*) FROM tbl_usuario WHERE nombre_usuario = :nombre_usuario";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->bindParam(':nombre_usuario', $nombre_usuario);
        $stmtCheck->execute();
        $exists = $stmtCheck->fetchColumn();

        if ($exists > 0) {
            echo "El nombre de usuario ya está registrado.";
        } else {
            // Insertar el nuevo usuario en la base de datos
            $query = "INSERT INTO tbl_usuario (nombre_usuario, nombre_real, apellidos, pwd_usuario, rol_usuario)
                      VALUES (:nombre_usuario, :nombre_real, :apellidos, :pwd_usuario, :rol_usuario)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt->bindParam(':nombre_real', $nombre_real);
            $stmt->bindParam(':apellidos', $apellidos);
            $stmt->bindParam(':pwd_usuario', $hashed_password);
            $stmt->bindParam(':rol_usuario', $rol_usuario);

            if ($stmt->execute()) {
                header("Location: ../public/crearUsuario.php"); // Redirigir a la lista de usuarios (puedes cambiar esto)
                exit;
            } else {
                echo "Error al crear el usuario.";
            }
        }
    } catch (PDOException $e) {
        echo "Error al procesar la creación del usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1>Crear Usuario</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
        </div>
        <div class="mb-3">
            <label for="nombre_real" class="form-label">Nombre Real</label>
            <input type="text" class="form-control" id="nombre_real" name="nombre_real" required>
        </div>
        <div class="mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
        </div>
        <div class="mb-3">
            <label for="contrasena_usuario" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="contrasena_usuario" name="contrasena_usuario" required>
        </div>
        <div class="mb-3">
            <label for="rol_usuario" class="form-label">Rol</label>
            <select class="form-select" id="rol_usuario" name="rol_usuario" required>
                <?php
                // Obtener los roles disponibles
                $queryRoles = "SELECT rol FROM tbl_rol";
                $stmtRoles = $conn->prepare($queryRoles);
                $stmtRoles->execute();
                $roles = $stmtRoles->fetchAll();
                
                foreach ($roles as $role) {
                    echo "<option value='{$role['rol']}'>{$role['rol']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
    </form>
</div>
</body>
</html>
