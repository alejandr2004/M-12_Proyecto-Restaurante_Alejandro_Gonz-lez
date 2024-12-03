<?php
session_start();

if (isset($_SESSION['loggedin'])) {

    header("Location: ./public/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login El Manantial</title>
    <link rel="stylesheet" href="./css/login.css">
    <link rel="shortcut icon" href="./img/icon.png" type="image/x-icon">
</head>
<body>
    <div class="login-container">
        <img src="./img/icon.png" class="icon">
        <form class="login-form" action="./private/access.php" method="POST" id="loginForm">
            <label for="codigo_empleado">Código de empleado</label>
            <input type="text" id="codigo_empleado" name="codigo_empleado" placeholder="Introduce el código de empleado" onblur="validar_codigo()" value="<?php echo isset($_SESSION['codigo_empleado']) ? htmlspecialchars($_SESSION['codigo_empleado']) : ''; ?>">
            <span id="codigo_empleado_error" class="error-message"></span>
            
            <label for="pwd">Contraseña</label>
            <input type="password" id="pwd" name="pwd" placeholder="Introduce la contraseña" onblur="validar_password()">
            <span id="pwd_error" class="error-message"></span>

            <?php if (isset($_SESSION['error'])): ?>
            <span class="error-message" style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); ?></span>
            <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <button type="submit" class="login-button" id="submitBtn">Entrar</button>
        </form>
    </div>
    <script src="./js/validation_login.js"></script>
</body>
</html>