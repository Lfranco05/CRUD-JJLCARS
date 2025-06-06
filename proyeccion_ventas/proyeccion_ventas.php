<?php
session_start();
include("../conexion.php");
include("../verificar_acceso.php");
verificarRol(['gerente']);

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

// Ventas por categorías
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

    if ($tipo === "Test de manejo") continue;

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
    "Servicio menor" => "rgba(47, 247, 137, 0.6)",
    "Servicio mayor" => "rgba(255, 99, 132, 0.6)",
    "Revision de frenos" => "rgba(54, 162, 235, 0.6)",
    "Cotizacion de vehiculo" => "rgba(255, 206, 86, 0.6)"
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

// Conteo de estados
$estadoQuery = "SELECT status, COUNT(*) AS cantidad FROM citas GROUP BY status";
$estadoResult = mysqli_query($connec, $estadoQuery);

$estados = [];
$cantidades = [];

while ($row = mysqli_fetch_assoc($estadoResult)) {
    $estados[] = ucfirst($row['status']);
    $cantidades[] = $row['cantidad'];
}

// Total de ventas por mes
$totalQuery = "
    SELECT 
        DATE_FORMAT(fecha, '%Y-%m') AS mes, 
        SUM(precio) AS total 
    FROM citas 
    WHERE status = 'Aprobada' 
    GROUP BY mes 
    ORDER BY mes ASC
";
$totalResult = mysqli_query($connec, $totalQuery);

$mesesTotales = [];
$totalesMensuales = [];

while ($row = mysqli_fetch_assoc($totalResult)) {
    $mesesTotales[] = $row['mes'];
    $totalesMensuales[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyección de Ventas</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/barra_lateral.css">
    <link rel="stylesheet" href="../css/proyecciones_css/proyeccion_ventas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f6f1e9;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            padding: 2rem;
            background: #ffffff;
            max-width: 1100px;
            margin: 0 auto;
            border-radius: 16px;
        }

        canvas {
            width: 100% !important;
            max-height: 400px;
            display: block;
            margin: 2rem auto;
        }

        h2 {
            text-align: center;
            margin-top: 2rem;
            color: #1b1f3b;
        }

        .chart-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2rem;
            margin-top: 2rem;
        }

        .chart-container {
            flex: 1;
            min-width: 45%;
            background-color: #f3f4f6;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 0 8px rgba(0,0,0,0.05);
        }

        .chart-container h2 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
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

            <div class="chart-row">
                <div class="chart-container">
                    <h2>Distribución de Citas por Estado</h2>
                    <canvas id="estadoCitas"></canvas>
                </div>
                <div class="chart-container">
                    <h2>Resumen Total de Ventas por Mes</h2>
                    <canvas id="totalMensual"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        new Chart(document.getElementById('ventasPorTipo').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($categorias); ?>,
                datasets: <?php echo json_encode($datasets); ?>
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            
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
                            label: context => context.dataset.label + ": $" + context.parsed.y.toLocaleString()
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('estadoCitas').getContext('2d'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($estados); ?>,
                datasets: [{
                    label: 'Estado de Citas',
                    data: <?php echo json_encode($cantidades); ?>,
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: 'rgba(255,255,255,0.9)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: context => context.label + ": " + context.parsed + " citas"
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('totalMensual').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($mesesTotales); ?>,
                datasets: [{
                    label: 'Total de Ventas',
                    data: <?php echo json_encode($totalesMensuales); ?>,
                    fill: false,
                    borderColor: 'rgb(178, 207, 109)',
                    backgroundColor: 'rgba(54, 162, 235, 0.4)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
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
                            label: context => "Total: $" + context.parsed.y.toLocaleString()
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
