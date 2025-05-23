<?php
include("../conexion.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID inválido.";
    exit();
}

$id = (int)$_GET['id'];

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $inventario = $_POST['inventario'];

    // Manejo de imagen
    if (!empty($_FILES['imagen']['name'])) {
        $imagen = basename($_FILES['imagen']['name']);
        $rutaDestino = "../imagenes/" . $imagen;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
    } else {
        // Mantener la imagen actual si no se sube una nueva
        $consulta_img = mysqli_prepare($connec, "SELECT imagen FROM vehiculos WHERE id = ?");
        mysqli_stmt_bind_param($consulta_img, "i", $id);
        mysqli_stmt_execute($consulta_img);
        mysqli_stmt_bind_result($consulta_img, $imagen);
        mysqli_stmt_fetch($consulta_img);
        mysqli_stmt_close($consulta_img);
    }

    $stmt = mysqli_prepare($connec, "UPDATE vehiculos SET marca = ?, modelo = ?, descripcion = ?, precio = ?, inventario = ?, imagen = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssssisi", $marca, $modelo, $descripcion, $precio, $inventario, $imagen, $id);
    $resultado = mysqli_stmt_execute($stmt);

    if ($resultado) {
        echo "<script>alert('Vehículo modificado correctamente.'); window.location.href='vehiculos.php';</script>";
    } else {
        echo "<script>alert('Error al modificar el vehículo.');</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connec);
    exit();
}

// Obtener datos actuales
$stmt = mysqli_prepare($connec, "SELECT marca, modelo, descripcion, precio, inventario, imagen FROM vehiculos WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $marca, $modelo, $descripcion, $precio, $inventario, $imagen);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>
 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Vehículo</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/vehiculos_css/modificar_vehiculos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
    <?php include('../barras/navbar.php'); ?>
    <?php include('../barras/barra_lateral.php'); ?>
    
    <div class="form-container">
        <h2>Modificar Vehículo</h2>
        <form method="post" enctype="multipart/form-data">
            <label>Marca:</label>
            <input type="text" name="marca" value="<?= htmlspecialchars($marca) ?>" required>

            <label>Modelo:</label>
            <input type="text" name="modelo" value="<?= htmlspecialchars($modelo) ?>" required>

            <label>Descripción:</label>
            <textarea name="descripcion" rows="4" required><?= htmlspecialchars($descripcion) ?></textarea>

            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($precio) ?>" required>

            <label>Inventario:</label>
            <input type="number" name="inventario" value="<?= htmlspecialchars($inventario) ?>" required>

            <label>Imagen actual:</label><br>
            <img class="preview" src="../imagenes/<?= htmlspecialchars($imagen) ?>" alt="Imagen actual del vehículo"><br>

            <label>Cambiar imagen:</label>
            <input type="file" name="imagen" accept="image/*">

            <div class="form-buttons">
                <input type="submit" value="Guardar Cambios">
                <a href="vehiculos.php" class="cancel-btn">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
