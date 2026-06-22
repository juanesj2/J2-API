<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CoupleFoodPlace;
use App\Models\CoupleFoodDish;
use App\Models\CoupleMovie;
use App\Models\Couple;

class WidgetController extends Controller
{
    private function getCoupleForUser($userId)
    {
        return Couple::where('user1_id', $userId)->orWhere('user2_id', $userId)->first();
    }

    // --- FOOD PLACES ---
    public function getFoodPlaces()
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json([]);

        $places = CoupleFoodPlace::where('couple_id', $couple->id)
            ->with('dishes')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($place) {
                if ($place->image_url) {
                    $place->image_url_full = asset('storage/' . $place->image_url);
                }
                $place->dishes->map(function ($dish) {
                    if ($dish->image_url) {
                        $dish->image_url_full = asset('storage/' . $dish->image_url);
                    }
                    return $dish;
                });
                return $place;
            });

        return response()->json($places);
    }

    public function addFoodPlace(Request $request)
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json(['message' => 'Sin pareja'], 403);

        $request->validate([
            'name' => 'required|string',
            'location' => 'nullable|string',
            'rating' => 'nullable|integer',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'is_favorite' => 'nullable|boolean'
        ]);

        $place = CoupleFoodPlace::create([
            'couple_id' => $couple->id,
            'name' => $request->name,
            'location' => $request->location,
            'rating' => $request->rating ?? 5,
            'description' => $request->description,
            'category' => $request->category,
            'is_favorite' => filter_var($request->is_favorite, FILTER_VALIDATE_BOOLEAN)
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('food_places', $filename, 'public');
            $place->image_url = $path;
            $place->save();
        }

        $place->load('dishes');
        if ($place->image_url) {
            $place->image_url_full = asset('storage/' . $place->image_url);
        }

        return response()->json($place);
    }

    public function updateFoodPlace(Request $request, $id)
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json(['message' => 'Sin pareja'], 403);

        $place = CoupleFoodPlace::where('couple_id', $couple->id)->find($id);
        if (!$place) return response()->json(['message' => 'Lugar no encontrado'], 404);

        $request->validate([
            'name' => 'required|string',
            'location' => 'nullable|string',
            'rating' => 'nullable|integer',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'is_favorite' => 'nullable|boolean'
        ]);

        $place->update([
            'name' => $request->name,
            'location' => $request->location,
            'rating' => $request->rating ?? $place->rating,
            'description' => $request->description,
            'category' => $request->category,
            'is_favorite' => $request->has('is_favorite') ? filter_var($request->is_favorite, FILTER_VALIDATE_BOOLEAN) : $place->is_favorite
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('food-places', 'public');
            $place->update(['image_url' => $path]);
        }

        return response()->json($place);
    }

    public function deleteFoodPlace($id)
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json([], 403);

        $place = CoupleFoodPlace::where('couple_id', $couple->id)->find($id);
        if ($place) {
            if ($place->image_url && \Storage::disk('public')->exists($place->image_url)) {
                \Storage::disk('public')->delete($place->image_url);
            }
            $place->delete();
        }

        return response()->json(['success' => true]);
    }

    // --- FOOD DISHES ---
    public function addFoodDish(Request $request, $placeId)
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json([], 403);

        $place = CoupleFoodPlace::where('couple_id', $couple->id)->find($placeId);
        if (!$place) return response()->json([], 404);

        $request->validate([
            'name' => 'required|string',
            'rating' => 'nullable|integer',
            'description' => 'nullable|string'
        ]);

        $dish = CoupleFoodDish::create([
            'food_place_id' => $place->id,
            'name' => $request->name,
            'rating' => $request->rating ?? 5,
            'description' => $request->description
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('food_dishes', $filename, 'public');
            $dish->image_url = $path;
            $dish->save();
        }

        if ($dish->image_url) {
            $dish->image_url_full = asset('storage/' . $dish->image_url);
        }

        return response()->json($dish);
    }

    public function updateFoodDish(Request $request, $placeId, $dishId)
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json([], 403);

        $place = CoupleFoodPlace::where('couple_id', $couple->id)->find($placeId);
        if (!$place) return response()->json([], 404);

        $dish = CoupleFoodDish::where('food_place_id', $place->id)->find($dishId);
        if (!$dish) return response()->json([], 404);

        $request->validate([
            'name' => 'required|string',
            'rating' => 'nullable|integer',
            'description' => 'nullable|string'
        ]);

        $dish->update([
            'name' => $request->name,
            'rating' => $request->rating ?? 5,
            'description' => $request->description
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('food_dishes', $filename, 'public');
            $dish->image_url = $path;
            $dish->save();
        }

        if ($dish->image_url) {
            $dish->image_url_full = asset('storage/' . $dish->image_url);
        }

        return response()->json($dish);
    }

    public function deleteFoodDish($placeId, $dishId)
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json([], 403);

        $place = CoupleFoodPlace::where('couple_id', $couple->id)->find($placeId);
        if (!$place) return response()->json([], 404);

        $dish = CoupleFoodDish::where('food_place_id', $place->id)->find($dishId);
        if ($dish) {
            if ($dish->image_url && \Storage::disk('public')->exists($dish->image_url)) {
                \Storage::disk('public')->delete($dish->image_url);
            }
            $dish->delete();
        }

        return response()->json(['success' => true]);
    }

    // --- MOVIES ---
    public function getMovies()
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json([]);

        $movies = CoupleMovie::where('couple_id', $couple->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($m) {
                if ($m->image_url) {
                    $m->image_url_full = asset('storage/' . $m->image_url);
                }
                return $m;
            });

        return response()->json($movies);
    }

    public function addMovie(Request $request)
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json(['message' => 'Sin pareja'], 403);

        $request->validate([
            'title' => 'required|string',
            'rating' => 'nullable|integer',
            'who_fell_asleep' => 'nullable|string',
            'favorite_quote' => 'nullable|string',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'is_favorite' => 'nullable|boolean'
        ]);

        $movie = CoupleMovie::create([
            'couple_id' => $couple->id,
            'title' => $request->title,
            'rating' => $request->rating ?? 5,
            'who_fell_asleep' => $request->who_fell_asleep,
            'favorite_quote' => $request->favorite_quote,
            'description' => $request->description,
            'genre' => $request->genre,
            'is_favorite' => filter_var($request->is_favorite, FILTER_VALIDATE_BOOLEAN)
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('movies', $filename, 'public');
            $movie->image_url = $path;
            $movie->save();
        }

        if ($movie->image_url) {
            $movie->image_url_full = asset('storage/' . $movie->image_url);
        }

        return response()->json($movie);
    }

    public function updateMovie(Request $request, $id)
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json(['message' => 'Sin pareja'], 403);

        $movie = CoupleMovie::where('couple_id', $couple->id)->find($id);
        if (!$movie) return response()->json(['message' => 'Película no encontrada'], 404);

        $request->validate([
            'title' => 'required|string',
            'rating' => 'nullable|integer',
            'who_fell_asleep' => 'nullable|string',
            'favorite_quote' => 'nullable|string',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'is_favorite' => 'nullable|boolean'
        ]);

        $movie->update([
            'title' => $request->title,
            'rating' => $request->rating ?? $movie->rating,
            'who_fell_asleep' => $request->who_fell_asleep,
            'favorite_quote' => $request->favorite_quote,
            'description' => $request->description,
            'genre' => $request->genre,
            'is_favorite' => $request->has('is_favorite') ? filter_var($request->is_favorite, FILTER_VALIDATE_BOOLEAN) : $movie->is_favorite
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->storeAs('movies', time() . '_' . $request->file('image')->getClientOriginalName(), 'public');
            if ($movie->image_url && \Storage::disk('public')->exists($movie->image_url)) {
                \Storage::disk('public')->delete($movie->image_url);
            }
            $movie->update(['image_url' => $path]);
        }

        return response()->json($movie);
    }

    public function deleteMovie($id)
    {
        $couple = $this->getCoupleForUser(Auth::id());
        if (!$couple) return response()->json([], 403);

        $movie = CoupleMovie::where('couple_id', $couple->id)->find($id);
        if ($movie) {
            if ($movie->image_url && \Storage::disk('public')->exists($movie->image_url)) {
                \Storage::disk('public')->delete($movie->image_url);
            }
            $movie->delete();
        }

        return response()->json(['success' => true]);
    }
}
