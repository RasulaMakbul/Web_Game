<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $games = [
            [
                'name' => 'Snake Classic',
                'slug' => 'snake',
                'description' => 'Guide the snake to eat food and grow longer without hitting walls or yourself in this classic arcade game.',
                'release_date' => '2023-01-01',
                'developer' => 'GameHub Studio',
                'publisher' => 'GameHub Studio',
                'genre' => 'Arcade',
                'platforms' => 'Web',
                'rating' => 8.5,
            ],
            [
                'name' => 'Memory Match',
                'slug' => 'memory',
                'description' => 'Test your memory by matching pairs of cards in this classic brain training game.',
                'release_date' => '2023-01-15',
                'developer' => 'GameHub Studio',
                'publisher' => 'GameHub Studio',
                'genre' => 'Puzzle',
                'platforms' => 'Web',
                'rating' => 7.8,
            ],
            [
                'name' => 'Space Shooter',
                'slug' => 'space-shooter',
                'description' => 'Defend Earth from alien invaders in this action-packed space shooter game.',
                'release_date' => '2023-02-01',
                'developer' => 'GameHub Studio',
                'publisher' => 'GameHub Studio',
                'genre' => 'Action',
                'platforms' => 'Web',
                'rating' => 9.2,
            ],
            [
                'name' => '3D Cube Runner',
                'slug' => 'cube-runner',
                'description' => 'Navigate through a 3D cube world, avoiding obstacles and collecting gems in this endless runner.',
                'release_date' => '2023-02-15',
                'developer' => 'GameHub Studio',
                'publisher' => 'GameHub Studio',
                'genre' => 'Action',
                'platforms' => 'Web',
                'rating' => 8.7,
            ],
            [
                'name' => '3D Speed Racer',
                'slug' => '3d-racing',
                'description' => 'Race against AI opponents in this high-speed 3D racing game.',
                'release_date' => '2023-03-01',
                'developer' => 'GameHub Studio',
                'publisher' => 'GameHub Studio',
                'genre' => 'Racing',
                'platforms' => 'Web',
                'rating' => 9.0,
            ],
            [
                'name' => 'Puzzle Master',
                'slug' => 'puzzle',
                'description' => 'Solve increasingly difficult sliding puzzles in this brain-teasing challenge.',
                'release_date' => '2023-03-15',
                'developer' => 'GameHub Studio',
                'publisher' => 'GameHub Studio',
                'genre' => 'Puzzle',
                'platforms' => 'Web',
                'rating' => 8.3,
            ],
        ];

        foreach ($games as $game) {
            Game::create($game);
        }
    }
}