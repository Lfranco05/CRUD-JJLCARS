<?php
include("../conexion.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MODFICAR</title>
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
            $usuario=$_POST['usuario'];
            $nombre=$_POST['nombre'];
            $telefono=$_POST['telefono'];
            $direccion=$_POST['direccion'];

            $sql="update alumnos set nombre='".$nombre."', telefono='".$telefono."',
            direccion='".$direccion."' where carnet='".$carnet."'";
            $resultado=mysqli_query($connec,$sql);

            if($resultado){
                 echo "<script language='JavaScript'>
                        alert('Los datos se actualizaron correctamente');
                        window.opener.location.reload();
                        window.close();
                        </script>";
            }else{
                echo "<script language=''JavaScript>
                alert('Los datos NO se actualizaron correctamente');
                window.opener.location.reload();
                window.close();
                </script>";
            }
            mysqli_close($connec);
    
        }else{  
            $usuario=$_GET['Usuario'];
            $sql="select * from Usuarios where carnet ='".$usuario."'";
            $resultado=mysqli_query($connec,$sql);
            $fila=mysqli_fetch_assoc($resultado);
            $nombre=$fila["nombre"];            
            $telefono=$fila["telefono"];
            $direccion=$fila["direccion"];
            mysqli_close($connec);
    ?>

        <h1>Editar o Modificar Alumno</h1>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <label>Nombre: </label>
            <input type="text" name="nombre" value="<?php echo $nombre; ?> "> <br>
            <label>No. de Teléfono: </label>
            <input type="text" name="telefono" value="<?php echo $telefono?>"> <br>
            <label>Dirección: </label>
            <input type="text" name="direccion" value="<?php echo $direccion?>"> <br>
            <input type="hidden" name="carnet" value="<?php echo $carnet?>">
            <input type="submit" name="enviar" value="Modificar">
            <a href="#" onclick="window.close()" class="back">Cerrar</a>
        </form>
    <?php
        }
    ?>
    </div>
</body>
</html>