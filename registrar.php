<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    $Usuario = mysqli_real_escape_string($connec, trim($_POST['username']));
    $Nombre = mysqli_real_escape_string($connec, trim($_POST['nom_usuario']));
    $password = mysqli_real_escape_string($connec, trim($_POST['password']));
    $TipoUsuario = mysqli_real_escape_string($connec, trim($_POST['rol']));

    $verificar = mysqli_query($connec, "SELECT * FROM usuarios WHERE Usuario = '$Usuario'");
    if (mysqli_num_rows($verificar) > 0) {
        echo "<script>alert('Este usuario ya está registrado'); window.location.href='registrar.php';</script>";
        exit();
    }

    $sql = "INSERT INTO usuarios (Usuario, password, Nombre, TipoUsuario) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connec, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $Usuario, $password, $Nombre, $TipoUsuario);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Usuario registrado exitosamente'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al registrar usuario'); window.location.href='registrar.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" type="text/css" href="../css/login_css/registrar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">

<!-- Fondo de video -->
<video autoplay muted loop class="video-background">
    <source src="FondoLogin/FondoLogin.mp4" type="video/mp4">
    Tu navegador no soporta video HTML5.
</video>

<div class="login-container">
    <h2>Crear Cuenta</h2>
    <p class="login-subtitle">JJLCARS</p>
    
    <form action="registrar.php" method="post">
        <div class="form-group">
            <label>Usuario:</label>
            <input type="text" name="username" placeholder="Usuario" required>
        </div>

        <div class="form-group">
            <label>Nombre Completo:</label>
            <input type="text" name="nom_usuario" placeholder="Nombre" required>
        </div>
        
        <div class="form-group">
            <label>Contraseña:</label>
            <div class="password-container">
                <input type="password" name="password" placeholder="Contraseña" required>
                <span class="toggle-password">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="" disabled selected>Selecciona un rol</option>
                <option value="gerente">Gerente</option>
                <option value="vendedor">Vendedor</option>
            </select>
        </div>

        <div class="button-group">
            <button type="submit" name="registrar" class="register-btn">
                <i class="fas fa-user-plus"></i> Registrar
            </button>
            <a href="login.php" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Volver al Login
            </a>
        </div>
    </form>
</div>

<script>
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const passwordInput = this.previousElementSibling;
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>

</body>
</html>
