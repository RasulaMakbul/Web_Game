<x-layout title="Play Memory Match">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Memory Match</h1>
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
                        <span class="badge bg-primary">Moves: <span id="moves">0</span></span>
                        <span class="badge bg-info">Time: <span id="timer">00:00</span></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <button id="startBtn" class="btn btn-success">Start Game</button>
                            <button id="resetBtn" class="btn btn-danger">Reset</button>
                        </div>
                        <div>
                            <select id="difficulty" class="form-select">
                                <option value="easy">Easy (4x3)</option>
                                <option value="medium">Medium (4x4)</option>
                                <option value="hard">Hard (6x4)</option>
                            </select>
                        </div>
                    </div>
                    <div id="gameBoard" class="d-flex flex-wrap justify-content-center"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Game Instructions</h5>
                </div>
                <div class="card-body">
                    <p>Match pairs of cards by flipping them over. Try to complete the game with the fewest moves and
                        fastest time.</p>
                    <ul>
                        <li>Click on a card to flip it</li>
                        <li>Find matching pairs of cards</li>
                        <li>Complete all pairs to win</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Leaderboard</h5>
                    <a href="/leaderboard/memory" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    {{-- @if ($topScores->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Player</th>
                                    <th>Moves</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topScores as $index => $score)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $score->user->name }}</td>
                                        <td>{{ $score->moves }}</td>
                                        <td>{{ gmdate('i:s', $score->time) }}</td>
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
                const gameBoard = document.getElementById('gameBoard');
                const movesElement = document.getElementById('moves');
                const timerElement = document.getElementById('timer');
                const startBtn = document.getElementById('startBtn');
                const resetBtn = document.getElementById('resetBtn');
                const difficultySelect = document.getElementById('difficulty');

                // Game variables
                let cards = [];
                let flippedCards = [];
                let matchedPairs = 0;
                let moves = 0;
                let gameStarted = false;
                let gameTimer;
                let seconds = 0;
                let gameSize = {
                    rows: 4,
                    cols: 3
                };

                // Card icons (using emojis for simplicity)
                const cardIcons = ['🍎', '🍌', '🍇', '🍊', '🍓', '🍒', '🍑', '🥝', '🍍', '🥭', '🍉', '🥥'];

                // Initialize game
                function initGame() {
                    // Clear board
                    gameBoard.innerHTML = '';
                    cards = [];
                    flippedCards = [];
                    matchedPairs = 0;
                    moves = 0;
                    seconds = 0;
                    movesElement.textContent = moves;
                    timerElement.textContent = '00:00';

                    // Set game size based on difficulty
                    switch (difficultySelect.value) {
                        case 'easy':
                            gameSize = {
                                rows: 3,
                                cols: 4
                            };
                            break;
                        case 'medium':
                            gameSize = {
                                rows: 4,
                                cols: 4
                            };
                            break;
                        case 'hard':
                            gameSize = {
                                rows: 4,
                                cols: 6
                            };
                            break;
                    }

                    // Create card pairs
                    const totalCards = gameSize.rows * gameSize.cols;
                    const pairsNeeded = totalCards / 2;
                    const selectedIcons = cardIcons.slice(0, pairsNeeded);
                    const cardValues = [...selectedIcons, ...selectedIcons];

                    // Shuffle cards
                    for (let i = cardValues.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [cardValues[i], cardValues[j]] = [cardValues[j], cardValues[i]];
                    }

                    // Create card elements
                    for (let i = 0; i < totalCards; i++) {
                        const card = document.createElement('div');
                        card.className = 'card memory-card';
                        card.dataset.value = cardValues[i];
                        card.dataset.index = i;

                        const cardInner = document.createElement('div');
                        cardInner.className = 'card-inner';

                        const cardFront = document.createElement('div');
                        cardFront.className = 'card-front';
                        cardFront.textContent = '?';

                        const cardBack = document.createElement('div');
                        cardBack.className = 'card-back';
                        cardBack.textContent = cardValues[i];

                        cardInner.appendChild(cardFront);
                        cardInner.appendChild(cardBack);
                        card.appendChild(cardInner);

                        card.addEventListener('click', flipCard);

                        gameBoard.appendChild(card);
                        cards.push(card);
                    }

                    // Set board dimensions
                    gameBoard.style.width = `${gameSize.cols * 110}px`;
                }

                // Flip card
                function flipCard() {
                    if (!gameStarted || flippedCards.length >= 2 || this.classList.contains('flipped') || this.classList
                        .contains('matched')) {
                        return;
                    }

                    this.classList.add('flipped');
                    flippedCards.push(this);

                    if (flippedCards.length === 2) {
                        moves++;
                        movesElement.textContent = moves;

                        const card1 = flippedCards[0];
                        const card2 = flippedCards[1];

                        if (card1.dataset.value === card2.dataset.value) {
                            // Match found
                            setTimeout(() => {
                                card1.classList.add('matched');
                                card2.classList.add('matched');
                                matchedPairs++;

                                if (matchedPairs === (gameSize.rows * gameSize.cols) / 2) {
                                    endGame();
                                }

                                flippedCards = [];
                            }, 500);
                        } else {
                            // No match
                            setTimeout(() => {
                                card1.classList.remove('flipped');
                                card2.classList.remove('flipped');
                                flippedCards = [];
                            }, 1000);
                        }
                    }
                }

                // Start game
                function startGame() {
                    if (gameStarted) return;

                    gameStarted = true;
                    startBtn.disabled = true;

                    // Start timer
                    gameTimer = setInterval(() => {
                        seconds++;
                        const mins = Math.floor(seconds / 60);
                        const secs = seconds % 60;
                        timerElement.textContent =
                            `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    }, 1000);
                }

                // Reset game
                function resetGame() {
                    clearInterval(gameTimer);
                    gameStarted = false;
                    startBtn.disabled = false;
                    initGame();
                }

                // End game
                function endGame() {
                    clearInterval(gameTimer);
                    gameStarted = false;
                    startBtn.disabled = false;

                    // Calculate score (lower is better)
                    const score = Math.round(10000 / (moves * seconds));

                    // Save score if user is logged in
                    @auth
                    fetch('/api/games/memory/score', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                moves: moves,
                                time: seconds,
                                difficulty: difficultySelect.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(
                                    `Congratulations! You completed the game in ${moves} moves and ${timerElement.textContent}. Your score has been saved!`
                                    );
                            } else {
                                alert(
                                    `Congratulations! You completed the game in ${moves} moves and ${timerElement.textContent}.`
                                    );
                            }
                        });
                @else
                    alert(
                        `Congratulations! You completed the game in ${moves} moves and ${timerElement.textContent}. Log in to save your scores!`
                        );
                @endauth
            }

            // Event listeners
            startBtn.addEventListener('click', startGame); resetBtn.addEventListener('click',
                resetGame); difficultySelect.addEventListener('change', initGame);

            // Initialize game on load
            initGame();
            });
        </script>

        <style>
            .memory-card {
                width: 100px;
                height: 100px;
                margin: 5px;
                perspective: 1000px;
                cursor: pointer;
            }

            .card-inner {
                position: relative;
                width: 100%;
                height: 100%;
                text-align: center;
                transition: transform 0.6s;
                transform-style: preserve-3d;
            }

            .memory-card.flipped .card-inner {
                transform: rotateY(180deg);
            }

            .card-front,
            .card-back {
                position: absolute;
                width: 100%;
                height: 100%;
                backface-visibility: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 5px;
                font-size: 2rem;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .card-front {
                background-color: #3498db;
                color: white;
            }

            .card-back {
                background-color: #2ecc71;
                color: white;
                transform: rotateY(180deg);
            }

            .memory-card.matched .card-back {
                background-color: #f1c40f;
            }
        </style>
    @endpush
</x-layout>
