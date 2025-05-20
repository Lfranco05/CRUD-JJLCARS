<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = mysqli_prepare($connec, "SELECT Usuario, Nombre FROM usuarios WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $usuarioNuevo = $_POST['Usuario'];
    $nombreNuevo = $_POST['Nombre'];
    
    $stmt = mysqli_prepare($connec, "UPDATE usuarios SET Usuario = ?, Nombre = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $usuarioNuevo, $nombreNuevo, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: empleados.php");
        exit();
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario - Universidad San Pablo</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/modificar_usuario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php 
        include('../barras/navbar.php');
        include('../barras/barra_lateral.php');
        ?>

        <div class="main-container">
            <div class="form-container">
                <h2 class="form-title">Modificar Usuario</h2>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                    <div class="form-group">
                        <label>Nombre de Usuario:</label>
                        <input type="text" name="Usuario" value="<?php echo htmlspecialchars($usuario['Usuario']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nombre Completo:</label>
                        <input type="text" name="Nombre" value="<?php echo htmlspecialchars($usuario['Nombre']); ?>" required>
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
