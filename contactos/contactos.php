<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: ../login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Búsqueda
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($connec, $_GET['buscar']) : '';
$where = '';
if (!empty($buscar)) {
    $where = "WHERE nombre LIKE '%$buscar%' OR correo LIKE '%$buscar%' OR tipoCita LIKE '%$buscar%' OR tipoCompra LIKE '%$buscar%'";
}

// Obtener citas
$sql = "SELECT * FROM citas $where ORDER BY fecha DESC, hora DESC";
$resultado = mysqli_query($connec, $sql);

// Total de citas
$sql_total = "SELECT COUNT(*) as total FROM citas $where";
$resultado_total = mysqli_query($connec, $sql_total);
$total_citas = mysqli_fetch_assoc($resultado_total)['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas Programadas - JJLCARS</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../css/contactos_css/contactos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="wrapper">
    <?php include('../barras/navbar.php'); ?>
    <?php include('../barras/barra_lateral.php'); ?>

    <div class="main-container">
        <h1>Citas Programadas</h1>

        <div class="search-form">
            <form method="get" action="contactos.php">
                <input type="text" name="buscar" placeholder="Buscar cita..." value="<?php echo htmlspecialchars($buscar); ?>">
                <button type="submit" class="btn-buscar">Buscar</button>
                <a href="contactos.php" class="btn-mostrar">Mostrar todos</a>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Cliente</th>
                        <th>Correo</th>
                        <th>Tipo de Cita</th>
                        <th>Detalle</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cita = mysqli_fetch_assoc($resultado)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cita['id']); ?></td>
                            <td><?php echo htmlspecialchars($cita['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($cita['correo']); ?></td>
                            <td><?php echo htmlspecialchars($cita['tipoCita']); ?></td>
                            <td><?php echo htmlspecialchars($cita['tipoCompra']); ?></td>
                            <td><?php echo htmlspecialchars($cita['status']); ?></td>
                            <td class="actions">
                                <a href="ver_cita.php?id=<?php echo $cita['id']; ?>" class="action-icon" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <!-- <a href="eliminar_mensaje.php?id=<?php echo $cita['id']; ?>" class="action-icon" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta cita?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a> -->
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="total-messages">
            Total de citas programadas: <?php echo $total_citas; ?>
        </div>
    </div>
</div>
</body>
</html>
