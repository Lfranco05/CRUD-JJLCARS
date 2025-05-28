<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

// Obtener vehículos desde la base de datos
$vehiculos = [];
$result = mysqli_query($connec, "SELECT * FROM vehiculos");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $vehiculos[] = $row;
    }
}

$mensaje = "";

// Procesamiento del formulario omitido por brevedad, ya lo tienes implementado...
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
                <select name="tipoCita" id="tipoCita" required onchange="mostrarOpciones()">
                    <option value="">Seleccione</option>
                    <option value="Servicio">Servicio</option>
                    <option value="Cotizacion">Cotizacion</option>
                    <option value="Test de manejo">Test de manejo</option>
                </select>

                <!-- esta wea de aca despliega un menu para el coso de servicios -->
                <div class="sub-menu" id="servicioOpciones">
                    <label for="tipoCompra">Tipo de Servicio</label>
                    <select name="tipoCompra" id="tipoCompra">
                        <option value="Servicio menor">Servicio menor</option>
                        <option value="Servicio mayor">Servicio mayor</option>
                        <option value="Revision de frenos">Revision de frenos</option>
                    </select>
                </div>

                <!-- menu para vehiculos, tomandolo desde la tabla de vehiculos, espero funciones paps -->
                <div class="vehiculos-lista" id="vehiculosLista">
                    <h3>Vehículos disponibles</h3>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                        <div class="vehiculo-item">
                            <img src="../Imagen/<?php echo $vehiculo['imagen']; ?>" alt="Imagen del vehículo" >
                            <div>
                                <p><strong>ID:</strong> <?php echo $vehiculo['id']; ?></p>
                                <p><strong>Marca:</strong> <?php echo $vehiculo['marca']; ?></p>
                                <p><strong>Modelo:</strong> <?php echo $vehiculo['modelo']; ?></p>
                                <p><strong>Precio:</strong> $<?php echo number_format($vehiculo['precio'], 2); ?></p>
                                <p><strong>Inventario:</strong> <?php echo $vehiculo['inventario']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- el resto de cosas que pide falta modificar -->
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

<script>
    function mostrarOpciones() {
        const tipoCita = document.getElementById('tipoCita').value;
        const servicioOpciones = document.getElementById('servicioOpciones');
        const vehiculosLista = document.getElementById('vehiculosLista');

        servicioOpciones.style.display = tipoCita === "Servicio" ? "block" : "none";
        vehiculosLista.style.display = (tipoCita === "Cotizacion" || tipoCita === "Test de manejo") ? "block" : "none";
    }
</script>
</body>
</html>
