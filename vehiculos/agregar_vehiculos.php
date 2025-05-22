<?php
include("../conexion.php");

if (isset($_POST['enviar'])) {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $fecha_agregado = date('Y-m-d');

    $imagen = $_FILES['imagen']['name'];
    $imagenTmp = $_FILES['imagen']['tmp_name'];
    $rutaImagen = "imagenes/" . basename($imagen);

    // Subir imagen a la tablita esa cosa fea.
    if (move_uploaded_file($imagenTmp, "../" . $rutaImagen)) {
        $stmt = mysqli_prepare($connec, "INSERT INTO vehiculos (marca, modelo, descripcion, precio, imagen, fecha_agregado) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssdss", $marca, $modelo, $descripcion, $precio, $rutaImagen, $fecha_agregado);
        $resultado = mysqli_stmt_execute($stmt);

        if ($resultado) {
            echo "<script>
                alert('Vehículo agregado correctamente.');
                window.opener.location.reload();
                window.close();
            </script>";
        } else {
            echo "<script>
                alert('Error al guardar el vehículo.');
                window.close();
            </script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>
            alert('Error al subir la imagen.');
            window.close();
        </script>";
    }

    mysqli_close($connec);
} else {
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Vehículo</title>
    <link rel="stylesheet" href="../css/vehiculos_css/agregar_vehiculos.css">
</head>
<body>
    <div class="form-container">
        <h2 class="form-title">Agregar Nuevo Vehículo</h2>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="marca">Marca:</label>
                <input type="text" name="marca" required>
            </div>

            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <input type="text" name="modelo" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" required></textarea>
            </div>

            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" step="0.01" name="precio" required>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" name="imagen" accept="image/*" required>
            </div>

            <div class="form-buttons">
                <input type="submit" name="enviar" value="Guardar" class="btn-submit">
                <a href="vehiculos.php" class="btn-cancel">Cerrar</a>
            </div>
        </form>
    </div>
</body>
</html>

<?php } ?>
