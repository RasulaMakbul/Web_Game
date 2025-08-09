<x-layout title="Play 3D Cube Runner">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">3D Cube Runner</h1>
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
                        <span class="badge badge-3d">3D Game</span>
                        <span class="badge bg-primary">Score: <span id="score">0</span></span>
                        <span class="badge bg-info">Distance: <span id="distance">0</span>m</span>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div id="gameContainer" style="width: 100%; height: 500px; position: relative;">
                        <div id="gameCanvas"></div>
                        <div id="gameOverlay"
                            class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                            style="background-color: rgba(0,0,0,0.7); display: none;">
                            <div class="text-center text-white">
                                <h2 id="overlayTitle">Game Over</h2>
                                <p id="overlayMessage">Your score: 0</p>
                                <button id="restartBtn" class="btn btn-primary">Play Again</button>
                            </div>
                        </div>
                    </div>
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
                    <p>Navigate through a 3D cube world, avoiding obstacles and collecting gems.</p>
                    <ul>
                        <li>Arrow keys or A/D: Move left/right</li>
                        <li>W or Up arrow: Jump</li>
                        <li>Collect blue gems for points</li>
                        <li>Avoid red obstacles</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Leaderboard</h5>
                    <a href="/leaderboard/cube-runner" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if ($topScores->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Player</th>
                                    <th>Score</th>
                                    <th>Distance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topScores as $index => $score)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $score->user->name }}</td>
                                        <td>{{ $score->score }}</td>
                                        <td>{{ $score->distance }}m</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No scores yet. Be the first!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const gameContainer = document.getElementById('gameCanvas');
                const scoreElement = document.getElementById('score');
                const distanceElement = document.getElementById('distance');
                const startBtn = document.getElementById('startBtn');
                const pauseBtn = document.getElementById('pauseBtn');
                const resetBtn = document.getElementById('resetBtn');
                const gameOverlay = document.getElementById('gameOverlay');
                const overlayTitle = document.getElementById('overlayTitle');
                const overlayMessage = document.getElementById('overlayMessage');
                const restartBtn = document.getElementById('restartBtn');

                // Three.js setup
                let scene, camera, renderer;
                let player, playerVelocity = 0;
                let cubes = [];
                let gems = [];
                let score = 0;
                let distance = 0;
                let gameRunning = false;
                let gamePaused = false;
                let gameSpeed = 0.1;
                let keys = {};
                let clock = new THREE.Clock();

                // Initialize Three.js
                function initThree() {
                    // Scene
                    scene = new THREE.Scene();
                    scene.background = new THREE.Color(0x87CEEB); // Sky blue
                    scene.fog = new THREE.Fog(0x87CEEB, 10, 50);

                    // Camera
                    camera = new THREE.PerspectiveCamera(75, gameContainer.clientWidth / 500, 0.1, 1000);
                    camera.position.set(0, 5, 10);
                    camera.lookAt(0, 0, 0);

                    // Renderer
                    renderer = new THREE.WebGLRenderer({
                        antialias: true
                    });
                    renderer.setSize(gameContainer.clientWidth, 500);
                    renderer.shadowMap.enabled = true;
                    gameContainer.appendChild(renderer.domElement);

                    // Lights
                    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
                    scene.add(ambientLight);

                    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
                    directionalLight.position.set(10, 20, 15);
                    directionalLight.castShadow = true;
                    scene.add(directionalLight);

                    // Ground
                    const groundGeometry = new THREE.PlaneGeometry(100, 100);
                    const groundMaterial = new THREE.MeshStandardMaterial({
                        color: 0x7CFC00, // Lawn green
                        roughness: 0.8
                    });
                    const ground = new THREE.Mesh(groundGeometry, groundMaterial);
                    ground.rotation.x = -Math.PI / 2;
                    ground.position.y = -2;
                    ground.receiveShadow = true;
                    scene.add(ground);

                    // Player
                    const playerGeometry = new THREE.BoxGeometry(1, 1, 1);
                    const playerMaterial = new THREE.MeshStandardMaterial({
                        color: 0x3498db, // Blue
                        roughness: 0.3
                    });
                    player = new THREE.Mesh(playerGeometry, playerMaterial);
                    player.position.set(0, 0, 5);
                    player.castShadow = true;
                    scene.add(player);

                    // Initial obstacles and gems
                    createObstacles();
                    createGems();
                }

                // Create obstacles
                function createObstacles() {
                    cubes = [];

                    for (let i = 0; i < 20; i++) {
                        const size = Math.random() * 1 + 0.5;
                        const geometry = new THREE.BoxGeometry(size, size, size);
                        const material = new THREE.MeshStandardMaterial({
                            color: 0xe74c3c, // Red
                            roughness: 0.7
                        });
                        const cube = new THREE.Mesh(geometry, material);

                        cube.position.set(
                            (Math.random() - 0.5) * 10,
                            -2 + size / 2,
                            -i * 5 - 10
                        );

                        cube.castShadow = true;
                        cube.receiveShadow = true;

                        scene.add(cube);
                        cubes.push(cube);
                    }
                }

                // Create gems
                function createGems() {
                    gems = [];

                    for (let i = 0; i < 15; i++) {
                        const geometry = new THREE.OctahedronGeometry(0.5, 0);
                        const material = new THREE.MeshStandardMaterial({
                            color: 0x3498db, // Blue
                            roughness: 0.2,
                            metalness: 0.8
                        });
                        const gem = new THREE.Mesh(geometry, material);

                        gem.position.set(
                            (Math.random() - 0.5) * 10,
                            Math.random() * 2 + 0.5,
                            -i * 7 - 5
                        );

                        gem.castShadow = true;

                        scene.add(gem);
                        gems.push(gem);
                    }
                }

                // Update game state
                function update() {
                    if (!gameRunning || gamePaused) return;

                    const delta = clock.getDelta();

                    // Move player
                    if (keys['ArrowLeft'] || keys['a'] || keys['A']) {
                        player.position.x -= 5 * delta;
                    }
                    if (keys['ArrowRight'] || keys['d'] || keys['D']) {
                        player.position.x += 5 * delta;
                    }
                    if ((keys['ArrowUp'] || keys['w'] || keys['W']) && player.position.y <= 0.1) {
                        playerVelocity = 5;
                    }

                    // Apply gravity and jumping
                    player.position.y += playerVelocity * delta;
                    playerVelocity -= 9.8 * delta;

                    if (player.position.y < 0) {
                        player.position.y = 0;
                        playerVelocity = 0;
                    }

                    // Keep player in bounds
                    player.position.x = Math.max(-5, Math.min(5, player.position.x));

                    // Move obstacles and gems
                    for (let cube of cubes) {
                        cube.position.z += gameSpeed;

                        // Reset position if passed
                        if (cube.position.z > 10) {
                            cube.position.z = -30;
                            cube.position.x = (Math.random() - 0.5) * 10;
                        }

                        // Check collision with player
                        if (
                            Math.abs(player.position.x - cube.position.x) < 0.8 &&
                            Math.abs(player.position.y - cube.position.y) < 0.8 &&
                            Math.abs(player.position.z - cube.position.z) < 0.8
                        ) {
                            gameOver();
                        }
                    }

                    for (let gem of gems) {
                        gem.position.z += gameSpeed;
                        gem.rotation.y += 2 * delta;

                        // Reset position if passed
                        if (gem.position.z > 10) {
                            gem.position.z = -30;
                            gem.position.x = (Math.random() - 0.5) * 10;
                            gem.position.y = Math.random() * 2 + 0.5;
                        }

                        // Check collection
                        if (
                            Math.abs(player.position.x - gem.position.x) < 0.8 &&
                            Math.abs(player.position.y - gem.position.y) < 0.8 &&
                            Math.abs(player.position.z - gem.position.z) < 0.8
                        ) {
                            // Collect gem
                            score += 10;
                            scoreElement.textContent = score;
                            gem.position.z = -30;
                            gem.position.x = (Math.random() - 0.5) * 10;
                            gem.position.y = Math.random() * 2 + 0.5;
                        }
                    }

                    // Update distance
                    distance += gameSpeed * 10;
                    distanceElement.textContent = Math.floor(distance);

                    // Increase difficulty over time
                    if (distance > 0 && distance % 100 < 1) {
                        gameSpeed += 0.01;
                    }

                    // Update camera to follow player
                    camera.position.x = player.position.x;
                    camera.position.z = player.position.z + 10;
                    camera.lookAt(player.position.x, player.position.y, player.position.z - 5);
                }

                // Game loop
                function gameLoop() {
                    requestAnimationFrame(gameLoop);
                    update();
                    renderer.render(scene, camera);
                }

                // Start game
                function startGame() {
                    if (gameRunning) return;

                    // Reset game state
                    player.position.set(0, 0, 5);
                    playerVelocity = 0;
                    score = 0;
                    distance = 0;
                    gameSpeed = 0.1;
                    scoreElement.textContent = score;
                    distanceElement.textContent = distance;

                    // Reset obstacles and gems
                    createObstacles();
                    createGems();

                    gameRunning = true;
                    gamePaused = false;
                    startBtn.disabled = true;
                    pauseBtn.disabled = false;
                    gameOverlay.style.display = 'none';

                    // Start game loop
                    gameLoop();
                }

                // Pause game
                function pauseGame() {
                    if (!gameRunning) return;

                    gamePaused = !gamePaused;
                    pauseBtn.textContent = gamePaused ? 'Resume' : 'Pause';

                    if (gamePaused) {
                        overlayTitle.textContent = 'Game Paused';
                        overlayMessage.textContent = 'Your score: ' + score;
                        gameOverlay.style.display = 'flex';
                    } else {
                        gameOverlay.style.display = 'none';
                    }
                }

                // Reset game
                function resetGame() {
                    gameRunning = false;
                    gamePaused = false;
                    startBtn.disabled = false;
                    pauseBtn.disabled = true;
                    pauseBtn.textContent = 'Pause';

                    // Clear scene
                    while (scene.children.length > 0) {
                        scene.remove(scene.children[0]);
                    }

                    // Reinitialize
                    initThree();
                }

                // Game over
                function gameOver() {
                    gameRunning = false;
                    startBtn.disabled = false;
                    pauseBtn.disabled = true;

                    overlayTitle.textContent = 'Game Over';
                    overlayMessage.textContent = `Your score: ${score} | Distance: ${Math.floor(distance)}m`;
                    gameOverlay.style.display = 'flex';

                    // Save score if user is logged in
                    @auth
                    fetch('/api/games/cube-runner/score', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                score: score,
                                distance: Math.floor(distance)
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Score saved successfully');
                            }
                        });
                @endauth
            }

            // Keyboard controls
            document.addEventListener('keydown', function(e) {
                keys[e.key] = true;
            });

            document.addEventListener('keyup', function(e) {
                keys[e.key] = false;
            });

            // Button controls
            startBtn.addEventListener('click', startGame); pauseBtn.addEventListener('click', pauseGame); resetBtn
            .addEventListener('click', resetGame); restartBtn.addEventListener('click', startGame);

            // Handle window resize
            window.addEventListener('resize', function() {
                camera.aspect = gameContainer.clientWidth / 500;
                camera.updateProjectionMatrix();
                renderer.setSize(gameContainer.clientWidth, 500);
            });

            // Initialize game on load
            initThree(); renderer.render(scene, camera);
            });
        </script>

        <style>
            .badge-3d {
                background-color: #6f42c1;
            }
        </style>
    @endpush
</x-layout>
