<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $games = Game::latest()->paginate(12);
        return view('games.index', compact('games'));
    }

    public function create()
    {
        return view('games.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games,slug',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'platforms' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'nullable|numeric|min:0|max:10|between:0,10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $game = new Game();
        $game->name = $request->name;
        $game->slug = $request->slug;
        $game->description = $request->description;
        $game->release_date = $request->release_date;
        $game->developer = $request->developer;
        $game->publisher = $request->publisher;
        $game->genre = $request->genre;
        $game->platforms = $request->platforms;
        $game->rating = $request->rating;

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('games/covers', $imageName, 'public');
            $game->cover_image = $imagePath;
        }

        $game->save();

        return redirect()->route('games.show', $game->id)
            ->with('success', 'Game created successfully!');
    }

    public function show($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        return view('games.show', compact('game'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        return view('games.edit', compact('game'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $gameId = $game->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games,slug,' . $gameId,
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'platforms' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'nullable|numeric|min:0|max:10|between:0,10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $game->name = $request->name;
        $game->slug = $request->slug;
        $game->description = $request->description;
        $game->release_date = $request->release_date;
        $game->developer = $request->developer;
        $game->publisher = $request->publisher;
        $game->genre = $request->genre;
        $game->platforms = $request->platforms;
        $game->rating = $request->rating;

        // Handle cover image update
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($game->cover_image) {
                Storage::disk('public')->delete($game->cover_image);
            }

            $image = $request->file('cover_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('games/covers', $imageName, 'public');
            $game->cover_image = $imagePath;
        }

        $game->save();

        return redirect()->route('games.show', $game->slug)
            ->with('success', 'Game updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $game = Game::findOrFail($id);

        // Delete cover image if exists
        if ($game->cover_image) {
            Storage::disk('public')->delete($game->cover_image);
        }

        $game->delete();

        return redirect()->route('games.index')
            ->with('success', 'Game deleted successfully!');
    }

    /**
     * Play a specific game.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function play($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        // Render the appropriate game view based on slug
        switch ($slug) {
            case 'snake':
                return view('games.play.snake', compact('game'));
            case 'memory':
                return view('games.play.memory', compact('game'));
            case 'space-shooter':
                return view('games.play.space-shooter', compact('game'));
            case 'cube-runner':
                return view('games.play.cube-runner', compact('game'));
            case '3d-racing':
                return view('games.play.3d-racing', compact('game'));
            case 'puzzle':
                return view('games.play.puzzle', compact('game'));
            default:
                return view('games.play.default', compact('game'));
        }
    }

    /**
     * Search for games by name or genre.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        $games = Game::where('name', 'like', '%' . $query . '%')
            ->orWhere('genre', 'like', '%' . $query . '%')
            ->orWhere('developer', 'like', '%' . $query . '%')
            ->latest()
            ->paginate(12);

        return view('games.search', compact('games', 'query'));
    }

    /**
     * Filter games by genre.
     *
     * @param  string  $genre
     * @return \Illuminate\Http\Response
     */
    public function filterByGenre($genre)
    {
        $games = Game::where('genre', $genre)
            ->latest()
            ->paginate(12);

        return view('games.genre', compact('games', 'genre'));
    }

    /**
     * Display top rated games.
     *
     * @return \Illuminate\Http\Response
     */
    public function topRated()
    {
        $games = Game::whereNotNull('rating')
            ->orderBy('rating', 'desc')
            ->paginate(12);

        return view('games.top-rated', compact('games'));
    }

    /**
     * Display recently released games.
     *
     * @return \Illuminate\Http\Response
     */
    public function recentReleases()
    {
        $games = Game::whereNotNull('release_date')
            ->where('release_date', '<=', now())
            ->orderBy('release_date', 'desc')
            ->paginate(12);

        return view('games.recent-releases', compact('games'));
    }

    /**
     * Display upcoming games.
     *
     * @return \Illuminate\Http\Response
     */
    public function upcomingGames()
    {
        $games = Game::whereNotNull('release_date')
            ->where('release_date', '>', now())
            ->orderBy('release_date', 'asc')
            ->paginate(12);

        return view('games.upcoming', compact('games'));
    }
}