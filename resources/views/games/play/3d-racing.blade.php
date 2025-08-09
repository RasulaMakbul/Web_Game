<x-layout title="Play 3D Speed Racer">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">3D Speed Racer</h1>
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
                        <span class="badge bg-primary">Speed: <span id="speed">0</span> km/h</span>
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
                                <p id="overlayMessage">Your distance: 0m</p>
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
                    <p>Race against AI opponents in this high-speed 3D racing game.</p>
                    <ul>
                        <li>Arrow keys or A/D: Steer left/right</li>
                        <li>W or Up arrow: Accelerate</li>
                        <li>S or Down arrow: Brake/Reverse</li>
                        <li>Avoid collisions and stay on track</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Leaderboard</h5>
                    <a href="/leaderboard/3d-racing" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    {{-- @if ($topScores->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Player</th>
                                    <th>Distance</th>
                                    <th>Max Speed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topScores as $index => $score)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $score->user->name }}</td>
                                        <td>{{ $score->distance }}m</td>
                                        <td>{{ $score->max_speed }} km/h</td>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const gameContainer = document.getElementById('gameCanvas');
                const speedElement = document.getElementById('speed');
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
                let playerCar, opponentCar;
                let road = [];
                let obstacles = [];
                let distance = 0;
                let speed = 0;
                let maxSpeed = 0;
                let gameRunning = false;
                let gamePaused = false;
                let keys = {};
                let clock = new THREE.Clock();

                // Initialize Three.js
                function initThree() {
                    // Scene
                    scene = new THREE.Scene();
                    scene.background = new THREE.Color(0x87CEEB); // Sky blue
                    scene.fog = new THREE.Fog(0x87CEEB, 10, 100);

                    // Camera
                    camera = new THREE.PerspectiveCamera(75, gameContainer.clientWidth / 500, 0.1, 1000);
                    camera.position.set(0, 8, 15);
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

                    // Create road
                    createRoad();

                    // Create player car
                    createPlayerCar();

                    // Create opponent car
                    createOpponentCar();

                    // Create obstacles
                    createObstacles();
                }

                // Create road
                function createRoad() {
                    road = [];

                    // Road segments
                    for (let i = 0; i < 20; i++) {
                        const roadGeometry = new THREE.PlaneGeometry(10, 10);
                        const roadMaterial = new THREE.MeshStandardMaterial({
                            color: 0x333333, // Dark gray
                            roughness: 0.8
                        });
                        const roadSegment = new THREE.Mesh(roadGeometry, roadMaterial);

                        roadSegment.rotation.x = -Math.PI / 2;
                        roadSegment.position.set(0, 0, -i * 10);

                        scene.add(roadSegment);
                        road.push(roadSegment);

                        // Road markings
                        const markingGeometry = new THREE.PlaneGeometry(0.5, 5);
                        const markingMaterial = new THREE.MeshStandardMaterial({
                            color: 0xffffff, // White
                            roughness: 0.9
                        });
                        const marking = new THREE.Mesh(markingGeometry, markingMaterial);

                        marking.rotation.x = -Math.PI / 2;
                        marking.position.set(0, 0.01, -i * 10);

                        scene.add(marking);
                    }
                }

                // Create player car
                function createPlayerCar() {
                    const carGroup = new THREE.Group();

                    // Car body
                    const bodyGeometry = new THREE.BoxGeometry(1.5, 0.5, 3);
                    const bodyMaterial = new THREE.MeshStandardMaterial({
                        color: 0xe74c3c, // Red
                        roughness: 0.4
                    });
                    const carBody = new THREE.Mesh(bodyGeometry, bodyMaterial);
                    carBody.position.y = 0.25;
                    carBody.castShadow = true;
                    carGroup.add(carBody);

                    // Car roof
                    const roofGeometry = new THREE.BoxGeometry(1.2, 0.4, 1.5);
                    const roofMaterial = new THREE.MeshStandardMaterial({
                        color: 0xc0392b, // Dark red
                        roughness: 0.4
                    });
                    const carRoof = new THREE.Mesh(roofGeometry, roofMaterial);
                    carRoof.position.set(0, 0.7, -0.3);
                    carRoof.castShadow = true;
                    carGroup.add(carRoof);

                    // Wheels
                    const wheelGeometry = new THREE.CylinderGeometry(0.3, 0.3, 0.2, 16);
                    const wheelMaterial = new THREE.MeshStandardMaterial({
                        color: 0x2c3e50, // Dark blue
                        roughness: 0.8
                    });

                    const positions = [{
                            x: -0.7,
                            y: 0.15,
                            z: 1
                        },
                        {
                            x: 0.7,
                            y: 0.15,
                            z: 1
                        },
                        {
                            x: -0.7,
                            y: 0.15,
                            z: -1
                        },
                        {
                            x: 0.7,
                            y: 0.15,
                            z: -1
                        }
                    ];

                    for (let pos of positions) {
                        const wheel = new THREE.Mesh(wheelGeometry, wheelMaterial);
                        wheel.rotation.z = Math.PI / 2;
                        wheel.position.set(pos.x, pos.y, pos.z);
                        wheel.castShadow = true;
                        carGroup.add(wheel);
                    }

                    carGroup.position.set(0, 0, 5);
                    playerCar = carGroup;
                    scene.add(playerCar);
                }

                // Create opponent car
                function createOpponentCar() {
                    const carGroup = new THREE.Group();

                    // Car body
                    const bodyGeometry = new THREE.BoxGeometry(1.5, 0.5, 3);
                    const bodyMaterial = new THREE.MeshStandardMaterial({
                        color: 0x3498db, // Blue
                        roughness: 0.4
                    });
                    const carBody = new THREE.Mesh(bodyGeometry, bodyMaterial);
                    carBody.position.y = 0.25;
                    carBody.castShadow = true;
                    carGroup.add(carBody);

                    // Car roof
                    const roofGeometry = new THREE.BoxGeometry(1.2, 0.4, 1.5);
                    const roofMaterial = new THREE.MeshStandardMaterial({
                        color: 0x2980b9, // Dark blue
                        roughness: 0.4
                    });
                    const carRoof = new THREE.Mesh(roofGeometry, roofMaterial);
                    carRoof.position.set(0, 0.7, -0.3);
                    carRoof.castShadow = true;
                    carGroup.add(carRoof);

                    // Wheels
                    const wheelGeometry = new THREE.CylinderGeometry(0.3, 0.3, 0.2, 16);
                    const wheelMaterial = new THREE.MeshStandardMaterial({
                        color: 0x2c3e50, // Dark blue
                        roughness: 0.8
                    });

                    const positions = [{
                            x: -0.7,
                            y: 0.15,
                            z: 1
                        },
                        {
                            x: 0.7,
                            y: 0.15,
                            z: 1
                        },
                        {
                            x: -0.7,
                            y: 0.15,
                            z: -1
                        },
                        {
                            x: 0.7,
                            y: 0.15,
                            z: -1
                        }
                    ];

                    for (let pos of positions) {
                        const wheel = new THREE.Mesh(wheelGeometry, wheelMaterial);
                        wheel.rotation.z = Math.PI / 2;
                        wheel.position.set(pos.x, pos.y, pos.z);
                        wheel.castShadow = true;
                        carGroup.add(wheel);
                    }

                    carGroup.position.set(2, 0, -10);
                    opponentCar = carGroup;
                    scene.add(opponentCar);
                }

                // Create obstacles
                function createObstacles() {
                    obstacles = [];

                    for (let i = 0; i < 5; i++) {
                        const obstacleGeometry = new THREE.ConeGeometry(0.5, 1.5, 8);
                        const obstacleMaterial = new THREE.MeshStandardMaterial({
                            color: 0xf1c40f, // Yellow
                            roughness: 0.7
                        });
                        const obstacle = new THREE.Mesh(obstacleGeometry, obstacleMaterial);

                        obstacle.position.set(
                            (Math.random() - 0.5) * 8,
                            0.75,
                            -i * 20 - 15
                        );

                        obstacle.castShadow = true;

                        scene.add(obstacle);
                        obstacles.push(obstacle);
                    }
                }

                // Update game state
                function update() {
                    if (!gameRunning || gamePaused) return;

                    const delta = clock.getDelta();

                    // Player controls
                    if (keys['ArrowLeft'] || keys['a'] || keys['A']) {
                        playerCar.position.x -= 5 * delta;
                    }
                    if (keys['ArrowRight'] || keys['d'] || keys['D']) {
                        playerCar.position.x += 5 * delta;
                    }
                    if (keys['ArrowUp'] || keys['w'] || keys['W']) {
                        speed = Math.min(speed + 10 * delta, 200);
                    }
                    if (keys['ArrowDown'] || keys['s'] || keys['S']) {
                        speed = Math.max(speed - 15 * delta, -20);
                    } else {
                        // Natural deceleration
                        if (speed > 0) {
                            speed = Math.max(speed - 5 * delta, 0);
                        } else if (speed < 0) {
                            speed = Math.min(speed + 5 * delta, 0);
                        }
                    }

                    // Keep player in bounds
                    playerCar.position.x = Math.max(-4, Math.min(4, playerCar.position.x));

                    // Update speed display
                    speedElement.textContent = Math.floor(Math.abs(speed));

                    // Track max speed
                    if (Math.abs(speed) > maxSpeed) {
                        maxSpeed = Math.abs(speed);
                    }

                    // Move road
                    for (let segment of road) {
                        segment.position.z += speed * 0.05 * delta;

                        // Reset position if passed
                        if (segment.position.z > 10) {
                            segment.position.z -= 200;
                        }
                    }

                    // Move obstacles
                    for (let obstacle of obstacles) {
                        obstacle.position.z += speed * 0.05 * delta;

                        // Reset position if passed
                        if (obstacle.position.z > 10) {
                            obstacle.position.z -= 100;
                            obstacle.position.x = (Math.random() - 0.5) * 8;
                        }

                        // Check collision with player
                        if (
                            Math.abs(playerCar.position.x - obstacle.position.x) < 1 &&
                            Math.abs(playerCar.position.z - obstacle.position.z) < 2
                        ) {
                            // Collision detected
                            speed = -20; // Bounce back
                        }
                    }

                    // Move opponent car
                    opponentCar.position.z += (speed * 0.05 + 0.1) * delta;

                    // Simple AI for opponent
                    if (opponentCar.position.x > 0) {
                        opponentCar.position.x -= 2 * delta;
                    } else {
                        opponentCar.position.x += 2 * delta;
                    }

                    // Reset opponent position if passed
                    if (opponentCar.position.z > 10) {
                        opponentCar.position.z = -30;
                        opponentCar.position.x = (Math.random() - 0.5) * 8;
                    }

                    // Update distance
                    if (speed > 0) {
                        distance += speed * 0.05 * delta;
                        distanceElement.textContent = Math.floor(distance);
                    }

                    // Update camera to follow player
                    camera.position.x = playerCar.position.x;
                    camera.position.z = playerCar.position.z + 15;
                    camera.lookAt(playerCar.position.x, 0, playerCar.position.z - 10);
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
                    playerCar.position.set(0, 0, 5);
                    opponentCar.position.set(2, 0, -10);
                    speed = 0;
                    maxSpeed = 0;
                    distance = 0;
                    speedElement.textContent = 0;
                    distanceElement.textContent = 0;

                    // Reset obstacles
                    for (let i = 0; i < obstacles.length; i++) {
                        obstacles[i].position.set(
                            (Math.random() - 0.5) * 8,
                            0.75,
                            -i * 20 - 15
                        );
                    }

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
                        overlayMessage.textContent =
                            `Distance: ${Math.floor(distance)}m | Max Speed: ${Math.floor(maxSpeed)} km/h`;
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
                    overlayMessage.textContent =
                        `Distance: ${Math.floor(distance)}m | Max Speed: ${Math.floor(maxSpeed)} km/h`;
                    gameOverlay.style.display = 'flex';

                    // Save score if user is logged in
                    @auth
                    fetch('/api/games/3d-racing/score', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                distance: Math.floor(distance),
                                max_speed: Math.floor(maxSpeed)
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
