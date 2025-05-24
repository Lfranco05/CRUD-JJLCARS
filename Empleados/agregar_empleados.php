<?php
include("../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $tipoUsuario = $_POST['tipoUsuario'];

    $stmt = mysqli_prepare($connec, "INSERT INTO usuarios (nombre, usuario, password, TipoUsuario) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nombre, $usuario, $password, $tipoUsuario);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($connec);
        echo "<script>
                alert('Empleado agregado correctamente.');
                window.location.href = 'empleados.php';
              </script>";
        exit;
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($connec);
        echo "<script>
                alert('Error al agregar empleado.');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Agregar Empleado</title>
    <link rel="stylesheet" href="../css/empleados_css/agregar_empleados.css" />
    <script>
        window.onload = function() {
            document.querySelector('[name="nombre"]').focus();
        };
    </script>
</head>
<body>
    <div class="form-container">
        <h2 class="form-title">Agregar Nuevo Empleado</h2>
        <form method="post" action="">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required />
            </div>

            <div class="form-group">
                <label>Usuario:</label>
                <input type="text" name="usuario" required />
            </div>

            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="password" required />
            </div>

            <div class="form-group">
                <label>Tipo de Usuario:</label>
                <input type="text" name="tipoUsuario" required />
            </div>

            <div class="form-buttons">
                <input type="submit" name="enviar" value="Guardar" class="btn-submit" />
                <a href="empleados.php" class="btn-cancel">Cerrar</a>
            </div>
        </form>
    </div>
</body>
</html>
