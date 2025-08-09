<!-- resources/views/games/show.blade.php -->
<x-layout title="{{ $game->name }}">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $game->name }}</h1>
        <div>
            <a href="{{ route('games.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to Games
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <img src="{{ $game->cover_image_url }}" class="card-img-top" alt="{{ $game->name }}">
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        @if ($game->rating)
                            <span class="badge bg-warning text-dark fs-6 me-2">{{ $game->rating }}/10</span>
                        @endif
                        <span class="badge bg-info text-dark fs-6 me-2">{{ $game->genre }}</span>
                        <span class="badge bg-secondary fs-6">{{ $game->formatted_release_date }}</span>
                    </div>

                    <div class="mb-3">
                        <h5>Platforms</h5>
                        <div class="d-flex flex-wrap">
                            @foreach ($game->platforms_array as $platform)
                                <span class="badge bg-light text-dark me-2 mb-2">{{ $platform }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Developer</h5>
                            <p>{{ $game->developer ?: 'Unknown' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Publisher</h5>
                            <p>{{ $game->publisher ?: 'Unknown' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Description</h5>
                        <p>{{ $game->description ?: 'No description available.' }}</p>
                    </div>

                    <div class="d-flex gap-2">
                        @if ($game->isPlayable())
                            <a href="{{ route('games.play', $game->slug) }}" class="btn btn-success">
                                <i class="bi bi-play-circle"></i> Play Game
                            </a>
                        @endif
                        <a href="{{ route('games.edit', $game->slug) }}" class="btn btn-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('games.destroy', $game->slug) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this game?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
