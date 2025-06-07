<?php
include("../conexion.php");
include("../verificar_acceso.php");
verificarRol(['gerente']);

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $nuevaPassword = $_POST['nueva_password'];

    // Verificar si el usuario y el nombre existen
    $stmt = mysqli_prepare($connec, "SELECT id FROM usuarios WHERE Nombre = ? AND Usuario = ?");
    mysqli_stmt_bind_param($stmt, "ss", $nombre, $usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) === 1) {
        // Usuario válido, proceder a actualizar contraseña
        mysqli_stmt_bind_result($stmt, $id_usuario);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        $updateStmt = mysqli_prepare($connec, "UPDATE usuarios SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($updateStmt, "si", $passwordHash, $id_usuario);
        
       if (mysqli_stmt_execute($updateStmt)) {
        header("Location: empleados.php");
         exit();
        } else {
            $mensaje = "Error al actualizar la contraseña.";
        }
        mysqli_stmt_close($updateStmt);
    } else {
        $mensaje = "Nombre o usuario incorrecto.";
        mysqli_stmt_close($stmt);
    }

    mysqli_close($connec);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reiniciar Contraseña</title>
    <link rel="stylesheet" href="../css/empleados_css/agregar_empleados.css">
    <style>
        .form-container {
            margin-top: 50px;
        }
        .mensaje {
            margin-bottom: 15px;
            text-align: center;
            color:rgb(0, 255, 157);
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2 class="form-title">Reiniciar Contraseña</h2>
    <?php if ($mensaje): ?>
        <p class="mensaje"><?php echo $mensaje; ?></p>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Nombre completo:</label>
            <input type="text" name="nombre" required />
        </div>
        <div class="form-group">
            <label>Usuario:</label>
            <input type="text" name="usuario" required />
        </div>
        <div class="form-group">
            <label>Nueva Contraseña:</label>
            <input type="password" name="nueva_password" required />
        </div>
        <div class="form-buttons">
            <input type="submit" value="Actualizar" class="btn-submit" />
            <a href="empleados.php" class="btn-cancel">Volver</a>
        </div>
    </form>
</div>
</body>
</html>
