<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['rol_usuario'] != 'Camarero') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar sala</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
</head>
<body>
<div class="navbar">
    <a href="../index.php">
        <img src="../img/icon.png" class="icon" alt="Icono">
    </a>
    <a href="./historial.php" class="right-link">Historial</a>
    <div class="user-info">
        <div class="dropdown">
            <i class="fas fa-caret-down" style="font-size: 16px; margin-right: 10px;"></i>
            <div class="dropdown-content">
                <a href="../private/logout.php">Cerrar Sesión</a>
            </div>
        </div>
        <span><?php echo $_SESSION['nombre_usuario']; ?></span>
    </div>
    <div class="hamburger" id="hamburger-icon">
        &#9776;
    </div>
</div>
<div class="mobile-nav" id="mobile-nav">
    <a href="./historial.php">Historial</a>
    <a href="#"><?php echo $_SESSION['nombre_usuario']; ?></a>
    <a href="../private/logout.php">Cerrar Sesión</a>
</div>

<div class="options">
    <div class="option terraza">
        <h2>Terraza</h2>
        <div class="button-container">
            <a href="select_salas.php?tipo=terraza" class="select-button">Seleccionar</a>
        </div>
    </div>
    <div class="option comedor">
        <h2>Comedor</h2>
        <div class="button-container">
            <a href="select_salas.php?tipo=comedor" class="select-button">Seleccionar</a>
        </div>
    </div>
    <div class="option privadas">
        <h2>Sala privada</h2>
        <div class="button-container">
            <a href="select_salas.php?tipo=privada" class="select-button">Seleccionar</a>
        </div>
    </div>
</div>

<script src="../js/dashboard.js"></script>
<script src="../js/navbar.js"></script>
</body>
</html>
