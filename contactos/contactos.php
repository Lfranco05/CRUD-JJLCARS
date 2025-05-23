<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: ../login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Consulta para buscar mensajes
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($connec, $_GET['buscar']) : '';
$where = '';
if (!empty($buscar)) {
    $where = "WHERE nombre LIKE '%$buscar%' OR correo LIKE '%$buscar%' OR carrera LIKE '%$buscar%'";
}

$sql = "SELECT * FROM contacto $where ORDER BY fecha_registro DESC";
$resultado = mysqli_query($connec, $sql);

// Contar total de mensajes
$sql_total = "SELECT COUNT(*) as total FROM contacto $where";
$resultado_total = mysqli_query($connec, $sql_total);
$total_mensajes = mysqli_fetch_assoc($resultado_total)['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas Programadas - JJLCARS</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/contactos_css/contactos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php 
        // Actualizar la ruta del avatar en la sesión
        $avatar_path = isset($_SESSION['avatar_path']) ? '../' . $_SESSION['avatar_path'] : '../avatars/default.png';
        ?>
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>
        
        <div class="main-container">
            <h1>Citas Programadas</h1>
            
            <div class="search-form">
                <form method="get" action="contactos.php">
                    <input type="text" name="buscar" placeholder="Buscar mensaje..." value="<?php echo htmlspecialchars($buscar); ?>">
                    <button type="submit" class="btn-buscar">Buscar</button>
                    <a href="contactos.php" class="btn-mostrar">Mostrar todos</a>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Mensaje</th>
                            <th>Fecha de creacion</th>
                            <th>Mas detalles</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($mensaje = mysqli_fetch_assoc($resultado)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mensaje['id']); ?></td>
                                <td><?php echo htmlspecialchars($mensaje['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($mensaje['correo']); ?></td>
                                <td><?php echo htmlspecialchars($mensaje['mensaje']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($mensaje['fecha_registro'])); ?></td>
                                <td class="actions">
                                    <a href="ver_mensaje.php?id=<?php echo $mensaje['id']; ?>" class="btn btn-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="eliminar_mensaje.php?id=<?php echo $mensaje['id']; ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('¿Está seguro de eliminar este mensaje?');">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="total-messages">
                Total de citas programadas: <?php echo $total_mensajes; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
 
