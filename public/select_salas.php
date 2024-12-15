<?php
session_start();
include '../db/conexion.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['rol_usuario'] != 'Camarero') {
    header("Location: ../index.php");
    exit();
}

// Obtener el tipo de sala desde la URL
$tipo_sala = isset($_GET['tipo']) ? $_GET['tipo'] : '';

// Verificar que el tipo de sala sea válido
$valid_types = ['terraza', 'comedor', 'privada'];
if (!in_array($tipo_sala, $valid_types)) {
    echo "Tipo de sala no válido.";
    exit();
}

// Obtener las salas del tipo seleccionado
$sql = "SELECT * FROM tbl_sala WHERE tipo_sala = :tipo_sala";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':tipo_sala', $tipo_sala, PDO::PARAM_STR);
$stmt->execute();
$salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Sala - <?php echo ucfirst($tipo_sala); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
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
            <!-- Botón Volver al Dashboard -->
            <a href="dashboard.php" class="back-button">Volver al Dashboard</a>

        </div>
    </div>

    <h1>Seleccionar Sala - <?php echo ucfirst($tipo_sala); ?></h1>

    <div class="container">
        <?php
        if (empty($salas)) {
            echo "<p>No hay salas disponibles de este tipo.</p>";
        } else {
            foreach ($salas as $sala) {
                $imagen_sala = $sala['imagen_sala'];
                echo '<div class="card" style="background-image: url(\'../img/salas/' . $imagen_sala . '\');">';
                echo '<h2>' . $sala['nombre_sala'] . '</h2>';
                echo '<form action="gestion_mesas.php" method="post">';
                echo '<button type="submit" name="sala" value="' . $sala['nombre_sala'] . '" class="select-button">Seleccionar</button>';
                echo '</form>';
                echo '</div>';
            }
        }
        ?>
    </div>

</body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        height: 100vh;
    }

    .navbar {
        background-color: #a36f53;
        color: #fff;
        padding: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .icon {
        height: 60px;
        width: 40px;
    }

    .navbar .user-info {
        display: flex;
        align-items: center;
        margin-right: 20px;
    }

    .navbar .user-info span {
        margin-left: 40px;
        margin-right: 20px;
    }

    .dropdown i {
        margin-right: 25px;
    }

    .dropdown {
        position: relative;
        display: inline-block;
        margin-right: 20px;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin: 20px auto;
        width: 80%;
    }

    .card {
        flex: 1 0 22%; /* Modificado para mostrar 4 por fila */
        margin: 10px;
        padding: 20px;
        text-align: center;
        background-size: cover;
        background-position: center;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid #d4a373;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .card h2 {
        margin: 0;
        padding: 10px;
    }

    .select-button {
        padding: 10px 20px;
        border: none;
        background-color: #d4a373;
        color: white;
        cursor: pointer;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .select-button:hover {
        background-color: #b97f52;
    }
    /* Estilo para el botón Volver al Dashboard */
.back-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #6c757d; /* Gris oscuro */
    color: white;
    text-align: center;
    border-radius: 5px;
    text-decoration: none;
    font-size: 16px;
    margin-top: 20px;
    transition: background-color 0.3s, transform 0.2s;
}

.back-button:hover {
    background-color: #5a6268; /* Gris más oscuro */
    transform: scale(1.05); /* Agrandar ligeramente el botón al pasar el cursor */
}

.back-button:active {
    background-color: #495057; /* Gris aún más oscuro para efecto al hacer clic */
    transform: scale(1);
}


    @media (max-width: 768px) {
        .container {
            flex-direction: column;
            align-items: center;
        }

        .card {
            width: 100%;
            margin: 10px 0;
        }

        .card h2 {
            font-size: 1.5em;
        }

        .navbar {
            flex-direction: column;
            align-items: flex-start;
        }

        .navbar .user-info {
            margin-top: 10px;
        }
    }
</style>
