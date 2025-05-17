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

// Contar el total de estudiantes
if (!empty($buscar)) {
    $countStmt = mysqli_prepare($connec, "SELECT COUNT(*) FROM alumno WHERE 
        LOWER(nombre) LIKE ? OR 
        LOWER(telefono) LIKE ? OR 
        LOWER(direccion) LIKE ? OR 
        LOWER(carrera) LIKE ? OR 
        LOWER(semestre) LIKE ?");
    $searchTerm = "%$buscar%";
    mysqli_stmt_bind_param($countStmt, "sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($countStmt);
    mysqli_stmt_bind_result($countStmt, $total_estudiantes);
    mysqli_stmt_fetch($countStmt);
    mysqli_stmt_close($countStmt);
} else {
    $countQuery = mysqli_query($connec, "SELECT COUNT(*) as total FROM alumno");
    $row = mysqli_fetch_assoc($countQuery);
    $total_estudiantes = $row['total'];
}

$total_pages = ceil($total_estudiantes / $limit);

// Obtener los estudiantes con paginación
if (!empty($buscar)) {
    $stmt = mysqli_prepare($connec, "SELECT id, nombre, telefono, direccion, carrera, semestre, estado 
        FROM alumno WHERE 
        LOWER(nombre) LIKE ? OR 
        LOWER(telefono) LIKE ? OR 
        LOWER(direccion) LIKE ? OR 
        LOWER(carrera) LIKE ? OR 
        LOWER(semestre) LIKE ? 
        LIMIT ? OFFSET ?");
    $searchTerm = "%$buscar%";
    mysqli_stmt_bind_param($stmt, "sssssii", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
} else {
    $stmt = mysqli_prepare($connec, "SELECT id, nombre, telefono, direccion, carrera, semestre, estado FROM alumno LIMIT ? OFFSET ?");
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
    <title>Clientes - JJLCARS</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/estudiantes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php 
        // Actualizar la ruta del avatar en la sesión
        $avatar_path = isset($_SESSION['avatar_path']) ? '../' . $_SESSION['avatar_path'] : '../avatar/1744068538_foto para curriculum 3.png';
        ?>
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>
        
        <div class="main-container">
            <h1>Listado de Clientes</h1>
            <div class="search-form">
                <form method="get" action="estudiantes.php">
                    <input type="text" name="buscar" placeholder="Buscar estudiante" value="<?php echo htmlspecialchars($buscar); ?>">
                    <input type="submit" value="Buscar">
                    <a href="estudiantes.php" class="back">Mostrar todos</a>
                </form>
                <a href="agregar_estudiante.php" class="new-student-btn">
                    <i class="fas fa-user-plus"></i> Nuevo Estudiante
                </a>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Carrera</th>
                            <th>Semestre</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['id']); ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($fila['direccion']); ?></td>
                                <td><?php echo htmlspecialchars($fila['carrera']); ?></td>
                                <td><?php echo htmlspecialchars($fila['semestre']); ?></td>
                                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                                <td>
                                    <a href="ver_estudiantes.php?id=<?php echo urlencode($fila['id']); ?>" class="view">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="modificar_estudiante.php?id=<?php echo urlencode($fila['id']); ?>" class="modify">
                                        <i class="fas fa-edit"></i> Modificar
                                    </a>
                                    <a href="eliminar_estudiante.php?id=<?php echo urlencode($fila['id']); ?>" class="delete" 
                                       onclick="return confirm('¿Estás seguro de eliminar este estudiante?');">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (mysqli_num_rows($resultado) == 0) { ?>
                            <tr>
                                <td colspan="8">No se encontraron estudiantes.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <p class="total-users">Total de estudiantes: <?php echo $total_estudiantes; ?></p>
            <div class="pagination">
                <?php if ($page > 1) { ?>
                    <a href="estudiantes.php?page=<?php echo $page - 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Anterior</a>
                <?php } else { ?>
                    <a href="#" class="disabled">Anterior</a>
                <?php } ?>
                <?php if ($page < $total_pages) { ?>
                    <a href="estudiantes.php?page=<?php echo $page + 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Siguiente</a>
                <?php } else { ?>
                    <a href="#" class="disabled">Siguiente</a>
                <?php } ?>
            </div>
        </div>
        <div class="logo-container">
            <img src="../img/logo.jpeg" alt="Logo Universidad San Pablo" class="logo-image">
        </div>
    </div>
</body>
</html>