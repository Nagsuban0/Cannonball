<?php
include '../database/config.php';

// Get next question (Example: first question)
$query = "SELECT * FROM questions ORDER BY id ASC LIMIT 1";
$result = $conn->query($query);
$question = $result->fetch_assoc();

echo json_encode($question);
?>
