<?php
include("../conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MODFICAR</title>
    <link rel="stylesheet" type="text/css" href="../css/agregar_empleados.css">
    <script>
        window.onload = function() {
            document.getElementsByName('nombre')[0].focus();
        }
    </script>
</head>
<body>
    <div class="form-container">
<?php
    if (isset($_POST['enviar'])) {
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $password = $_POST['password']; 
        $tipoUsuario = $_POST['tipoUsuario'];

        $stmt = mysqli_prepare($connec, "INSERT INTO empleados (nombre, usuario, password, TipoUsuario) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $nombre, $usuario, $password, $tipoUsuario);
        $resultado = mysqli_stmt_execute($stmt);

        if ($resultado) {
            echo "<script language='JavaScript'>
            alert('Empleado agregado correctamente.');
            window.opener.location.reload();
            window.close();
            </script>";
        } else {
            echo "<script language='JavaScript'>
            alert('ERROR al agregar empleado.');
            window.opener.location.reload();
            window.close();
            </script>";
        }

        mysqli_stmt_close($stmt);
        mysqli_close($connec);
    } else {
?>

    <h2 class="form-title">Agregar Nuevo Empleado</h2>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="tipoUsuario">Tipo de Usuario:</label>
            <input type="text" name="tipoUsuario" required>
        </div>

        <div class="form-buttons">
            <input type="submit" name="enviar" value="Guardar" class="btn-submit">
            <a href="#" onclick="window.close()" class="btn-cancel">Cerrar</a>
        </div>
    </form>

<?php } ?>
</div>
