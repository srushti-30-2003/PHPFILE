<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .game-board {
            display: inline-block;
            border: 1px solid #000;
            margin-top: 20px;
            padding: 10px;
        }
        .cell {
            width: 20px;
            height: 20px;
            display: inline-block;
        }
        .snake {
            background-color: green;
        }
        .food {
            background-color: red;
        }
    </style>
</head>
<body>
    <h2>Snake Game</h2>
    <div class="game-board">
        <?php
            session_start();

            // Initialize or reset game state
            if (!isset($_SESSION['snake'])) {
                $_SESSION['snake'] = [
                    [10, 10], // initial position of the snake
                    [10, 9]   // initial position of the snake (head and tail)
                ];
                $_SESSION['direction'] = 'right'; // initial direction
                $_SESSION['food'] = generateFoodPosition(); // generate initial food position
                $_SESSION['score'] = 0; // initialize score
            }

            // Handle form submission for changing direction
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['direction'])) {
                    $_SESSION['direction'] = $_POST['direction'];
                }
            }

            // Move the snake
            moveSnake($_SESSION['direction']);

            // Check for collisions
            checkCollisions();

            // Render the game board
            renderBoard();

            function generateFoodPosition() {
                // Generate random food position within the game board
                $x = rand(0, 19);
                $y = rand(0, 19);
                return [$x, $y];
            }

            function moveSnake($direction) {
                $snake = $_SESSION['snake'];

                // Determine new head position based on direction
                $head = $snake[0];
                if ($direction === 'up') {
                    $new_head = [$head[0] - 1, $head[1]];
                } elseif ($direction === 'down') {
                    $new_head = [$head[0] + 1, $head[1]];
                } elseif ($direction === 'left') {
                    $new_head = [$head[0], $head[1] - 1];
                } elseif ($direction === 'right') {
                    $new_head = [$head[0], $head[1] + 1];
                }

                // Check if new head position is out of bounds
                if ($new_head[0] < 0 || $new_head[0] >= 20 || $new_head[1] < 0 || $new_head[1] >= 20) {
                    endGame();
                    return;
                }

                // Check if new head position overlaps with snake body
                foreach ($snake as $segment) {
                    if ($new_head[0] === $segment[0] && $new_head[1] === $segment[1]) {
                        endGame();
                        return;
                    }
                }

                // Add new head position to the beginning of the snake array
                array_unshift($snake, $new_head);

                // Check if snake eats the food
                $food = $_SESSION['food'];
                if ($new_head[0] === $food[0] && $new_head[1] === $food[1]) {
                    // Generate new food position
                    $_SESSION['food'] = generateFoodPosition();
                    $_SESSION['score']++;
                } else {
                    // Remove the last segment of the snake
                    array_pop($snake);
                }

                // Update session variable with new snake position
                $_SESSION['snake'] = $snake;
            }

            function checkCollisions() {
                // Collision checks are handled within moveSnake() function
            }

            function endGame() {
                // End the game and display score
                echo '<p>Game Over! Your score: ' . $_SESSION['score'] . '</p>';
                echo '<form method="post">';
                echo '<button type="submit" name="restart">Restart Game</button>';
                echo '</form>';
                session_destroy(); // Destroy session to restart the game
            }

            function renderBoard() {
                // Render the game board
                $snake = $_SESSION['snake'];
                $food = $_SESSION['food'];

                echo '<div>';
                for ($row = 0; $row < 20; $row++) {
                    for ($col = 0; $col < 20; $col++) {
                        $is_snake = false;
                        foreach ($snake as $segment) {
                            if ($row === $segment[0] && $col === $segment[1]) {
                                echo '<div class="cell snake"></div>';
                                $is_snake = true;
                                break;
                            }
                        }
                        if (!$is_snake && $row === $food[0] && $col === $food[1]) {
                            echo '<div class="cell food"></div>';
                        } else {
                            echo '<div class="cell"></div>';
                        }
                    }
                    echo '<br>';
                }
                echo '</div>';
            }
        ?>
    </div>
    <form method="post">
        <button type="submit" name="direction" value="up">Up</button><br>
        <button type="submit" name="direction" value="left">Left</button>
        <button type="submit" name="direction" value="right">Right</button><br>
        <button type="submit" name="direction" value="down">Down</button>
    </form>
</body>
</html>
