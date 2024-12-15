<?php
// Incluir el archivo de conexión
include('../db/conexion.php');

// Variables para los filtros
$user_filtro = isset($_GET['user']) ? $_GET['user'] : '';
$rol_filtro = isset($_GET['rol_usuario']) ? $_GET['rol_usuario'] : '';

// Consultar los usuarios sin filtro (por si no hay filtros)
try {
    $query = "SELECT * FROM tbl_usuario";
    $stmt = $conn->query($query);
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
    <title>Gestión de Usuarios</title>
    <!-- Enlace al archivo CSS -->
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Incluir Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">Gestión de Usuarios</h1>

        <!-- Formulario de filtros -->
        <form action="../private/filtrosUsuarios.php" method="get" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="user" class="form-control" placeholder="Buscar por usuario" value="<?= htmlspecialchars($user_filtro) ?>">
                </div>
                <div class="col-md-3">
                    <select name="rol_usuario" class="form-control">
                        <option value="">Selecciona rol</option>
                        <option value="Administrador" <?= ($rol_filtro === 'Administrador' ? 'selected' : '') ?>>Administrador</option>
                        <option value="Usuario" <?= ($rol_filtro === 'Usuario' ? 'selected' : '') ?>>Usuario</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>

        <!-- Botón para añadir un nuevo usuario -->
        <a href="crearUsuario.php" class="btn btn-primary mb-3">Añadir Nuevo Usuario</a>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Volver</a>

        <!-- Tabla con los usuarios -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Usuario</th>
                    <th>Nombre Usuario</th>
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
