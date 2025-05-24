<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID no válido.";
    exit();
}

$id = (int)$_GET['id'];
$mensaje = "";

$stmt = mysqli_prepare($connec, "SELECT Usuario, Nombre, TipoUsuario FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $usuario, $nombre, $tipoUsuario);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $nombre = trim($_POST['nombre']);
    $tipoUsuario = trim($_POST['tipoUsuario']);

    if ($usuario && $nombre && $tipoUsuario) {
        $stmt = mysqli_prepare($connec, "UPDATE usuarios SET Usuario = ?, Nombre = ?, TipoUsuario = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $usuario, $nombre, $tipoUsuario, $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: empleados.php");
            exit(); 
        } else {
            $mensaje = "Error al actualizar: " . mysqli_error($connec);
        }
        mysqli_stmt_close($stmt);
    } else {
        $mensaje = "Completa los campos";
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Empleado</title>
    <link rel="stylesheet" type="text/css" href="../css/empleados_css/modificar_empleados.css">
</head>
<body>
    <div class="form-container">
        <h2>Modificar Empleado</h2>
        <?php if ($mensaje) echo "<p class='mensaje'>$mensaje</p>"; ?>
        <form method="POST">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" value="<?php echo htmlspecialchars($usuario); ?>" required>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

            <label for="tipoUsuario">Tipo de Usuario:</label>
            <select name="tipoUsuario" id="tipoUsuario" required>
                <option value="Gerente" <?php if ($tipoUsuario == "Gerente") echo "selected"; ?>>Gerente</option>
                <option value="Vendedor" <?php if ($tipoUsuario == "Vendedor") echo "selected"; ?>>Vendedor</option>
            </select>

            <input type="submit" value="Guardar Cambios">
            <a href="empleados.php" class="cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>
