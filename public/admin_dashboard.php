<?php
session_start();

// Verificar si el usuario está logueado y si tiene el rol de administrador
if (!isset($_SESSION['loggedin']) || $_SESSION['rol_usuario'] !== 'Administrador') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
        }
        .container {
            text-align: center;
            background: #fff;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .container h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .button {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        /* Estilo específico para el botón de Cerrar Sesión */
        .button-logout {
            background-color: #dc3545; /* Rojo */
        }
        .button-logout:hover {
            background-color: #c82333; /* Rojo oscuro */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido al Panel de Administración</h1>
        <a href="crud_usuarios.php" class="button">Gestionar Usuarios</a>
        <a href="CRUD_salas.php" class="button">Gestionar Salas</a>
        <a href="../private/logout.php" class="button button-logout">Cerrar Sesión</a> <!-- Botón de cierre de sesión -->
    </div>
</body>
</html>
