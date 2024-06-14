
<?php
// Importar Google2FA
use PragmaRX\Google2FAQRCode\Google2FA;

session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$editSuccess = false;

// Registrar usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $mysqli->prepare("INSERT INTO users (username, password, email, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $email, $role);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();
}

 // Generar el secreto MFA
 $google2fa = new Google2FA();
 $secret = $google2fa->generateSecretKey();

 // Guardar el secreto en la base de datos
 $sql = "UPDATE users SET mfa_secret = :mfa_secret WHERE id = :id";
 $stmt = $pdo->prepare($sql);
 $stmt->execute(['mfa_secret' => $secret, 'id' => $user_id]);

 // Redirigir o mostrar un mensaje de éxito
 header("Location: dashboard.php");
 exit();


// Cambiar contraseña de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changePassword'])) {
    $id = $_POST['id'];
    $newPassword = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);

    $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $newPassword, $id);
    $stmt->execute();
    $stmt->close();
}


// Obtener lista de usuarios
$result = $mysqli->query("SELECT id, username, email, rol FROM users");

// Verificar si se redirige desde la edición con éxito
if (isset($_GET['edit']) && $_GET['edit'] == 'success') {
    $editSuccess = true;
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
    <title>Administración de Usuarios</title>
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

        <h2 class="text-center">Administración de Usuarios</h2>

        <!-- Formulario de Registro -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Registrar Usuario</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="register" value="1">
                    <div class="form-group">
                        <label for="username">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Nombre de Usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Correo Electrónico" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Rol</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Registrar</button>
                </form>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Correo Electrónico</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['rol']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm editBtn" data-id="<?php echo $row['id']; ?>" data-username="<?php echo $row['username']; ?>" data-email="<?php echo $row['email']; ?>" data-role="<?php echo $row['rol']; ?>">Editar</button>
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="<?php echo $row['id']; ?>">Eliminar</button>
                        <button class="btn btn-info btn-sm changePasswordBtn" data-id="<?php echo $row['id']; ?>">Cambiar Contraseña</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="edit_user.php">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label for="edit-username">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="edit-username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-email">Correo Electrónico</label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-role">Rol</label>
                            <select class="form-control" id="edit-role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Usuario -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Eliminar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este usuario?
                </div>
                <div class="modal-footer">
                    <form method="POST" action="delete_user.php">
                        <input type="hidden" id="delete-id" name="id">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Edición -->
    <div class="modal fade" id="editSuccessModal" tabindex="-1" role="dialog" aria-labelledby="editSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSuccessModalLabel">Usuario Editado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¡El usuario ha sido editado exitosamente!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- En el cuerpo del documento HTML -->
<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Cambiar Contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="changePassword" value="1">
                    <input type="hidden" id="change-password-id" name="id">
                    <div class="form-group">
                        <label for="newPassword">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Nueva Contraseña</button>
                </form>
            </div>
        </div>
    </div>
</div>



    <script>
        // Pasar datos al modal de edición
        $(document).on('click', '.editBtn', function() {
            var id = $(this).data('id');
            var username = $(this).data('username');
            var email = $(this).data('email');
            var role = $(this).data('role');
            $('#edit-id').val(id);
            $('#edit-username').val(username);
            $('#edit-email').val(email);
            $('#edit-role').val(role);
            $('#editModal').modal('show');
        });

        // Pasar datos al modal de eliminación
        $(document).on('click', '.deleteBtn', function() {
            var id = $(this).data('id');
            $('#delete-id').val(id);
            $('#deleteModal').modal('show');
        });

        <!-- En el footer del documento HTML -->

    // Pasar datos al modal de cambio de contraseña
    $(document).on('click', '.changePasswordBtn', function() {
        var id = $(this).data('id');
        $('#change-password-id').val(id);
        $('#changePasswordModal').modal('show');
    });



        // Mostrar modal de confirmación si la edición fue exitosa
        <?php if ($editSuccess): ?>
            $(document).ready(function() {
                $('#editSuccessModal').modal('show');
            });
        <?php endif; ?>
    </script>
</body
