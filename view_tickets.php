<?php
require 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el rol del usuario
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT rol FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_role);
$stmt->fetch();
$stmt->close();

// Obtener tickets basado en el rol del usuario
if ($user_role == 'admin') {
    $result = $mysqli->query("SELECT * FROM tickets");
} else {
    $result = $mysqli->query("SELECT * FROM tickets WHERE user_id = $user_id AND status != 'Cancelled'");
}

// Función para cancelar un ticket
if (isset($_POST['cancel_ticket'])) {
    $ticket_id = $_POST['ticket_id'];
    
    $stmt = $mysqli->prepare("UPDATE tickets SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->close();
    header("Location: view_tickets.php");
}

// Función para finalizar un ticket
if (isset($_POST['finalize_ticket'])) {
    $ticket_id = $_POST['ticket_id'];
    
    $stmt = $mysqli->prepare("UPDATE tickets SET status = 'closed' WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->close();
}

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
    <title>Visualización de Tickets</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <header class="mb-4">
            <div class="row">
                <div class="col">
                    <a href="dashboard.php" class="btn btn-primary">Volver a Menu</a>
                </div>
                <div class="col-6 text-right">
            <form method="POST">
                <button class="btn btn-outline-danger my-2 my-sm-0" type="submit" name="logout">Logout</button>
            </form>
            </div>
            </div>
        </header>

        <h2 class="mb-4">Tickets Pendientes</h2>
        
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $row['subject']; ?></h5>
                    <p class="card-text"><?php echo $row['description']; ?></p>
                    <p class="card-text">Status: <?php echo $row['status']; ?></p>
                    <?php if ($user_role == 'admin' && $row['status'] == 'open'): ?>
                        <form method="POST">
                            <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-success" name="finalize_ticket">Finalizar</button>
                        </form>
                    <?php elseif ($user_role == 'user' && $row['status'] != 'cancelled'): ?>
                        <form method="POST">
                            <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-danger" name="cancel_ticket">Cancelar Ticket</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        
    </div>
</body>
</html>
