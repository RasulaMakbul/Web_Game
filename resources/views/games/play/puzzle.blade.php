<x-layout title="Play Puzzle Master">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Puzzle Master</h1>
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
                        <span class="badge bg-primary">Level: <span id="level">1</span></span>
                        <span class="badge bg-info">Moves: <span id="moves">0</span></span>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div id="puzzleContainer" class="d-flex justify-content-center">
                        <!-- Puzzle will be generated here -->
                    </div>
                    <div class="mt-3">
                        <button id="startBtn" class="btn btn-success">Start Game</button>
                        <button id="resetBtn" class="btn btn-danger">Reset</button>
                        <button id="hintBtn" class="btn btn-warning">Hint</button>
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
                    <p>Solve increasingly difficult puzzles in this brain-teasing challenge.</p>
                    <ul>
                        <li>Click on tiles to move them</li>
                        <li>Arrange tiles in numerical order</li>
                        <li>Complete the puzzle with the fewest moves</li>
                        <li>Use hints if you get stuck</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Leaderboard</h5>
                    <a href="/leaderboard/puzzle" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    {{-- @if ($topScores->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Player</th>
                                    <th>Level</th>
                                    <th>Moves</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topScores as $index => $score)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $score->user->name }}</td>
                                        <td>{{ $score->level }}</td>
                                        <td>{{ $score->moves }}</td>
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
                const puzzleContainer = document.getElementById('puzzleContainer');
                const levelElement = document.getElementById('level');
                const movesElement = document.getElementById('moves');
                const startBtn = document.getElementById('startBtn');
                const resetBtn = document.getElementById('resetBtn');
                const hintBtn = document.getElementById('hintBtn');

                // Game variables
                let tiles = [];
                let emptyPos = {
                    row: 3,
                    col: 3
                };
                let level = 1;
                let moves = 0;
                let gameStarted = false;
                let tileSize = 80;
                let gridSize = 4;

                // Initialize game
                function initGame() {
                    // Clear container
                    puzzleContainer.innerHTML = '';
                    tiles = [];
                    moves = 0;
                    movesElement.textContent = moves;

                    // Set grid size based on level
                    if (level <= 3) {
                        gridSize = 3;
                    } else if (level <= 6) {
                        gridSize = 4;
                    } else {
                        gridSize = 5;
                    }

                    tileSize = Math.min(80, 320 / gridSize);

                    // Create tiles
                    for (let row = 0; row < gridSize; row++) {
                        tiles[row] = [];
                        for (let col = 0; col < gridSize; col++) {
                            if (row === gridSize - 1 && col === gridSize - 1) {
                                // Empty space
                                tiles[row][col] = 0;
                                emptyPos = {
                                    row,
                                    col
                                };
                            } else {
                                tiles[row][col] = row * gridSize + col + 1;
                            }
                        }
                    }

                    // Shuffle tiles
                    shuffleTiles();

                    // Create tile elements
                    renderPuzzle();
                }

                // Shuffle tiles
                function shuffleTiles() {
                    // Make random moves to shuffle
                    const shuffleMoves = gridSize * gridSize * 10;

                    for (let i = 0; i < shuffleMoves; i++) {
                        const directions = [];

                        if (emptyPos.row > 0) directions.push('up');
                        if (emptyPos.row < gridSize - 1) directions.push('down');
                        if (emptyPos.col > 0) directions.push('left');
                        if (emptyPos.col < gridSize - 1) directions.push('right');

                        const direction = directions[Math.floor(Math.random() * directions.length)];

                        switch (direction) {
                            case 'up':
                                swapTiles(emptyPos.row - 1, emptyPos.col);
                                break;
                            case 'down':
                                swapTiles(emptyPos.row + 1, emptyPos.col);
                                break;
                            case 'left':
                                swapTiles(emptyPos.row, emptyPos.col - 1);
                                break;
                            case 'right':
                                swapTiles(emptyPos.row, emptyPos.col + 1);
                                break;
                        }
                    }

                    // Reset move counter after shuffling
                    moves = 0;
                    movesElement.textContent = moves;
                }

                // Render puzzle
                function renderPuzzle() {
                    puzzleContainer.innerHTML = '';
                    puzzleContainer.style.width = `${gridSize * tileSize}px`;
                    puzzleContainer.style.height = `${gridSize * tileSize}px`;
                    puzzleContainer.style.position = 'relative';
                    puzzleContainer.style.backgroundColor = '#f8f9fa';
                    puzzleContainer.style.border = '2px solid #dee2e6';
                    puzzleContainer.style.borderRadius = '5px';

                    for (let row = 0; row < gridSize; row++) {
                        for (let col = 0; col < gridSize; col++) {
                            if (tiles[row][col] !== 0) {
                                const tile = document.createElement('div');
                                tile.className = 'puzzle-tile';
                                tile.textContent = tiles[row][col];
                                tile.style.width = `${tileSize - 4}px`;
                                tile.style.height = `${tileSize - 4}px`;
                                tile.style.position = 'absolute';
                                tile.style.left = `${col * tileSize + 2}px`;
                                tile.style.top = `${row * tileSize + 2}px`;
                                tile.style.backgroundColor = '#4e73df';
                                tile.style.color = 'white';
                                tile.style.display = 'flex';
                                tile.style.alignItems = 'center';
                                tile.style.justifyContent = 'center';
                                tile.style.fontSize = `${tileSize / 3}px`;
                                tile.style.fontWeight = 'bold';
                                tile.style.borderRadius = '5px';
                                tile.style.cursor = 'pointer';
                                tile.style.transition = 'all 0.2s ease';
                                tile.dataset.row = row;
                                tile.dataset.col = col;

                                tile.addEventListener('click', function() {
                                    if (!gameStarted) return;

                                    const tileRow = parseInt(this.dataset.row);
                                    const tileCol = parseInt(this.dataset.col);

                                    // Check if tile is adjacent to empty space
                                    if (
                                        (Math.abs(tileRow - emptyPos.row) === 1 && tileCol === emptyPos.col) ||
                                        (Math.abs(tileCol - emptyPos.col) === 1 && tileRow === emptyPos.row)
                                    ) {
                                        swapTiles(tileRow, tileCol);
                                        moves++;
                                        movesElement.textContent = moves;

                                        // Check if puzzle is solved
                                        if (isPuzzleSolved()) {
                                            setTimeout(() => {
                                                levelComplete();
                                            }, 300);
                                        }
                                    }
                                });

                                tile.addEventListener('mouseenter', function() {
                                    if (gameStarted) {
                                        this.style.backgroundColor = '#2e59d9';
                                        this.style.transform = 'scale(1.05)';
                                    }
                                });

                                tile.addEventListener('mouseleave', function() {
                                    this.style.backgroundColor = '#4e73df';
                                    this.style.transform = 'scale(1)';
                                });

                                puzzleContainer.appendChild(tile);
                            }
                        }
                    }
                }

                // Swap tiles
                function swapTiles(row, col) {
                    tiles[emptyPos.row][emptyPos.col] = tiles[row][col];
                    tiles[row][col] = 0;
                    emptyPos = {
                        row,
                        col
                    };
                    renderPuzzle();
                }

                // Check if puzzle is solved
                function isPuzzleSolved() {
                    let expectedValue = 1;

                    for (let row = 0; row < gridSize; row++) {
                        for (let col = 0; col < gridSize; col++) {
                            if (row === gridSize - 1 && col === gridSize - 1) {
                                // Last position should be empty
                                if (tiles[row][col] !== 0) return false;
                            } else {
                                if (tiles[row][col] !== expectedValue) return false;
                                expectedValue++;
                            }
                        }
                    }

                    return true;
                }

                // Level complete
                function levelComplete() {
                    gameStarted = false;

                    // Calculate score (lower is better)
                    const score = Math.max(0, 1000 - moves * 10);

                    // Save score if user is logged in
                    @auth
                    fetch('/api/games/puzzle/score', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                level: level,
                                moves: moves
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Score saved successfully');
                            }
                        });
                @endauth

                // Show completion message
                if (confirm(`Level ${level} completed in ${moves} moves! Move to next level?`)) {
                    level++;
                    levelElement.textContent = level;
                    initGame();
                    startGame();
                } else {
                    initGame();
                }
            }

            // Start game
            function startGame() {
                if (gameStarted) return;

                gameStarted = true;
                startBtn.disabled = true;
                resetBtn.disabled = false;
                hintBtn.disabled = false;
            }

            // Reset game
            function resetGame() {
                gameStarted = false;
                startBtn.disabled = false;
                resetBtn.disabled = true;
                hintBtn.disabled = true;
                initGame();
            }

            // Show hint
            function showHint() {
                if (!gameStarted) return;

                // Find a tile that can be moved and highlight it
                const directions = [];

                if (emptyPos.row > 0) directions.push({
                    row: emptyPos.row - 1,
                    col: emptyPos.col
                });
                if (emptyPos.row < gridSize - 1) directions.push({
                    row: emptyPos.row + 1,
                    col: emptyPos.col
                });
                if (emptyPos.col > 0) directions.push({
                    row: emptyPos.row,
                    col: emptyPos.col - 1
                });
                if (emptyPos.col < gridSize - 1) directions.push({
                    row: emptyPos.row,
                    col: emptyPos.col + 1
                });

                if (directions.length > 0) {
                    const hint = directions[Math.floor(Math.random() * directions.length)];
                    const tiles = document.querySelectorAll('.puzzle-tile');

                    for (let tile of tiles) {
                        const tileRow = parseInt(tile.dataset.row);
                        const tileCol = parseInt(tile.dataset.col);

                        if (tileRow === hint.row && tileCol === hint.col) {
                            // Highlight the tile
                            tile.style.backgroundColor = '#f6c23e';
                            tile.style.transform = 'scale(1.1)';

                            // Reset after 1 second
                            setTimeout(() => {
                                tile.style.backgroundColor = '#4e73df';
                                tile.style.transform = 'scale(1)';
                            }, 1000);

                            break;
                        }
                    }
                }
            }

            // Event listeners
            startBtn.addEventListener('click', startGame); resetBtn.addEventListener('click', resetGame); hintBtn
            .addEventListener('click', showHint);

            // Initialize game on load
            initGame();
            });
        </script>
    @endpush
</x-layout>
