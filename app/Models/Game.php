<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'release_date',
        'developer',
        'publisher',
        'genre',
        'platforms',
        'cover_image',
        'rating',
    ];

    protected $casts = [
        'release_date' => 'date',
        'rating' => 'float',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the formatted release date.
     *
     * @return string
     */
    public function getFormattedReleaseDateAttribute()
    {
        return $this->release_date ? $this->release_date->format('F j, Y') : 'TBD';
    }

    /**
     * Get the cover image URL.
     *
     * @return string
     */
    public function getCoverImageUrlAttribute()
    {
        // If we have a custom cover image in storage, use it
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }

        // Otherwise, use a placeholder image based on the game slug
        return $this->getPlaceholderImageUrl();
    }

    private function getPlaceholderImageUrl()
    {
        $colors = [
            'snake' => '4CAF50',      // Green
            'memory' => '2196F3',     // Blue
            'space-shooter' => 'F44336', // Red
            'cube-runner' => '9C27B0',  // Purple
            '3d-racing' => 'FF9800',    // Orange
            'puzzle' => '00BCD4',      // Cyan
        ];

        $color = $colors[$this->slug] ?? '607D8B'; // Default gray

        return "https://via.placeholder.com/300x400/{$color}/FFFFFF?text=" . urlencode($this->name);
    }

    /**
     * Get the platforms as an array.
     *
     * @return array
     */
    public function getPlatformsArrayAttribute()
    {
        if ($this->platforms) {
            return explode(', ', $this->platforms);
        }

        return [];
    }

    /**
     * Check if this game is playable.
     *
     * @return bool
     */
    public function isPlayable()
    {
        $playableGames = ['snake', 'memory', 'space-shooter', 'cube-runner', '3d-racing', 'puzzle'];
        return in_array($this->slug, $playableGames);
    }
}