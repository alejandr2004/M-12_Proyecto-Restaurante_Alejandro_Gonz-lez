<?php
include_once '../db/conexion.php';

$noResultsMessage = '';  // Asegúrate de inicializar la variable

$filters = [];
$params = [];
$types = '';

if (!empty($_GET['camarero'])) {
    $filters[] = 'c.nombre_camarero LIKE ?';
    $params[] = '%' . $_GET['camarero'] . '%';
    $types .= 's';
}

if (!empty($_GET['mesa'])) {
    $filters[] = 'm.id_mesa = ?';
    $params[] = $_GET['mesa'];
    $types .= 'i';
}

if (!empty($_GET['fecha'])) {
    $filters[] = 'DATE(o.fecha_hora_ocupacion) = ?';
    $params[] = $_GET['fecha'];
    $types .= 's';
}

if (!empty($_GET['sala'])) {
    $filters[] = 's.id_sala = ?';
    $params[] = $_GET['sala'];
    $types .= 'i';
}

if (!empty($filters)) {
    $query = "SELECT o.id_ocupacion, c.nombre_camarero, m.id_mesa, m.estado_mesa, s.nombre_sala, o.fecha_hora_ocupacion, o.fecha_hora_desocupacion
            FROM tbl_ocupacion o
            JOIN tbl_camarero c ON o.id_camarero = c.id_camarero
            JOIN tbl_mesa m ON o.id_mesa = m.id_mesa
            JOIN tbl_sala s ON m.id_sala = s.id_sala";

    $query .= ' WHERE ' . implode(' AND ', $filters);

    $stmt = mysqli_prepare($conn, $query);
    if ($types) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($filters) && mysqli_num_rows($result) === 0) {
        $noResultsMessage = "No hay resultados para los filtros seleccionados.";
    }
} else {
    $result = null;
}
?>