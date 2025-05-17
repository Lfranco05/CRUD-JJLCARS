<?php
session_start();
include("conexion.php");

if (isset($_SESSION["usuarioingresando"]) && $_SESSION["usuarioingresando"] === true) {
    header("Location: inicio/principal.php");  // Modificada esta línea
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enviar'])) {
    $username = mysqli_real_escape_string($connec, trim($_POST['username']));
    $password = mysqli_real_escape_string($connec, trim($_POST['password']));
    
    // Buscar el usuario en la base de datos
    $sql = "SELECT id, username, nom_usuario, password, avatar FROM usuario WHERE username = ?";
    $stmt = mysqli_prepare($connec, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if ($fila = mysqli_fetch_assoc($resultado)) {
        // Verificar la contraseña hasheada
        if (password_verify($password, $fila['password'])) {
            // Iniciar sesión
            $_SESSION['usuarioingresando'] = true;
            $_SESSION['id'] = $fila['id'];
            $_SESSION['username'] = $fila['username'];
            $_SESSION['nom_usuario'] = $fila['nom_usuario'];
            $_SESSION['avatar'] = $fila['avatar'];
            
            // Manejar la opción "Recordarme"
            if (isset($_POST['recordar'])) {
                // Establecer cookies por 30 días
                setcookie('remember_user', $username, time() + (30 * 24 * 60 * 60), '/');
                setcookie('remember_pass', base64_encode($password), time() + (30 * 24 * 60 * 60), '/');
            } else {
                // Si no se marca "Recordarme", eliminar las cookies existentes
                setcookie('remember_user', '', time() - 3600, '/');
                setcookie('remember_pass', '', time() - 3600, '/');
            }
            
            // Redirigir al usuario
            header("Location: inicio/principal.php");
            exit();
        } else {
            echo "<script>alert('Usuario o contraseña incorrectos');</script>";
        }
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos');</script>";
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login de Usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Enlaza el CSS especializado para el login -->
    <link rel="stylesheet" type="text/css" href="login.css">
    <!-- SweetAlert2 (opcional si lo usas en esta página) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">

<div class="login-container">
    <h2>Iniciar Sesión</h2>
    <p class="login-subtitle">Sistema de Usuarios</p>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-group">
            <label for="user">Usuario:</label>
            <input type="text" id="user" name="username" placeholder="Ingresa tu usuario" required 
                value="<?php echo isset($_COOKIE['remember_user']) ? $_COOKIE['remember_user'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <div class="password-container">
                <input type="password" id="contrasena" name="password" placeholder="Ingresa tu contraseña" required
                    value="<?php echo isset($_COOKIE['remember_pass']) ? base64_decode($_COOKIE['remember_pass']) : ''; ?>">
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i id="toggleIcon" class="fa-solid fa-eye"></i>
                </span>
            </div>
        </div>

        <div class="form-check">
            <input type="checkbox" name="recordar" id="recordar" <?php echo isset($_COOKIE['remember_user']) ? 'checked' : ''; ?>>
            <label for="recordar">Recordarme</label>
        </div>
        <!-- Contenedor para ambos botones -->
        <div class="button-group">
            <button type="submit" name="enviar" class="login-btn">Ingresar</button>
            <a href="registrar.php" class="register-btn">Crear Cuenta</a>
        </div>
    </form>
</div>

<script>
function togglePasswordVisibility() {
    var passwordInput = document.getElementById("contrasena");
    var toggleIcon = document.getElementById("toggleIcon");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>
