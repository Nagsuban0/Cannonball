<?php
include '../database/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $user_type = $_POST['user_type'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, username = ?, user_type = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $fullname, $username, $user_type, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, username = ?, user_type = ? WHERE id = ?");
        $stmt->bind_param("sssi", $fullname, $username, $user_type, $id);
    }

    if ($stmt->execute()) {
        header("Location: admin_students.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
