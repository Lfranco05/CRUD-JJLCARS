<?php
session_start();
include("../conexion.php");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $carnet = mysqli_real_escape_string($connec, $_POST['carnet']);
    $nombre = mysqli_real_escape_string($connec, $_POST['nombre']);
    $telefono = mysqli_real_escape_string($connec, $_POST['telefono']);
    $direccion = mysqli_real_escape_string($connec, $_POST['direccion']);
    $estado = mysqli_real_escape_string($connec, $_POST['estado']);
    $carrera = mysqli_real_escape_string($connec, $_POST['carrera']);
    $semestre = mysqli_real_escape_string($connec, $_POST['semestre']);
    $promedio = mysqli_real_escape_string($connec, $_POST['promedio']);

    $sql = "INSERT INTO alumno (carnet, nombre, telefono, direccion, estado, fecha_ingreso, ultima_actualizacion, carrera, semestre, promedio) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?)";
    
    $stmt = mysqli_prepare($connec, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssd", $carnet, $nombre, $telefono, $direccion, $estado, $carrera, $semestre, $promedio);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: estudiantes.php");
        exit();
    } else {
        $error = "Error al agregar el estudiante: " . mysqli_error($connec);
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Estudiante - Universidad San Pablo</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/agregar_estudiante.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>
        
        <div class="main-container">
            <div class="form-container">
                <h1 class="form-title">Agregar Nuevo Estudiante</h1>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="carnet">Carnet:</label>
                        <input type="text" id="carnet" name="carnet" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" required>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" required>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select id="estado" name="estado" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="carrera">Carrera:</label>
                        <input type="text" id="carrera" name="carrera" required>
                    </div>

                    <div class="form-group">
                        <label for="semestre">Semestre:</label>
                        <input type="number" id="semestre" name="semestre" min="1" max="10" required>
                    </div>

                    <div class="form-group">
                        <label for="promedio">Promedio:</label>
                        <input type="number" id="promedio" name="promedio" step="0.01" min="0" max="100" required>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Guardar
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