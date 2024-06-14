<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $subject = htmlspecialchars($_POST['subject']); // Sanitizar el input del usuario
    $description = htmlspecialchars($_POST['description']); // Sanitizar el input del usuario

    $stmt = $mysqli->prepare("INSERT INTO tickets (user_id, subject, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $subject, $description);
    $stmt->execute();
    $stmt->close();

    // Redirigir a la página de visualización de tickets o a donde sea necesario
    header("Location: view_tickets.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Ticket - Sistema de Helpdesk</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container mt-5">
        <div class="row mb-3">
            <div class="col-6">
                <a href="dashboard.php" class="btn btn-secondary">Volver a Menú</a>
            </div>
            <div class="col-6 text-right">
            <form method="POST">
                <button class="btn btn-outline-danger my-2 my-sm-0" type="submit" name="logout">Logout</button>
            </form>
            </div>
        </div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Crear Nuevo Ticket
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="subject">Asunto</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Ingrese el asunto del ticket" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control" id="description" name="description" rows="5" placeholder="Ingrese la descripción del ticket" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Crear Ticket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
