<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

$query = "SELECT id, marca, modelo, imagen, inventario FROM vehiculos";
$resultado = mysqli_query($connec, $query);

if (!$resultado) {
    die("Error al obtener datos del inventario: " . mysqli_error($connec));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../css/vehiculos_css/inventario.css">
    <title>Inventario de Vehículos</title>
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>

        <div class="main-container">
            <h1>Inventario de Vehículos</h1>
            <div class="inventario-container">
                <?php while ($vehiculo = mysqli_fetch_assoc($resultado)) { ?>
                    <div class="vehiculo-card">
                        <img src="../<?php echo htmlspecialchars($vehiculo['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($vehiculo['marca']); ?>">
                        <div class="vehiculo-info">
                            <p><strong>ID:</strong> <?php echo $vehiculo['id']; ?></p>
                            <p><strong>Marca:</strong> <?php echo htmlspecialchars($vehiculo['marca']); ?></p>
                            <p><strong>Modelo:</strong> <?php echo htmlspecialchars($vehiculo['modelo']); ?></p>
                             <p><strong>Inventario:</strong> <?php echo $vehiculo['inventario']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>