<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ?, rol = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $role, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: register.php?edit=success");
    exit();
}
?>
