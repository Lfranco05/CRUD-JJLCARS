<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

$query = "
    SELECT 
        DATE_FORMAT(fecha, '%Y-%m') AS mes, 
        tipoCompra, 
        SUM(precio) AS total 
    FROM citas 
    WHERE status = 'Aprobada' 
    GROUP BY mes, tipoCompra 
    ORDER BY mes ASC
";

$resultado = mysqli_query($connec, $query);

$datos = [];
$categorias = [];

while ($row = mysqli_fetch_assoc($resultado)) {
    $mes = $row['mes'];
    $tipo = $row['tipoCompra'];
    $total = $row['total'];

    if (!isset($datos[$tipo])) {
        $datos[$tipo] = [];
    }
    $datos[$tipo][$mes] = $total;
    if (!in_array($mes, $categorias)) {
        $categorias[] = $mes;
    }
}

sort($categorias);

$datasets = [];
$colores = [
    "Servicio menor" => "rgba(123, 47, 247, 0.6)",
    "Servicio mayor" => "rgba(255, 99, 132, 0.6)",
    "Revision de frenos" => "rgba(54, 162, 235, 0.6)",
    "Cotizacion de vehiculo" => "rgba(255, 206, 86, 0.6)",
    "Test de manejo" => "rgba(75, 192, 192, 0.6)"
];

foreach ($datos as $tipoCompra => $ventasPorMes) {
    $datosMensuales = [];
    foreach ($categorias as $mes) {
        $datosMensuales[] = isset($ventasPorMes[$mes]) ? $ventasPorMes[$mes] : 0;
    }

    $datasets[] = [
        "label" => $tipoCompra,
        "data" => $datosMensuales,
        "backgroundColor" => $colores[$tipoCompra] ?? "rgba(153, 102, 255, 0.6)"
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyección de Ventas por Categoría</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../css/proyecciones_css/proyeccion_ventas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-container {
            padding: 2rem;
            background: #f9f9f9;
            max-width: 1100px; /* Ancho ajustado */
            margin: 0 auto;
        }
        canvas {
            width: 100%;
            height: 500px; /* Altura ajustada */
            display: block;
            margin: 2rem auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>

        <div class="main-container">
            <h2>Proyección de Ventas por Categoría</h2>
            <canvas id="ventasPorTipo"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('ventasPorTipo').getContext('2d');

        const ventasChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($categorias); ?>,
                datasets: <?php echo json_encode($datasets); ?>
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Ventas en $'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Mes'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ": $" + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
