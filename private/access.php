<?php
session_start();
include '../db/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_empleado = trim($_POST['codigo_empleado']);
    $pwd = trim($_POST['pwd']);

    $_SESSION['codigo_empleado'] = $codigo_empleado;
    $_SESSION['pwd'] = $pwd;

    if (empty($codigo_empleado) || empty($pwd)) {
        $_SESSION['error'] = "Ambos campos son obligatorios.";
        header("Location: ../index.php");
        exit();
    }

    $sql = "SELECT * FROM tbl_camarero WHERE codigo_camarero = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $codigo_empleado);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $usuario = mysqli_fetch_assoc($result);

            if (password_verify($pwd, $usuario['password_camarero'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['usuario_id'] = $usuario['id_camarero'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_camarero'];

                unset($_SESSION['codigo_empleado']);
                unset($_SESSION['pwd']);
                unset($_SESSION['error']);

                header("Location: ../public/dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Los datos introducidos son incorrectos.";
                header("Location: ../index.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Los datos introducidos son incorrectos.";
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Error en la consulta: " . mysqli_error($conn);
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../public/login.php");
    exit();
}
?>
