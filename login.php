<?php
// Importar Google2FA
use PragmaRX\Google2FA\Google2FA;

require 'vendor/autoload.php'; // Asegúrate de cargar Google2FA con Composer
require 'database.php'; // Asegúrate de que este archivo establece la conexión a la base de datos

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $mfa_code = $_POST['mfa_code'];

    $stmt = $mysqli->prepare("SELECT id, password, mfa_secret FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $mfa_secret);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        // Validar el código MFA
        $google2fa = new Google2FA();
        if ($google2fa->verifyKey($mfa_secret, $mfa_code)) {
            // Código MFA válido, iniciar sesión
            $_SESSION['user_id'] = $id;
            header("Location: dashboard.php");
            exit();
        } else {
            // Código MFA no válido
            echo "Código MFA no válido.";
        }
    } else {
        // Usuario o contraseña no válidos
        echo "Usuario o contraseña incorrectos.";
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de Inicio de Sesión</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Help Desk</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="login.php">
                            <div class="form-group">
                                <label for="username">Nombre de Usuario</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Nombre de Usuario" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                            </div>
                            <div class="form-group">
                                <label for="mfa_code">Código de Acceso</label>
                                <input type="password" class="form-control" id="mfa_code" name="mfa_code" placeholder="Código de Acceso" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">Iniciar Sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


