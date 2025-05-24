<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Navbar - JJLCARS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <style>
        .time {
            font-family: monospace;
            font-size: 14px;
            color: #4b4b4b;
            animation: aparecer 0.5s ease-in-out;
        }

        @keyframes aparecer {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <!-- Logo y JJLcars nombre -->
        <a href="../inicio/principal.php" class="navbar-brand">
            <img src="../img/logo.jpg" alt="Logo de JJLCARS">
            JJLCARS
        </a>

        <div class="navbar-right">
            <!-- Hora -->
            <span class="time" id="horaActual"></span>

            <!-- Nombre de usuario desde la sesión -->
            <div class="user-info">
                <span class="user-name">
                    <?php 
                    if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                    }
                    include("../conexion.php");
                    if (isset($_SESSION['username'])) {
                        $username = $_SESSION['username'];
                        $stmt = $connec->prepare("SELECT nom_usuario FROM usuario WHERE username = ?");
                        $stmt->bind_param("s", $username);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $user = $result->fetch_assoc();
                        echo htmlspecialchars($user['nom_usuario'] ?? $username);
                        $stmt->close();
                    } else {
                        echo '';
                    }
                    ?>
                </span>
            </div>

            <!-- Botón de logout -->
            <a href="../logout.php" class="logout-btn" title="">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    </nav>

    <script>
        function actualizarHora() {
            const ahora = new Date();
            const hora = ahora.toLocaleTimeString();
            document.getElementById("horaActual").textContent = hora;
        }

        actualizarHora();
        setInterval(actualizarHora, 1000);
    </script>
</body>
</html>
