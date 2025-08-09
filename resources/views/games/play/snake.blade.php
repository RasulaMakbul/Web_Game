<x-layout title="Play Snake Classic">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Snake Classic</h1>
        <div>
            <a href="{{ route('games.show', $game->id) }}"
                class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to Game
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Game Area</h5>
                    <div>
                        <span class="badge bg-success">2D Game</span>
                        <span class="badge bg-primary">Score: <span id="score">0</span></span>
                    </div>
                </div>
                <div class="card-body text-center">
                    <canvas id="gameCanvas" width="600" height="400" class="border border-dark"></canvas>
                    <div class="mt-3">
                        <button id="startBtn" class="btn btn-success">Start Game</button>
                        <button id="pauseBtn" class="btn btn-warning" disabled>Pause</button>
                        <button id="resetBtn" class="btn btn-danger">Reset</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Game Instructions</h5>
                </div>
                <div class="card-body">
                    <p>Use arrow keys to control the snake. Eat the red food to grow longer and increase your score.</p>
                    <ul>
                        <li>Avoid hitting walls or yourself</li>
                        <li>Each food gives you 10 points</li>
                        <li>Game speeds up as you score more</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Leaderboard</h5>
                    <a href="/leaderboard/snake" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    {{-- @if ($topScores->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Player</th>
                                    <th>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topScores as $index => $score)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $score->user->name }}</td>
                                        <td>{{ $score->score }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No scores yet. Be the first!</p>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const canvas = document.getElementById('gameCanvas');
                const ctx = canvas.getContext('2d');
                const scoreElement = document.getElementById('score');
                const startBtn = document.getElementById('startBtn');
                const pauseBtn = document.getElementById('pauseBtn');
                const resetBtn = document.getElementById('resetBtn');

                // Game variables
                let snake = [{
                    x: 200,
                    y: 200
                }];
                let direction = {
                    x: 20,
                    y: 0
                }; // start moving right
                let nextDirection = {
                    x: 20,
                    y: 0
                }; // buffer next direction to avoid instant reversal
                let food = {
                    x: 0,
                    y: 0
                };
                let score = 0;
                let gameRunning = false;
                let gamePaused = false;
                let gameSpeed = 100;
                let gameLoop;

                // Initialize game
                function init() {
                    snake = [{
                        x: 200,
                        y: 200
                    }];
                    direction = {
                        x: 20,
                        y: 0
                    };
                    nextDirection = {
                        x: 20,
                        y: 0
                    };
                    score = 0;
                    scoreElement.textContent = score;
                    gameSpeed = 100;
                    generateFood();
                    draw();
                }

                // Generate food at random position avoiding snake
                function generateFood() {
                    let valid = false;
                    while (!valid) {
                        food.x = Math.floor(Math.random() * (canvas.width / 20)) * 20;
                        food.y = Math.floor(Math.random() * (canvas.height / 20)) * 20;

                        // Check if food overlaps with snake
                        valid = true;
                        for (let segment of snake) {
                            if (segment.x === food.x && segment.y === food.y) {
                                valid = false;
                                break;
                            }
                        }
                    }
                }

                // Draw game elements
                function draw() {
                    // Clear canvas
                    ctx.fillStyle = '#f8f9fa';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    // Draw snake
                    ctx.fillStyle = '#198754';
                    ctx.strokeStyle = '#0f5132';
                    for (let segment of snake) {
                        ctx.fillRect(segment.x, segment.y, 18, 18);
                        ctx.strokeRect(segment.x, segment.y, 18, 18);
                    }

                    // Draw food
                    ctx.fillStyle = '#dc3545';
                    ctx.beginPath();
                    ctx.arc(food.x + 10, food.y + 10, 9, 0, Math.PI * 2);
                    ctx.fill();
                }

                // Update game state
                function update() {
                    if (!gameRunning || gamePaused) return;

                    // Update direction from nextDirection (avoid instant reverse)
                    if (
                        (nextDirection.x !== -direction.x || nextDirection.y !== -direction.y) &&
                        (nextDirection.x !== direction.x || nextDirection.y !== direction.y)
                    ) {
                        direction = nextDirection;
                    }

                    // Calculate new head position
                    const head = {
                        x: snake[0].x + direction.x,
                        y: snake[0].y + direction.y
                    };

                    // Check wall collision
                    if (head.x < 0 || head.x >= canvas.width || head.y < 0 || head.y >= canvas.height) {
                        gameOver();
                        return;
                    }

                    // Check self collision (skip checking head itself)
                    for (let i = 0; i < snake.length; i++) {
                        if (head.x === snake[i].x && head.y === snake[i].y) {
                            gameOver();
                            return;
                        }
                    }

                    snake.unshift(head);

                    // Check food collision
                    if (head.x === food.x && head.y === food.y) {
                        score += 10;
                        scoreElement.textContent = score;
                        generateFood();

                        // Increase speed every 50 points
                        if (score % 50 === 0 && gameSpeed > 50) {
                            gameSpeed -= 5;
                            clearInterval(gameLoop);
                            gameLoop = setInterval(gameStep, gameSpeed);
                        }
                    } else {
                        snake.pop();
                    }

                    draw();
                }

                // Game step
                function gameStep() {
                    update();
                }

                // Start game
                function startGame() {
                    if (gameRunning) return;

                    init();
                    gameRunning = true;
                    gamePaused = false;
                    startBtn.disabled = true;
                    pauseBtn.disabled = false;

                    gameLoop = setInterval(gameStep, gameSpeed);
                }

                // Pause game
                function pauseGame() {
                    if (!gameRunning) return;

                    gamePaused = !gamePaused;
                    pauseBtn.textContent = gamePaused ? 'Resume' : 'Pause';
                }

                // Reset game
                function resetGame() {
                    clearInterval(gameLoop);
                    gameRunning = false;
                    gamePaused = false;
                    startBtn.disabled = false;
                    pauseBtn.disabled = true;
                    pauseBtn.textContent = 'Pause';
                    init();
                }

                // Game over
                function gameOver() {
                    clearInterval(gameLoop);
                    gameRunning = false;
                    startBtn.disabled = false;
                    pauseBtn.disabled = true;

                    @auth
                    fetch('/api/games/snake/score', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                score: score
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Game Over! Your score has been saved: ' + score);
                            } else {
                                alert('Game Over! Score: ' + score);
                            }
                        });
                @else
                    alert('Game Over! Score: ' + score + '. Log in to save your scores!');
                @endauth
            }

            // Keyboard controls
            document.addEventListener('keydown', function(e) {
                if (!gameRunning || gamePaused) return;

                switch (e.key) {
                    case 'ArrowUp':
                        if (direction.y === 0) {
                            nextDirection = {
                                x: 0,
                                y: -20
                            };
                        }
                        break;
                    case 'ArrowDown':
                        if (direction.y === 0) {
                            nextDirection = {
                                x: 0,
                                y: 20
                            };
                        }
                        break;
                    case 'ArrowLeft':
                        if (direction.x === 0) {
                            nextDirection = {
                                x: -20,
                                y: 0
                            };
                        }
                        break;
                    case 'ArrowRight':
                        if (direction.x === 0) {
                            nextDirection = {
                                x: 20,
                                y: 0
                            };
                        }
                        break;
                }
            });

            // Button controls
            startBtn.addEventListener('click', startGame); pauseBtn.addEventListener('click', pauseGame); resetBtn
            .addEventListener('click', resetGame);

            // Initialize game on load (draw initial state, but don't start moving)
            init();
            });
        </script>
    @endpush

</x-layout>
