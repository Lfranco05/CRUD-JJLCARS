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

// Paginación
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM citas $where ORDER BY fecha DESC, hora DESC LIMIT $limit OFFSET $offset";
$resultado = mysqli_query($connec, $sql);

$sql_total = "SELECT COUNT(*) as total FROM citas $where";
$resultado_total = mysqli_query($connec, $sql_total);
$total_citas = mysqli_fetch_assoc($resultado_total)['total'];
$total_pages = ceil($total_citas / $limit);
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

    <style>
        :root {
            --azul-oscuro: #475569;
            --azul-hover: #2c2c2c;
            --marron: #8b5e3c;
            --violeta: #7b2ff7;
            --violeta-hover: #5a1dcf;
            --rojo: #e74c3c;
            --rojo-hover: #c0392b;
            --fondo: #f3f3f8;
            --blanco: #fff;
            --gris-claro: #f4f4f4;
            --gris-borde: #e0e0e0;
        }

        body {
            font-family: "Segoe UI", sans-serif;
            background-color: var(--fondo);
            margin: 0;
            padding: 0;
        }

        .main-container {
            padding: 30px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            color: #1b1f3b;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .search-form input[type="text"] {
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 12px;
            flex: 1;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .search-form input[type="text"]:focus {
            border-color: var(--azul-oscuro);
            outline: none;
            box-shadow: 0 0 5px rgba(123, 47, 247, 0.3);
        }

        .btn-buscar,
        .btn-mostrar,
        .btn-info,
        .btn-danger,
        .btn-anterior,
        .btn-siguiente {
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            color: var(--blanco);
            display: inline-block;
            font-size: 14px;
        }

        .btn-buscar, .btn-anterior {
            background-color: var(--azul-oscuro);
        }

        .btn-buscar:hover, .btn-anterior:hover {
            background-color: var(--azul-hover);
            transform: scale(1.05);
        }

        .btn-mostrar, .btn-siguiente {
            background-color: var(--marron);
        }

        .btn-mostrar:hover, .btn-siguiente:hover {
            background-color: var(--azul-hover);
            transform: scale(1.05);
        }

        .btn-info {
            background-color: var(--violeta);
        }

        .btn-info:hover {
            background-color: var(--violeta-hover);
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: var(--rojo);
        }

        .btn-danger:hover {
            background-color: var(--rojo-hover);
            transform: scale(1.05);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            background-color: var(--blanco);
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        thead {
            background-color: var(--gris-claro);
            color: #333;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--gris-borde);
        }

        th {
            font-weight: 600;
            font-size: 15px;
        }

        td {
            font-size: 14px;
            color: #444;
        }

        tbody tr:hover {
            background-color: #fafafa;
            transition: background-color 0.3s;
        }

        .actions a {
            margin-right: 10px;
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .total-messages {
            margin-top: 20px;
            text-align: left;
        }

        .total-users {
            color: #888;
            font-size: 15px;
            font-weight: normal;
            margin-bottom: 10px;
        }

        .pagination {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination button[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
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
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="total-messages">
            <p class="total-users">Total de citas: <?php echo $total_citas; ?></p>
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&buscar=<?php echo urlencode($buscar); ?>" class="btn-anterior">Anterior</a>
            <?php else: ?>
                <button class="btn-anterior" disabled>Anterior</button>
            <?php endif; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>&buscar=<?php echo urlencode($buscar); ?>" class="btn-siguiente">Siguiente</a>
            <?php else: ?>
                <button class="btn-siguiente" disabled>Siguiente</button>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
