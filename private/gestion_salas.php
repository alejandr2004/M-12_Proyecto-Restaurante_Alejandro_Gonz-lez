<?php
include_once '../db/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sala'])) {
    $sala = $_POST['sala'];

    try {
        // Obtener el ID de la sala
        $query = "SELECT id_sala FROM tbl_sala WHERE nombre_sala = :nombre_sala";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre_sala', $sala, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $id_sala = $row['id_sala'];

            // Obtener las mesas asociadas a la sala
            $queryMesas = "SELECT * FROM tbl_mesa WHERE id_sala = :id_sala";
            $stmtMesas = $conn->prepare($queryMesas);
            $stmtMesas->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
            $stmtMesas->execute();
            $mesas = $stmtMesas->fetchAll(PDO::FETCH_ASSOC);

            // Procesar o mostrar las mesas según sea necesario
            foreach ($mesas as $mesa) {
                echo "Mesa ID: {$mesa['id_mesa']} - Estado: {$mesa['estado_mesa']}<br>";
            }
        } else {
            echo "No se ha encontrado ninguna sala con el nombre especificado.";
        }

        // Verificar la capacidad total de la sala
        $queryVerCapacidad = "SELECT capacidad_total FROM tbl_sala WHERE nombre_sala = :nombre_sala";
        $stmtVerCapacidad = $conn->prepare($queryVerCapacidad);
        $stmtVerCapacidad->bindParam(':nombre_sala', $sala, PDO::PARAM_STR);
        $stmtVerCapacidad->execute();
        $row = $stmtVerCapacidad->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            echo "<h2 style='text-align: center;'>Capacidad total de la sala: " . $row['capacidad_total'] . "</h2>";
        } else {
            echo "No se encontró la sala especificada.";
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
