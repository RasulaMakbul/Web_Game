<x-layout title="Play Space Shooter">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Space Shooter</h1>
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
                        <span class="badge bg-danger">Lives: <span id="lives">3</span></span>
                    </div>
                </div>
                <div class="card-body text-center">
                    <canvas id="gameCanvas" width="600" height="500" class="border border-dark"></canvas>
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
                    <p>Defend Earth from alien invaders! Use arrow keys to move and spacebar to shoot.</p>
                    <ul>
                        <li>Arrow keys: Move your spaceship</li>
                        <li>Spacebar: Fire weapons</li>
                        <li>Avoid enemy ships and projectiles</li>
                        <li>Destroy enemies to earn points</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Leaderboard</h5>
                    <a href="/leaderboard/space-shooter" class="btn btn-sm btn-outline-primary">View All</a>
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
                const livesElement = document.getElementById('lives');
                const startBtn = document.getElementById('startBtn');
                const pauseBtn = document.getElementById('pauseBtn');
                const resetBtn = document.getElementById('resetBtn');

                // Game variables
                let player = {
                    x: canvas.width / 2 - 25,
                    y: canvas.height - 70,
                    width: 50,
                    height: 50,
                    speed: 5,
                    color: '#3498db'
                };

                let bullets = [];
                let enemies = [];
                let enemyBullets = [];
                let particles = [];
                let stars = [];
                let score = 0;
                let lives = 3;
                let gameRunning = false;
                let gamePaused = false;
                let gameLoop;
                let enemySpawnTimer = 0;
                let keys = {};

                // Initialize stars background
                function initStars() {
                    stars = [];
                    for (let i = 0; i < 100; i++) {
                        stars.push({
                            x: Math.random() * canvas.width,
                            y: Math.random() * canvas.height,
                            size: Math.random() * 2,
                            speed: Math.random() * 2 + 1
                        });
                    }
                }

                // Draw stars
                function drawStars() {
                    ctx.fillStyle = 'white';
                    for (let star of stars) {
                        ctx.beginPath();
                        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2);
                        ctx.fill();

                        // Move star
                        star.y += star.speed;
                        if (star.y > canvas.height) {
                            star.y = 0;
                            star.x = Math.random() * canvas.width;
                        }
                    }
                }

                // Draw player
                function drawPlayer() {
                    // Draw spaceship body
                    ctx.fillStyle = player.color;
                    ctx.beginPath();
                    ctx.moveTo(player.x + player.width / 2, player.y);
                    ctx.lineTo(player.x, player.y + player.height);
                    ctx.lineTo(player.x + player.width, player.y + player.height);
                    ctx.closePath();
                    ctx.fill();

                    // Draw cockpit
                    ctx.fillStyle = '#2c3e50';
                    ctx.beginPath();
                    ctx.arc(player.x + player.width / 2, player.y + player.height / 2, 8, 0, Math.PI * 2);
                    ctx.fill();
                }

                // Draw bullets
                function drawBullets() {
                    ctx.fillStyle = '#f1c40f';
                    for (let i = bullets.length - 1; i >= 0; i--) {
                        const bullet = bullets[i];
                        ctx.fillRect(bullet.x, bullet.y, bullet.width, bullet.height);

                        // Move bullet
                        bullet.y -= bullet.speed;

                        // Remove if off screen
                        if (bullet.y + bullet.height < 0) {
                            bullets.splice(i, 1);
                        }
                    }
                }

                // Draw enemies
                function drawEnemies() {
                    for (let i = enemies.length - 1; i >= 0; i--) {
                        const enemy = enemies[i];

                        // Draw enemy ship
                        ctx.fillStyle = enemy.color;
                        ctx.beginPath();
                        ctx.moveTo(enemy.x + enemy.width / 2, enemy.y + enemy.height);
                        ctx.lineTo(enemy.x, enemy.y);
                        ctx.lineTo(enemy.x + enemy.width, enemy.y);
                        ctx.closePath();
                        ctx.fill();

                        // Move enemy
                        enemy.y += enemy.speed;

                        // Random shooting
                        if (Math.random() < 0.01) {
                            enemyBullets.push({
                                x: enemy.x + enemy.width / 2 - 2,
                                y: enemy.y + enemy.height,
                                width: 4,
                                height: 10,
                                speed: 3
                            });
                        }

                        // Remove if off screen
                        if (enemy.y > canvas.height) {
                            enemies.splice(i, 1);
                        }
                    }
                }

                // Draw enemy bullets
                function drawEnemyBullets() {
                    ctx.fillStyle = '#e74c3c';
                    for (let i = enemyBullets.length - 1; i >= 0; i--) {
                        const bullet = enemyBullets[i];
                        ctx.fillRect(bullet.x, bullet.y, bullet.width, bullet.height);

                        // Move bullet
                        bullet.y += bullet.speed;

                        // Remove if off screen
                        if (bullet.y > canvas.height) {
                            enemyBullets.splice(i, 1);
                        }
                    }
                }

                // Draw particles (explosions)
                function drawParticles() {
                    for (let i = particles.length - 1; i >= 0; i--) {
                        const particle = particles[i];

                        ctx.fillStyle = particle.color;
                        ctx.beginPath();
                        ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
                        ctx.fill();

                        // Update particle
                        particle.x += particle.vx;
                        particle.y += particle.vy;
                        particle.life--;

                        // Remove if life is over
                        if (particle.life <= 0) {
                            particles.splice(i, 1);
                        }
                    }
                }

                // Create explosion
                function createExplosion(x, y, color) {
                    for (let i = 0; i < 20; i++) {
                        particles.push({
                            x: x,
                            y: y,
                            size: Math.random() * 3 + 1,
                            vx: (Math.random() - 0.5) * 5,
                            vy: (Math.random() - 0.5) * 5,
                            color: color,
                            life: Math.random() * 20 + 10
                        });
                    }
                }

                // Spawn enemy
                function spawnEnemy() {
                    const size = Math.random() * 30 + 20;
                    enemies.push({
                        x: Math.random() * (canvas.width - size),
                        y: -size,
                        width: size,
                        height: size,
                        speed: Math.random() * 2 + 1,
                        color: `hsl(${Math.random() * 360}, 70%, 50%)`
                    });
                }

                // Check collisions
                function checkCollisions() {
                    // Bullet-enemy collisions
                    for (let i = bullets.length - 1; i >= 0; i--) {
                        const bullet = bullets[i];

                        for (let j = enemies.length - 1; j >= 0; j--) {
                            const enemy = enemies[j];

                            if (
                                bullet.x < enemy.x + enemy.width &&
                                bullet.x + bullet.width > enemy.x &&
                                bullet.y < enemy.y + enemy.height &&
                                bullet.y + bullet.height > enemy.y
                            ) {
                                // Hit!
                                createExplosion(enemy.x + enemy.width / 2, enemy.y + enemy.height / 2, enemy.color);
                                enemies.splice(j, 1);
                                bullets.splice(i, 1);
                                score += 10;
                                scoreElement.textContent = score;
                                break;
                            }
                        }
                    }

                    // Player-enemy collisions
                    for (let i = enemies.length - 1; i >= 0; i--) {
                        const enemy = enemies[i];

                        if (
                            player.x < enemy.x + enemy.width &&
                            player.x + player.width > enemy.x &&
                            player.y < enemy.y + enemy.height &&
                            player.y + player.height > enemy.y
                        ) {
                            // Hit!
                            createExplosion(enemy.x + enemy.width / 2, enemy.y + enemy.height / 2, enemy.color);
                            enemies.splice(i, 1);
                            lives--;
                            livesElement.textContent = lives;

                            if (lives <= 0) {
                                gameOver();
                            }
                        }
                    }

                    // Player-enemy bullet collisions
                    for (let i = enemyBullets.length - 1; i >= 0; i--) {
                        const bullet = enemyBullets[i];

                        if (
                            player.x < bullet.x + bullet.width &&
                            player.x + player.width > bullet.x &&
                            player.y < bullet.y + bullet.height &&
                            player.y + player.height > bullet.y
                        ) {
                            // Hit!
                            createExplosion(bullet.x, bullet.y, '#e74c3c');
                            enemyBullets.splice(i, 1);
                            lives--;
                            livesElement.textContent = lives;

                            if (lives <= 0) {
                                gameOver();
                            }
                        }
                    }
                }

                // Update game state
                function update() {
                    if (!gameRunning || gamePaused) return;

                    // Move player
                    if (keys['ArrowLeft'] && player.x > 0) {
                        player.x -= player.speed;
                    }
                    if (keys['ArrowRight'] && player.x < canvas.width - player.width) {
                        player.x += player.speed;
                    }
                    if (keys['ArrowUp'] && player.y > 0) {
                        player.y -= player.speed;
                    }
                    if (keys['ArrowDown'] && player.y < canvas.height - player.height) {
                        player.y += player.speed;
                    }

                    // Spawn enemies
                    enemySpawnTimer++;
                    if (enemySpawnTimer > 60) {
                        spawnEnemy();
                        enemySpawnTimer = 0;
                    }

                    // Check collisions
                    checkCollisions();

                    // Draw everything
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    drawStars();
                    drawPlayer();
                    drawBullets();
                    drawEnemies();
                    drawEnemyBullets();
                    drawParticles();
                }

                // Game step
                function gameStep() {
                    update();
                }

                // Start game
                function startGame() {
                    if (gameRunning) return;

                    // Reset game state
                    player.x = canvas.width / 2 - 25;
                    player.y = canvas.height - 70;
                    bullets = [];
                    enemies = [];
                    enemyBullets = [];
                    particles = [];
                    score = 0;
                    lives = 3;
                    enemySpawnTimer = 0;
                    scoreElement.textContent = score;
                    livesElement.textContent = lives;

                    gameRunning = true;
                    gamePaused = false;
                    startBtn.disabled = true;
                    pauseBtn.disabled = false;

                    gameLoop = setInterval(gameStep, 1000 / 60); // 60 FPS
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

                    // Clear canvas
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    initStars();
                    drawStars();
                }

                // Game over
                function gameOver() {
                    clearInterval(gameLoop);
                    gameRunning = false;
                    startBtn.disabled = false;
                    pauseBtn.disabled = true;

                    // Save score if user is logged in
                    @auth
                    fetch('/api/games/space-shooter/score', {
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
                keys[e.key] = true;

                // Spacebar to shoot
                if (e.key === ' ' && gameRunning && !gamePaused) {
                    bullets.push({
                        x: player.x + player.width / 2 - 2,
                        y: player.y,
                        width: 4,
                        height: 10,
                        speed: 7
                    });
                    e.preventDefault(); // Prevent scrolling
                }
            });

            document.addEventListener('keyup', function(e) {
                keys[e.key] = false;
            });

            // Button controls
            startBtn.addEventListener('click', startGame); pauseBtn.addEventListener('click', pauseGame); resetBtn
            .addEventListener('click', resetGame);

            // Initialize game on load
            initStars(); drawStars();
            });
        </script>
    @endpush
</x-layout>
