<?php
// Incluir el archivo de conexión
include('../db/conexion.php');

// Obtener el ID del usuario desde la URL
$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    echo "ID de usuario no proporcionado.";
    exit;
}

try {
    // Consultar datos del usuario
    $queryUsuario = "SELECT * FROM tbl_usuario WHERE id_usuario = :id";
    $stmtUsuario = $conn->prepare($queryUsuario);
    $stmtUsuario->bindParam(':id', $id_usuario, PDO::PARAM_INT);
    $stmtUsuario->execute();
    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "Usuario no encontrado.";
        exit;
    }

    // Consultar roles disponibles
    $queryRoles = "SELECT * FROM tbl_rol";
    $stmtRoles = $conn->query($queryRoles);
    $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al consultar datos: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container my-5">
        <h1>Editar Usuario</h1>
        <form action="../private/procesarEditarUsuario.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
            <div class="mb-3">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="nombre_real" class="form-label">Nombre Real</label>
                <input type="text" class="form-control" id="nombre_real" name="nombre_real" value="<?= htmlspecialchars($usuario['nombre_real']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="rol_usuario" class="form-label">Rol</label>
                <select class="form-select" id="rol_usuario" name="rol_usuario" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= htmlspecialchars($rol['rol']) ?>" <?= $usuario['rol_usuario'] === $rol['rol'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rol['rol']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
