<?php
session_start();
include_once("conexion.php");

// Verificar que se reciban los datos del formulario
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

// Limpiar datos recibidos
$username = mysqli_real_escape_string($connec, trim($_POST['user']));
$password = trim($_POST['contrasena']);

// Validar campos vacíos
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
//Ayuda Dios
// Buscar el usuario en la base de datos
$sql = "SELECT id, Usuario, Nombre, password, TipoUsuario, correo FROM usuarios WHERE Usuario = ?";
$stmt = mysqli_prepare($connec, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

// Si el usuario existe
if ($fila = mysqli_fetch_assoc($resultado)) {
    // Verificar la contraseña encriptada
    if (password_verify($password, $fila['password'])) {
        // Guardar datos del usuario en la sesión
        $_SESSION['usuarioingresando'] = true;
        $_SESSION['id'] = $fila['id'];
        $_SESSION['Usuario'] = $fila['Usuario'];
        $_SESSION['Nombre'] = $fila['Nombre'];
        $_SESSION['TipoUsuario'] = $fila['TipoUsuario'];
        $_SESSION['correo'] = $fila['correo'];

        // Redirigir al panel principal
        header("Location: inicio/principal.php");
        exit();
    } else {
        // Contraseña incorrecta
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Contraseña incorrecta',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location.href = 'login.php';
            });
        </script>";
    }
} else {
    // Usuario no encontrado
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

// Cerrar conexiones
mysqli_stmt_close($stmt);
mysqli_close($connec);
?>
