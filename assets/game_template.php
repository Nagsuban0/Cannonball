<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include('../database/config.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];
$fullname = "Unknown User"; // Default value if query fails

// Fetch user full name
$userQuery = "SELECT fullname FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $userQuery);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $fullname);
    if (!mysqli_stmt_fetch($stmt)) {
        // Handle error fetching data
        error_log("No user found with ID: " . $user_id);
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Query Error: " . mysqli_error($conn));
}

// Get the level from the URL
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;
$question = "Question not found";
$options = [];
$correctAnswer = "";

// Fetch the question for the current level
$sql = "SELECT * FROM questions WHERE game_level = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $level);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $questionData = mysqli_fetch_assoc($result);
    
    $question = $questionData['question'];
    $correctAnswer = $questionData['correct_answer'];
    $options = [
        $questionData['option1'],
        $questionData['option2'],
        $questionData['option3'],
        $questionData['option4']
    ];

    shuffle($options); // Shuffle options for random order
} else {
    // Redirect to the scoreboard if no question found
    header("Location: ../game_template.php");
    exit();
}

$nextLevel = $level + 1;
$nextLevelQuery = "SELECT COUNT(*) as count FROM questions WHERE game_level = ?";
$stmt = mysqli_prepare($conn, $nextLevelQuery);
mysqli_stmt_bind_param($stmt, "i", $nextLevel);
mysqli_stmt_execute($stmt);
$nextLevelResult = mysqli_stmt_get_result($stmt);
$nextLevelExists = $nextLevelResult && mysqli_fetch_assoc($nextLevelResult)['count'] > 0;

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cannonball Game - Level <?php echo $level; ?></title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            margin: 0;
            overflow: hidden;
        }
        #user-info, #level-info {
            position: absolute;
            top: 10px;
            font-size: 18px;
            font-weight: bold;
            color: white;
            background: rgba(0, 0, 0, 0.7);
            padding: 8px 12px;
            border-radius: 5px;
            z-index: 10;
        }
        #user-info { left: 10px; }
        #level-info { right: 10px; }
        #question {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 28px;
            color: white;
            background: rgba(0, 0, 0, 0.7);
            padding: 15px;
            border-radius: 10px;
            z-index: 10;
        }
        #gameContainer {
            position: relative;
            width: 100vw;
            height: 100vh;
        }
        canvas {
            position: absolute;
            width: 100vw;
            height: 100vh;
            background: url('https://media.istockphoto.com/id/1390006977/vector/wooden-ship-deck-with-view-to-pirate-boat-in-sea.jpg?s=612x612&w=0&k=20&c=StEYieaADT71wjwf9u4FwJg1wsdtaDT-GNu8XHr6ytI=') no-repeat center center/cover;
            display: block;
        }
    </style>
