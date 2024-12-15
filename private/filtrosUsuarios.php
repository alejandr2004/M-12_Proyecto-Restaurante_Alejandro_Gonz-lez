<?php
// Incluir el archivo de conexión
include('../db/conexion.php');

// Variables para los filtros
$nombre_usuario_filtro = isset($_GET['nombre_usuario']) ? $_GET['nombre_usuario'] : '';
$rol_filtro = isset($_GET['rol_usuario']) ? $_GET['rol_usuario'] : '';

// Consultar los usuarios con los filtros aplicados
try {
    $query = "SELECT * FROM tbl_usuario WHERE nombre_usuario LIKE :nombre_usuario AND rol_usuario LIKE :rol_usuario";
    $stmt = $conn->prepare($query);

    // Enlazar parámetros para evitar inyección SQL
    $stmt->bindValue(':nombre_usuario', '%' . $nombre_usuario_filtro . '%');
    $stmt->bindValue(':rol_usuario', '%' . $rol_filtro . '%');

    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al consultar usuarios: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtros de Usuarios</title>
    <!-- Enlace al archivo CSS -->
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Incluir Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">Filtros de Usuarios</h1>

        <!-- Botón para volver a la página de gestión de usuarios -->
        <a href="../public/CRUD_usuarios.php" class="btn btn-secondary mb-3">Volver</a>

        <!-- Tabla con los usuarios filtrados -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Nombre Real</th>
                    <th>Apellidos</th>
                    <th>Contraseña</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_real']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
                        <td><?= htmlspecialchars($usuario['pwd_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['rol_usuario']) ?></td>
                        <td>
                            <!-- Botones para editar y eliminar -->
                            <a href="editarUsuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="../private/eliminarUsuario.php?id_usuario=<?= htmlspecialchars($usuario['id_usuario']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
