<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario ha iniciado sesión y si tiene uno de los roles permitidos.
 *
 * @param array $rolesPermitidos Lista de roles permitidos gerente y vendedor
 */
function verificarRol($rolesPermitidos = []) {
    // Validar que la sesión esté activa
    if (!isset($_SESSION['Usuario']) || !isset($_SESSION['TipoUsuario']) || !isset($_SESSION['usuarioingresando']) || $_SESSION['usuarioingresando'] !== true) {
        header("Location: ../login.php");
        exit;
    }

    $rolUsuario = strtolower(trim($_SESSION['TipoUsuario']));

    // Si el rol del usuario no está en la lista permitida
    if (!in_array($rolUsuario, $rolesPermitidos)) {
        header("Location: ../sin_acceso.php");
        exit;
    }
}
