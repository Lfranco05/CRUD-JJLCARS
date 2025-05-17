<?php
session_start();
include_once("conexion.php");

// Validar que se recibieron datos del formulario
if (!isset($_POST['user']) || !isset($_POST['contrasena'])) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor complete todos los campos',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'login.php';
        });
    </script>";
    exit();
}

// Limpiar y validar datos
$username = mysqli_real_escape_string($connec, trim($_POST['user']));
$password = trim($_POST['contrasena']);

if (empty($username) || empty($password)) {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Campos vacíos',
            text: 'Por favor complete todos los campos',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'login.php';
        });
    </script>";
    exit();
}

// Consulta preparada
$sql = "SELECT id, username, nom_usuario, password, avatar FROM usuario WHERE username = ?";
$stmt = mysqli_prepare($connec, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($fila = mysqli_fetch_assoc($resultado)) {
    if (password_verify($password, $fila['password'])) {
        $_SESSION['usuarioingresando'] = true;
        $_SESSION['id'] = $fila['id'];
        $_SESSION['username'] = $fila['username'];
        $_SESSION['nom_usuario'] = $fila['nom_usuario'];
        
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
        
        header("Location: inicio/principal.php");
        exit();
    }
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Usuario no encontrado',
            confirmButtonColor: '#3085d6'
        }).then(() => {
            window.location.href = 'login.php';
        });
    </script>";
}

mysqli_stmt_close($stmt);
mysqli_close($connec);
?>
