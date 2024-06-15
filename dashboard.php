<?php
// Importar Google2FA
use PragmaRX\Google2FA\Google2FA;

session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si el usuario es administrador
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT rol FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Desk - Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Help Desk</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="view_tickets.php">Ver Tickets</a>
                </li>
                <?php if ($role === 'user'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="create_ticket.php">Crear Ticket</a>
                </li>
                <?php endif; ?>
                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Administrar Usuarios</a>
                    </li>
                <?php endif; ?>
            </ul>
            <form class="form-inline" method="POST">
                <button class="btn btn-outline-danger my-2 my-sm-0" type="submit" name="logout">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Contenido dinámico según la opción seleccionada del menú -->
        <h2>Bienvenido al sistema de Help Desk</h2>
        <p>Utiliza el menú de navegación para ver tus tickets o crear un nuevo ticket.</p>
    </div>
</body>
</html>
