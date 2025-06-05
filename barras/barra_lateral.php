<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . "/../conexion.php");

$username = $_SESSION['Usuario'] ?? '';
$rol = $_SESSION['TipoUsuario'] ?? '';

// Obtener avatar y nombre del usuario
$query = "SELECT Nombre, avatar FROM usuarios WHERE Usuario = ?";
$stmt = mysqli_prepare($connec, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$nom_usuario = $user['Nombre'] ?? $username;
$default_avatar = '../avatars/default.png';
$avatar_path = $default_avatar;

if (!empty($user['avatar'])) {
    $avatar_rel = ltrim($user['avatar'], '/');
    $avatar_absoluto = __DIR__ . '/../' . $avatar_rel;
    $avatar_relativo = '../' . $avatar_rel;

    if (file_exists($avatar_absoluto)) {
        $avatar_path = $avatar_relativo;
    }
}

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
        <h3>PERFIL</h3>
        <a href="../inicio/principal.php" class="<?php echo $current_page == 'principal.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Inicio
        </a>
    </nav>

    <?php if ($rol === 'gerente'): ?>
        <div class="menu-section">
            <h3>PANEL</h3>
            <a href="../proyeccion_ventas/proyeccion_ventas.php" class="<?php echo $current_page == 'proyeccion_ventas.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Proyecciones de ventas
            </a>
            <a href="../clientes/clientes.php" class="<?php echo $current_page == 'clientes.php' ? 'active' : ''; ?>">
                <i class="fa-regular fa-circle-user"></i> Clientes
            </a>
            <a href="../contactos/contactos.php" class="<?php echo $current_page == 'contactos.php' ? 'active' : ''; ?>">
               <i class="fa-regular fa-address-book"></i> Citas Programadas
            </a>
        </div>

        <div class="menu-section">
            <h3>VENTAS</h3>
            <a href="../Empleados/empleados.php" class="<?php echo $current_page == 'empleados.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Empleados
            </a>
            <a href="../ventas/ventas.php" class="<?php echo $current_page == 'ventas.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Ventas
            </a>
        </div>

        <div class="menu-section">
            <h3>VEHÍCULOS</h3>
            <a href="../vehiculos/vehiculos.php" class="<?php echo $current_page == 'vehiculos.php' ? 'active' : ''; ?>">
                <i class="fas fa-car"></i> Vehículos
            </a>
            <a href="../inventario/inventario.php" class="<?php echo $current_page == 'inventario.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Inventario
            </a>
        </div>
    <?php elseif ($rol === 'vendedor'): ?>
        <div class="menu-section">
            <h3>VENTAS</h3>
            <a href="../ventas/ventas.php" class="<?php echo $current_page == 'ventas.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Ventas
            </a>
        </div>

        <div class="menu-section">
            <h3>CONTACTO Y STOCK</h3>
            <a href="../contactos/contactos.php" class="<?php echo $current_page == 'contactos.php' ? 'active' : ''; ?>">
                <i class="fa-regular fa-address-book"></i> Citas Programadas
            </a>
            <a href="../inventario/inventario.php" class="<?php echo $current_page == 'inventario.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Inventario
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('avatarUpload').addEventListener('change', function () {
    if (this.files && this.files[0]) {
        document.getElementById('avatarForm').submit();
    }
});
</script>
