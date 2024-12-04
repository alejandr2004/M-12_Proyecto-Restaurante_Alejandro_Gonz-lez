<?php
session_start();
include '../db/conexion.php';

// Verificamos que el usuario esté logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Obtener el número total de salas
$sql = "SELECT COUNT(*) AS total FROM tbl_sala";
$stmt = $conn->prepare($sql);
$stmt->execute();
$total_salas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Definir el número de tarjetas por página
$cards_per_page = 6;
$total_pages = ceil($total_salas / $cards_per_page);

// Obtener la página actual (por defecto es la primera)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $cards_per_page;

// Obtener las salas para la página actual
$sql = "SELECT * FROM tbl_sala LIMIT :offset, :cards_per_page";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':cards_per_page', $cards_per_page, PDO::PARAM_INT);
$stmt->execute();
$salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Salas</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* Estilos para las tarjetas */
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
        }

        .card {
            width: 30%;
            height: 250px;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 24px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .card h2 {
            z-index: 2;
        }

        .card .overlay {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        /* Estilo para el botón de detalles */
        .button-detalle {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button-detalle:hover {
            background-color: rgba(0, 0, 0, 0.9);
        }

        /* Paginador */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .pagination a:hover {
            background-color: #0056b3;
        }

        .pagination .active {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Gestión de Salas</h1>

    <div class="container">
        <?php
        // Mostrar las tarjetas de las salas
        foreach ($salas as $sala) {
            $_SESSION['id_sala'] = $sala['id_sala'];  // Obtener el nombre de la sala
            $imagen_sala = $sala['imagen_sala'];  // Obtener el nombre de la imagen
            $id_sala = $sala['nombre_sala'];  // Obtener el ID de la sala
            $sala_url = 'detalle_sala.php?sala=' . urlencode($id_sala);  // Usar el ID de la sala en la URL
            echo '<a href="' . $sala_url . '" class="card" style="background-image: url(\'../img/salas/' . $imagen_sala . '\');">';
            echo '<div class="overlay"></div>';
            echo '<h2>' . $sala['nombre_sala'] . '</h2>';
            echo '</a>';
        }
        ?>
    </div>

    <!-- Paginador -->
    <div class="pagination">
        <?php
        // Mostrar los enlaces de paginación
        for ($i = 1; $i <= $total_pages; $i++) {
            echo '<a href="CRUD_salas.php?page=' . $i . '" class="' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';
        }
        ?>
    </div>

</body>
</html>
