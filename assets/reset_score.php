<?php
include '../database/config.php';

// Check if reset request is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // Reset score query
    $sql = "UPDATE users SET score = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Redirect to scoreboard with success message
        header("Location: admin_scoreboard.php?success=Score reset successfully");
        exit;
    } else {
        echo "Error resetting score: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
