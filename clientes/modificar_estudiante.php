<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = mysqli_prepare($connec, "SELECT nombre, telefono, direccion, carrera, semestre FROM alumno WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $estudiante = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $carrera = $_POST['carrera'];
    $semestre = $_POST['semestre'];
    
    $stmt = mysqli_prepare($connec, "UPDATE alumno SET nombre = ?, telefono = ?, direccion = ?, carrera = ?, semestre = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssssi", $nombre, $telefono, $direccion, $carrera, $semestre, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: estudiantes.php");
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
    <title>Modificar Estudiante - Universidad San Pablo</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/modificar_estudiantes.css">
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
                <h2 class="form-title">Modificar Estudiante</h2>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($estudiante['nombre']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="text" name="telefono" value="<?php echo htmlspecialchars($estudiante['telefono']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Dirección:</label>
                        <input type="text" name="direccion" value="<?php echo htmlspecialchars($estudiante['direccion']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Carrera:</label>
                        <input type="text" name="carrera" value="<?php echo htmlspecialchars($estudiante['carrera']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Semestre:</label>
                        <input type="text" name="semestre" value="<?php echo htmlspecialchars($estudiante['semestre']); ?>" required>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="estudiantes.php" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>