<?php
session_start();
include_once '../db/conexion.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}
$sala='';
$mesas = [];
include_once '../private/gestion_salas.php';


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar mesa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/gestion_mesas">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="navbar">
        <a href="../index.php">
            <img src="../img/icon.png" class="icon" alt="Icono">
        </a>
        <div class="user-info">
            <div class="dropdown">
                <i class="fas fa-caret-down" style="font-size: 16px; margin-right: 10px;"></i>
                <div class="dropdown-content">
                    <a href="../private/logout.php">Cerrar Sesión</a>
                </div>
            </div>
            <span><?php echo $_SESSION['nombre_usuario']; ?></span>
        </div>
    </div>
<?php if ($sala): ?>
    <div class="slider-container">
        <button id="prevArrow" class="arrow-btn">&lt;</button>
        <form method="POST" action="../private/proccess_mesas.php?sala">
            <div class="slider" id="mesaSlider">
                <?php 
                    $imagenesSillas = [
                        2 => "../img/mesas/mesa-2.png",
                        3 => "../img/mesas/mesa-3.png",
                        4 => "../img/mesas/mesa-4.png",
                        5 => "../img/mesas/mesa-5.png",
                        6 => "../img/mesas/mesa-6.png",
                        10 => "../img/mesas/mesa-10.png"
                    ];
                ?>
                <?php foreach ($mesas as $mesa): ?>
                    <div class="option <?php echo $mesa['estado_mesa'] == 'libre' ? 'libre' : 'ocupada'; ?>">
                        <input type="radio" name="mesa" value="<?php echo $mesa['id_mesa']; ?>" id="mesa_<?php echo $mesa['id_mesa']; ?>" <?php echo $mesa['estado_mesa'] == 'ocupada' ? 'disabled' : ''; ?>>
                        <label for="mesa_<?php echo $mesa['id_mesa']; ?>">
                            <h2>Mesa <?php echo $mesa['id_mesa']; ?></h2>
                            <p>Sillas: <?php echo $mesa['num_sillas_mesa']; ?></p>

                            <?php
                                $numSillas = $mesa['num_sillas_mesa'];
                                $imgSrc = isset($imagenesSillas[$numSillas]) ? $imagenesSillas[$numSillas] : ""; 
                            ?>
                            <?php if ($imgSrc): ?>
                                <img src="<?php echo $imgSrc; ?>" alt="Imagen de la mesa" class="mesa-img">
                            <?php endif; ?>
                        </label>
                        <?php if ($mesa['estado_mesa'] == 'ocupada'): ?>
                            <button type="submit" class="select-button" name="desocupar" value="<?php echo $mesa['id_mesa']; ?>">Desocupar</button>
                        <?php else: ?>
                            <button type="submit" name="ocupar" class="select-button" value="<?php echo $mesa['id_mesa']; ?>">Ocupar</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
        <button id="nextArrow" class="arrow-btn">&gt;</button>
    </div>
<?php endif; ?>
<script src="../js/slider.js"></script>
</body>
</html>