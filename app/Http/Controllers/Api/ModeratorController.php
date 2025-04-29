<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ModeratorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:moderator']);
    }

    public function indexStories (): JsonResponse
    {
        $stories = Story::paginate(10);
        return response()->json(['data' => $stories]);
    }

    public function updateStory(Request $request, Story $story)
    {
        Gate::authorize('update', $story);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'genre' => 'nullable|string|max:255',
        ]);

        $story->update($request->all());
        return response()->json(['message' => 'Story updated successfully', 'data' => $story]);
    }

    public function destroyStory(Story $story)
    {
        Gate::authorize('delete', $story);

        $story->delete();
        return response()->json(['message' => 'Story deleted successfully']);
    }
}
