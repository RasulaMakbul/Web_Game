<!-- resources/views/components/game-image.blade.php -->
@php
    $imageUrl = $game->cover_image_url;
@endphp

<img src="{{ $imageUrl }}" class="{{ $class }}" alt="{{ $alt }}"
    onerror="this.src='https://via.placeholder.com/300x400/607D8B/FFFFFF?text={{ urlencode($alt) }}'">
