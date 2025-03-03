<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Selection</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100vw;
            height: 100vh;
            background: url('https://img.freepik.com/free-vector/background-knowledge-day-celebration_52683-131709.jpg?semt=ais_hybrid') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            overflow: hidden;
            position: relative;
        }
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
            position: relative;
            z-index: 2;
        }
        h1 {
            margin-bottom: 20px;
        }
        .game-option {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            font-size: 20px;
            font-weight: bold;
            color: white;
            background: #ff5722;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s, background 0.3s;
        }
        .game-option:hover {
            background: #e64a19;
            transform: scale(1.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes flyUp {
            from { transform: translateY(100vh); opacity: 0; }
            to { transform: translateY(-10vh); opacity: 1; }
        }
        .floating-element {
            position: absolute;
            font-size: 24px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.7);
            animation: flyUp 5s linear infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Select Your Game</h1>
        <button class="game-option" onclick="location.href='angry_bird.php'">Angry Bird</button>
        <button class="game-option" onclick="location.href='cannon_ball.php'">Cannon Ball</button>
    </div>
    
    <script>
        function createFloatingElements() {
            const symbols = ['1+1=2', '2+2=4', '3+3=6', '4+4=8', '5+5=10', '6×2=12', '8÷2=4', '10-3=7', 'A', 'B', 'C', 'X', 'Y', 'Z', '✔', '✖', '📊'];
            for (let i = 0; i < 30; i++) {
                let element = document.createElement('div');
                element.className = 'floating-element';
                element.innerText = symbols[Math.floor(Math.random() * symbols.length)];
                element.style.left = Math.random() * 100 + 'vw';
                element.style.animationDuration = (3 + Math.random() * 3) + 's';
                document.body.appendChild(element);
            }
        }
        createFloatingElements();
    </script>
</body>
</html>