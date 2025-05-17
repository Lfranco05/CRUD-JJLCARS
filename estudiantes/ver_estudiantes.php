<?php
session_start();
include("../conexion.php");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id'])) {
    header("Location: estudiantes.php");
    exit();
}

// Obtener los datos del estudiante
$id = mysqli_real_escape_string($connec, $_GET['id']);
$stmt = mysqli_prepare($connec, "SELECT * FROM alumno WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$estudiante = mysqli_fetch_assoc($resultado);

// Verificar si se encontró el estudiante
if (!$estudiante) {
    header("Location: estudiantes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Estudiante - Universidad San Pablo</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/ver_estudiantes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php 
        include('../barras/navbar.php');
        include('../barras/barra_lateral.php');
        ?>

        <div class="main-container">
            <div class="detalles-container">
                <h2 class="detalles-titulo">Detalles del Estudiante</h2>
                
                <div class="detalle-campo">
                    <span class="detalle-etiqueta">ID:</span>
                    <div class="detalle-valor"><?php echo htmlspecialchars($estudiante['id']); ?></div>
                </div>

                <div class="detalle-campo">
                    <span class="detalle-etiqueta">Nombre:</span>
                    <div class="detalle-valor"><?php echo htmlspecialchars($estudiante['nombre']); ?></div>
                </div>

                <div class="detalle-campo">
                    <span class="detalle-etiqueta">Teléfono:</span>
                    <div class="detalle-valor"><?php echo htmlspecialchars($estudiante['telefono']); ?></div>
                </div>

                <div class="detalle-campo">
                    <span class="detalle-etiqueta">Dirección:</span>
                    <div class="detalle-valor"><?php echo htmlspecialchars($estudiante['direccion']); ?></div>
                </div>

                <div class="detalle-campo">
                    <span class="detalle-etiqueta">Carrera:</span>
                    <div class="detalle-valor"><?php echo htmlspecialchars($estudiante['carrera']); ?></div>
                </div>

                <div class="detalle-campo">
                    <span class="detalle-etiqueta">Semestre:</span>
                    <div class="detalle-valor"><?php echo htmlspecialchars($estudiante['semestre']); ?></div>
                </div>

                <div class="detalle-campo">
                    <span class="detalle-etiqueta">Estado:</span>
                    <div class="detalle-valor"><?php echo htmlspecialchars($estudiante['estado']); ?></div>
                </div>

                <div class="botones-accion">
                    <a href="estudiantes.php" class="btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>