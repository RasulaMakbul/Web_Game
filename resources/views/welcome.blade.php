<!-- resources/views/welcome.blade.php -->
<x-layout title="Welcome to GameHub">
    <div class="hero-section mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white">Welcome to GameHub</h1>
                    <p class="lead text-white">Play amazing 2D and 3D games online. Save your progress and compete on
                        leaderboards!</p>
                    <a href="{{ route('games.index') }}" class="btn btn-primary btn-lg">Continue Playing</a>
                    {{-- @guest
                        <div class="d-flex gap-3">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Join Now</a>
                            <a href="{{ route('games.index') }}" class="btn btn-outline-light btn-lg">Browse Games</a>
                        </div>
                    @else
                        <a href="{{ route('games.index') }}" class="btn btn-primary btn-lg">Continue Playing</a>
                    @endguest --}}
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1511512578047-dfb367046420?ixlib=rb-4.0.3"
                        class="img-fluid rounded" alt="Gaming">
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <h2 class="text-center mb-4">Featured Games</h2>

        <div class="row g-4">
            <!-- Snake Game -->
            <div class="col-md-4">
                <div class="card game-card">
                    <img src="https://images.unsplash.com/photo-1614730321146-b6fa6a46bcb4?ixlib=rb-4.0.3"
                        class="card-img-top game-thumbnail" alt="Snake Game">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title">Snake Classic</h5>
                            <span class="badge bg-success">2D</span>
                        </div>
                        <p class="card-text">Guide the snake to eat food and grow longer without hitting walls or
                            yourself.</p>
                        <div class="d-flex justify-content-between">
                            <a href="/games/snake" class="btn btn-primary">Play Now</a>
                            <a href="/leaderboard/snake" class="btn btn-outline-secondary"><i class="bi bi-trophy"></i>
                                Leaderboard</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Memory Match -->
            <div class="col-md-4">
                <div class="card game-card">
                    <img src="https://images.unsplash.com/photo-1626808642875-4aa6a2e2b5e5?ixlib=rb-4.0.3"
                        class="card-img-top game-thumbnail" alt="Memory Match">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title">Memory Match</h5>
                            <span class="badge bg-success">2D</span>
                        </div>
                        <p class="card-text">Test your memory by matching pairs of cards in this classic brain training
                            game.</p>
                        <div class="d-flex justify-content-between">
                            <a href="/games/memory" class="btn btn-primary">Play Now</a>
                            <a href="/leaderboard/memory" class="btn btn-outline-secondary"><i class="bi bi-trophy"></i>
                                Leaderboard</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Space Shooter -->
            <div class="col-md-4">
                <div class="card game-card">
                    <img src="https://images.unsplash.com/photo-1614730321146-b6fa6a46bcb4?ixlib=rb-4.0.3"
                        class="card-img-top game-thumbnail" alt="Space Shooter">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title">Space Shooter</h5>
                            <span class="badge bg-success">2D</span>
                        </div>
                        <p class="card-text">Defend Earth from alien invaders in this action-packed space shooter.</p>
                        <div class="d-flex justify-content-between">
                            <a href="/games/space-shooter" class="btn btn-primary">Play Now</a>
                            <a href="/leaderboard/space-shooter" class="btn btn-outline-secondary"><i
                                    class="bi bi-trophy"></i> Leaderboard</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3D Cube Runner -->
            <div class="col-md-4">
                <div class="card game-card">
                    <img src="https://images.unsplash.com/photo-1551650975-87de7e7f2e1b?ixlib=rb-4.0.3"
                        class="card-img-top game-thumbnail" alt="3D Cube Runner">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title">3D Cube Runner</h5>
                            <span class="badge badge-3d">3D</span>
                        </div>
                        <p class="card-text">Navigate through a 3D cube world, avoiding obstacles and collecting gems.
                        </p>
                        <div class="d-flex justify-content-between">
                            <a href="/games/cube-runner" class="btn btn-primary">Play Now</a>
                            <a href="/leaderboard/cube-runner" class="btn btn-outline-secondary"><i
                                    class="bi bi-trophy"></i> Leaderboard</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3D Racing -->
            <div class="col-md-4">
                <div class="card game-card">
                    <img src="https://images.unsplash.com/photo-1549399542-7e244f5c9c68?ixlib=rb-4.0.3"
                        class="card-img-top game-thumbnail" alt="3D Racing">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title">3D Speed Racer</h5>
                            <span class="badge badge-3d">3D</span>
                        </div>
                        <p class="card-text">Race against AI opponents in this high-speed 3D racing game.</p>
                        <div class="d-flex justify-content-between">
                            <a href="/games/3d-racing" class="btn btn-primary">Play Now</a>
                            <a href="/leaderboard/3d-racing" class="btn btn-outline-secondary"><i
                                    class="bi bi-trophy"></i> Leaderboard</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Puzzle Master -->
            <div class="col-md-4">
                <div class="card game-card">
                    <img src="https://images.unsplash.com/photo-1588482575657-6604c2d6e1c3?ixlib=rb-4.0.3"
                        class="card-img-top game-thumbnail" alt="Puzzle Master">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title">Puzzle Master</h5>
                            <span class="badge bg-success">2D</span>
                        </div>
                        <p class="card-text">Solve increasingly difficult puzzles in this brain-teasing challenge.</p>
                        <div class="d-flex justify-content-between">
                            <a href="/games/puzzle" class="btn btn-primary">Play Now</a>
                            <a href="/leaderboard/puzzle" class="btn btn-outline-secondary"><i
                                    class="bi bi-trophy"></i> Leaderboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <h2 class="text-center mb-4">Top Rated Games</h2>

        <div class="row">
            @foreach (\App\Models\Game::whereNotNull('rating')->orderBy('rating', 'desc')->take(3)->get() as $game)
                <div class="col-md-4 mb-4">
                    <div class="card game-card">
                        <img src="{{ $game->cover_image_url }}" class="card-img-top game-thumbnail"
                            alt="{{ $game->name }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title">{{ $game->name }}</h5>
                                <span class="badge bg-warning text-dark">{{ $game->rating }}/10</span>
                            </div>
                            <p class="card-text">{{ Str::limit($game->description, 100) }}</p>
                            <a href="{{ route('games.show', $game->slug) }}" class="btn btn-sm btn-primary">View
                                Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layout>
