<?php
include '../database/config.php';
session_start();

// Debugging: Ensure database connection works
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Handle adding questions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['level'], $_POST['question'], $_POST['correct_answer'], 
              $_POST['option1'], $_POST['option2'], $_POST['option3'], $_POST['option4']) &&
        !empty($_POST['level']) && !empty($_POST['question']) && !empty($_POST['correct_answer']) &&
        !empty($_POST['option1']) && !empty($_POST['option2']) && !empty($_POST['option3']) && !empty($_POST['option4'])
    ) {
        $level = intval($_POST['level']);
        $question = trim($_POST['question']);
        $correct_answer = trim($_POST['correct_answer']);
        $option1 = trim($_POST['option1']);
        $option2 = trim($_POST['option2']);
        $option3 = trim($_POST['option3']);
        $option4 = trim($_POST['option4']);

        // Ensure column names match your database table
        $stmt = $conn->prepare("INSERT INTO questions (game_level, question, correct_answer, option1, option2, option3, option4) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("issssss", $level, $question, $correct_answer, $option1, $option2, $option3, $option4);

            if ($stmt->execute()) {
                // Create game file dynamically
                $gameFile = "../game$level.php";
                if (!file_exists($gameFile)) {
                    $template = file_get_contents("../game_template.php");
                    $template = str_replace("{{LEVEL}}", $level, $template);
                    file_put_contents($gameFile, $template);
                }

                header("Location: admin_dashboard.php?success=1");
                exit();
            } else {
                die("Error inserting question: " . $stmt->error);
            }

            $stmt->close();
        } else {
            die("Error preparing statement: " . $conn->error);
        }
    } else {
        die("⚠️ All fields are required.");
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM questions WHERE id = $delete_id");

    // If deleting a specific level, remove the corresponding game file
    $file_path = "../game$delete_id.php";
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    header("Location: admin_dashboard.php?deleted=1");
    exit();
}

// Fetch questions from the database
$questions_result = $conn->query("SELECT * FROM questions ORDER BY id DESC");

if (!$questions_result) {
    die("Query failed: " . $conn->error);
}

$questions = [];
while ($row = $questions_result->fetch_assoc()) {
    $questions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; text-align: center; }
        .navbar { background: #333; padding: 15px; display: flex; justify-content: center; gap: 20px; }
        .navbar a { color: white; text-decoration: none; padding: 10px 15px; border-radius: 5px; transition: 0.3s; }
        .navbar a:hover { background: #555; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; margin-top: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { color: #333; }
        form { display: flex; flex-direction: column; gap: 10px; }
        label { font-weight: bold; text-align: left; }
        input, select { padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #218838; }
        .message { margin-top: 10px; color: green; font-weight: bold; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; background: white; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        .delete-btn { background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
        .delete-btn:hover { background: #c82333; }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="admin_dashboard.php">Admin Dashboard</a>
        <a href="admin_scoreboard.php">Admin Scoreboard</a>
        <a href="admin_students.php">Admin Students</a>
    </div>

    <div class="container">
        <h2>Add a Question</h2>
        <?php if (isset($_GET['success'])) { echo "<p class='message'>✅ Question added successfully!</p>"; } ?>
        <?php if (isset($_GET['deleted'])) { echo "<p class='message'>🗑️ Question deleted successfully!</p>"; } ?>

        <form action="admin_dashboard.php" method="POST">
            <label>Level:</label>
            <select name="level" required>
                <option value="">Select Level</option>
                <?php for ($i = 1; $i <= 20; $i++) { ?>
                    <option value="<?= $i; ?>">Level <?= $i; ?></option>
                <?php } ?>
            </select>

            <label>Question:</label>
            <input type="text" name="question" required>

            <label>Correct Answer:</label>
            <input type="text" name="correct_answer" required>

            <label>Option 1:</label>
            <input type="text" name="option1" required>

            <label>Option 2:</label>
            <input type="text" name="option2" required>

            <label>Option 3:</label>
            <input type="text" name="option3" required>

            <label>Option 4:</label>
            <input type="text" name="option4" required>

            <button type="submit">Add Question</button>
        </form>

        <h2>Existing Questions</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Level</th>
                <th>Question</th>
                <th>Correct Answer</th>
                <th>Options</th>
                <th>Action</th>
            </tr>
            <?php foreach ($questions as $row) { ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['game_level']; ?></td>
                <td><?= htmlspecialchars($row['question']); ?></td>
                <td><?= htmlspecialchars($row['correct_answer']); ?></td>
                <td><?= htmlspecialchars($row['option1']) . ", " . htmlspecialchars($row['option2']) . ", " . htmlspecialchars($row['option3']) . ", " . htmlspecialchars($row['option4']); ?></td>
                <td><a href="admin_dashboard.php?delete_id=<?= $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a></td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>
