<?php
session_start();
include("../conexion.php");

// Seguridad xd
if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Verifica id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: clientes.php");
    exit();
}

$id = (int)$_GET['id'];
$mensaje = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $correo = trim($_POST['correo']);
    // $telefono = trim($_POST['telefono']); por el momento esto esta desactivado para adaptarlo a la nueva base

    // lo cambia en la base de datos
    $stmt = mysqli_prepare($connec, "UPDATE clientes SET nombre = ?, usuario = ?, correo = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $usuario, $correo, $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: clientes.php");
        exit();
    } else {
        $mensaje = "Error al actualizar cliente.";
        mysqli_stmt_close($stmt);
    }
}


$stmt = mysqli_prepare($connec, "SELECT nombre, usuario, correo FROM clientes WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$cliente = mysqli_fetch_assoc($resultado);

if (!$cliente) {
    header("Location: clientes.php");
    exit();
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes - JJLCARS</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../css/clientes_css/modificar_clientes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="wrapper">
    <?php include('../barras/navbar.php'); ?>
    <?php include('../barras/barra_lateral.php'); ?>

    <div class="main-container">
        <h1>Modificar Cliente</h1>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required value="<?php echo htmlspecialchars($cliente['nombre']); ?>">

            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" required value="<?php echo htmlspecialchars($cliente['usuario']); ?>">

            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" required value="<?php echo htmlspecialchars($cliente['correo']); ?>">

            <input type="submit" value="Guardar cambios">
            <a href="clientes.php" class="volver">Cancelar</a>
        </form>
    </div>
</div>
</body>
</html>
