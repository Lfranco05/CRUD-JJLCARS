<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
</head>
<body>
    <nav class="navbar">
        <a href="../inicio/principal.php" class="navbar-brand">
            <img src="../img/logo.jpeg" alt="Logo Universidad">
            JJLCARS Tienda de Carros
        </a>
        
        <div class="navbar-right">
            <div class="user-info">
                <span class="user-name">
                    <?php 
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
                        echo 'Usuario';
                    }
                    ?>
                </span>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </a>
        </div>
    </nav>
</body>
</html>