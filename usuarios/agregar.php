<?php
include("conexion.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar</title>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script>
        window.onload = function() {
            document.getElementsByName('nombre')[0].focus();
        }
    </script>
</head>
<body>
    <div class="form-container">
    <?php
        if(isset($_POST['enviar'])){
            $nocarne=$_POST['carnet'];
            $nombre=$_POST['nombre'];            
            $telefono=$_POST['telefono'];
            $direccion=$_POST['direccion'];

            $sql="insert into alumnos(carnet,nombre,telefono,direccion)
            values(".$nocarne.",'".$nombre."',".$telefono.",'".$direccion."')";

            $resultado=mysqli_query($connec,$sql);
            
            if($resultado){
                echo "<script language='JavaScript'>
                alert('Los Datos Fueron Ingresados Correctamente a la BD');
                window.opener.location.reload();
                window.close();
                </script>";
            }else{
                echo "<script language='JavaScript'>
                alert('ERROR: Los Datos NO Fueron Ingresados Correctamente a la BD');
                window.opener.location.reload();
                window.close();
                </script>";
            }
            mysqli_close($connec);

        }else{  
    ?>

        <h1>Agregar Nuevo Alumno</h1>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <label>No. Carnet: </label>
            <input type="text" name="carnet"> <br>
            <label>Nombre: </label>
            <input type="text" name="nombre"> <br>
            <label>No. de Teléfono: </label>
            <input type="text" name="telefono"> <br>
            <label>Dirección: </label>
            <input type="text" name="direccion"> <br>
            <input type="submit" name="enviar" value="GUARDAR">
            <a href="#" onclick="window.close()" class="back">Cerrar</a>
        </form>
    <?php
        }
    ?>
    </div>
</body>
</html>