<?php
session_start();
include("../conexion.php");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

// Configuración de paginación
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Obtener el término de búsqueda si existe
$buscar = isset($_GET['buscar']) ? trim(strtolower($_GET['buscar'])) : '';

// Contar el total de usuarios (para paginación)
if (!empty($buscar)) {
    $countStmt = mysqli_prepare($connec, "SELECT COUNT(*) FROM usuario WHERE LOWER(username) LIKE ? OR LOWER(nom_usuario) LIKE ?");
    $searchTerm = "%$buscar%";
    mysqli_stmt_bind_param($countStmt, "ss", $searchTerm, $searchTerm);
    mysqli_stmt_execute($countStmt);
    mysqli_stmt_bind_result($countStmt, $total_usuarios);
    mysqli_stmt_fetch($countStmt);
    mysqli_stmt_close($countStmt);
} else {
    $countQuery = mysqli_query($connec, "SELECT COUNT(*) as total FROM usuario");
    $row = mysqli_fetch_assoc($countQuery);
    $total_usuarios = $row['total'];
}

$total_pages = ceil($total_usuarios / $limit);

// Obtener los usuarios con paginación usando prepared statements
// Modificar la consulta de búsqueda
if (!empty($buscar)) {
    $stmt = mysqli_prepare($connec, "SELECT id, username, nom_usuario 
        FROM usuario WHERE 
        LOWER(username) LIKE ? OR 
        LOWER(nom_usuario) LIKE ? 
        LIMIT ? OFFSET ?");
    $searchTerm = "%$buscar%";
    mysqli_stmt_bind_param($stmt, "ssii", $searchTerm, $searchTerm, $limit, $offset);
} else {
    // Modificar la consulta SQL para incluir los campos adicionales
    $stmt = mysqli_prepare($connec, "SELECT id, username, nom_usuario FROM usuario LIMIT ? OFFSET ?");
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
    <title>Lista de Usuarios - Universidad San Pablo</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php 
        // Actualizar la ruta del avatar en la sesión
        $avatar_path = isset($_SESSION['avatar_path']) ? '../' . $_SESSION['avatar_path'] : '../avatars/1744068538_foto para curriculum 3.png';
        ?>
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>
        
        <div class="main-container">
            <h1>Lista de Usuarios</h1>
            <div class="search-form">
                <form method="get" action="usuarios.php">
                    <input type="text" name="buscar" placeholder="Buscar usuario" value="<?php echo htmlspecialchars($buscar); ?>">
                    <input type="submit" value="Buscar">
                    <a href="usuarios.php" class="back">Mostrar todos</a>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['id']); ?></td>
                            <td><?php echo htmlspecialchars($fila['username']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nom_usuario']); ?></td>
                            <td>
                                <a href="ver_usuario.php?id=<?php echo urlencode($fila['id']); ?>" class="view">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="modificar_usuario.php?id=<?php echo urlencode($fila['id']); ?>" class="modify">
                                    <i class="fas fa-edit"></i> Modificar
                                </a>
                                <a href="eliminar_usuario.php?id=<?php echo urlencode($fila['id']); ?>" class="delete" 
                                   onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (mysqli_num_rows($resultado) == 0) { ?>
                        <tr>
                            <td colspan="3">No se encontraron usuarios.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <p class="total-users">Total de usuarios: <?php echo $total_usuarios; ?></p>
            <div class="pagination">
                <?php if ($page > 1) { ?>
                    <a href="usuarios.php?page=<?php echo $page - 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Anterior</a>
                <?php } else { ?>
                    <a href="#" class="disabled">Anterior</a>
                <?php } ?>
                <?php if ($page < $total_pages) { ?>
                    <a href="usuarios.php?page=<?php echo $page + 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Siguiente</a>
                <?php } else { ?>
                    <a href="#" class="disabled">Siguiente</a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="logo-container">
        <img src="../img/logo.jpeg" alt="Logo Universidad San Pablo" class="logo-image">
    </div>
</div>
</body>
</html>