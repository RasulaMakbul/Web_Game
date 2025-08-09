<x-layout title="All Games">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">All Games</h1>
        <div>
            <a href="{{ route('games.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="bi bi-plus-circle"></i> Add New Game
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('games.search') }}" method="GET" class="d-flex">
                <input class="form-control me-2" type="search" name="query" placeholder="Search games..."
                    aria-label="Search" value="{{ request('query') }}">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('games.top-rated') }}" class="btn btn-outline-primary btn-sm">Top Rated</a>
                <a href="{{ route('games.recent-releases') }}" class="btn btn-outline-primary btn-sm">Recent</a>
                <a href="{{ route('games.upcoming') }}" class="btn btn-outline-primary btn-sm">Upcoming</a>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse ($games as $game)
            <div class="col-md-3 mb-4">
                <div class="card game-card">
                    <!-- Simple image tag instead of component for debugging -->
                    <img src="{{ $game->cover_image_url }}" class="card-img-top game-thumbnail"
                        alt="{{ $game->name }}"
                        onerror="this.src='https://via.placeholder.com/300x400/607D8B/FFFFFF?text={{ urlencode($game->name) }}'">
                    <div class="card-body">
                        <h5 class="card-title">{{ $game->name }}</h5>
                        <p class="card-text">
                            <small class="text-muted">{{ $game->genre }}</small>
                        </p>
                        @if ($game->rating)
                            <div class="mb-2">
                                <span class="badge bg-warning text-dark">{{ $game->rating }}/10</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('games.show', $game->slug) }}" class="btn btn-sm btn-primary">View
                            Details</a>
                        @if ($game->isPlayable())
                            <a href="{{ route('games.play', $game->slug) }}" class="btn btn-sm btn-success">Play</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <h4>No games found</h4>
                    <p class="mb-0">There are no games in the database yet.</p>
                    <a href="{{ route('games.create') }}" class="btn btn-primary mt-2">Add Your First Game</a>
                </div>
            </div>
        @endforelse
    </div>

    @if ($games->count() > 0)
        <div class="d-flex justify-content-center">
            {{ $games->links() }}
        </div>
    @endif
</x-layout>
