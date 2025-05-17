<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Consultas para estadísticas
$sql_total = "SELECT COUNT(*) as total FROM alumno";
$resultado_total = mysqli_query($connec, $sql_total);
$total_alumnos = mysqli_fetch_assoc($resultado_total)['total'];

// Promedio general de todos los alumnos
$sql_promedio = "SELECT AVG(promedio) as promedio_general FROM alumno";
$resultado_promedio = mysqli_query($connec, $sql_promedio);
$promedio_general = mysqli_fetch_assoc($resultado_promedio)['promedio_general'];

// Estudiantes por carrera
$sql_carreras = "SELECT carrera, COUNT(*) as cantidad FROM alumno GROUP BY carrera";
$resultado_carreras = mysqli_query($connec, $sql_carreras);

// Estudiantes por semestre
$sql_semestres = "SELECT semestre, COUNT(*) as cantidad FROM alumno GROUP BY semestre ORDER BY semestre";
$resultado_semestres = mysqli_query($connec, $sql_semestres);

// Alumnos recientes
$sql_recientes = "SELECT carnet, nombre, carrera, semestre, promedio FROM alumno ORDER BY fecha_registro DESC LIMIT 5";
$resultado_recientes = mysqli_query($connec, $sql_recientes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Universidad San Pablo</title>
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/barra_lateral.css">
    <link rel="stylesheet" type="text/css" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/sidebar.js"></script>  <!-- Agregada la referencia al script -->
</head>
<body>
    <div class="wrapper">
        <?php include('../barras/navbar.php'); ?>
        <?php include('../barras/barra_lateral.php'); ?>
        
        <div class="main-container">
            <h1>Dashboard de Alumnos</h1>
            
            <div class="stats-container">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h3>Total Alumnos</h3>
                        <p><?php echo $total_alumnos; ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <div class="stat-info">
                        <h3>Promedio General</h3>
                        <p><?php echo number_format($promedio_general, 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="charts-container">
                <div class="chart-card">
                    <h3>Alumnos por Carrera</h3>
                    <canvas id="carrerasChart"></canvas>
                </div>
                
                <div class="chart-card">
                    <h3>Alumnos por Semestre</h3>
                    <canvas id="semestresChart"></canvas>
                </div>
            </div>

            <div class="recent-container">
                <h3>Alumnos Recientes</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Carnet</th>
                                <th>Nombre</th>
                                <th>Carrera</th>
                                <th>Semestre</th>
                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($alumno = mysqli_fetch_assoc($resultado_recientes)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($alumno['carnet']); ?></td>
                                    <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($alumno['carrera']); ?></td>
                                    <td><?php echo htmlspecialchars($alumno['semestre']); ?></td>
                                    <td><?php echo htmlspecialchars($alumno['promedio']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos para el gráfico de carreras
        const carrerasData = {
            labels: [<?php 
                mysqli_data_seek($resultado_carreras, 0);
                while ($carrera = mysqli_fetch_assoc($resultado_carreras)) {
                    echo '"' . $carrera['carrera'] . '",';
                }
            ?>],
            datasets: [{
                label: 'Alumnos por Carrera',
                data: [<?php 
                    mysqli_data_seek($resultado_carreras, 0);
                    while ($carrera = mysqli_fetch_assoc($resultado_carreras)) {
                        echo $carrera['cantidad'] . ',';
                    }
                ?>],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ]
            }]
        };

        // Datos para el gráfico de semestres
        const semestresData = {
            labels: [<?php 
                mysqli_data_seek($resultado_semestres, 0);
                while ($semestre = mysqli_fetch_assoc($resultado_semestres)) {
                    echo '"Semestre ' . $semestre['semestre'] . '",';
                }
            ?>],
            datasets: [{
                label: 'Alumnos por Semestre',
                data: [<?php 
                    mysqli_data_seek($resultado_semestres, 0);
                    while ($semestre = mysqli_fetch_assoc($resultado_semestres)) {
                        echo $semestre['cantidad'] . ',';
                    }
                ?>],
                backgroundColor: '#36A2EB',
                borderColor: '#2980b9',
                borderWidth: 1
            }]
        };

        // Configuración de los gráficos
        new Chart(document.getElementById('carrerasChart').getContext('2d'), {
            type: 'pie',
            data: carrerasData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        new Chart(document.getElementById('semestresChart').getContext('2d'), {
            type: 'bar',
            data: semestresData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>