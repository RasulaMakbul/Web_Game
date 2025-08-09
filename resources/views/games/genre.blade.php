<x-layout title="{{ $genre }} Games">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $genre }} Games</h1>
        <div>
            <a href="{{ route('games.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to All Games
            </a>
        </div>
    </div>

    <div class="row">
        @forelse ($games as $game)
            <div class="col-md-3 mb-4">
                <div class="card game-card">
                    <x-game-image :game="$game" class="card-img-top game-thumbnail" />
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
                        <a href="{{ route('games.show', $game->slug) }}" class="btn btn-sm btn-primary">View Details</a>
                        @if ($game->isPlayable())
                            <a href="{{ route('games.play', $game->slug) }}" class="btn btn-sm btn-success">Play</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No games found in this genre.
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $games->links() }}
    </div>
</x-layout>
