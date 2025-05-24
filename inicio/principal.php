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
           <div class="welcome-card video-card">
    <video autoplay muted loop playsinline>
        <source src="../carros/fondo_inicioSesion.mp4" type="video/mp4">
    </video>
    <div class="overlay-text">
        <h1>JJLCARS</h1>
        <p>Calidad es lo que nos caracteriza.</p>
    </div>
        </div>

    <div>

    <div>    

    <!-- CARRUSEL DENTRO DE LA OPCION DE INICIO -->

    <div class="carrusel">
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

    </div>


    <div class="info-card empresa">
    <h2>Sobre JJLCARS</h2>
    <img src="../img/logo.jpg" alt="Imagen de la empresa" class="empresa-img">

    <div class="empresa-contenido">
        <h3>Nuestra Historia</h3>
        <p>
            JJLCARS nació en 2025 con la visión de revolucionar el mercado automotriz digital. Lo que comenzó como un pequeño catálogo de autos en línea, se transformó rápidamente en una de las plataformas más confiables para encontrar vehículos nuevos y seminuevos con transparencia, calidad y confianza.
        </p>

        <h3>Misión</h3>
        <p>
            Ofrecer a nuestros clientes la mejor experiencia al momento de buscar, comparar y adquirir vehículos, proporcionando atención personalizada, confianza y un catálogo actualizado con las mejores opciones del mercado.
        </p>

        <h3>Visión</h3>
        <p>
            Ser la plataforma líder en soluciones automotrices digitales en América Latina, innovando constantemente para conectar a las personas con el vehículo de sus sueños.
        </p>
    </div>
    </div>


    <!-- Moviemiento de raton para desplegar esta opcion  -->
    <script>
    window.addEventListener('scroll', function () {
    document.querySelectorAll('.oculto-inicio').forEach(el => {
        const rect = el.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        if (rect.top < windowHeight - 100 && rect.bottom > 100) {
            el.classList.add('visible');
        }
    });
    });
    </script>


    <!-- Carrusel dentro de la pagina de incio  -->

    <script>
    const track = document.querySelector('.carrusel-track');
    let items = document.querySelectorAll('.carrusel-item');
    let index = 1; // empieza en 1 por el primer clon
    let interval;

    function cloneItems() {
    const firstClone = items[0].cloneNode(true);
    const lastClone = items[items.length - 1].cloneNode(true);
    firstClone.classList.add('clone');
    lastClone.classList.add('clone');
    track.appendChild(firstClone);
    track.insertBefore(lastClone, items[0]);
    items = document.querySelectorAll('.carrusel-item');
    }

    function updateCarrusel(animate = true) {
    const width = items[0].offsetWidth + 20; // incluye el gap si tienes uno
    track.style.transition = animate ? 'transform 0.6s ease-in-out' : 'none';
    track.style.transform = `translateX(-${index * width}px)`;
    }

    function startAutoScroll() {
    interval = setInterval(() => {
        index++;
        updateCarrusel();
    }, 4000);
    }

    track.addEventListener('transitionend', () => {
    if (items[index].classList.contains('clone')) {
        const realIndex = index === 0 ? items.length - 2 : 1;
        index = realIndex;
        updateCarrusel(false); 
    }
    });

    window.addEventListener('load', () => {
    cloneItems();
    updateCarrusel(false);
    startAutoScroll();
    });

    window.addEventListener('resize', () => {
    updateCarrusel(false);
    });
    </script>



</body>
</html>
