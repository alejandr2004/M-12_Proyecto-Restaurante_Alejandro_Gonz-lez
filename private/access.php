<?php
session_start();
include '../db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_usuario = trim($_POST['codigo_usuario']);
    $pwd = trim($_POST['pwd']);

    // Almacenamos los datos temporalmente en la sesión para validarlos después
    $_SESSION['codigo_usuario'] = $codigo_usuario;
    $_SESSION['pwd'] = $pwd;

    // Validar si los campos están vacíos
    if (empty($codigo_usuario) || empty($pwd)) {
        $_SESSION['error'] = "Ambos campos son obligatorios.";
        header("Location: ../index.php");
        exit();
    }

    try {
        // Consulta preparada para obtener los datos del usuario
        $sql = "SELECT * FROM tbl_usuario WHERE nombre_usuario = :codigo_usuario";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':codigo_usuario', $codigo_usuario, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        var_dump($result);
        echo $_SESSION['codigo_usuario'];
        echo $_SESSION['pwd'];

        if ($result) {
            // Verificar la contraseña utilizando password_verify
                // Inicio de sesión exitoso
                $_SESSION['loggedin'] = true;
                $_SESSION['usuario_id'] = $result['id_usuario'];
                $_SESSION['nombre_usuario'] = $result['nombre_usuario'];

                echo $_SESSION['usuario_id'];

                // Eliminar las variables temporales de sesión
                unset($_SESSION['codigo_usuario']);
                unset($_SESSION['pwd']);
                unset($_SESSION['error']);

                // Redirigir a la página correspondiente según el rol del usuario
                if ($result['rol_usuario'] == "Administrador") {
                    header("Location: ../public/CRUD_salas.php");
                } else if ($result['rol_usuario'] == "Camarero") {
                    header("Location: ../public/dashboard.php");
                } else {
                    header("Location: ../public/fichar.php");
                }

                exit();
        } else {
            // Usuario no encontrado
            $_SESSION['error'] = "Los datos introducidos son incorrectos.";
            header("Location: ../index.php");
            exit();
        }
    } catch (PDOException $e) {
        // Manejo de errores de conexión o consulta
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
        header("Location: ../index.php");
        exit();
    }
} else {
    // Si no se recibe un formulario POST, redirigir al login
    header("Location: ../public/login.php");
    exit();
}
?>

