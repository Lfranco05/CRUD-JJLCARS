<?php
session_start();
include('../conexion.php');

// Seguridad de login va muchis
if (!isset($_SESSION['Usuario'])) {
    header('Location: ../login.php');
    exit();
}

// Obtener información usuarios
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
            <div class="info-card hidden">


    <p>Lo mejor de nostros</p>

    <!-- CARRUSEL DENTRO DE LA OPCION DE INICIO -->

    <div class="carrusel">
    <button class="carrusel-btn prev"><i class="fas fa-chevron-left"></i></button>
    <div class="carrusel-track">
        <div class="carrusel-item">
            <img src="../carros/BMW 3 Series 2025.jpg" alt="BMW 3 Series 2025">
            <h4>BMW 3 Series 2025</h4>
            <p>Elegancia alemana y tecnología de última generación en un sedán deportivo.</p>
        </div>
        <div class="carrusel-item">
            <img src="../carros/chevrolet-tahoe.jpg" alt="Chevrolet Tahoe">
            <h4>Chevrolet Tahoe</h4>
            <p>Gran espacio y potencia para aventuras familiares o trabajo pesado.</p>
        </div>
        <div class="carrusel-item">
            <img src="../carros/Ferrari1.jpg" alt="Ferrari">
            <h4>Ferrari</h4>
            <p>Diseño italiano con alto rendimiento y un rugido inconfundible.</p>
        </div>
        <div class="carrusel-item">
            <img src="../carros/Silverado 2025 .jpg" alt="Silverado 2025">
            <h4>Silverado 2025</h4>
            <p>Pickup robusta con diseño renovado y capacidad extrema.</p>
        </div>
        <div class="carrusel-item">
            <img src="../carros/Silverado 2025 .jpg" alt="Silverado 2025">
            <h4>Silverado 2025</h4>
            <p>Pickup robusta con diseño renovado y capacidad extrema.</p>
        </div>
        <div class="carrusel-item">
            <img src="../carros/Silverado 2025 .jpg" alt="Silverado 2025">
            <h4>Silverado 2025</h4>
            <p>Pickup robusta con diseño renovado y capacidad extrema.</p>
        </div>
    </div>
    <button class="carrusel-btn next"><i class="fas fa-chevron-right"></i></button>
    </div>


</div>
    <h2></h2>
    <h2>Sobre JJLCARS</h2>
    <img src="../img/logo.jpeg" alt="Imagen de la empresa" class="empresa-img">
        </div>
    </div>
</div>
    <!-- Implementacion de hora del sistema -->
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

    <!-- Moviemiento de raton para desplegar esta opcion  -->
    <script>
    window.addEventListener('scroll', function () {
        const infoCard = document.querySelector('.info-card');
        const rect = infoCard.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        if (rect.top < windowHeight - 100 && rect.bottom > 100) {
            infoCard.classList.add('visible');
        } else {
            infoCard.classList.remove('visible');
        }
    });
    </script>

    <!-- Carrusel dentro de la pagina de incio  -->

<script>
    const track = document.querySelector('.carrusel-track');
    const items = document.querySelectorAll('.carrusel-item');
    const btnPrev = document.querySelector('.carrusel-btn.prev');
    const btnNext = document.querySelector('.carrusel-btn.next');

    let index = 0;
    const total = items.length;

    function updateCarrusel() {
        const width = items[0].offsetWidth;
        track.style.transform = `translateX(-${index * width}px)`;
    }

    btnPrev.addEventListener('click', () => {
        index = (index - 1 + total) % total;
        updateCarrusel();
    });

    btnNext.addEventListener('click', () => {
        index = (index + 1) % total;
        updateCarrusel();
    });

    // Loop infinito automático cada 4 segundos
    setInterval(() => {
        index = (index + 1) % total;
        updateCarrusel();
    }, 4000);

    window.addEventListener('resize', updateCarrusel);
    window.addEventListener('load', updateCarrusel);
</script>


</body>
</html>
