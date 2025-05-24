<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../login.php");
    exit();
}

$usuario = [
    'Usuario' => '',
    'Nombre' => '',
    'TipoUsuario' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = mysqli_prepare($connec, "SELECT Usuario, Nombre, TipoUsuario FROM usuarios WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);

    if (!$usuario) {
        echo "<script>alert('Empleado no encontrado.'); window.location.href='empleados.php';</script>";
        exit();
    }

    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $usuarioNuevo = $_POST['Usuario'];
    $nombreNuevo = $_POST['Nombre'];
    $tipoUsuarioNuevo = $_POST['TipoUsuario'];

    $stmt = mysqli_prepare($connec, "UPDATE usuarios SET Usuario = ?, Nombre = ?, TipoUsuario = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $usuarioNuevo, $nombreNuevo, $tipoUsuarioNuevo, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: empleados.php");
        exit();
    } else {
        echo "<script>alert('Error al actualizar el usuario.');</script>";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/empleados_css/modificar_empleados.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>

        <div class="main-container">
            <div class="form-container">
                <h2 class="form-title">Modificar empleado</h2>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                    <div class="form-group">
                        <label><i class="fa-regular fa-star"></i> Usuario</label>
                        <input type="text" name="Usuario" value="<?php echo htmlspecialchars($usuario['Usuario']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-cube"></i> Nombre</label>
                        <input type="text" name="Nombre" value="<?php echo htmlspecialchars($usuario['Nombre']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-chart-simple"></i> Tipo de Usuario</label>
                        <input type="text" name="TipoUsuario" value="<?php echo htmlspecialchars($usuario['TipoUsuario']); ?>" required>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-guardar">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="empleados.php" class="btn-cancelar">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
