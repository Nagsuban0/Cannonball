<?php
include '../database/config.php';

header('Content-Type: application/json');

$query = "SELECT id, question, correct_answer, option1, option2, option3, option4 FROM questions ORDER BY id ASC";
$result = $conn->query($query);

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

echo json_encode($questions);
?>
