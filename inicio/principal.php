<?php
session_start();
include('../conexion.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['Usuario'])) {
    header('Location: ../login.php');
    exit();
}

// Obtener información del usuario
$username = $_SESSION['Usuario'];
$stmt = $connec->prepare("SELECT Nombre FROM usuarios WHERE Usuario = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($nombre_usuario);
$stmt->fetch();
$stmt->close();

if (!$nombre_usuario) {
    $nombre_usuario = $username;
}

// Contar el total de usuarios
$stmt = $connec->prepare("SELECT COUNT(*) FROM usuarios");
$stmt->execute();
$stmt->bind_result($total_usuarios);
$stmt->fetch();
$stmt->close();

// Configurar la ruta del avatar
$_SESSION['avatar_path'] = isset($_SESSION['avatar_path']) ? $_SESSION['avatar_path'] : 'avatars/default_avatar.png';

if (!empty($_SESSION['avatar_path']) && strpos($_SESSION['avatar_path'], 'avatars/') === false) {
    $_SESSION['avatar_path'] = 'avatars/' . $_SESSION['avatar_path'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIENDA DE CARROS - JJLCARS</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/principal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php 
        include('../barras/navbar.php'); 
        include('../barras/barra_lateral.php'); 
        ?>
        
        <div class="main-container">
            <div class="welcome-card">
                <h1>Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></h1>
                <p>Usuario: <?php echo htmlspecialchars($username); ?></p>
                <p>La hora del sistema es: <span id="hora"></span></p>
            </div>

            <div class="summary-card">
                <h3>Total Usuarios</h3>
                <div class="summary-value"><?php echo $total_usuarios; ?></div>
                <p>Usuarios registrados en el sistema</p>
            </div>

            <div class="logo-container">
                <img src="../img/logo.jpg" alt="Logo JJLCARS" class="logo-image">
            </div>
        </div>
    </div>
</div>
    <script>
        function actualizarHora() {
            var fecha = new Date();
            var hora = fecha.getHours();
            var minutos = fecha.getMinutes();
            var segundos = fecha.getSeconds();
            var ampm = hora >= 12 ? 'PM' : 'AM';
            
            hora = hora % 12;
            hora = hora ? hora : 12;
            minutos = minutos < 10 ? '0' + minutos : minutos;
            segundos = segundos < 10 ? '0' + segundos : segundos;
            
            var horaActual = hora + ':' + minutos + ':' + segundos + ' ' + ampm;
            document.getElementById('hora').innerHTML = horaActual;
        }

        window.onload = function() {
            actualizarHora();
            setInterval(actualizarHora, 1000);
        };
    </script>
</body>
</html>
