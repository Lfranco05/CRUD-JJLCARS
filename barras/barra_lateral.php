<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . "/../conexion.php");

// Obtener avatar del usuario
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$query = "SELECT avatar FROM usuario WHERE username = ?";
$stmt = mysqli_prepare($connec, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Ruta del avatar
$default_avatar = 'default.png';
$avatar_path = '../' . (!empty($user['avatar']) ? $user['avatar'] : 'avatars/' . $default_avatar);
if (!file_exists(__DIR__ . '/../' . ($user['avatar'] ?? 'avatars/' . $default_avatar))) {
    $avatar_path = '../avatars/' . $default_avatar;
}

$nom_usuario = isset($_SESSION['username']) ? $_SESSION['username'] : 'Usuario';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="profile">
        <div class="avatar-container">
            <form id="avatarForm" action="../upload_avatar.php" method="post" enctype="multipart/form-data">
                <label for="avatarUpload">
                    <img src="<?php echo htmlspecialchars($avatar_path); ?>" alt="Avatar de usuario">
                    <div class="avatar-overlay">
                        <div class="upload-btn"><i class="fas fa-camera"></i></div>
                    </div>
                </label>
                <input type="file" id="avatarUpload" name="avatar" accept="image/*" style="display: none;">
            </form>
        </div>
        <p class="profile-name"><?php echo htmlspecialchars($nom_usuario); ?></p>
    </div>

    <nav class="menu-section">
        <h3>PERFIL ADMINISTRADOR</h3>
        <a href="../inicio/principal.php" class="<?php echo $current_page == 'principal.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Inicio
        </a>
    </nav>

    <div class="menu-section">
            <h3>PANEL</h3>
            <a href="../estudiantes/dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Proyecciones de ventas
            </a>
            <a href="../estudiantes/estudiantes.php" class="<?php echo $current_page == 'estudiantes.php' ? 'active' : ''; ?>">
                <i class="fas fa-graduation-cap"></i> Clientes
            </a>
            <a href="../contactos/contactos.php" class="<?php echo $current_page == 'contactos.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> Citas Programadas
            </a>
        </div>

    <div class="menu-section">
        <h3>VENTAS</h3>
        <a href="../usuarios/usuarios.php" class="<?php echo $current_page == 'usuarios.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Empleados
        <a href="../ventas/ventas.php" class="<?php echo $current_page == 'ventas.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Ventas
        </a>
        <a href="../vendedores/vendedores.php" class="<?php echo $current_page == 'vendedores.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-tie"></i> Vendedores
        </a>
    </div>

    <div class="menu-section">
        <h3>VEHÍCULOS</h3>
        <a href="../vehiculos/vehiculos.php" class="<?php echo $current_page == 'vehiculos.php' ? 'active' : ''; ?>">
            <i class="fas fa-car"></i> Vehículos
        </a>
        <a href="../marcas/marcas.php" class="<?php echo $current_page == 'marcas.php' ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i> Marcas
        </a>
    </div>
</div>

<script>
document.getElementById('avatarUpload').addEventListener('change', function () {
    if (this.files && this.files[0]) {
        document.getElementById('avatarForm').submit();
    }
});
</script>
</body>
</html>
