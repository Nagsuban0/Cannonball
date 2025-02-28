<?php
session_start();
include('../database/config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in!";
    exit();
}

$user_id = $_SESSION['user_id'];
$score = isset($_POST['score']) ? (int)$_POST['score'] : 0;

// Update the user's score in the database
$sql = "UPDATE users SET score = score + ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $score, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "Score updated successfully!";
    } else {
        echo "Error updating score: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
