<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$buscar = isset($_GET['buscar']) ? trim(strtolower($_GET['buscar'])) : '';

if (!empty($buscar)) {
    $searchTerm = "%$buscar%";
    $countStmt = mysqli_prepare($connec, "SELECT COUNT(*) FROM citas WHERE LOWER(nombre) LIKE ? OR LOWER(tipoCita) LIKE ? OR LOWER(tipoCompra) LIKE ?");
    mysqli_stmt_bind_param($countStmt, "sss", $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($countStmt);
    mysqli_stmt_bind_result($countStmt, $total_citas);
    mysqli_stmt_fetch($countStmt);
    mysqli_stmt_close($countStmt);

    $stmt = mysqli_prepare($connec, "SELECT id, tipoCita, tipoCompra, precio, nombre, status FROM citas WHERE LOWER(nombre) LIKE ? OR LOWER(tipoCita) LIKE ? OR LOWER(tipoCompra) LIKE ? LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "sssii", $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
} else {
    $res = mysqli_query($connec, "SELECT COUNT(*) AS total FROM citas");
    $row = mysqli_fetch_assoc($res);
    $total_citas = $row['total'];

    $stmt = mysqli_prepare($connec, "SELECT id, tipoCita, tipoCompra, precio, nombre, status FROM citas LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

$total_pages = ceil($total_citas / $limit);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas - JJLCARS</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../../css/ventas_css/ventas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>

        <div class="main-container">
            <h1>Listado de Ventas</h1>

            <div class="search-form">
                <form method="get" action="ventas.php">
                    <input type="text" name="buscar" placeholder="Buscar por cliente o tipo de cita" value="<?php echo htmlspecialchars($buscar); ?>">
                    <input type="submit" value="Buscar">
                    <a href="ventas.php" class="back">Mostrar todos</a>
                    <div class="actions">
                    <a href="nueva_cita.php" class="add-button"><i class="fas fa-plus"></i> Nueva Cita</a>
            </div>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo de Cita</th>
                        <th>Tipo de Compra</th>
                        <th>Precio</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?php echo $fila['id']; ?></td>
                                <td><?php echo htmlspecialchars($fila['tipoCita']); ?></td>
                                <td><?php echo htmlspecialchars($fila['tipoCompra']); ?></td>
                                <td>$<?php echo number_format($fila['precio']); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                <td>
                                    <form action="cambiar_estado.php" method="post">
                                        <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">
                                        <select name="nuevo_estado" onchange="this.form.submit()">
                                            <option value="Pendiente" <?php if ($fila['status'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                            <option value="Aprobada" <?php if ($fila['status'] == 'Aprobada') echo 'selected'; ?>>Aprobada</option>
                                            <option value="Cancelada" <?php if ($fila['status'] == 'Cancelada') echo 'selected'; ?>>Cancelada</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <a href="ver_cita.php?id=<?php echo $fila['id']; ?>" class="action-icon" title="Ver"><i class="fas fa-eye"></i></a>
                                    <a href="eliminar_cita.php?id=<?php echo $fila['id']; ?>" class="delete" onclick="return confirm('¿Estás seguro de eliminar esta cita?');"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No se encontraron citas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <p class="total-users">Total de citas: <?php echo $total_citas; ?></p>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Anterior</a>
                <?php else: ?>
                    <a href="#" class="disabled">Anterior</a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Siguiente</a>
                <?php else: ?>
                    <a href="#" class="disabled">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
