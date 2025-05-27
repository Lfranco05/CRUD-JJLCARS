<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipoCita = trim($_POST['tipoCita']);
    $tipoCompra = trim($_POST['tipoCompra']);
    $precio = floatval($_POST['precio']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $status = "Pendiente";

    if ($tipoCita && $tipoCompra && $precio > 0 && $nombre && $correo && $fecha && $hora) {
        $stmt = mysqli_prepare($connec, "INSERT INTO citas (tipoCita, tipoCompra, precio, nombre, correo, fecha, hora, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssdsssss", $tipoCita, $tipoCompra, $precio, $nombre, $correo, $fecha, $hora, $status);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ventas.php");
            exit();
        } else {
            $mensaje = "Error al guardar la cita.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $mensaje = "Todos los campos son obligatorios y el precio debe ser mayor a 0.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Cita - JJLCARS</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../../css/ventas_css/nueva_cita.css">
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>

        <div class="main-container">
            <div class="form-container">
                <h2>Registrar Nueva Cita</h2>

                <?php if (!empty($mensaje)): ?>
                    <p class="mensaje-error"><?php echo $mensaje; ?></p>
                <?php endif; ?>

                <form method="post" action="nueva_cita.php">
                    <label for="tipoCita">Tipo de Cita</label>
                    <select name="tipoCita" id="tipoCita" required>
                        <option value="">Seleccione</option>
                        <option value="Servicio">Servicio</option>
                        <option value="Cotizacion">Cotizacion</option>
                        <option value="Test de manejo">Test de manejo</option>
                    </select>

                    <label for="tipoCompra">Tipo de Compra</label>
                    <select name="tipoCompra" id="tipoCompra" required>
                        <option value="">Seleccione</option>
                        <option value="Servicio menor">Servicio menor</option>
                        <option value="Servicio mayor">Servicio mayor</option>
                        <option value="Revision de frenos">Revision de frenos</option>
                        <option value="Cotizacion de vehiculo">Cotizacion de vehiculo</option>
                        <option value="Test de manejo">Test de manejo</option>
                    </select>

                    <label for="precio">Precio</label>
                    <input type="number" name="precio" step="0.01" min="1" required>

                    <label for="nombre">Nombre del Cliente</label>
                    <input type="text" name="nombre" maxlength="100" required>

                    <label for="correo">Correo del Cliente</label>
                    <input type="email" name="correo" maxlength="100" required>

                    <label for="fecha">Fecha de la Cita</label>
                    <input type="date" name="fecha" required>

                    <label for="hora">Hora de la Cita</label>
                    <input type="time" name="hora" required>

                    <input type="submit" value="Guardar Cita">
                </form>

                <div class="volver">
                    <a href="ventas.php">← Volver a Ventas</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
