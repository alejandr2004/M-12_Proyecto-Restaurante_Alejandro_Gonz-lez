<?php
include_once '../db/conexion.php';

if (isset($_GET['action'])) {
    if ($_GET['action'] === 'sala_concurrida') {
        $query = "SELECT s.nombre_sala, COUNT(DISTINCT o.id_ocupacion) AS total_ocupaciones
                    FROM tbl_ocupacion o
                    JOIN tbl_mesa m ON o.id_mesa = m.id_mesa
                    JOIN tbl_sala s ON m.id_sala = s.id_sala
                    GROUP BY s.id_sala
                    ORDER BY total_ocupaciones DESC
                    LIMIT 1";

        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            echo "<p class='info'>La sala más concurrida es: <strong>{$row['nombre_sala']}</strong> con {$row['total_ocupaciones']} ocupaciones.</p>";
        } else {
            echo "<p class='info'>No hay datos disponibles para la sala más concurrida.</p>";
        }
    }

    if ($_GET['action'] === 'mesa_concurrida') {
        $query = "SELECT m.id_mesa, COUNT(DISTINCT o.id_ocupacion) AS total_ocupaciones
                    FROM tbl_ocupacion o
                    JOIN tbl_mesa m ON o.id_mesa = m.id_mesa
                    GROUP BY m.id_mesa
                    ORDER BY total_ocupaciones DESC
                    LIMIT 1";

        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            echo "<p class='info'>La mesa más concurrida es: <strong>Mesa {$row['id_mesa']}</strong> con {$row['total_ocupaciones']} ocupaciones.</p>";
        } else {
            echo "<p class='info'>No hay datos disponibles para la mesa más concurrida.</p>";
        }
    }
}
?>
