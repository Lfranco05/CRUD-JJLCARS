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
    $countStmt = mysqli_prepare($connec, "SELECT COUNT(*) FROM usuarios WHERE LOWER(Usuario) LIKE ? OR LOWER(Nombre) LIKE ?");
    $searchTerm = "%$buscar%";
    mysqli_stmt_bind_param($countStmt, "ss", $searchTerm, $searchTerm);
    mysqli_stmt_execute($countStmt);
    mysqli_stmt_bind_result($countStmt, $total_usuarios);
    mysqli_stmt_fetch($countStmt);
    mysqli_stmt_close($countStmt);
} else {
    $countQuery = mysqli_query($connec, "SELECT COUNT(*) as total FROM usuarios");
    $row = mysqli_fetch_assoc($countQuery);
    $total_usuarios = $row['total'];
}

$total_pages = ceil($total_usuarios / $limit);

if (!empty($buscar)) {
    $stmt = mysqli_prepare($connec, "SELECT id, Usuario, Nombre, TipoUsuario FROM usuarios WHERE LOWER(Usuario) LIKE ? OR LOWER(Nombre) LIKE ? LIMIT ? OFFSET ?");
    $searchTerm = "%$buscar%";
    mysqli_stmt_bind_param($stmt, "ssii", $searchTerm, $searchTerm, $limit, $offset);
} else {
    $stmt = mysqli_prepare($connec, "SELECT id, Usuario, TipoUsuario, Nombre FROM usuarios LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (!$resultado) {
    die("Error en la consulta SQL: " . mysqli_error($connec));
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/empleados_css/empleados.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php 
        $avatar_path = isset($_SESSION['avatar_path']) ? '../' . $_SESSION['avatar_path'] : '../avatars/default.png';
        ?>
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>
        
        <div class="main-container">
            <h1>JJLCARS</h1>
            <div class="search-form">
                <form method="get" action="empleados.php">
                    <input type="text" name="buscar" placeholder="Buscar usuario" value="<?php echo htmlspecialchars($buscar); ?>">
                    <input type="submit" value="Buscar">
                    <a href="empleados.php" class="back">Mostrar todos</a>
                    <a href="agregar_empleados.php" class="back">Agregar empleado</a>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Puesto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['id']); ?></td>
                            <td><?php echo htmlspecialchars($fila['Nombre']); ?></td>
                            <td><?php echo htmlspecialchars($fila['TipoUsuario']); ?></td>
                            <td>
                    <a href="ver_empleados.php?id=<?php echo $fila['id']; ?>" class="action-icon" title="Ver">
                    <i class="fas fa-eye"></i>
                    </a>
                    <a href="modificar_empleados.php?id=<?php echo $fila['id']; ?>" class="action-icon" title="Modificar">
                <i class="fas fa-edit"></i>
                    </a>
                    <a href="eliminar_empleados.php?id=<?php echo $fila['id']; ?>" class="action-icon" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                    <i class="fas fa-trash-alt"></i>
                        </a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (mysqli_num_rows($resultado) == 0) { ?>
                        <tr>
                            <td colspan="4">Ninguna coincidencia</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <p class="total-users">Empleados en JJLCARS: <?php echo $total_usuarios; ?></p>
            <div class="pagination">
                <?php if ($page > 1) { ?>
                    <a href="empleados.php?page=<?php echo $page - 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Anterior</a>
                <?php } else { ?>
                    <a href="#" class="disabled">Anterior</a>
                <?php } ?>
                <?php if ($page < $total_pages) { ?>
                    <a href="empleados.php?page=<?php echo $page + 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Siguiente</a>
                <?php } else { ?>
                    <a href="#" class="disabled">Siguiente</a>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
