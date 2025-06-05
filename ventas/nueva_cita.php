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

// Procesar formulario si se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipoCita = $_POST["tipoCita"];
    $tipoCompra = $_POST["tipoCompra"] ?? null;
    $precio = floatval($_POST["precio"]);
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $status = "Pendiente";
    $vehiculo_id = isset($_POST['vehiculo_id']) ? intval($_POST['vehiculo_id']) : null;

    $stmt = mysqli_prepare($connec, "INSERT INTO citas (tipoCita, tipoCompra, precio, nombre, correo, fecha, hora, status, vehiculo_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssdsssssi", $tipoCita, $tipoCompra, $precio, $nombre, $correo, $fecha, $hora, $status, $vehiculo_id);

    
    if (mysqli_stmt_execute($stmt)) {
    header("Location: ventas.php");
    exit();
    } else {
    $mensaje = "Error al registrar la cita.";
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
    <style>
        .vehiculo-item {
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 10px;
            margin: 10px 0;
            transition: 0.3s;
            display: flex;
            gap: 20px;
            align-items: center;
            background-color: #f9f9f9;
        }
        .vehiculo-item:hover {
            border-color: #999;
            background-color: #eee;
        }
        .vehiculo-item.seleccionado {
            border-color: #7b2ff7;
            background-color: #f0e8ff;
        }
        .vehiculo-item img {
            width: 160px;
            height: auto;
            border-radius: 8px;
        }
        .vehiculos-lista {
            display: none;
        }
        .sub-menu {
            display: none;
        }
    </style>
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

                <!-- Se usa select solo para Servicio -->
    <div class="sub-menu" id="servicioOpciones">
    <label for="tipoCompraSelect">Tipo de Servicio</label>
    <select id="tipoCompraSelect" onchange="actualizarTipoCompra()">
        <option value="Servicio menor">Servicio menor</option>
        <option value="Servicio mayor">Servicio mayor</option>
        <option value="Revision de frenos">Revision de frenos</option>
    </select>
    </div>

<!-- Campo oculto real que se enviará en el formulario -->
<input type="hidden" name="tipoCompra" id="tipoCompra">


                <input type="hidden" name="vehiculo_id" id="vehiculo_id">

                <div class="vehiculos-lista" id="vehiculosLista">
                    <h3>Vehículos disponibles</h3>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                        <div class="vehiculo-item" onclick="seleccionarVehiculo(<?php echo $vehiculo['id']; ?>, <?php echo $vehiculo['precio']; ?>, this)">
                            <img src="../Imagen/<?php echo $vehiculo['imagen']; ?>" alt="Imagen del vehículo">
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

                <label for="precio">Precio</label>
                <input type="number" name="precio" id="precio" step="0.01" min="1" required>

                <label for="nombre">Nombre del Cliente</label>
                <input type="text" name="nombre" maxlength="100" required>

                <label for="correo">Correo del Cliente</label>
                <input type="email" name="correo" maxlength="100" required>

                <label for="fecha">Fecha de la Cita</label>
                <input type="date" name="fecha" required>

                <label for="hora">Hora de la Cita</label>
                <input type="time" name="hora" required>

                <div class="form-buttons">
                    <button type="submit" class="guardar">Guardar</button>
                    <a href="ventas.php" class="cerrar">Cerrar</a>
                </div>

            </form>

            <div class="volver">
            </div>
        </div>
    </div>
</div>

    <script>
    function mostrarOpciones() {
        const tipoCita = document.getElementById('tipoCita').value;
        const servicioOpciones = document.getElementById('servicioOpciones');
        const vehiculosLista = document.getElementById('vehiculosLista');
        const tipoCompra = document.getElementById('tipoCompra');

        // Mostrar y ocultar campos dependiendo lo que se seleccione
        servicioOpciones.style.display = (tipoCita === "Servicio") ? "block" : "none";
        vehiculosLista.style.display = (tipoCita === "Cotizacion" || tipoCita === "Test de manejo") ? "block" : "none";

        // Asignar tipoCompra dinámico
        if (tipoCita === "Servicio") {
            tipoCompra.value = document.getElementById("tipoCompraSelect").value;
        } else if (tipoCita === "Cotizacion" || tipoCita === "Test de manejo") {
            tipoCompra.value = tipoCita; // guarda lo seleccionado, test, cotizacion xd
        } else {
            tipoCompra.value = "";
        }
    }

    function actualizarTipoCompra() {
        const selectedValue = document.getElementById("tipoCompraSelect").value;
        document.getElementById("tipoCompra").value = selectedValue;
    }

    function seleccionarVehiculo(id, precio, element) {
        document.getElementById("vehiculo_id").value = id;
        document.getElementById("precio").value = precio;

        document.querySelectorAll(".vehiculo-item").forEach(item => {
            item.classList.remove("seleccionado");
        });
        element.classList.add("seleccionado");
    }
    </script>

</body>
</html>