</head>
<body>
    <div id="user-info">
        👤 <?php echo htmlspecialchars($fullname); ?>
    </div>
    <div id="level-info">🏆 Level <?php echo $level; ?></div>

    <h2 id="question"><?php echo $question; ?></h2>

    <div id="gameContainer">
        <canvas id="gameCanvas"></canvas>
    </div>

    <script>
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        let wheelImg = new Image();
        wheelImg.src = "./image/cannonwheel.png";

        let cannonImg = new Image();
        cannonImg.src = "./image/cannon1.png";

        let chestImg = new Image();
        chestImg.src = "https://static.vecteezy.com/system/resources/thumbnails/044/026/397/small_2x/red-treasure-chest-illustration-pirate-box-game-achievement-success-gift-antique-trunk-ui-winner-bonus-reward-vintage-chest-png.png";

        let cannonPos = { x: 100, y: canvas.height - 150 };
        let targetAngle = 0;

        let currentQuestion = { 
            text: "<?php echo $question; ?>", 
            correctAnswer: "<?php echo $correctAnswer; ?>"
        };
        let answers = <?php echo json_encode($options); ?>;

        let chests = [
            { x: canvas.width * 0.8, y: canvas.height * 0.2, answer: answers[0] },
            { x: canvas.width * 0.8, y: canvas.height * 0.4, answer: answers[1] },
            { x: canvas.width * 0.8, y: canvas.height * 0.6, answer: answers[2] },
            { x: canvas.width * 0.8, y: canvas.height * 0.8, answer: answers[3] }
        ];
        let cannonballs = [];

        function drawGame() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw chests with answers
            chests.forEach(chest => {
                ctx.drawImage(chestImg, chest.x, chest.y, 100, 100);
                ctx.font = "40px Arial";
                ctx.fillStyle = "black";
                ctx.fillText(chest.answer, chest.x + 100, chest.y + 60);
            });

            // Draw wheel
            ctx.drawImage(wheelImg, cannonPos.x - 60, cannonPos.y - 80, 150, 150);

            // Draw cannon
            ctx.save();
            ctx.translate(cannonPos.x, cannonPos.y);
            ctx.rotate(targetAngle);
            ctx.drawImage(cannonImg, 16, -60, 150, 100);
            ctx.restore();

            // Draw cannonballs
            cannonballs.forEach((ball, index) => {
                ctx.beginPath();
                ctx.arc(ball.x, ball.y, 30, 0, Math.PI * 2);
                ctx.fillStyle = "black";
                ctx.fill();

                ball.x += ball.vx; 
                ball.y += ball.vy; 

                chests.forEach((chest, chestIndex) => {
                    if (
                        ball.x + 10 > chest.x &&
                        ball.x - 10 < chest.x + 100 &&
                        ball.y + 10 > chest.y &&
                        ball.y - 10 < chest.y + 100
                    ) {
                        setTimeout(() => {
                            if (chest.answer === currentQuestion.correctAnswer) {
                                alert("✅ Correct! Moving to next level...");

                                // Update score through AJAX (sending 10 points per level)
                                fetch('./update_score.php', {
                                    method: 'POST',
                                    body: new URLSearchParams({
                                        'score': 10 // 10 points for correct answer
                                    })
                                })
                                .then(response => response.text())
                                .then(data => {
                                    console.log(data); // Handle response

                                    // After updating the score, go to the next level or scoreboard
                                    let nextLevelExists = <?php echo $nextLevelExists ? "true" : "false"; ?>;
                                    if (nextLevelExists) {
                                        window.location.href = `game_template.php?level=<?php echo $nextLevel; ?>`;
                                    } else {
                                        window.location.href = "./admin_scoreboard.php";
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                            } else {
                                alert("❌ Wrong Answer! Try again.");
                                window.location.href = `game_template.php?level=<?php echo $nextLevel; ?>`;
                            }
                        }, 100);
                        cannonballs.splice(index, 1);
                    }
                });

                if (ball.x > canvas.width || ball.y > canvas.height) {
                    cannonballs.splice(index, 1);
                }
            });
        }

        canvas.addEventListener("mousemove", (event) => {
            let rect = canvas.getBoundingClientRect();
            let targetX = event.clientX - rect.left;
            let targetY = event.clientY - rect.top;
            
            let dx = targetX - cannonPos.x;
            let dy = targetY - cannonPos.y;
            targetAngle = Math.atan2(dy, dx);

            drawGame();
        });

        canvas.addEventListener("click", (event) => {
            let rect = canvas.getBoundingClientRect();
            let targetX = event.clientX - rect.left;
            let targetY = event.clientY - rect.top;

            fireCannon(targetX, targetY);
        });

        function fireCannon(targetX, targetY) {
            let x = cannonPos.x, y = cannonPos.y;
            let dx = targetX - x;
            let dy = targetY - y;
            let speed = 12;
            let angle = Math.atan2(dy, dx);

            let vx = Math.cos(angle) * speed;
            let vy = Math.sin(angle) * speed;

            cannonballs.push({ x, y, vx, vy });
        }

        function gameLoop() {
            drawGame();
            requestAnimationFrame(gameLoop);
        }

        gameLoop();
    </script>
</body>
</html>
