<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $stmt = $mysqli->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $new_password, $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo 'Password has been reset';
    } else {
        echo 'Invalid token';
    }
    $stmt->close();
}
?>
<form method="POST">
    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>" required>
    <input type="password" name="new_password" placeholder="New Password" required>
    <button type="submit">Set New Password</button>
</form>
