<?php
session_start();
include("../conexion.php");
include("../verificar_acceso.php");
verificarRol(['gerente']);

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: login.php");
    exit();
}

// Ventas por categorías por mes 
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
        $datosMensuales[] = $ventasPorMes[$mes] ?? 0;
    }
    $datasets[] = [
        "label" => $tipoCompra,
        "data" => $datosMensuales,
        "backgroundColor" => $colores[$tipoCompra] ?? "rgba(153, 102, 255, 0.6)"
    ];
}

// Conteo de estados de las citas aksjkdajkd
$estadoQuery = "SELECT status, COUNT(*) AS cantidad FROM citas GROUP BY status";
$estadoResult = mysqli_query($connec, $estadoQuery);

$estados = [];
$cantidades = [];
while ($row = mysqli_fetch_assoc($estadoResult)) {
    $estados[] = ucfirst($row['status']);
    $cantidades[] = $row['cantidad'];
}

// Total de ventas por día
$totalQuery = "
    SELECT 
        DATE(fecha) AS dia, 
        SUM(precio) AS total 
    FROM citas 
    WHERE status = 'Aprobada' 
    GROUP BY dia 
    ORDER BY dia ASC
";
$totalResult = mysqli_query($connec, $totalQuery);

$diasTotales = [];
$totalesDiarios = [];
while ($row = mysqli_fetch_assoc($totalResult)) {
    $diasTotales[] = $row['dia'];
    $totalesDiarios[] = $row['total'];
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
        .main-container {
            padding: 2rem;
            background: #f6f1e9;
            max-width: 1300px;
            margin: 0 auto;
        }
        .grafica-principal canvas {
            width: 100% !important;
            height: 500px !important;
        }
        .graficas-secundarias {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
            margin-top: 3rem;
        }
        .grafica-secundaria {
            flex: 1;
            min-width: 400px;
        }
        canvas {
            display: block;
            margin: auto;
        }
        h2 {
            text-align: center;
            margin-top: 2rem;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>

        <div class="main-container">
            <h2>Proyección de Ventas por Categoría</h2>
            <div class="grafica-principal">
                <canvas id="ventasPorTipo"></canvas>
            </div>

            <div class="graficas-secundarias">
                <div class="grafica-secundaria">
                    <h2>Distribución de Citas por Estado</h2>
                    <canvas id="estadoCitas"></canvas>
                </div>
                <div class="grafica-secundaria">
                    <h2>Resumen Diario de Ventas</h2>
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
                            label: ctx => ctx.dataset.label + ": $" + ctx.parsed.y.toLocaleString()
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
                            label: ctx => ctx.label + ": " + ctx.parsed + " citas"
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('totalMensual').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($diasTotales); ?>,
                datasets: [{
                    label: 'Total de Ventas Diarias',
                    data: <?php echo json_encode($totalesDiarios); ?>,
                    fill: false,
                    borderColor: 'rgb(0, 150, 255)',
                    backgroundColor: 'rgba(0, 150, 255, 0.2)',
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
                            text: 'Día'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => "Total: $" + ctx.parsed.y.toLocaleString()
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
