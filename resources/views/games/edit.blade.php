<x-layout title="Edit Game: {{ $game->name }}">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Game: {{ $game->name }}</h1>
        <div>
            <a href="{{ route('games.show', $game->slug) }}"
                class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to Game
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('games.update', $game->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Game Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', $game->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                id="slug" name="slug" value="{{ old('slug', $game->slug) }}" required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="5">{{ old('description', $game->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="release_date" class="form-label">Release Date</label>
                                    <input type="date"
                                        class="form-control @error('release_date') is-invalid @enderror"
                                        id="release_date" name="release_date"
                                        value="{{ old('release_date', $game->release_date ? $game->release_date->format('Y-m-d') : '') }}">
                                    @error('release_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rating" class="form-label">Rating (0-10)</label>
                                    <input type="number" class="form-control @error('rating') is-invalid @enderror"
                                        id="rating" name="rating" value="{{ old('rating', $game->rating) }}"
                                        min="0" max="10" step="0.1">
                                    @error('rating')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="developer" class="form-label">Developer</label>
                                    <input type="text" class="form-control @error('developer') is-invalid @enderror"
                                        id="developer" name="developer"
                                        value="{{ old('developer', $game->developer) }}">
                                    @error('developer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="publisher" class="form-label">Publisher</label>
                                    <input type="text" class="form-control @error('publisher') is-invalid @enderror"
                                        id="publisher" name="publisher"
                                        value="{{ old('publisher', $game->publisher) }}">
                                    @error('publisher')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="genre" class="form-label">Genre</label>
                                    <input type="text" class="form-control @error('genre') is-invalid @enderror"
                                        id="genre" name="genre" value="{{ old('genre', $game->genre) }}">
                                    @error('genre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="platforms" class="form-label">Platforms (comma separated)</label>
                                    <input type="text" class="form-control @error('platforms') is-invalid @enderror"
                                        id="platforms" name="platforms"
                                        value="{{ old('platforms', $game->platforms) }}">
                                    @error('platforms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror"
                                id="cover_image" name="cover_image" accept="image/*">
                            @error('cover_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Accepted formats: jpeg, png, jpg, gif. Max size: 2MB.</div>

                            @if ($game->cover_image)
                                <div class="mt-2">
                                    <p>Current Cover:</p>
                                    <img src="{{ $game->cover_image_url }}" class="img-thumbnail"
                                        alt="{{ $game->name }}" style="max-height: 200px;">
                                </div>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Game</button>
                            <a href="{{ route('games.show', $game->slug) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layout>
