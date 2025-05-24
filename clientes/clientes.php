<?php
session_start();
include("../conexion.php");

// Seguridad de inicio de sesión
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

    // Contar total clientes con búsqueda
    $countStmt = mysqli_prepare($connec, "SELECT COUNT(*) FROM clientes WHERE LOWER(nombre) LIKE ? OR LOWER(usuario) LIKE ? OR LOWER(correo) LIKE ?");
    mysqli_stmt_bind_param($countStmt, "sss", $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($countStmt);
    mysqli_stmt_bind_result($countStmt, $total_clientes);
    mysqli_stmt_fetch($countStmt);
    mysqli_stmt_close($countStmt);

    $stmt = mysqli_prepare($connec, "SELECT id, nombre, usuario, correo, TipoCliente FROM clientes WHERE LOWER(nombre) LIKE ? OR LOWER(usuario) LIKE ? OR LOWER(correo) LIKE ? LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "sssii", $searchTerm, $searchTerm, $searchTerm, $limit, $offset);

} else {
    // Contar total clientes sin búsqueda
    $res = mysqli_query($connec, "SELECT COUNT(*) AS total FROM clientes");
    $row = mysqli_fetch_assoc($res);
    $total_clientes = $row['total'];

    // Obtener datos sin búsqueda con paginación
    $stmt = mysqli_prepare($connec, "SELECT id, Nombre, usuario, correo, TipoCliente FROM clientes LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

$total_pages = ceil($total_clientes / $limit);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes - JJLCARS</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../../css/clientes_css/clientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>

        <div class="main-container">
            <h1>Listado de Clientes</h1>

            <div class="search-form">
                <form method="get" action="clientes.php">
                    <input type="text" name="buscar" placeholder="Buscar cliente" value="<?php echo htmlspecialchars($buscar); ?>">
                    <input type="submit" value="Buscar">
                    <a href="clientes.php" class="back">Mostrar todos</a>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?php echo $fila['id']; ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($fila['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($fila['correo']); ?></td>
                                <td>
                                    <a href="ver_clientes.php?id=<?php echo $fila['id']; ?>" class="action-icon" title="Ver"><i class="fas fa-eye"></i></a>
                                    <a href="modificar_clientes.php?id=<?php echo $fila['id']; ?>" class="modify"><i class="fas fa-edit"></i></a>
                                    <a href="eliminar_clientes.php?id=<?php echo $fila['id']; ?>" class="delete" onclick="return confirm('¿Estás seguro de eliminar este cliente?');"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No se encontraron clientes.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <p class="total-users">Total de clientes: <?php echo $total_clientes; ?></p>
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
