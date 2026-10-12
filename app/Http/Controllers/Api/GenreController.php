<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        return response()->json(Genre::latest()->get());
    }

    public function store(StoreGenreRequest $request)
    {
        $genre = Genre::create($request->validated());
        return response()->json($genre, 201);
    }

    public function show(Genre $genre)
    {
        return response()->json($genre);
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());
        return response()->json($genre);
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return response()->json(null, 204);
    }
}
