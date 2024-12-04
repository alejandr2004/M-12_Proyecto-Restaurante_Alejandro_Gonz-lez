<?php
include_once '../db/conexion.php';

$noResultsMessage = ''; // Inicializar la variable

$filters = [];
$params = [];

// Construcción dinámica de filtros y parámetros
if (!empty($_GET['camarero'])) {
    $filters[] = 'c.nombre_camarero LIKE :camarero';
    $params[':camarero'] = '%' . $_GET['camarero'] . '%';
}

if (!empty($_GET['mesa'])) {
    $filters[] = 'm.id_mesa = :mesa';
    $params[':mesa'] = $_GET['mesa'];
}

if (!empty($_GET['fecha'])) {
    $filters[] = 'DATE(o.fecha_hora_ocupacion) = :fecha';
    $params[':fecha'] = $_GET['fecha'];
}

if (!empty($_GET['sala'])) {
    $filters[] = 's.id_sala = :sala';
    $params[':sala'] = $_GET['sala'];
}

// Construcción de la consulta con los filtros
if (!empty($filters)) {
    $query = "SELECT o.id_ocupacion, c.nombre_camarero, m.id_mesa, m.estado_mesa, s.nombre_sala, 
                     o.fecha_hora_ocupacion, o.fecha_hora_desocupacion
              FROM tbl_ocupacion o
              JOIN tbl_camarero c ON o.id_camarero = c.id_camarero
              JOIN tbl_mesa m ON o.id_mesa = m.id_mesa
              JOIN tbl_sala s ON m.id_sala = s.id_sala";

    $query .= ' WHERE ' . implode(' AND ', $filters);

    // Preparar la consulta
    $stmt = $conn->prepare($query);

    // Ejecutar la consulta con los parámetros
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si hay resultados
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($filters) && empty($result)) {
        $noResultsMessage = "No hay resultados para los filtros seleccionados.";
    }
} else {
    $result = null;
}
?>
