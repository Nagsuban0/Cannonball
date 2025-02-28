<?php
// Connect to the database
include('../database/config.php');

// Get the total number of questions in the database
$sql = "SELECT DISTINCT game_level FROM questions ORDER BY game_level";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $level = $row['game_level'];

        // Fetch the question data for this level
        $sql_question = "SELECT * FROM questions WHERE game_level = ?";
        $stmt = mysqli_prepare($conn, $sql_question);
        mysqli_stmt_bind_param($stmt, "i", $level);
        mysqli_stmt_execute($stmt);
        $result_question = mysqli_stmt_get_result($stmt);

        if ($result_question && mysqli_num_rows($result_question) > 0) {
            $questionData = mysqli_fetch_assoc($result_question);

            $question = $questionData['question'];
            $correctAnswer = $questionData['correct_answer'];
            $options = [
                $questionData['option1'],
                $questionData['option2'],
                $questionData['option3'],
                $questionData['option4']
            ];

            // Generate a new PHP file for the game level
            $newGameFile = fopen("game_template" . $level . ".php", "w");

            $gameContent = "<?php
// Game level $level
\$question = \"$question\";
\$correctAnswer = \"$correctAnswer\";
\$options = " . var_export($options, true) . ";

// Include the game template logic
include('game_template.php');
?>";

            fwrite($newGameFile, $gameContent);
            fclose($newGameFile);
            echo "New game level file (game_template$level.php) created successfully!<br>";
        }
    }
} else {
    echo "No questions found in the database!";
}

mysqli_close($conn);
?>
