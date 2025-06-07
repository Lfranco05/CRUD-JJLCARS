<?php
session_start();
include("conexion.php");

// Si ya se inició sesión antes, redirige a la página de inicio.
if (isset($_SESSION["usuarioingresando"]) && $_SESSION["usuarioingresando"] === true) {
    header("Location: inicio/principal.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enviar'])) {
    $username = mysqli_real_escape_string($connec, trim($_POST['username']));
    $password = trim($_POST['password']);
    $rol = mysqli_real_escape_string($connec, trim($_POST['rol']));

    $sql = "SELECT id, Usuario, Nombre, password, TipoUsuario FROM usuarios WHERE Usuario = ?";
    $stmt = mysqli_prepare($connec, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        if (password_verify($password, $fila['password']) && $rol === $fila['TipoUsuario']) {
            $_SESSION['usuarioingresando'] = true;
            $_SESSION['id'] = $fila['id'];
            $_SESSION['Usuario'] = $fila['Usuario'];
            $_SESSION['Nombre'] = $fila['Nombre'];
            $_SESSION['TipoUsuario'] = $fila['TipoUsuario'];

            // Redireccionamiento general
            header("Location: inicio/principal.php");
            exit();
        } else {
            $error = "Usuario, contraseña o rol incorrectos";
        }
    } else {
        $error = "Usuario, contraseña o rol incorrectos";
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
    <link rel="stylesheet" href="../css/login_css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">

<video autoplay muted loop class="video-background">
    <source src="FondoLogin/FondoLogin.mp4" type="video/mp4">
</video>

<div class="login-container">
    <h2>Iniciar Sesión</h2>
    <p class="login-subtitle">JJLCARS</p>

    <?php if (isset($error)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error; ?>',
                confirmButtonColor: '#3085d6'
            });
        </script>
    <?php endif; ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-group">
            <label for="user">Usuario:</label>
            <input type="text" id="user" name="username" placeholder="Ingresa tu usuario" required>
        </div>

        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <div class="password-container">
                <input type="password" id="contrasena" name="password" placeholder="Ingresa tu contraseña" required>
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i id="toggleIcon" class="fa-solid fa-eye"></i>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label for="rol">Seleccionar Rol:</label>
            <select name="rol" id="rol" required>
                <option value="" disabled selected>Selecciona un rol</option>
                <option value="gerente">Gerente</option>
                <option value="vendedor">Vendedor</option>
            </select>
        </div>

        <div class="button-group">
            <button type="submit" name="enviar" class="login-btn">Ingresar</button>
        </div>
    </form>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById("contrasena");
    const toggleIcon = document.getElementById("toggleIcon");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>

</body>
</html>
