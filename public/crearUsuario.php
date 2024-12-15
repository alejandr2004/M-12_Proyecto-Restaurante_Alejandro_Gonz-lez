<?php
// Incluir el archivo de conexión
include('../db/conexion.php');

try {
    // Consultar todos los roles disponibles
    $query = "SELECT * FROM tbl_rol";
    $stmt = $conn->query($query);
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al consultar roles: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <!-- Enlace al archivo CSS -->
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Incluir Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">Crear Usuario</h1>
        
        <!-- Formulario para crear un nuevo usuario -->
        <form action="../private/procesarCrearUsuario.php" method="POST">
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
                <label for="rol_usuario" class="form-label">Rol de Usuario</label>
                <div class="d-flex">
                    <select class="form-select" id="rol_usuario" name="rol_usuario" required>
                        <option value="" selected disabled>Seleccione un rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= htmlspecialchars($rol['rol']) ?>">
                                <?= htmlspecialchars($rol['rol']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="crearRol.php" class="btn btn-secondary ms-2">Crear Rol</a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </form>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
