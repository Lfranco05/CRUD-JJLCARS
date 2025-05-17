<?php
session_start();
require_once 'conexion.php';

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $username = $_SESSION['username'];
    $upload_dir = 'avatars/';

    // Crear el directorio si no existe
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Validar el tipo de imagen
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($_FILES['avatar']['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=tipo_no_valido');
        exit();
    }

    // Validar tamaño del archivo (máx 2MB)
    if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=archivo_grande');
        exit();
    }

    // Generar nombre seguro
    $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $safe_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($_FILES['avatar']['name']));
    $avatar_path = $upload_dir . $safe_filename;

    // Mover archivo y actualizar BD
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
        $stmt = $connec->prepare("UPDATE usuario SET avatar = ? WHERE username = ?");
        $stmt->bind_param("ss", $avatar_path, $username);
        $stmt->execute();

        // Guardar ruta actualizada en sesión
        $_SESSION['avatar_path'] = $avatar_path;

        $stmt->close();
    }
}

// Redirigir de vuelta
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>